<?php

namespace App\Filament\Teacher\Resources\Exams;

use App\Filament\Teacher\Resources\Exams\Pages\CreateExam;
use App\Filament\Teacher\Resources\Exams\Pages\EditExam;
use App\Filament\Teacher\Resources\Exams\Pages\ListExams;
use App\Filament\Teacher\Resources\Exams\Schemas\ExamForm;
use App\Filament\Teacher\Resources\Exams\Tables\ExamsTable;
use App\Models\Exam;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Exams';
    protected static string | UnitEnum | null $navigationGroup = 'Content Management';
    protected static ?string $recordTitleAttribute = 'Exams';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('course', fn ($q) => $q->where('teacher_id', auth()->id()))
            ->withCount(['questions', 'sessions']);
    }

    public static function form(Schema $schema): Schema
    {
        return ExamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExams::route('/'),
            'create' => CreateExam::route('/create'),
            'edit' => EditExam::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
