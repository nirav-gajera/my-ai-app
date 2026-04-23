<?php

namespace App\Services;

use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\Message;
use function Laravel\Ai\agent;

class OpenAIService
{
    public function embedding(string $text): array
    {
        return $this->embeddings([$text])[0] ?? [];
    }

    public function embeddings(array $inputs): array
    {
        return Embeddings::for($inputs)
            ->timeout((int) config('services.gemini.timeout', 60))
            ->generate(
                provider: Lab::Gemini,
                model: (string) config('services.gemini.embedding_model', 'gemini-embedding-001'),
            )
            ->embeddings;
    }

    public function answerQuestion(string $question, string $context, array $history = []): array
    {
        $response = agent(
            instructions: implode("\n", [
                'You are a helpful and knowledgeable assistant that answers questions based on provided context.',
                'Guidelines:
                1. Answer ONLY using the provided context
                2. If the answer is not in the context, clearly state that the information is not available
                3. Cite the source when providing information
                4. Be concise and clear in your responses
                5. If the user asks about something outside the context, offer to help with related topics from the knowledge base
                6. Maintain a professional and friendly tone
                
                Never:
                - Make up information not in the context
                - Assume information beyond what is provided
                - Be rude or dismissive',
                '',
                "Knowledge base context:\n{$context}",
            ]),
            messages: $this->normalizeHistory($history),
        )->prompt(
            prompt: $question,
            provider: Lab::Gemini,
            model: (string) config('services.gemini.chat_model', 'gemini-2.5-flash-lite'),
        );

        return [
            'content' => trim($response->text),
            'usage' => $response->usage->toArray(),
            'meta' => $response->meta->toArray(),
        ];
    }

    private function normalizeHistory(array $history): array
    {
        return collect($history)
            ->map(function (array $item) {
                $text = trim((string) ($item['content'] ?? ''));

                if ($text === '') {
                    return null;
                }

                return new Message(
                    role: ($item['role'] ?? '') === 'assistant' ? 'assistant' : 'user',
                    content: $text,
                );
            })
            ->filter()
            ->values()
            ->all();
    }
}
