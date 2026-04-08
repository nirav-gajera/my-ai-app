<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('knowledge_document_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('chunk_index')->default(0)->after('embedding');
            $table->unsignedInteger('character_count')->default(0)->after('chunk_index');
            $table->string('source_name')->nullable()->after('character_count');
            $table->json('metadata')->nullable()->after('source_name');
            $table->index(['knowledge_document_id', 'chunk_index']);
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['knowledge_document_id', 'chunk_index']);
            $table->dropConstrainedForeignId('knowledge_document_id');
            $table->dropColumn(['chunk_index', 'character_count', 'source_name', 'metadata']);
        });
    }
};
