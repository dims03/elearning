<?php

namespace App\Filament\Teacher\Resources\Exams\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sessions';
    protected static ?string $title = 'Hasil Ujian Siswa';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('attempt_number')
                    ->label('Percobaan ke-')
                    ->alignCenter(),

                TextColumn::make('score')
                    ->label('Nilai')
                    ->suffix('%')
                    ->sortable()
                    ->color(fn ($state) => match (true) {
                        $state === null => 'gray',
                        $state >= 80   => 'success',
                        $state >= 60   => 'warning',
                        default        => 'danger',
                    }),

                IconColumn::make('is_passed')
                    ->label('Lulus')
                    ->boolean(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'graded'      => 'success',
                        'in_progress' => 'warning',
                        'submitted'   => 'info',
                        'expired'     => 'danger',
                        default       => 'gray',
                    }),

                TextColumn::make('started_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('submitted_at')
                    ->label('Submit')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum submit'),
            ])
            ->filters([
                SelectFilter::make('status')
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
            ])
            ->headerActions([])
            ->actions([
                Action::make('view_answers')
                    ->label('Lihat Jawaban')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'Jawaban: ' . $record->user->name)
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(function ($record) {
                        $answers = $record->answers()->with(['question.options', 'selectedOption'])->get();
                        return view('filament.teacher.modals.exam-answers', compact('answers', 'record'));
                    }),

                // Grade essay manual
                Action::make('grade_essay')
                    ->label('Grade Essay')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn ($record) =>
                        $record->answers()->whereHas('question', fn ($q) =>
                            $q->where('type', 'essay')
                        )->exists()
                    )
                    ->modalHeading(fn ($record) => 'Grade Essay: ' . $record->user->name)
                    ->modalWidth('4xl')
                    ->fillForm(function ($record) {
                        $fields = [];
                        foreach ($record->answers()->whereHas('question', fn ($q) => $q->where('type', 'essay'))->with('question')->get() as $answer) {
                            $fields['score_' . $answer->id]    = $answer->score_given;
                            $fields['feedback_' . $answer->id] = $answer->teacher_feedback;
                        }
                        return $fields;
                    })
                    ->form(function ($record) {
                        $fields = [];
                        $essayAnswers = $record->answers()
                            ->whereHas('question', fn ($q) => $q->where('type', 'essay'))
                            ->with('question')
                            ->get();

                        foreach ($essayAnswers as $answer) {
                            $fields[] = Fieldset::make('Soal: ' . $answer->question->question_text)
                                ->schema([
                                    Forms\Components\Placeholder::make('answer_' . $answer->id)
                                        ->label('Jawaban Siswa')
                                        ->content($answer->answer_text ?? '(tidak dijawab)')
                                        ->columnSpanFull(),

                                    Forms\Components\TextInput::make('score_' . $answer->id)
                                        ->label('Nilai (maks: ' . $answer->question->points . ' poin)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue($answer->question->points)
                                        ->required(),

                                    Forms\Components\Textarea::make('feedback_' . $answer->id)
                                        ->label('Feedback untuk siswa')
                                        ->rows(2),
                                ])->columns(2);
                        }

                        return $fields ?: [
                            Forms\Components\Placeholder::make('no_essay')
                                ->content('Tidak ada soal essay.'),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        $essayAnswers = $record->answers()
                            ->whereHas('question', fn ($q) => $q->where('type', 'essay'))
                            ->with('question')
                            ->get();

                        foreach ($essayAnswers as $answer) {
                            $scoreKey    = 'score_' . $answer->id;
                            $feedbackKey = 'feedback_' . $answer->id;
                            if (isset($data[$scoreKey])) {
                                $answer->gradeEssay(
                                    (int) $data[$scoreKey],
                                    $data[$feedbackKey] ?? null
                                );
                            }
                        }

                        // Recalculate total score
                        $score = $record->calculateScore();
                        $record->update([
                            'score'     => $score,
                            'is_passed' => $score >= $record->exam->pass_score,
                            'status'    => 'graded',
                        ]);
                    })
                    ->successNotificationTitle('Essay berhasil dinilai!'),
            ]);
    }
}
