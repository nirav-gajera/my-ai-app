<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIService
{
    private const DEFAULT_CHAT_MODEL = 'gemini-2.5-flash-lite';
    private const ALLOWED_CHAT_MODELS = [
        'gemini-2.5-flash-lite',
        'gemini-2.5-flash-medium',
        'gemini-2.5-pro',
    ];
    private const DEFAULT_EMBEDDING_MODEL = 'gemini-embedding-001';
    private const ALLOWED_EMBEDDING_MODELS = [
        'gemini-embedding-001',
    ];

    private function client(string $version): PendingRequest
    {
        $apiKey = (string) config('services.gemini.key');

        if ($apiKey === '') {
            throw new RuntimeException('Missing GEMINI_API_KEY.');
        }

        return Http::baseUrl("https://generativelanguage.googleapis.com/{$version}")
            ->acceptJson()
            ->contentType('application/json')
            ->timeout((int) config('services.gemini.timeout', 60))
            ->retry(2, 400)
            ->withQueryParameters([
                'key' => $apiKey,
            ]);
    }

    public function embedding(string $text): array
    {
        return $this->embeddings([$text])[0] ?? [];
    }

    public function embeddings(array $inputs): array
    {
        $model = $this->resolveEmbeddingModel();
        $version = (string) config('services.gemini.embedding_version', 'v1beta');

        $payload = [
            'requests' => array_map(fn (string $input) => [
                'model' => "models/{$model}",
                'taskType' => 'RETRIEVAL_DOCUMENT',
                'content' => [
                    'parts' => [
                        ['text' => $input],
                    ],
                ],
            ], $inputs),
        ];

        $res = $this->client($version)->post("models/{$model}:batchEmbedContents", $payload);

        if (!$res->successful()) {
            throw new RuntimeException('Embedding request failed: ' . $res->body());
        }

        return collect($res->json('embeddings', []))
            ->map(fn (array $item) => $item['values'] ?? [])
            ->values()
            ->all();
    }

    public function answerQuestion(string $question, string $context, array $history = []): array
    {
        $version = (string) config('services.gemini.chat_version', 'v1');
        $conversation = $this->normalizeHistory($history, $context, $question);
        $models = $this->resolveChatModels();

        foreach ($models as $model) {
            $res = $this->client($version)->post("models/{$model}:generateContent", [
                'contents' => $conversation,
                'generationConfig' => [
                    'temperature' => 0.2,
                    'topP' => 0.8,
                    'maxOutputTokens' => 1024,
                ],
            ]);

            if ($res->successful()) {
                return $this->parseChatResponse($res);
            }

            if ($res->status() === 404) {
                continue;
            }

            throw new RuntimeException('Chat request failed: ' . $res->body());
        }

        throw new RuntimeException('Chat request failed: no supported Gemini chat model found.');
    }

    private function normalizeHistory(array $history, string $context, string $question): array
    {
        $conversation = [];

        foreach ($history as $item) {
            $role = ($item['role'] ?? '') === 'assistant' ? 'model' : 'user';
            $text = trim((string) ($item['content'] ?? ''));

            if ($text === '') {
                continue;
            }

            $conversation[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $text],
                ],
            ];
        }

        $conversation[] = [
            'role' => 'user',
            'parts' => [
                [
                    'text' => implode("\n\n", [
                        'You are a retrieval-augmented assistant for a Laravel application.',
                        'Answer only from the supplied knowledge base context.',
                        'If the context is insufficient, say that the answer is not available in the uploaded knowledge base.',
                        "Knowledge base context:\n{$context}",
                        "Question:\n{$question}",
                    ]),
                ],
            ],
        ];

        return $conversation;
    }

    private function parseChatResponse($res): array
    {
        $parts = $res->json('candidates.0.content.parts', []);
        $content = collect($parts)
            ->pluck('text')
            ->filter()
            ->implode("\n");

        return [
            'content' => trim($content),
            'usage' => $res->json('usageMetadata', []),
        ];
    }

    private function resolveChatModels(): array
    {
        $preferred = (string) config('services.gemini.chat_model', self::DEFAULT_CHAT_MODEL);
        $models = array_filter([$preferred, self::DEFAULT_CHAT_MODEL], fn ($model) => in_array($model, self::ALLOWED_CHAT_MODELS, true));

        return array_values(array_unique($models));
    }

    private function resolveEmbeddingModel(): string
    {
        $preferred = (string) config('services.gemini.embedding_model', self::DEFAULT_EMBEDDING_MODEL);

        if (in_array($preferred, self::ALLOWED_EMBEDDING_MODELS, true)) {
            return $preferred;
        }

        return self::DEFAULT_EMBEDDING_MODEL;
    }
}
