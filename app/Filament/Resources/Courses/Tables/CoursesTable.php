<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('')
                    ->width(60)
                    ->height(40),

                TextColumn::make('title')
                    ->label('Judul Kursus')
                    ->searchable()
                    ->sortable()
                    ->limit(35),

                TextColumn::make('teacher.name')
                    ->label('Guru')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge(),

                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'beginner'     => 'success',
                        'intermediate' => 'warning',
                        'advanced'     => 'danger',
                    }),

                TextColumn::make('enrollments_count')
                    ->label('Siswa')
                    ->counts('enrollments')
                    ->sortable(),

                TextColumn::make('chapters_count')
                    ->label('Chapter')
                    ->counts('chapters'),

                TextColumn::make('exams_count')
                    ->label('Ujian')
                    ->counts('exams'),

                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ]),

                IconColumn::make('is_free')
                    ->label('Gratis')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ]),
                SelectFilter::make('level')
                    ->options([
                        'beginner'     => 'Beginner',
                        'intermediate' => 'Intermediate',
                        'advanced'     => 'Advanced',
                    ]),
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_free')
                    ->label('Kursus Gratis'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
