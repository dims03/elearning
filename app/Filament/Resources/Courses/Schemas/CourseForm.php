<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('title')
                        ->label('Judul'),
                    Select::make('teacher_id')
                        ->label('Guru')
                        ->relationship('teacher', 'name'),
                    Select::make('category_id')
                        ->label('Kategori')
                        ->relationship('category', 'name'),
                    Select::make('level')
                        ->label('Level')
                        ->options([
                            'beginner'     => 'Pemula',
                            'intermediate' => 'Menengah',
                            'advanced'     => 'Lanjut',
                        ]),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft'     => 'Draft',
                            'published' => 'Published',
                            'archived'  => 'Archived',
                        ])
                        ->required(),
                ])->columnSpan(2)->columns(2),
            ]);
    }
}
