<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'status',
        'enrolled_at',
        'completed_at',
        'progress_percent',
    ];

    protected $casts = [
        'enrolled_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function markCompleted(): void
    {
        $this->update([
            'status'           => 'completed',
            'completed_at'     => now(),
            'progress_percent' => 100,
        ]);
    }
}