<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Textinput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, Forms\Set $set) =>
                                $set('slug', Str::slug($state))
                            ),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        Select::make('parent_id')
                            ->label('Parent Kategori')
                            ->relationship('parent', 'name')
                            ->placeholder('— Tidak ada (root) —')
                            ->preload()
                            ->searchable(),

                        TextInput::make('icon')
                            ->label('Icon (heroicon)')
                            ->placeholder('heroicon-o-academic-cap')
                            ->maxLength(100),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(2),

                        TextInput::make('order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }
}
