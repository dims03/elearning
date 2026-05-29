<?php

namespace App\Filament\Teacher\Resources\Courses\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()->tabs([
                    Tabs\Tab::make('Informasi Dasar')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            TextInput::make('title')
                                ->label('Course Title')
                                ->required()
                                ->maxlength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(
                                    fn($state, Set $set) =>
                                    $set('slug', Str::slug($state))
                                ),
                            Textinput::make('slug')
                                ->label('Slug URL')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxlength(255),
                            Select::make('category_id')
                                ->label('Category')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('level')
                                ->label('Level')
                                ->options([
                                    'beginner'     => 'Pemula',
                                    'intermediate' => 'Menengah',
                                    'advanced'     => 'Lanjut',
                                ])
                                ->required(),

                            RichEditor::make('description')
                                ->label('Deskripsi Kursus')
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'underline',
                                    'bulletList',
                                    'orderedList',
                                    'h2',
                                    'h3',
                                    'link',
                                ]),
                        ])->columns(2),

                    Tabs\Tab::make('Media & Settings')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            FileUpload::make('thumbnail')
                                ->label('Thumbnail')
                                ->image()
                                ->directory('courses/thumbnails')
                                ->imageEditor()
                                ->columnSpanFull(),
                            Select::make('status')
                                ->label('Status Publikasi')
                                ->options([
                                    'draft'     => 'Draft',
                                    'published' => 'Published',
                                    'archived'  => 'Archived',
                                ])
                                ->default('draft')
                                ->required(),
                            Toggle::make('is_free')
                                ->label('Kursus Gratis')
                                ->default(true),
                            TextInput::make('duration_minutes')
                                ->label('Total Durasi (menit)')
                                ->numeric()
                                ->default(0),
                        ])->columns(2),
                ])->columnSpanFull(),
                Hidden::make('teacher_id')
                    ->default(auth()->id()),
            ]);
    }
}
