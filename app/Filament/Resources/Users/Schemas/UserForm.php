<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Textinput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Textinput::make('password')
                            ->label('Password')
                            ->required()
                            ->revealable()
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context) => $context === 'create'),
                        TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),
                    Section::make('Role & Akses')
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->required(),
                    ]),

                    Section::make('Profile')
                        ->schema([
                            Textarea::make('bio')
                                ->label('Bio')
                                ->rows(3)
                                ->maxLength(500),
                            FileUpload::make('avatar_url')
                                ->label('Avatar')
                                ->image()
                                ->disk('public')
                                ->directory('avatars')
                                ->visibility('public')
                                ->maxSize(1024) // 1MB
                                ->maxFiles(1)
                                ->imageEditor(),
                        ])
            ]);
    }
}
