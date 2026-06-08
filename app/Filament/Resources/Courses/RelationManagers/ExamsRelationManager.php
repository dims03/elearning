<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ExamsRelationManager extends RelationManager
{
    protected static string $relationship = 'exams';

    protected static ?string $title = 'Exam';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('exam_tabs')
                    ->tabs([
                        Tabs\Tab::make('Informasi Ujian')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul Ujian')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('instructions')
                                    ->label('Petunjuk Pengerjaan')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'bulletList',
                                        'orderedList',
                                    ]),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Pengaturan')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\TextInput::make('duration_minutes')
                                    ->label('Durasi (menit)')
                                    ->numeric()
                                    ->required()
                                    ->default(60)
                                    ->minValue(1),

                                Forms\Components\TextInput::make('pass_score')
                                    ->label('Nilai Kelulusan (%)')
                                    ->numeric()
                                    ->required()
                                    ->default(70)
                                    ->minValue(0)
                                    ->maxValue(100),

                                Forms\Components\TextInput::make('max_attempts')
                                    ->label('Maksimal Percobaan')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'closed' => 'Closed',
                                    ])
                                    ->required()
                                    ->default('draft'),

                                Forms\Components\Toggle::make('is_randomized')
                                    ->label('Acak Urutan Soal')
                                    ->default(false),

                                Forms\Components\Toggle::make('show_result_immediately')
                                    ->label('Tampilkan Hasil Langsung')
                                    ->default(true),

                                Forms\Components\DateTimePicker::make('start_at')
                                    ->label('Waktu Mulai')
                                    ->nullable(),

                                Forms\Components\DateTimePicker::make('end_at')
                                    ->label('Waktu Selesai')
                                    ->nullable()
                                    ->after('start_at'),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Soal Ujian')
                            ->icon('heroicon-o-square-3-stack-3d')
                            ->badge(fn ($record) => $record?->questions()->count() ?? 0)
                            ->schema([
                                Forms\Components\Repeater::make('questions')
                                    ->label('')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('question_text')
                                            ->label('Pertanyaan')
                                            ->required()
                                            ->columnSpanFull(),

                                        Forms\Components\Select::make('type')
                                            ->label('Jenis Soal')
                                            ->options([
                                                'multiple_choice' => 'Multiple Choice',
                                                'true_false' => 'True / False',
                                                'essay' => 'Essay',
                                            ])
                                            ->default('multiple_choice')
                                            ->live()
                                            ->required(),

                                        Forms\Components\TextInput::make('points')
                                            ->label('Poin')
                                            ->numeric()
                                            ->default(1)
                                            ->required(),

                                        Forms\Components\FileUpload::make('image')
                                            ->label('Gambar Soal')
                                            ->image()
                                            ->directory('questions/images')
                                            ->nullable(),

                                        Forms\Components\Textarea::make('explanation')
                                            ->label('Penjelasan Jawaban')
                                            ->rows(2)
                                            ->columnSpanFull(),

                                        Forms\Components\Repeater::make('options')
                                            ->label('Pilihan Jawaban')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\TextInput::make('option_text')
                                                    ->label('Teks Pilihan')
                                                    ->required(),

                                                Forms\Components\Toggle::make('is_correct')
                                                    ->label('Jawaban Benar'),
                                            ])
                                            ->columns(2)
                                            ->reorderable('order')
                                            ->minItems(2)
                                            ->maxItems(5)
                                            ->defaultItems(4)
                                            ->columnSpanFull()
                                            ->visible(fn (Get $get) => in_array($get('type'), ['multiple_choice', 'true_false'], true)),

                                        Forms\Components\Placeholder::make('essay_info')
                                            ->label('')
                                            ->content('Soal essay akan dinilai manual setelah siswa submit.')
                                            ->columnSpanFull()
                                            ->visible(fn (Get $get) => $get('type') === 'essay'),
                                    ])
                                    ->columns(2)
                                    ->reorderable('order')
                                    ->cloneable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state) => '[' . strtoupper($state['type'] ?? 'SOAL') . '] ' . (strlen($state['question_text'] ?? '') > 60 ? substr($state['question_text'], 0, 60) . '...' : ($state['question_text'] ?? 'Soal baru')))
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Jumlah Soal')
                    ->counts('questions'),

                Tables\Columns\TextColumn::make('sessions_count')
                    ->label('Percobaan')
                    ->counts('sessions'),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->suffix(' menit'),

                Tables\Columns\TextColumn::make('pass_score')
                    ->label('Nilai Lulus')
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'closed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('start_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('end_at')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                    ]),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('+ Tambah Ujian'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }
}
