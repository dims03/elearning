<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = User::role('teacher')->get();
        $budi     = $teachers->firstWhere('email', 'budi@elearning.com');
        $siti     = $teachers->firstWhere('email', 'siti@elearning.com');
        $ahmad    = $teachers->firstWhere('email', 'ahmad@elearning.com');

        $catPhp        = Category::where('slug', 'php-laravel')->first();
        $catJava       = Category::where('slug', 'java')->first();
        $catJaringan   = Category::where('slug', 'cisco-networking')->first();
        $catKeamanan   = Category::where('slug', 'keamanan-siber')->first();
        $catJs         = Category::where('slug', 'javascript')->first();
        $catDb         = Category::where('slug', 'mysql')->first();

        $courses = [
            // ── Kursus 1 ──────────────────────────────────────────────────────
            [
                'teacher'     => $budi,
                'category'    => $catPhp,
                'title'       => 'Laravel untuk Pemula',
                'description' => '<p>Pelajari Laravel dari dasar hingga membuat aplikasi web profesional.</p>',
                'level'       => 'beginner',
                'status'      => 'published',
                'is_free'     => true,
                'chapters'    => [
                    [
                        'title'   => 'Pengenalan Laravel',
                        'lessons' => [
                            ['title' => 'Apa itu Laravel?',         'type' => 'text',  'duration' => 10,
                             'content' => '<h2>Laravel Framework</h2><p>Laravel adalah framework PHP yang elegan dan ekspresif. Dibuat oleh Taylor Otwell, Laravel menyediakan struktur yang bersih dan tools yang powerful untuk membangun aplikasi web modern.</p><h3>Kenapa Laravel?</h3><ul><li>Sintaks yang elegan dan mudah dipahami</li><li>Ekosistem yang kaya (Eloquent ORM, Blade, Queue, dll)</li><li>Komunitas besar dan dokumentasi lengkap</li><li>Keamanan built-in (CSRF, XSS protection)</li></ul>'],
                            ['title' => 'Instalasi & Setup',        'type' => 'text',  'duration' => 15,
                             'content' => '<h2>Instalasi Laravel</h2><p>Untuk menginstal Laravel, kamu memerlukan:</p><ul><li>PHP >= 8.2</li><li>Composer</li><li>Node.js</li></ul><h3>Langkah Instalasi</h3><pre>composer create-project laravel/laravel myapp\ncd myapp\nphp artisan serve</pre>'],
                            ['title' => 'Struktur Folder Laravel',  'type' => 'text',  'duration' => 10,
                             'content' => '<h2>Struktur Folder</h2><ul><li><strong>app/</strong> - Kode utama aplikasi (Models, Controllers)</li><li><strong>resources/</strong> - Views, CSS, JS</li><li><strong>routes/</strong> - Definisi routing</li><li><strong>database/</strong> - Migration dan seeder</li><li><strong>public/</strong> - File yang bisa diakses publik</li></ul>'],
                        ],
                    ],
                    [
                        'title'   => 'Routing & Controller',
                        'lessons' => [
                            ['title' => 'Membuat Route Dasar',      'type' => 'text',  'duration' => 20,
                             'content' => '<h2>Routing di Laravel</h2><p>Route didefinisikan di <code>routes/web.php</code>:</p><pre>Route::get(\'/hello\', function () {\n    return \'Hello World!\';\n});\n\nRoute::get(\'/user/{id}\', function ($id) {\n    return \'User \' . $id;\n});</pre>'],
                            ['title' => 'Resource Controller',      'type' => 'text',  'duration' => 25,
                             'content' => '<h2>Resource Controller</h2><p>Membuat controller dengan semua method CRUD sekaligus:</p><pre>php artisan make:controller PostController --resource</pre><p>Lalu daftarkan di routes:</p><pre>Route::resource(\'posts\', PostController::class);</pre>'],
                        ],
                    ],
                    [
                        'title'   => 'Eloquent ORM',
                        'lessons' => [
                            ['title' => 'Model & Migration',        'type' => 'text',  'duration' => 20,
                             'content' => '<h2>Eloquent Model</h2><p>Buat model dan migration sekaligus:</p><pre>php artisan make:model Post -m</pre>'],
                            ['title' => 'Relasi Antar Model',       'type' => 'text',  'duration' => 30,
                             'content' => '<h2>Relasi di Eloquent</h2><h3>HasMany</h3><pre>class User extends Model {\n    public function posts(): HasMany {\n        return $this->hasMany(Post::class);\n    }\n}</pre>'],
                            ['title' => 'Query Builder Lanjutan',   'type' => 'text',  'duration' => 25,
                             'content' => '<h2>Query Builder</h2><pre>User::where(\'active\', true)\n     ->orderBy(\'name\')\n     ->paginate(15);</pre>'],
                        ],
                    ],
                ],
            ],

            // ── Kursus 2 ──────────────────────────────────────────────────────
            [
                'teacher'     => $budi,
                'category'    => $catJava,
                'title'       => 'Pemrograman Java Dasar',
                'description' => '<p>Kuasai Java dari nol hingga bisa membuat aplikasi desktop dan Android.</p>',
                'level'       => 'beginner',
                'status'      => 'published',
                'is_free'     => true,
                'chapters'    => [
                    [
                        'title'   => 'Dasar Java',
                        'lessons' => [
                            ['title' => 'Pengenalan Java',           'type' => 'text', 'duration' => 10,
                             'content' => '<h2>Apa itu Java?</h2><p>Java adalah bahasa pemrograman berorientasi objek yang populer. <strong>Write Once, Run Anywhere</strong> adalah filosofi Java.</p>'],
                            ['title' => 'Variabel & Tipe Data',     'type' => 'text', 'duration' => 20,
                             'content' => '<h2>Tipe Data Java</h2><pre>int umur = 25;\nString nama = "Budi";\ndouble nilai = 85.5;\nboolean lulus = true;</pre>'],
                            ['title' => 'Percabangan & Looping',    'type' => 'text', 'duration' => 25,
                             'content' => '<h2>Kontrol Alur</h2><pre>if (nilai >= 70) {\n    System.out.println("Lulus");\n} else {\n    System.out.println("Tidak Lulus");\n}</pre>'],
                        ],
                    ],
                    [
                        'title'   => 'OOP di Java',
                        'lessons' => [
                            ['title' => 'Class & Object',            'type' => 'text', 'duration' => 30,
                             'content' => '<h2>Class dan Object</h2><pre>class Mahasiswa {\n    String nama;\n    int nim;\n    \n    void perkenalan() {\n        System.out.println("Halo, saya " + nama);\n    }\n}</pre>'],
                            ['title' => 'Inheritance & Polymorphism','type' => 'text', 'duration' => 35,
                             'content' => '<h2>Inheritance</h2><pre>class Hewan {\n    void bersuara() { }\n}\nclass Kucing extends Hewan {\n    void bersuara() {\n        System.out.println("Meow!");\n    }\n}</pre>'],
                        ],
                    ],
                ],
            ],

            // ── Kursus 3 ──────────────────────────────────────────────────────
            [
                'teacher'     => $siti,
                'category'    => $catJaringan,
                'title'       => 'Dasar Jaringan Komputer',
                'description' => '<p>Memahami konsep jaringan komputer dari dasar hingga konfigurasi router dan switch.</p>',
                'level'       => 'beginner',
                'status'      => 'published',
                'is_free'     => true,
                'chapters'    => [
                    [
                        'title'   => 'Konsep Dasar Jaringan',
                        'lessons' => [
                            ['title' => 'Apa itu Jaringan Komputer?', 'type' => 'text', 'duration' => 15,
                             'content' => '<h2>Jaringan Komputer</h2><p>Jaringan komputer adalah kumpulan perangkat yang saling terhubung untuk berbagi data dan sumber daya.</p><h3>Jenis Jaringan</h3><ul><li><strong>LAN</strong> - Local Area Network</li><li><strong>WAN</strong> - Wide Area Network</li><li><strong>MAN</strong> - Metropolitan Area Network</li></ul>'],
                            ['title' => 'Model OSI 7 Layer',          'type' => 'text', 'duration' => 30,
                             'content' => '<h2>Model OSI</h2><ol><li>Physical Layer</li><li>Data Link Layer</li><li>Network Layer</li><li>Transport Layer</li><li>Session Layer</li><li>Presentation Layer</li><li>Application Layer</li></ol>'],
                            ['title' => 'IP Address & Subnetting',    'type' => 'text', 'duration' => 40,
                             'content' => '<h2>IP Address</h2><p>IP Address adalah alamat unik yang diberikan ke setiap perangkat dalam jaringan.</p><h3>Format IPv4</h3><p><code>192.168.1.1</code> — 4 oktet, masing-masing 0-255</p>'],
                        ],
                    ],
                    [
                        'title'   => 'Perangkat Jaringan',
                        'lessons' => [
                            ['title' => 'Router & Switch',            'type' => 'text', 'duration' => 25,
                             'content' => '<h2>Router</h2><p>Router menghubungkan dua atau lebih jaringan yang berbeda dan menentukan jalur terbaik untuk pengiriman data.</p><h2>Switch</h2><p>Switch menghubungkan perangkat dalam satu jaringan LAN dan bekerja di Layer 2 OSI.</p>'],
                            ['title' => 'Konfigurasi Dasar Cisco',    'type' => 'text', 'duration' => 45,
                             'content' => '<h2>Cisco IOS Commands</h2><pre>enable\nconfigure terminal\nhostname Router1\ninterface GigabitEthernet0/0\nip address 192.168.1.1 255.255.255.0\nno shutdown</pre>'],
                        ],
                    ],
                ],
            ],

            // ── Kursus 4 ──────────────────────────────────────────────────────
            [
                'teacher'     => $siti,
                'category'    => $catKeamanan,
                'title'       => 'Keamanan Siber Fundamentals',
                'description' => '<p>Pelajari dasar-dasar keamanan siber, jenis ancaman, dan cara melindungi sistem.</p>',
                'level'       => 'intermediate',
                'status'      => 'published',
                'is_free'     => false,
                'chapters'    => [
                    [
                        'title'   => 'Pengenalan Keamanan Siber',
                        'lessons' => [
                            ['title' => 'Ancaman Siber Modern',       'type' => 'text', 'duration' => 20,
                             'content' => '<h2>Jenis Ancaman Siber</h2><ul><li>Malware (virus, trojan, ransomware)</li><li>Phishing</li><li>SQL Injection</li><li>Man-in-the-Middle Attack</li><li>DDoS Attack</li></ul>'],
                            ['title' => 'CIA Triad',                  'type' => 'text', 'duration' => 15,
                             'content' => '<h2>CIA Triad</h2><ul><li><strong>Confidentiality</strong> — Kerahasiaan data</li><li><strong>Integrity</strong> — Integritas data</li><li><strong>Availability</strong> — Ketersediaan sistem</li></ul>'],
                        ],
                    ],
                    [
                        'title'   => 'Teknik Perlindungan',
                        'lessons' => [
                            ['title' => 'Enkripsi & Kriptografi',     'type' => 'text', 'duration' => 35,
                             'content' => '<h2>Enkripsi</h2><p>Enkripsi mengubah data plaintext menjadi ciphertext yang tidak bisa dibaca tanpa kunci.</p><h3>Jenis Enkripsi</h3><ul><li>Symmetric (AES, DES)</li><li>Asymmetric (RSA, ECC)</li></ul>'],
                            ['title' => 'Firewall & IDS/IPS',         'type' => 'text', 'duration' => 30,
                             'content' => '<h2>Firewall</h2><p>Firewall memfilter traffic jaringan berdasarkan aturan yang telah ditetapkan.</p>'],
                        ],
                    ],
                ],
            ],

            // ── Kursus 5 ──────────────────────────────────────────────────────
            [
                'teacher'     => $ahmad,
                'category'    => $catJs,
                'title'       => 'JavaScript Modern (ES6+)',
                'description' => '<p>Kuasai JavaScript modern dengan fitur ES6+ dan konsep pemrograman asynchronous.</p>',
                'level'       => 'intermediate',
                'status'      => 'published',
                'is_free'     => true,
                'chapters'    => [
                    [
                        'title'   => 'ES6+ Features',
                        'lessons' => [
                            ['title' => 'Arrow Functions & Destructuring', 'type' => 'text', 'duration' => 25,
                             'content' => '<h2>Arrow Functions</h2><pre>// Sebelum ES6\nfunction tambah(a, b) { return a + b; }\n\n// ES6+\nconst tambah = (a, b) => a + b;</pre><h2>Destructuring</h2><pre>const { nama, umur } = user;\nconst [first, ...rest] = array;</pre>'],
                            ['title' => 'Promises & Async/Await',     'type' => 'text', 'duration' => 35,
                             'content' => '<h2>Async/Await</h2><pre>async function fetchData() {\n    try {\n        const response = await fetch(url);\n        const data = await response.json();\n        return data;\n    } catch (error) {\n        console.error(error);\n    }\n}</pre>'],
                            ['title' => 'Modules & Classes',           'type' => 'text', 'duration' => 20,
                             'content' => '<h2>ES6 Classes</h2><pre>class Animal {\n    constructor(name) {\n        this.name = name;\n    }\n    speak() {\n        console.log(`${this.name} makes a noise.`);\n    }\n}</pre>'],
                        ],
                    ],
                ],
            ],

            // ── Kursus 6 ──────────────────────────────────────────────────────
            [
                'teacher'     => $ahmad,
                'category'    => $catDb,
                'title'       => 'MySQL untuk Developer',
                'description' => '<p>Pelajari MySQL dari query dasar hingga optimasi performa database.</p>',
                'level'       => 'beginner',
                'status'      => 'published',
                'is_free'     => true,
                'chapters'    => [
                    [
                        'title'   => 'Dasar MySQL',
                        'lessons' => [
                            ['title' => 'Instalasi & Setup MySQL',    'type' => 'text', 'duration' => 15,
                             'content' => '<h2>MySQL Setup</h2><pre>sudo apt install mysql-server\nmysql -u root -p\nCREATE DATABASE myapp;</pre>'],
                            ['title' => 'DDL - CREATE TABLE',         'type' => 'text', 'duration' => 20,
                             'content' => '<h2>DDL Commands</h2><pre>CREATE TABLE users (\n    id INT PRIMARY KEY AUTO_INCREMENT,\n    name VARCHAR(255) NOT NULL,\n    email VARCHAR(255) UNIQUE,\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n);</pre>'],
                            ['title' => 'DML - SELECT, INSERT, UPDATE','type' => 'text', 'duration' => 30,
                             'content' => '<h2>DML Commands</h2><pre>-- Select\nSELECT * FROM users WHERE active = 1;\n\n-- Insert\nINSERT INTO users (name, email) VALUES (\'Budi\', \'budi@email.com\');\n\n-- Update\nUPDATE users SET name = \'Budi Baru\' WHERE id = 1;</pre>'],
                        ],
                    ],
                    [
                        'title'   => 'Query Lanjutan',
                        'lessons' => [
                            ['title' => 'JOIN - Menggabungkan Tabel', 'type' => 'text', 'duration' => 35,
                             'content' => '<h2>SQL JOIN</h2><pre>SELECT u.name, o.total\nFROM users u\nINNER JOIN orders o ON u.id = o.user_id\nWHERE o.status = \'completed\';</pre>'],
                            ['title' => 'Index & Optimasi Query',     'type' => 'text', 'duration' => 30,
                             'content' => '<h2>Index</h2><p>Index mempercepat pencarian data tetapi memperlambat INSERT/UPDATE.</p><pre>CREATE INDEX idx_email ON users(email);\nEXPLAIN SELECT * FROM users WHERE email = \'test@email.com\';</pre>'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $teacher  = $courseData['teacher'];
            $category = $courseData['category'];

            if (! $teacher || ! $category) continue;

            $course = Course::firstOrCreate(
                ['slug' => Str::slug($courseData['title'])],
                [
                    'teacher_id'       => $teacher->id,
                    'category_id'      => $category->id,
                    'title'            => $courseData['title'],
                    'description'      => $courseData['description'],
                    'level'            => $courseData['level'],
                    'status'           => $courseData['status'],
                    'is_free'          => $courseData['is_free'],
                    'duration_minutes' => 0,
                ]
            );

            $totalDuration = 0;

            foreach ($courseData['chapters'] as $chapterOrder => $chapterData) {
                $chapter = Chapter::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $chapterData['title']],
                    [
                        'order'        => $chapterOrder + 1,
                        'is_published' => true,
                    ]
                );

                foreach ($chapterData['lessons'] as $lessonOrder => $lessonData) {
                    Lesson::firstOrCreate(
                        ['chapter_id' => $chapter->id, 'title' => $lessonData['title']],
                        [
                            'type'             => $lessonData['type'],
                            'content'          => $lessonData['content'] ?? null,
                            'duration_minutes' => $lessonData['duration'],
                            'order'            => $lessonOrder + 1,
                            'is_published'     => true,
                            'is_free_preview'  => $lessonOrder === 0,
                        ]
                    );
                    $totalDuration += $lessonData['duration'];
                }
            }

            $course->update(['duration_minutes' => $totalDuration]);
        }

        $this->command->info('✅ Courses created: 6 courses with chapters and lessons');
    }
}
