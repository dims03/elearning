<?php

namespace App\Filament\Teacher\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
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
                    ->width(80)
                    ->height(50),
                TextColumn::make('title')
                    ->label('Course Title')
                    ->searchable()
                    ->limit(35)
                    ->description(fn ($record) => $record->category?->name),
                TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                     ->color(fn (string $state) => match ($state) {
                        'beginner'     => 'success',
                        'intermediate' => 'warning',
                        'advanced'     => 'danger',
                    }),
                TextColumn::make('chapters_count')
                    ->label('Chapters')
                    // ->counts('chapters')
                    ->sortable(),
                TextColumn::make('enrollments_count')
                    ->label('Enrollments')
                    // ->counts('enrollments')
                    ->sortable(),
                TextColumn::make('exams_count')
                    ->label('Exams')
                    // ->counts('exams')
                    ->sortable(),
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
            ])
            ->filters([
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
