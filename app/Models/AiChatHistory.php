<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class AiChatHistory extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'session_title',
        'role',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public static function getSessionsForUser(int $userId): \Illuminate\Support\Collection
    {
        return static::where('user_id', $userId)
            ->select([
                'session_id',
                DB::raw("MAX(NULLIF(session_title, '')) as session_title"),
                DB::raw('MAX(created_at) as last_message_at'),
                DB::raw('COUNT(*) as message_count'),
            ])
            ->groupBy('session_id')
            ->orderByDesc('last_message_at')
            ->get();
    }

    public static function getMessages(int $userId, string $sessionId): \Illuminate\Support\Collection
    {
        return static::where('user_id', $userId)
            ->where('session_id', $sessionId)
            ->orderBy('created_at')
            ->get();
    }
}
