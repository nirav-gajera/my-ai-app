<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('session_id')->nullable()->default(null)->change();
        });

        Schema::table('knowledge_documents', function (Blueprint $table) {
            $table->string('session_id')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->string('session_id')->nullable(false)->change();
        });

        Schema::table('knowledge_documents', function (Blueprint $table) {
            $table->string('session_id')->nullable(false)->change();
        });
    }
};
