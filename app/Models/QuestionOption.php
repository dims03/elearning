<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function examAnswers()
    {
        return $this->hasMany(ExamAnswer::class, 'selected_option_id');
    }
}