<?php

namespace App\Console\Commands;

use App\Models\ExamSession;
use Illuminate\Console\Command;


class ExpireExamSession extends Command
{
    protected $signature = 'exam:expire-sessions';
    protected $description = 'Expire exam sessions that have passed their end time';

    public function handle(): void
    {
        $expired = ExamSession::where('status', 'in_progress')
            ->where('expires_at', '<', now())
            ->with(['answers', 'exam.questions'])
            ->get();

        foreach ($expired as $session) {
            $session->answers->each(fn ($a) => $a->autoGrade());

            $score      = $session->calculateScore();
            $isPassed   = $score >= $session->exam->pass_score;

            $session->update([
                'status'    => 'graded',
                'score'     => $score,
                'is_passed' => $isPassed,
                'submitted_at' => $session->expired_at,
            ]);
            
            $this->info("Session #{$session->id} expired → Score: {$score}%");
        }
        $this->info("Total expired: {$expired->count()} sessions");
    }
}