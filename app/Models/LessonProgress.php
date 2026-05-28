<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id',
        'is_completed',
        'completed_at',
        'watched_seconds',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function markCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    public function updateWatchedSeconds(int $seconds): void
    {
        if ($seconds > $this->watched_seconds) {
            $this->update(['watched_seconds' => $seconds]);
        }
    }
}