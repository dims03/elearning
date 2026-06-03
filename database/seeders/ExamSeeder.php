<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $exams = [
            // ── Laravel ───────────────────────────────────────────────────────
            'Laravel untuk Pemula' => [
                [
                    'title'            => 'Kuis 1 — Dasar Laravel',
                    'duration_minutes' => 30,
                    'pass_score'       => 70,
                    'max_attempts'     => 2,
                    'is_randomized'    => true,
                    'questions'        => [
                        ['text' => 'Apa kepanjangan dari MVC dalam Laravel?',
                         'type' => 'multiple_choice', 'points' => 2,
                         'explanation' => 'MVC = Model-View-Controller, pola arsitektur yang digunakan Laravel.',
                         'options' => [
                             ['text' => 'Model-View-Controller', 'correct' => true],
                             ['text' => 'Model-View-Component', 'correct' => false],
                             ['text' => 'Module-View-Controller', 'correct' => false],
                             ['text' => 'Model-Variable-Controller', 'correct' => false],
                         ]],
                        ['text' => 'Perintah artisan untuk membuat controller adalah...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'explanation' => 'php artisan make:controller NamaController',
                         'options' => [
                             ['text' => 'php artisan make:controller', 'correct' => true],
                             ['text' => 'php artisan create:controller', 'correct' => false],
                             ['text' => 'php artisan new:controller', 'correct' => false],
                             ['text' => 'php artisan generate:controller', 'correct' => false],
                         ]],
                        ['text' => 'File konfigurasi database Laravel ada di folder...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'config/database.php', 'correct' => true],
                             ['text' => 'app/database.php', 'correct' => false],
                             ['text' => 'database/config.php', 'correct' => false],
                             ['text' => '.env.database', 'correct' => false],
                         ]],
                        ['text' => 'Laravel menggunakan template engine bernama Blade.',
                         'type' => 'true_false', 'points' => 1,
                         'explanation' => 'Benar, Laravel menggunakan Blade sebagai template engine.',
                         'options' => [
                             ['text' => 'true', 'correct' => true],
                             ['text' => 'false', 'correct' => false],
                         ]],
                        ['text' => 'Jelaskan perbedaan antara Route::get() dan Route::post() di Laravel!',
                         'type' => 'essay', 'points' => 5, 'options' => []],
                    ],
                ],
                [
                    'title'            => 'Ujian Akhir — Laravel',
                    'duration_minutes' => 60,
                    'pass_score'       => 75,
                    'max_attempts'     => 1,
                    'is_randomized'    => true,
                    'questions'        => [
                        ['text' => 'Eloquent ORM digunakan untuk...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'Berinteraksi dengan database menggunakan PHP objects', 'correct' => true],
                             ['text' => 'Membuat tampilan HTML', 'correct' => false],
                             ['text' => 'Mengelola routing aplikasi', 'correct' => false],
                             ['text' => 'Mengirim email', 'correct' => false],
                         ]],
                        ['text' => 'Method hasMany() digunakan untuk relasi...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'Satu ke banyak (one-to-many)', 'correct' => true],
                             ['text' => 'Banyak ke banyak (many-to-many)', 'correct' => false],
                             ['text' => 'Satu ke satu (one-to-one)', 'correct' => false],
                             ['text' => 'Banyak ke satu (many-to-one)', 'correct' => false],
                         ]],
                        ['text' => 'Migration digunakan untuk membuat versi database.',
                         'type' => 'true_false', 'points' => 1,
                         'options' => [
                             ['text' => 'true', 'correct' => true],
                             ['text' => 'false', 'correct' => false],
                         ]],
                        ['text' => 'Jelaskan apa itu Middleware di Laravel dan berikan contoh penggunaannya!',
                         'type' => 'essay', 'points' => 5, 'options' => []],
                        ['text' => 'Apa fungsi dari php artisan migrate:rollback?',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'Membatalkan migration terakhir', 'correct' => true],
                             ['text' => 'Menjalankan semua migration', 'correct' => false],
                             ['text' => 'Menghapus semua tabel', 'correct' => false],
                             ['text' => 'Reset database ke awal', 'correct' => false],
                         ]],
                    ],
                ],
            ],

            // ── Java ──────────────────────────────────────────────────────────
            'Pemrograman Java Dasar' => [
                [
                    'title'            => 'Kuis Java — OOP',
                    'duration_minutes' => 45,
                    'pass_score'       => 70,
                    'max_attempts'     => 2,
                    'is_randomized'    => false,
                    'questions'        => [
                        ['text' => 'Kata kunci untuk membuat class turunan di Java adalah...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'explanation' => 'extends digunakan untuk inheritance di Java.',
                         'options' => [
                             ['text' => 'extends', 'correct' => true],
                             ['text' => 'implements', 'correct' => false],
                             ['text' => 'inherits', 'correct' => false],
                             ['text' => 'super', 'correct' => false],
                         ]],
                        ['text' => 'Java adalah bahasa yang strongly typed.',
                         'type' => 'true_false', 'points' => 1,
                         'options' => [
                             ['text' => 'true', 'correct' => true],
                             ['text' => 'false', 'correct' => false],
                         ]],
                        ['text' => 'Method yang dipanggil pertama kali saat program Java dijalankan adalah...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'public static void main(String[] args)', 'correct' => true],
                             ['text' => 'public void start()', 'correct' => false],
                             ['text' => 'public static void run()', 'correct' => false],
                             ['text' => 'public void init()', 'correct' => false],
                         ]],
                        ['text' => 'Jelaskan konsep Encapsulation dalam OOP Java!',
                         'type' => 'essay', 'points' => 5, 'options' => []],
                    ],
                ],
            ],

            // ── Jaringan ──────────────────────────────────────────────────────
            'Dasar Jaringan Komputer' => [
                [
                    'title'            => 'Ujian Jaringan Dasar',
                    'duration_minutes' => 45,
                    'pass_score'       => 70,
                    'max_attempts'     => 2,
                    'is_randomized'    => true,
                    'questions'        => [
                        ['text' => 'Berapa jumlah layer pada model OSI?',
                         'type' => 'multiple_choice', 'points' => 2,
                         'explanation' => 'Model OSI memiliki 7 layer.',
                         'options' => [
                             ['text' => '7', 'correct' => true],
                             ['text' => '4', 'correct' => false],
                             ['text' => '5', 'correct' => false],
                             ['text' => '6', 'correct' => false],
                         ]],
                        ['text' => 'IP Address 192.168.x.x termasuk dalam kelas...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'Kelas C', 'correct' => true],
                             ['text' => 'Kelas A', 'correct' => false],
                             ['text' => 'Kelas B', 'correct' => false],
                             ['text' => 'Kelas D', 'correct' => false],
                         ]],
                        ['text' => 'Router bekerja pada layer Network (Layer 3) OSI.',
                         'type' => 'true_false', 'points' => 1,
                         'options' => [
                             ['text' => 'true', 'correct' => true],
                             ['text' => 'false', 'correct' => false],
                         ]],
                        ['text' => 'Switch bekerja di layer yang sama dengan router.',
                         'type' => 'true_false', 'points' => 1,
                         'explanation' => 'Switch bekerja di Layer 2 (Data Link), sedangkan Router di Layer 3 (Network).',
                         'options' => [
                             ['text' => 'true', 'correct' => false],
                             ['text' => 'false', 'correct' => true],
                         ]],
                        ['text' => 'Jelaskan perbedaan antara TCP dan UDP!',
                         'type' => 'essay', 'points' => 4, 'options' => []],
                    ],
                ],
            ],

            // ── JavaScript ────────────────────────────────────────────────────
            'JavaScript Modern (ES6+)' => [
                [
                    'title'            => 'Kuis JavaScript ES6+',
                    'duration_minutes' => 30,
                    'pass_score'       => 70,
                    'max_attempts'     => 2,
                    'is_randomized'    => true,
                    'questions'        => [
                        ['text' => 'Sintaks arrow function yang benar adalah...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'const fn = (x) => x * 2', 'correct' => true],
                             ['text' => 'const fn = function(x) -> x * 2', 'correct' => false],
                             ['text' => 'fn = (x) { return x * 2 }', 'correct' => false],
                             ['text' => 'const fn = (x): x * 2', 'correct' => false],
                         ]],
                        ['text' => 'async/await adalah cara modern untuk menangani asynchronous di JavaScript.',
                         'type' => 'true_false', 'points' => 1,
                         'options' => [
                             ['text' => 'true', 'correct' => true],
                             ['text' => 'false', 'correct' => false],
                         ]],
                        ['text' => 'Destructuring assignment digunakan untuk...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'Mengekstrak nilai dari array atau object', 'correct' => true],
                             ['text' => 'Menghapus property dari object', 'correct' => false],
                             ['text' => 'Membuat copy dari object', 'correct' => false],
                             ['text' => 'Mengubah tipe data', 'correct' => false],
                         ]],
                        ['text' => 'Apa perbedaan let, const, dan var dalam JavaScript?',
                         'type' => 'essay', 'points' => 5, 'options' => []],
                    ],
                ],
            ],

            // ── MySQL ─────────────────────────────────────────────────────────
            'MySQL untuk Developer' => [
                [
                    'title'            => 'Ujian MySQL Dasar',
                    'duration_minutes' => 45,
                    'pass_score'       => 75,
                    'max_attempts'     => 2,
                    'is_randomized'    => false,
                    'questions'        => [
                        ['text' => 'Perintah untuk menampilkan semua data dari tabel users adalah...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'SELECT * FROM users', 'correct' => true],
                             ['text' => 'GET * FROM users', 'correct' => false],
                             ['text' => 'SHOW * FROM users', 'correct' => false],
                             ['text' => 'FETCH * FROM users', 'correct' => false],
                         ]],
                        ['text' => 'PRIMARY KEY dapat berisi nilai NULL.',
                         'type' => 'true_false', 'points' => 1,
                         'explanation' => 'PRIMARY KEY tidak boleh NULL dan harus unik.',
                         'options' => [
                             ['text' => 'true', 'correct' => false],
                             ['text' => 'false', 'correct' => true],
                         ]],
                        ['text' => 'JOIN yang menampilkan semua data dari kedua tabel adalah...',
                         'type' => 'multiple_choice', 'points' => 2,
                         'options' => [
                             ['text' => 'FULL OUTER JOIN', 'correct' => true],
                             ['text' => 'INNER JOIN', 'correct' => false],
                             ['text' => 'LEFT JOIN', 'correct' => false],
                             ['text' => 'RIGHT JOIN', 'correct' => false],
                         ]],
                        ['text' => 'Index mempercepat proses SELECT tapi memperlambat INSERT.',
                         'type' => 'true_false', 'points' => 1,
                         'options' => [
                             ['text' => 'true', 'correct' => true],
                             ['text' => 'false', 'correct' => false],
                         ]],
                        ['text' => 'Jelaskan perbedaan INNER JOIN dan LEFT JOIN beserta contoh kasusnya!',
                         'type' => 'essay', 'points' => 4, 'options' => []],
                    ],
                ],
            ],
        ];

        foreach ($exams as $courseTitle => $examList) {
            $course = Course::where('title', $courseTitle)->first();
            if (! $course) continue;

            foreach ($examList as $examData) {
                $exam = Exam::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $examData['title']],
                    [
                        'description'              => 'Ujian untuk mengukur pemahaman materi ' . $course->title,
                        'duration_minutes'         => $examData['duration_minutes'],
                        'pass_score'               => $examData['pass_score'],
                        'max_attempts'             => $examData['max_attempts'],
                        'is_randomized'            => $examData['is_randomized'],
                        'show_result_immediately'  => true,
                        'status'                   => 'published',
                        'start_at'                 => now()->subDays(30),
                        'end_at'                   => now()->addDays(60),
                    ]
                );

                foreach ($examData['questions'] as $order => $qData) {
                    $question = Question::firstOrCreate(
                        ['exam_id' => $exam->id, 'question_text' => $qData['text']],
                        [
                            'type'        => $qData['type'],
                            'points'      => $qData['points'],
                            'order'       => $order + 1,
                            'explanation' => $qData['explanation'] ?? null,
                        ]
                    );

                    foreach ($qData['options'] as $optOrder => $optData) {
                        QuestionOption::firstOrCreate(
                            ['question_id' => $question->id, 'option_text' => $optData['text']],
                            [
                                'is_correct' => $optData['correct'],
                                'order'      => $optOrder + 1,
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info('✅ Exams created with questions and options');
    }
}
