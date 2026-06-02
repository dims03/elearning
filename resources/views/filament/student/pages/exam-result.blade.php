<x-filament-panels::page>

<div class="max-w-3xl mx-auto py-6">

    {{-- Header hasil --}}
    <div class="text-center mb-8">
        @if($isPassed)
            <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                <x-heroicon-s-trophy class="w-12 h-12 text-green-500"/>
            </div>
            <h2 class="text-2xl font-bold text-green-600 mb-1">Selamat, Kamu Lulus! 🎉</h2>
        @else
            <div class="w-24 h-24 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <x-heroicon-s-x-circle class="w-12 h-12 text-red-400"/>
            </div>
            <h2 class="text-2xl font-bold text-red-500 mb-1">Belum Lulus</h2>
        @endif
        <p class="text-gray-500">{{ $exam->title }} — {{ $exam->course->title }}</p>
    </div>

    {{-- Score card --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-400 mb-1">Nilai Kamu</p>
            <p class="text-3xl font-bold {{ $isPassed ? 'text-green-600' : 'text-red-500' }}">
                {{ $score }}%
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-400 mb-1">Nilai Lulus</p>
            <p class="text-3xl font-bold text-gray-700 dark:text-gray-300">{{ $passingScore }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
            <p class="text-xs text-gray-400 mb-1">Percobaan ke-</p>
            <p class="text-3xl font-bold text-gray-700 dark:text-gray-300">{{ $sessionData->attempt_number }}</p>
        </div>
    </div>

    {{-- Review jawaban --}}
    @if($exam->show_result_immediately)
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Review Jawaban</h3>

        <div class="space-y-4">
            @foreach($exam->questions as $index => $question)
                @php $answer = $answers->get($question->id); @endphp

                <div class="bg-white dark:bg-gray-800 rounded-xl border p-5
                    {{ $answer?->is_correct === true ? 'border-green-200 dark:border-green-800' : '' }}
                    {{ $answer?->is_correct === false ? 'border-red-200 dark:border-red-800' : '' }}
                    {{ $answer?->is_correct === null ? 'border-gray-200 dark:border-gray-700' : '' }}">

                    <div class="flex items-start justify-between mb-2">
                        <span class="text-xs text-gray-400">Soal {{ $index + 1 }}</span>
                        @if($question->isEssay())
                            <span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-600 rounded-full">
                                Essay — menunggu penilaian guru
                            </span>
                        @elseif($answer?->is_correct)
                            <span class="text-xs px-2 py-0.5 bg-green-100 text-green-600 rounded-full font-medium">
                                ✓ Benar (+{{ $answer->score_given }} poin)
                            </span>
                        @else
                            <span class="text-xs px-2 py-0.5 bg-red-100 text-red-600 rounded-full font-medium">
                                ✗ Salah (0 poin)
                            </span>
                        @endif
                    </div>

                    <p class="font-medium text-gray-900 dark:text-white text-sm mb-3">
                        {{ $question->question_text }}
                    </p>

                    @if($question->isEssay())
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-sm text-gray-600 dark:text-gray-300">
                            {{ $answer?->answer_text ?? '(tidak dijawab)' }}
                        </div>
                        @if($answer?->teacher_feedback)
                            <p class="text-xs text-blue-600 mt-2">
                                💬 Feedback guru: {{ $answer->teacher_feedback }}
                            </p>
                        @endif
                    @else
                        <div class="space-y-1.5">
                            @foreach($question->options as $option)
                                <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                                    {{ $option->is_correct ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' : '' }}
                                    {{ !$option->is_correct && $answer?->selected_option_id == $option->id ? 'bg-red-50 dark:bg-red-900/20 text-red-600' : '' }}
                                    {{ !$option->is_correct && $answer?->selected_option_id != $option->id ? 'text-gray-500' : '' }}">
                                    @if($option->is_correct)
                                        <x-heroicon-s-check-circle class="w-4 h-4 text-green-500 flex-shrink-0"/>
                                    @elseif($answer?->selected_option_id == $option->id)
                                        <x-heroicon-s-x-circle class="w-4 h-4 text-red-400 flex-shrink-0"/>
                                    @else
                                        <span class="w-4 h-4 flex-shrink-0"></span>
                                    @endif
                                    {{ $option->option_text }}
                                    @if($answer?->selected_option_id == $option->id && !$option->is_correct)
                                        <span class="text-xs text-red-400 ml-auto">Jawabanmu</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Penjelasan --}}
                        @if($question->explanation)
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 bg-blue-50 dark:bg-blue-900/20 p-2 rounded-lg">
                                💡 {{ $question->explanation }}
                            </div>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Tombol navigasi --}}
    <div class="flex gap-3 mt-8 justify-center">
        <a href="{{ \App\Filament\Student\Pages\MyExam::getUrl() }}"
           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg text-sm">
            ← Kembali ke Ujian
        </a>
        <a href="{{ \App\Filament\Student\Pages\MyCourses::getUrl() }}"
           class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm">
            Ke Kursus Saya
        </a>
    </div>

</div>

</x-filament-panels::page>
