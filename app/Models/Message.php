<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'citations',
        'meta',
        'reaction',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'citations' => 'array',
            'meta' => 'array',
            'is_pinned' => 'boolean',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
