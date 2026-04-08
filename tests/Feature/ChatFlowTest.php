<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\KnowledgeDocument;
use App\Services\OpenAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ChatFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_answers_a_question_and_stores_messages(): void
    {
        $sessionId = 'test-workspace';
        $this->withSession(['rag_workspace_key' => $sessionId]);

        $knowledgeDocument = KnowledgeDocument::create([
            'session_id' => $sessionId,
            'title' => 'Laravel Docs',
            'source_name' => 'docs.md',
            'source_type' => 'file',
            'original_content' => 'Laravel queues process background jobs.',
            'chunk_count' => 1,
        ]);

        Document::create([
            'knowledge_document_id' => $knowledgeDocument->id,
            'content' => 'Laravel queues process background jobs and can be monitored with workers.',
            'embedding' => json_encode([1.0, 0.0, 0.0], JSON_THROW_ON_ERROR),
            'chunk_index' => 0,
            'character_count' => 72,
            'source_name' => 'docs.md',
            'metadata' => ['title' => 'Laravel Docs'],
        ]);

        $mock = Mockery::mock(OpenAIService::class);
        $mock->shouldReceive('embedding')
            ->once()
            ->andReturn([1.0, 0.0, 0.0]);
        $mock->shouldReceive('answerQuestion')
            ->once()
            ->andReturn([
                'content' => 'Laravel queues handle background jobs.',
                'usage' => ['total_tokens' => 42],
            ]);

        $this->app->instance(OpenAIService::class, $mock);

        $conversationResponse = $this->postJson('/conversations');
        $conversationResponse->assertCreated();
        $conversationId = $conversationResponse->json('conversation.id');
        $this->assertNotNull($conversationId);

        $response = $this->postJson("/conversations/{$conversationId}/messages", [
            'question' => 'What are Laravel queues for?',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('assistant_message.content', 'Laravel queues handle background jobs.')
            ->assertJsonPath('assistant_message.citations.0.title', 'Laravel Docs');

        $this->assertDatabaseCount('messages', 2);
        $this->assertDatabaseHas('conversations', [
            'id' => $conversationId,
            'title' => 'What are Laravel queues for?',
        ]);
    }
}
