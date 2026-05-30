<?php

namespace App\Filament\Teacher\Resources\Exams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Textcolumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->limit(25),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('questions_count')
                    ->label('Questions'),
                // ->counts('questions'),
                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' mins'),
                TextColumn::make('pass_score')
                    ->label('Pass Score')
                    ->suffix(' %'),
                TextColumn::make('max_attempts')
                    ->label('Max Attempts'),
                TextColumn::make('sessions_count')
                    ->label('Sessions'),
                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                    ]),
                TextColumn::make('start_at')
                    ->label('Start At')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                    ]),
                SelectFilter::make('course')
                    ->relationship(
                        'course',
                        'title',
                        fn (Builder $query) => $query->where('teacher_id', Auth::id())
                    )
                    ->label('Course'),
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
