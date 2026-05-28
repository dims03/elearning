<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Buat roles
        $admin   = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);
        $teacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Buat Super Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@elearning.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );
        $adminUser->assignRole($admin);

        // Buat contoh Guru
        $teacherUser = User::firstOrCreate(
            ['email' => 'guru@elearning.com'],
            [
                'name'     => 'Budi Santoso',
                'password' => bcrypt('password'),
            ]
        );
        $teacherUser->assignRole($teacher);

        // Buat contoh Siswa
        $studentUser = User::firstOrCreate(
            ['email' => 'siswa@elearning.com'],
            [
                'name'     => 'Ani Rahayu',
                'password' => bcrypt('password'),
            ]
        );
        $studentUser->assignRole($student);

        $this->command->info('✅ Roles & Users seeder selesai!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['admin',   'admin@elearning.com',   'password'],
                ['teacher', 'guru@elearning.com',    'password'],
                ['student', 'siswa@elearning.com',   'password'],
            ]
        );
    }
}
