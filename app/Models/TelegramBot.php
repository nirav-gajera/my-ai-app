<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    protected $fillable = [
        'name',
        'token',
        'bot_username',
        'bot_url',
        'webhook_url',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'token' => 'encrypted',
        ];
    }

    /**
     * Scope to get the currently active bot.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the currently active bot instance, or null if none.
     */
    public static function getActive(): ?self
    {
        return static::active()->first();
    }

    /**
     * Activate this bot and deactivate all others.
     */
    public function activate(): void
    {
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate this bot.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
