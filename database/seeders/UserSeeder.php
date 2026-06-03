<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin ───────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@elearning.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'phone'    => '081234567890',
                'bio'      => 'Administrator platform e-learning.',
            ]
        );
        $admin->syncRoles(['admin', 'teacher']); // admin bisa akses teacher panel

        // ── Guru / Teacher ────────────────────────────────────────────────────
        $teachers = [
            [
                'name'  => 'Budi Santoso',
                'email' => 'budi@elearning.com',
                'phone' => '081234567891',
                'bio'   => 'Guru pemrograman dengan pengalaman 10 tahun di industri.',
            ],
            [
                'name'  => 'Siti Rahayu',
                'email' => 'siti@elearning.com',
                'phone' => '081234567892',
                'bio'   => 'Spesialis jaringan komputer dan keamanan sistem.',
            ],
            [
                'name'  => 'Ahmad Fauzi',
                'email' => 'ahmad@elearning.com',
                'phone' => '081234567893',
                'bio'   => 'Pengembang web full-stack dengan fokus di Laravel dan Vue.',
            ],
        ];

        foreach ($teachers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password'),
                 'phone' => $data['phone'], 'bio' => $data['bio']]
            );
            $user->syncRoles(['teacher']);
        }

        // ── Siswa / Student ───────────────────────────────────────────────────
        $students = [
            ['name' => 'Ani Rahayu',      'email' => 'ani@student.com'],
            ['name' => 'Bimo Prasetyo',   'email' => 'bimo@student.com'],
            ['name' => 'Citra Dewi',      'email' => 'citra@student.com'],
            ['name' => 'Dodi Kurniawan',  'email' => 'dodi@student.com'],
            ['name' => 'Eka Putri',       'email' => 'eka@student.com'],
            ['name' => 'Fajar Nugroho',   'email' => 'fajar@student.com'],
            ['name' => 'Gita Sari',       'email' => 'gita@student.com'],
            ['name' => 'Hendra Wijaya',   'email' => 'hendra@student.com'],
            ['name' => 'Indah Permata',   'email' => 'indah@student.com'],
            ['name' => 'Joko Susilo',     'email' => 'joko@student.com'],
        ];

        foreach ($students as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password'),
                 'phone' => '08' . rand(1000000000, 9999999999),
                 'bio'   => 'Siswa aktif di platform e-learning.']
            );
            $user->syncRoles(['student']);
        }

        $this->command->info('✅ Users created: 1 admin, 3 teachers, 10 students');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['admin',   'admin@elearning.com', 'password'],
                ['teacher', 'budi@elearning.com',  'password'],
                ['teacher', 'siti@elearning.com',  'password'],
                ['teacher', 'ahmad@elearning.com', 'password'],
                ['student', 'ani@student.com',     'password'],
                ['student', '... (10 siswa)',      'password'],
            ]
        );
    }
}
