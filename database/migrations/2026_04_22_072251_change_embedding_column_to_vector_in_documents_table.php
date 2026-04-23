<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable pgvector extension
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::table('documents', function (Blueprint $table) {
            // Drop the old longText column
            $table->dropColumn('embedding');
        });

        // Add the new vector column with 3072 dimensions
        DB::statement('ALTER TABLE documents ADD COLUMN embedding vector(3072)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('embedding');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->longText('embedding')->after('content');
        });
    }
};
