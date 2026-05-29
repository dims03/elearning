<?php

namespace App\Filament\Teacher\Resources\Exams\Schemas;

use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Components\Utilities\Get;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()->tabs([
                    Tabs\Tab::make('Exam Information')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Select::make('course_id')
                                ->label('Course')
                                ->options(fn(): array => Course::query()
                                    ->where('teacher_id', Auth::id())
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                                    ->toArray())
                                ->searchable()
                                ->preload()
                                ->exists('courses', 'id')
                                ->required(),
                            TextInput::make('title')
                                ->label('Exam Title')
                                ->required()
                                ->maxLength(255),
                            Textarea::make('description')
                                ->label('Description')
                                ->rows(2)
                                ->columnSpanFull(),
                            RichEditor::make('instructions')
                                ->label('Petunjuk Pengerjaan')
                                ->columnSpanFull()
                                ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList']),
                        ])->columns(2),
                    Tabs\Tab::make('Settings')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            TextInput::make('duration_minutes')
                                ->label('Duration (minutes)')
                                ->numeric()
                                ->required()
                                ->default(60)
                                ->minValue(1),
                            TextInput::make('pass_score')
                                ->label('Passing Score (%)')
                                ->numeric()
                                ->required()
                                ->default(70)
                                ->minValue(0)
                                ->maxValue(100),
                            TextInput::make('max_attempts')
                                ->label('Max Attempts')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'closed' => 'Closed',
                                ])
                                ->required()
                                ->default('draft'),
                            Toggle::make('is_randomized')
                                ->label('Acak Urutan Soal')
                                ->default(false),
                            Toggle::make('show_result_immediately')
                                ->label('Tampilkan Hasil Langsung')
                                ->default(true),
                            DateTimePicker::make('start_at')
                                ->label('Waktu Mulai')
                                ->nullable(),
                            DateTimePicker::make('end_at')
                                ->label('Waktu Selesai')
                                ->nullable()
                                ->after('start_at'),
                        ])->columns(2),
                    Tabs\Tab::make('Exam Questions')
                        ->icon('heroicon-o-square-3-stack-3d')
                        ->badge(fn ($record) => $record?->questions()->count() ?? 0)
                        ->schema([
                            Repeater::make('questions')
                                ->label('')
                                ->relationship()
                                ->schema([
                                    TextInput::make('question_text')
                                        ->label('Question Text')
                                        ->required()
                                        ->columnSpanFull(),
                                    Select::make('type')
                                        ->label('Question Type')
                                        ->options([
                                            'multiple_choice' => 'Multiple Choice',
                                            'true_false' => 'True/False',
                                            'essay' => 'Essay',
                                        ])
                                        ->default('multiple_choice')
                                        ->live()
                                        ->required(),
                                    TextInput::make('points')
                                        ->label('Points')
                                        ->numeric()
                                        ->default(1)
                                        ->required(),
                                    FileUpload::make('image')
                                        ->label('Gambar Soal (opsional)')
                                        ->image()
                                        ->directory('questions/images')
                                        ->nullable(),
                                    Textarea::make('explanation')
                                        ->label('Penjelasan Jawaban')
                                        ->rows(2)
                                        ->columnSpanFull(),

                                    Repeater::make('options')
                                        ->label('Pilihan Jawaban')
                                        ->relationship()
                                        ->schema([
                                            TextInput::make('option_text')
                                                ->label('Teks Pilihan')
                                                ->required(),

                                            Toggle::make('is_correct')
                                                ->label('✓ Benar'),
                                        ])
                                        ->columns(2)
                                        ->minItems(2)
                                        ->maxItems(5)
                                        ->defaultItems(4)
                                        ->columnSpanFull()
                                        ->visible(
                                            fn(Get $get) =>
                                            in_array($get('type'), ['multiple_choice', 'true_false'], true)
                                        ),
                                    Placeholder::make('essay_info')
                                        ->label('')
                                        ->content('Soal essay akan dinilai manual oleh guru setelah siswa submit.')
                                        ->columnSpanFull()
                                        ->visible(
                                            fn(Get $get) =>
                                            $get('type') === 'essay'
                                        ),
                                ])
                                ->columns(2)
                                ->reorderable('order')
                                ->cloneable()
                                ->collapsible()
                                ->itemLabel(
                                    fn(array $state) =>
                                    '[' . strtoupper($state['type'] ?? 'soal') . '] ' .
                                        (strlen($state['question_text'] ?? '') > 60
                                            ? substr($state['question_text'], 0, 60) . '...'
                                            : ($state['question_text'] ?? 'Soal baru'))
                                )
                                ->columnSpanFull(),
                        ])
                ])->columnSpanFull(),
            ]);
    }
}
