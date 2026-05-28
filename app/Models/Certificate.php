<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'exam_session_id',
        'code',
        'file_path',
        'final_score',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Certificate $cert) {
            if (empty($cert->code)) {
                // Format: CERT-XXXXXXXX (uppercase, unik)
                $cert->code = 'CERT-' . strtoupper(Str::random(8));
            }
            if (empty($cert->issued_at)) {
                $cert->issued_at = now();
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getVerifyUrlAttribute(): string
    {
        return route('certificate.verify', $this->code);
    }

    public function hasFile(): bool
    {
        return ! empty($this->file_path);
    }
}
