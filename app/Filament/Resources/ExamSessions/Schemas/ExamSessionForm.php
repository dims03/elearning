<?php

namespace App\Filament\Resources\ExamSessions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExamSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    Select::make('user_id')
                        ->label('Siswa')
                        ->relationship('user', 'name'),
                    Select::make('exam_id')
                        ->label('Ujian')
                        ->relationship('exam', 'title'),
                    TextInput::make('score')
                        ->label('Nilai'),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'in_progress' => 'Sedang Berjalan',
                            'submitted'   => 'Submitted',
                            'graded'      => 'Sudah Dinilai',
                            'expired'     => 'Kadaluarsa',
                        ]),
                    Toggle::make('is_passed')
                        ->label('Lulus'),
                ])->columns(2)->columnSpanFull(),
            ]);
    }
}
