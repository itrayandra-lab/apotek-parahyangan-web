<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'user_id',
        'type',
        'content',
        'metadata',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isUserMessage(): bool
    {
        return $this->type === 'user';
    }

    public function isBotMessage(): bool
    {
        return $this->type === 'bot';
    }

    public function isSystemMessage(): bool
    {
        return $this->type === 'system';
    }

    public function getFormattedContentAttribute(): string
    {
        $content = htmlspecialchars($this->content, ENT_QUOTES, 'UTF-8');

        $content = preg_replace(
            '/(https?:\\/\\/[^\\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">$1</a>',
            $content
        );

        return nl2br($content);
    }
}
