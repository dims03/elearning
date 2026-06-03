<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::role('student')->get();
        $courses  = Course::with('chapters.lessons')->where('status', 'published')->get();

        // Setiap siswa enroll ke 2-4 kursus secara acak
        foreach ($students as $student) {
            $enrollCount    = rand(2, 4);
            $randomCourses  = $courses->random(min($enrollCount, $courses->count()));

            foreach ($randomCourses as $course) {
                // Cek sudah enroll belum
                if (CourseEnrollment::where('user_id', $student->id)
                    ->where('course_id', $course->id)->exists()) {
                    continue;
                }

                // Progress acak: 0%, 25%, 50%, 75%, 100%
                $progressOptions = [0, 25, 50, 75, 100];
                $progress        = $progressOptions[array_rand($progressOptions)];

                $allLessons = $course->chapters->sortBy('order')
                    ->flatMap(fn ($c) => $c->lessons->sortBy('order'))
                    ->values();

                $totalLessons     = $allLessons->count();
                $completedCount   = (int) round($totalLessons * $progress / 100);
                $lessonsToComplete = $allLessons->take($completedCount);

                $enrolledAt   = now()->subDays(rand(10, 60));
                $completedAt  = $progress >= 100 ? now()->subDays(rand(1, 10)) : null;

                $enrollment = CourseEnrollment::create([
                    'user_id'          => $student->id,
                    'course_id'        => $course->id,
                    'status'           => $progress >= 100 ? 'completed' : 'active',
                    'progress_percent' => $progress,
                    'enrolled_at'      => $enrolledAt,
                    'completed_at'     => $completedAt,
                ]);

                // Buat lesson progress untuk lesson yang sudah diselesaikan
                foreach ($lessonsToComplete as $lesson) {
                    LessonProgress::firstOrCreate(
                        ['user_id' => $student->id, 'lesson_id' => $lesson->id],
                        [
                            'is_completed'    => true,
                            'completed_at'    => $enrolledAt->copy()->addDays(rand(1, 5)),
                            'watched_seconds' => $lesson->duration_minutes * 60,
                        ]
                    );
                }
            }
        }

        $total = CourseEnrollment::count();
        $this->command->info("✅ Enrollments created: {$total} enrollments");
    }
}
