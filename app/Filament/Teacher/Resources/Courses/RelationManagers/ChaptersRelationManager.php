<?php

namespace App\Filament\Teacher\Resources\Courses\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Utilities\Get;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';
    protected static ?string $title = 'Chapter & Materi';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->label('Judul Chapter')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('order')
                ->label('Urutan')
                ->numeric()
                ->default(0),

            Forms\Components\Textarea::make('description')
                ->label('Deskripsi')
                ->rows(2)
                ->columnSpanFull(),

            Forms\Components\Toggle::make('is_published')
                ->label('Publish Chapter')
                ->default(false),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->width(40),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Chapter')
                    ->searchable(),

                Tables\Columns\TextColumn::make('lessons_count')
                    ->label('Jumlah Materi')
                    ->counts('lessons'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
            ])
            ->headerActions([
                Actions\CreateAction::make()->label('+ Tambah Chapter'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),

                Actions\Action::make('manage_lessons')
                    ->label('Kelola Materi')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'Materi: ' . $record->title)
                    ->modalWidth('5xl')
                    ->fillForm(fn ($record) => [
                        'lessons' => $record->lessons->map(fn ($l) => [
                            'id'              => $l->id,
                            'title'           => $l->title,
                            'type'            => $l->type,
                            'video_url'       => $l->video_url,
                            'attachment'      => $l->attachment,
                            'content'         => $l->content,
                            'duration_minutes' => $l->duration_minutes,
                            'is_free_preview' => $l->is_free_preview,
                        ])->toArray(),
                    ])
                    ->form([
                        Forms\Components\Repeater::make('lessons')
                            ->label('Daftar Materi')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul Materi')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('type')
                                    ->label('Tipe Konten')
                                    ->options([
                                        'video' => '🎬 Video',
                                        'pdf'   => '📄 PDF',
                                        'text'  => '📝 Teks',
                                    ])
                                    ->default('text')
                                    ->live()
                                    ->required(),

                                Forms\Components\TextInput::make('duration_minutes')
                                    ->label('Durasi (menit)')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\Toggle::make('is_free_preview')
                                    ->label('Preview Gratis')
                                    ->default(false),

                                Forms\Components\TextInput::make('video_url')
                                    ->label('URL Video (YouTube/Vimeo)')
                                    ->url()
                                    ->placeholder('https://youtube.com/...')
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get) => $get('type') === 'video'),

                                Forms\Components\FileUpload::make('attachment')
                                    ->label('Upload PDF')
                                    ->disk('public')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->directory('lessons/pdf')
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get) => $get('type') === 'pdf'),

                                Forms\Components\RichEditor::make('content')
                                    ->label('Konten Teks')
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline',
                                        'bulletList', 'orderedList',
                                        'h2', 'h3', 'link', 'blockquote',
                                    ])
                                    ->visible(fn (Get $get) => $get('type') === 'text'),
                            ])
                            ->columns(2)
                            ->reorderable('order')
                            ->cloneable()
                            ->collapsible()
                            ->itemLabel(fn (array $state) =>
                                ($state['type'] ? strtoupper($state['type']) . ' — ' : '')
                                . ($state['title'] ?? 'Materi baru')
                            )
                            ->columnSpanFull(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->lessons()->delete();
                        foreach ($data['lessons'] as $i => $lesson) {
                            $record->lessons()->create([
                                'title'            => $lesson['title'],
                                'type'             => $lesson['type'],
                                'video_url'        => $lesson['video_url'] ?? null,
                                'attachment'       => $lesson['attachment'] ?? null,
                                'content'          => $lesson['content'] ?? null,
                                'duration_minutes' => $lesson['duration_minutes'] ?? 0,
                                'is_free_preview'  => $lesson['is_free_preview'] ?? false,
                                'order'            => $i + 1,
                                'is_published'     => true,
                            ]);
                        }
                    })
                    ->successNotificationTitle('Materi berhasil disimpan!'),
            ]);
    }
}
