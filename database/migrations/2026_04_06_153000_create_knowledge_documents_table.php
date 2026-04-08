<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_documents', function (Blueprint $table) {
            $table->id();
            // $table->string('session_id')->index();
            $table->string('title');
            $table->string('source_name')->nullable();
            $table->string('source_type')->default('text');
            $table->longText('original_content');
            $table->unsignedInteger('chunk_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_documents');
    }
};
