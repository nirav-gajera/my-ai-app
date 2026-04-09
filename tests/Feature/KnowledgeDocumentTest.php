<?php

namespace Tests\Feature;

use App\Models\KnowledgeDocument;
use App\Models\User;
use App\Services\OpenAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class KnowledgeDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_indexes_a_knowledge_document(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $mock = Mockery::mock(OpenAIService::class);
        $mock->shouldReceive('embeddings')
            ->once()
            ->andReturn([[0.11, 0.22, 0.33]]);

        $this->app->instance(OpenAIService::class, $mock);

        $response = $this->postJson('/knowledge-documents', [
            'title' => 'Support FAQ',
            'content' => str_repeat('Laravel RAG support content. ', 4),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('document.title', 'Support FAQ')
            ->assertJsonPath('document.chunk_count', 1);

        $this->assertDatabaseCount('knowledge_documents', 1);
        $this->assertDatabaseCount('documents', 1);
        $this->assertSame(1, KnowledgeDocument::first()->chunk_count);
    }
}
