<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular()
                    ->size(40),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Textcolumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'admin'   => 'danger',
                        'teacher' => 'warning',
                        'student' => 'success',
                        default   => 'gray',
                    }),
                // TextColumn::make('enrollments_count')
                //     ->label('Kursus')
                //     ->counts('enrollments')
                //     ->sortable(),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Filter Role'),
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
