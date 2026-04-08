<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'knowledge_document_id',
        'content',
        'embedding',
        'chunk_index',
        'character_count',
        'source_name',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function knowledgeDocument(): BelongsTo
    {
        return $this->belongsTo(KnowledgeDocument::class);
    }
}
