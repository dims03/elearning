<div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">

    {{-- Header info session --}}
    <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-700">
        <div>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ $record->user->name }}
            </p>
            <p class="text-xs text-gray-400">
                Percobaan ke-{{ $record->attempt_number }} ·
                Submit: {{ $record->submitted_at?->format('d M Y H:i') ?? 'Belum submit' }}
            </p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-400">Nilai</p>
            <p class="text-2xl font-bold {{ $record->is_passed ? 'text-green-600' : 'text-red-500' }}">
                {{ $record->score ?? '—' }}%
            </p>
        </div>
    </div>

    {{-- Daftar jawaban --}}
    @forelse($answers as $index => $answer)
        @php $question = $answer->question; @endphp

        <div class="rounded-lg border p-4
            {{ $answer->is_correct === true  ? 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/10' : '' }}
            {{ $answer->is_correct === false ? 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/10' : '' }}
            {{ $answer->is_correct === null  ? 'border-gray-200 dark:border-gray-700' : '' }}">

            {{-- Nomor & tipe --}}
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500">
                    Soal {{ $index + 1 }} · {{ strtoupper($question->type) }} · {{ $question->points }} poin
                </span>

                @if($question->isEssay())
                    <span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full">
                        Perlu dinilai manual
                    </span>
                @elseif($answer->is_correct)
                    <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-medium">
                        ✓ Benar (+{{ $answer->score_given }})
                    </span>
                @else
                    <span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-medium">
                        ✗ Salah (0)
                    </span>
                @endif
            </div>

            {{-- Teks pertanyaan --}}
            <p class="text-sm font-medium text-gray-900 dark:text-white mb-3">
                {{ $question->question_text }}
            </p>

            {{-- Jawaban berdasar tipe --}}
            @if($question->isEssay())
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 p-3">
                    <p class="text-xs text-gray-400 mb-1">Jawaban siswa:</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        {{ $answer->answer_text ?? '(tidak dijawab)' }}
                    </p>
                </div>
                @if($answer->score_given > 0 || $answer->teacher_feedback)
                    <div class="mt-2 text-xs text-blue-600 dark:text-blue-400">
                        Nilai diberikan: {{ $answer->score_given }}/{{ $question->points }} poin
                        @if($answer->teacher_feedback)
                            · Feedback: {{ $answer->teacher_feedback }}
                        @endif
                    </div>
                @endif

            @else
                {{-- Multiple choice / true false --}}
                <div class="space-y-1.5">
                    @foreach($question->options as $option)
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                            {{ $option->is_correct ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : '' }}
                            {{ !$option->is_correct && $answer->selected_option_id == $option->id
                                ? 'bg-red-100 dark:bg-red-900/30 text-red-600' : '' }}
                            {{ !$option->is_correct && $answer->selected_option_id != $option->id
                                ? 'text-gray-500 dark:text-gray-400' : '' }}">

                            @if($option->is_correct)
                                <x-heroicon-s-check-circle class="w-4 h-4 text-green-500 flex-shrink-0"/>
                            @elseif($answer->selected_option_id == $option->id)
                                <x-heroicon-s-x-circle class="w-4 h-4 text-red-400 flex-shrink-0"/>
                            @else
                                <span class="w-4 h-4 flex-shrink-0 rounded-full border-2 border-gray-300"></span>
                            @endif

                            <span>{{ $option->option_text }}</span>

                            @if($answer->selected_option_id == $option->id && !$option->is_correct)
                                <span class="ml-auto text-xs text-red-400">← Pilihan siswa</span>
                            @elseif($answer->selected_option_id == $option->id && $option->is_correct)
                                <span class="ml-auto text-xs text-green-500">← Pilihan siswa ✓</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Penjelasan jawaban --}}
            @if($question->explanation)
                <div class="mt-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-xs text-blue-600 dark:text-blue-400">
                        💡 <strong>Penjelasan:</strong> {{ $question->explanation }}
                    </p>
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-8 text-gray-400">
            <x-heroicon-o-clipboard-document class="w-10 h-10 mx-auto mb-2"/>
            <p class="text-sm">Belum ada jawaban.</p>
        </div>
    @endforelse

</div>