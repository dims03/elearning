<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSessionSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::role('student')->get();

        foreach ($students as $student) {
            // Ambil kursus yang diikuti siswa ini
            $enrolledCourseIds = CourseEnrollment::where('user_id', $student->id)
                ->pluck('course_id');

            // Ambil ujian dari kursus yang diikuti
            $exams = Exam::whereIn('course_id', $enrolledCourseIds)
                ->where('status', 'published')
                ->get();

            foreach ($exams as $exam) {
                // 70% chance siswa mengerjakan ujian
                if (rand(1, 10) > 7) continue;

                $attempts = rand(1, $exam->max_attempts);

                for ($attempt = 1; $attempt <= $attempts; $attempt++) {
                    $startedAt   = now()->subDays(rand(1, 20));
                    $submittedAt = $startedAt->copy()->addMinutes(rand(15, $exam->duration_minutes));

                    $session = ExamSession::create([
                        'exam_id'        => $exam->id,
                        'user_id'        => $student->id,
                        'attempt_number' => $attempt,
                        'status'         => 'graded',
                        'started_at'     => $startedAt,
                        'submitted_at'   => $submittedAt,
                        'expires_at'     => $startedAt->copy()->addMinutes($exam->duration_minutes),
                    ]);

                    $questions    = $exam->questions()->with('options')->get();
                    $totalPoints  = 0;
                    $earnedPoints = 0;

                    // Simulasi jawaban — semakin banyak attempt, semakin tinggi skor
                    $correctChance = match ($attempt) {
                        1 => rand(40, 80),  // attempt 1: 40-80% benar
                        2 => rand(60, 95),  // attempt 2: 60-95% benar
                        default => rand(70, 100),
                    };

                    foreach ($questions as $question) {
                        $totalPoints += $question->points;

                        if ($question->type === 'essay') {
                            // Essay: guru belum grade, score 0 dulu
                            $scoreGiven = rand(0, 1) === 1
                                ? rand(0, $question->points)
                                : 0;

                            ExamAnswer::create([
                                'exam_session_id'  => $session->id,
                                'question_id'      => $question->id,
                                'selected_option_id' => null,
                                'answer_text'      => $this->generateEssayAnswer($question->question_text),
                                'is_correct'       => $scoreGiven > 0,
                                'score_given'      => $scoreGiven,
                                'teacher_feedback' => $scoreGiven > 0 ? 'Jawaban cukup baik.' : null,
                            ]);
                            $earnedPoints += $scoreGiven;

                        } else {
                            // MCQ / True-False
                            $isCorrect = rand(1, 100) <= $correctChance;
                            $correctOption = $question->options->firstWhere('is_correct', true);
                            $wrongOptions  = $question->options->where('is_correct', false);

                            $selectedOption = $isCorrect
                                ? $correctOption
                                : $wrongOptions->random();

                            $score = $isCorrect ? $question->points : 0;
                            $earnedPoints += $score;

                            ExamAnswer::create([
                                'exam_session_id'    => $session->id,
                                'question_id'        => $question->id,
                                'selected_option_id' => $selectedOption?->id,
                                'answer_text'        => null,
                                'is_correct'         => $isCorrect,
                                'score_given'        => $score,
                            ]);
                        }
                    }

                    // Hitung final score
                    $score    = $totalPoints > 0
                        ? (int) round(($earnedPoints / $totalPoints) * 100)
                        : 0;
                    $isPassed = $score >= $exam->pass_score;

                    $session->update([
                        'score'     => $score,
                        'is_passed' => $isPassed,
                    ]);
                }
            }
        }

        $total  = ExamSession::count();
        $passed = ExamSession::where('is_passed', true)->count();
        $this->command->info("✅ Exam sessions created: {$total} sessions, {$passed} passed");
    }

    private function generateEssayAnswer(string $question): string
    {
        $answers = [
            'Menurut saya, konsep ini sangat penting dalam pemrograman modern. Dengan menggunakan pendekatan yang tepat, kita dapat membuat kode yang lebih bersih dan mudah dipelihara.',
            'Berdasarkan materi yang telah dipelajari, perbedaan utamanya terletak pada cara penggunaan dan scope dari masing-masing elemen tersebut.',
            'Konsep ini digunakan untuk memisahkan logika aplikasi agar lebih terstruktur. Contohnya dalam Laravel, kita menggunakan middleware untuk memfilter request sebelum sampai ke controller.',
            'Dalam pemrograman berorientasi objek, konsep ini memungkinkan kita untuk membuat kode yang lebih modular dan reusable.',
            'Perbedaan utama antara keduanya adalah pada cara kerja dan penggunaannya dalam konteks yang berbeda-beda.',
        ];

        return $answers[array_rand($answers)];
    }
}
