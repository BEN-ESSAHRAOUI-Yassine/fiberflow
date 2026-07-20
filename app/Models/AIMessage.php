<?php

namespace App\Models;

use App\Enums\MessageRole;
use Database\Factories\AIMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIMessage extends Model
{
    /** @use HasFactory<AIMessageFactory> */
    use HasFactory;

    protected $fillable = ['conversation_id', 'role', 'content'];

    protected function casts(): array
    {
        return [
            'role' => MessageRole::class,
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AIConversation::class, 'conversation_id');
    }
}
