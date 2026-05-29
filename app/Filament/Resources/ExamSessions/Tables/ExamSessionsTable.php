<?php

namespace App\Filament\Resources\ExamSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ExamSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('exam.title')
                    ->label('Ujian')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('exam.course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->limit(25),
                TextColumn::make('attempt_number')
                    ->label('Percobaan')
                    ->alignCenter(),
                TextColumn::make('score')
                    ->label('Nilai')
                    ->suffix('%')
                    ->sortable()
                    ->color(fn($state) => match (true) {
                        $state === null    => 'gray',
                        $state >= 80       => 'success',
                        $state >= 60       => 'warning',
                        default            => 'danger',
                    }),
                IconColumn::make('is_passed')
                    ->label('Lulus')
                    ->boolean(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'graded'      => 'success',
                        'in_progress' => 'warning',
                        'submitted'   => 'info',
                        'expired'     => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('started_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('Submit')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('started_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'in_progress' => 'Sedang Berjalan',
                        'submitted'   => 'Submitted',
                        'graded'      => 'Sudah Dinilai',
                        'expired'     => 'Kadaluarsa',
                    ]),
                TernaryFilter::make('is_passed')
                    ->label('Kelulusan')
                    ->trueLabel('Lulus')
                    ->falseLabel('Tidak Lulus'),
                SelectFilter::make('exam')
                    ->relationship('exam', 'title')
                    ->label('Ujian')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
