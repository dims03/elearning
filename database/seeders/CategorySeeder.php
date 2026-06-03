<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Pemrograman',
                'icon'        => 'heroicon-o-code-bracket',
                'description' => 'Kursus pemrograman berbagai bahasa dan framework',
                'children'    => ['PHP & Laravel', 'Python', 'JavaScript', 'Java', 'Kotlin'],
            ],
            [
                'name'        => 'Jaringan Komputer',
                'icon'        => 'heroicon-o-server',
                'description' => 'Jaringan, keamanan, dan infrastruktur IT',
                'children'    => ['Cisco Networking', 'Keamanan Siber', 'Cloud Computing'],
            ],
            [
                'name'        => 'Desain & Multimedia',
                'icon'        => 'heroicon-o-paint-brush',
                'description' => 'Desain grafis, UI/UX, dan multimedia',
                'children'    => ['UI/UX Design', 'Adobe Photoshop', 'Video Editing'],
            ],
            [
                'name'        => 'Database',
                'icon'        => 'heroicon-o-circle-stack',
                'description' => 'Manajemen dan optimasi database',
                'children'    => ['MySQL', 'PostgreSQL', 'MongoDB'],
            ],
        ];

        foreach ($categories as $order => $data) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'        => $data['name'],
                    'icon'        => $data['icon'],
                    'description' => $data['description'],
                    'order'       => $order + 1,
                    'is_active'   => true,
                ]
            );

            foreach ($data['children'] as $childOrder => $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'name'      => $childName,
                        'parent_id' => $parent->id,
                        'order'     => $childOrder + 1,
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('✅ Categories created: 4 parent, 11 child categories');
    }
}
