<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Document;
use App\Models\KnowledgeDocument;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RagService
{
    public function __construct(
        private readonly OpenAIService $openAI,
        private readonly TextChunker $chunker,
        private readonly SimilarityService $similarity,
    ) {
    }

    public function ingest(
        int $userId,
        string $title,
        string $content,
        ?string $sourceName = null,
        string $sourceType = 'text',
    ): KnowledgeDocument {
        $chunks = $this->chunker->split($content);
        $embeddings = $this->openAI->embeddings($chunks);

        return DB::transaction(function () use ($userId, $title, $content, $sourceName, $sourceType, $chunks, $embeddings) {
            $document = KnowledgeDocument::create([
                'user_id' => $userId,
                'title' => $title,
                'source_name' => $sourceName,
                'source_type' => $sourceType,
                'original_content' => $content,
                'chunk_count' => count($chunks),
            ]);

            foreach ($chunks as $index => $chunk) {
                Document::create([
                    'knowledge_document_id' => $document->id,
                    'content' => $chunk,
                    'embedding' => json_encode($embeddings[$index] ?? [], JSON_THROW_ON_ERROR),
                    'chunk_index' => $index,
                    'character_count' => mb_strlen($chunk),
                    'source_name' => $sourceName ?? $title,
                    'metadata' => [
                        'title' => $title,
                        'source_type' => $sourceType,
                    ],
                ]);
            }

            return $document->loadCount('chunks');
        });
    }

    public function reindex(
        KnowledgeDocument $document,
        string $title,
        string $content,
        ?string $sourceName = null,
        string $sourceType = 'text',
    ): KnowledgeDocument {
        $chunks = $this->chunker->split($content);
        $embeddings = $this->openAI->embeddings($chunks);

        return DB::transaction(function () use ($document, $title, $content, $sourceName, $sourceType, $chunks, $embeddings) {
            $document->chunks()->delete();

            $document->update([
                'title' => $title,
                'source_name' => $sourceName,
                'source_type' => $sourceType,
                'original_content' => $content,
                'chunk_count' => count($chunks),
            ]);

            foreach ($chunks as $index => $chunk) {
                Document::create([
                    'knowledge_document_id' => $document->id,
                    'content' => $chunk,
                    'embedding' => json_encode($embeddings[$index] ?? [], JSON_THROW_ON_ERROR),
                    'chunk_index' => $index,
                    'character_count' => mb_strlen($chunk),
                    'source_name' => $sourceName ?? $title,
                    'metadata' => [
                        'title' => $title,
                        'source_type' => $sourceType,
                    ],
                ]);
            }

            return $document->fresh()->loadCount('chunks');
        });
    }

    public function answer(Conversation $conversation, string $question): array
    {
        $history = $conversation->messages()
            ->latest()
            ->take(8)
            ->get(['role', 'content'])
            ->reverse()
            ->values()
            ->map(fn (Message $message) => [
                'role' => $message->role,
                'content' => $message->content,
            ])
            ->all();

        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => $question,
        ]);

        $matches = $this->retrieveRelevantChunks($conversation->user_id, $question);

        if ($matches === []) {
            $assistantMessage = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => 'I could not find relevant information in the uploaded knowledge base. Add documents or ask a question that matches the current data.',
                'citations' => [],
            ]);

            $this->touchConversation($conversation, $question);

            return [
                'conversation' => $conversation->fresh(),
                'user_message' => $userMessage,
                'assistant_message' => $assistantMessage,
            ];
        }

        $context = collect($matches)
            ->map(fn (array $match, int $index) => sprintf(
                "[Source %d | %s | chunk %d]\n%s",
                $index + 1,
                $match['title'],
                $match['chunk_index'] + 1,
                $match['content']
            ))
            ->implode("\n\n");

        $response = $this->openAI->answerQuestion($question, $context, $history);

        $assistantMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $response['content'],
            'citations' => collect($matches)->map(fn (array $match) => [
                'document_id' => $match['knowledge_document_id'],
                'title' => $match['title'],
                'source_name' => $match['source_name'],
                'chunk_index' => $match['chunk_index'],
                'score' => round($match['score'], 4),
            ])->all(),
            'meta' => [
                'usage' => $response['usage'],
            ],
        ]);

        $this->touchConversation($conversation, $question);

        return [
            'conversation' => $conversation->fresh(),
            'user_message' => $userMessage,
            'assistant_message' => $assistantMessage,
        ];
    }

    public function retrieveRelevantChunks(int $userId, string $question, int $limit = 5): array
    {
        $questionEmbedding = $this->openAI->embedding($question);

        $chunks = Document::query()
            // ->whereVectorSimilarTo('embedding', $questionEmbedding, minSimilarity: 0.4)
            ->with('knowledgeDocument:id,title,user_id,source_name')
            ->whereHas('knowledgeDocument', fn ($query) => $query->where('user_id', $userId))
            ->get();

        $topMatches = $chunks->map(function (Document $chunk) use ($questionEmbedding) {
            $embedding = json_decode($chunk->embedding, true) ?: [];
            $knowledgeDocument = $chunk->knowledgeDocument;

            return [
                'knowledge_document_id' => $knowledgeDocument?->id,
                'title' => $knowledgeDocument?->title ?? 'Untitled document',
                'source_name' => $chunk->source_name ?? $knowledgeDocument?->source_name,
                'content' => $chunk->content,
                'chunk_index' => (int) $chunk->chunk_index,
                'score' => $this->similarity->cosine($questionEmbedding, $embedding),
            ];
        })
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->all();

        $bestScore = $topMatches[0]['score'] ?? 0.0;

        if ($bestScore < 0.15) {
            return [];
        }

        return $topMatches;
    }

    private function touchConversation(Conversation $conversation, string $question): void
    {
        $title = $conversation->messages()->where('role', 'user')->count() === 1
            ? Str::limit($question, 60, '...')
            : $conversation->title;

        $conversation->update([
            'title' => $title,
            'last_message_at' => now(),
        ]);
    }
}
