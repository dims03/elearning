<x-filament-panels::page>

{{-- Timer bar fixed di atas --}}
<div class="fixed top-0 left-0 right-0 z-50 bg-gray-900 text-white px-6 py-3 flex items-center justify-between shadow-lg">
    <div class="flex items-center gap-3">
        <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-blue-400"/>
        <div>
            <p class="text-xs text-gray-400">{{ $currentExam->course->title }}</p>
            <p class="font-semibold text-sm">{{ $currentExam->title }}</p>
        </div>
    </div>

    {{-- Timer --}}
    <div class="flex items-center gap-3">
        <div class="text-center">
            <p class="text-xs text-gray-400 mb-0.5">Sisa Waktu</p>
            <p id="timer"
               class="font-mono font-bold text-xl tabular-nums"
               data-seconds="{{ $remainingSeconds }}"
               data-session-id="{{ $session->id }}"
               wire:ignore>
                {{ gmdate('H:i:s', $remainingSeconds) }}
            </p>
        </div>
    </div>

    {{-- Progress jawaban --}}
    <div class="text-right">
        <p class="text-xs text-gray-400">Terjawab</p>
        <p class="font-bold text-sm">
            <span id="answered-count">{{ $answeredCount }}</span>/{{ $totalQuestions }}
        </p>
    </div>
</div>

{{-- Spacer untuk fixed timer bar --}}
<div class="h-16"></div>

{{-- Konten soal --}}
<div class="max-w-3xl mx-auto py-6 space-y-6">

    @foreach($questions as $index => $question)
        <div id="question-{{ $question->id }}"
             class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">

            {{-- Nomor & poin --}}
            <div class="flex items-start justify-between mb-3">
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 rounded-full">
                    Soal {{ $index + 1 }}
                </span>
                <span class="text-xs text-gray-400">{{ $question->points }} poin</span>
            </div>

            {{-- Teks pertanyaan --}}
            <p class="text-gray-900 dark:text-white font-medium mb-4 leading-relaxed">
                {{ $question->question_text }}
            </p>

            {{-- Gambar soal (jika ada) --}}
            @if($question->image)
                <img src="{{ asset('storage/' . $question->image) }}"
                     class="rounded-lg mb-4 max-h-48 object-contain"/>
            @endif

            {{-- ── Pilihan jawaban berdasar tipe ────────────────── --}}

            @if($question->type === 'multiple_choice')
                <div class="space-y-2">
                    @foreach($question->options as $option)
                        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors
                            {{ ($answers[$question->id] ?? '') == $option->id
                                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <input type="radio"
                                   name="q{{ $question->id }}"
                                   value="{{ $option->id }}"
                                   {{ ($answers[$question->id] ?? '') == $option->id ? 'checked' : '' }}
                                   wire:click="saveAnswer({{ $question->id }}, '{{ $option->id }}')"
                                   class="text-blue-600"/>
                            <span class="text-sm text-gray-800 dark:text-gray-200">
                                {{ $option->option_text }}
                            </span>
                        </label>
                    @endforeach
                </div>

            @elseif($question->type === 'true_false')
                <div class="flex gap-3">
                    @foreach([1 => 'Benar ✓', 0 => 'Salah ✗'] as $val => $label)
                        <label class="flex-1 flex items-center justify-center gap-2 p-3 rounded-lg border cursor-pointer transition-colors
                            {{ ($answers[$question->id] ?? '') == $val
                                ? ($val == 1 ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20')
                                : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <input type="radio"
                                   name="q{{ $question->id }}"
                                   value="{{ $val }}"
                                   {{ ($answers[$question->id] ?? '') == $val ? 'checked' : '' }}
                                   wire:click="saveAnswer({{ $question->id }}, '{{ $val }}')"
                                   class="hidden"/>
                            <span class="font-medium text-sm">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

            @elseif($question->type === 'essay')
                <textarea
                    rows="4"
                    placeholder="Tulis jawaban kamu di sini..."
                    wire:model.lazy="answers.{{ $question->id }}"
                    wire:change="saveAnswer({{ $question->id }}, $event.target.value)"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-3 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ $answers[$question->id] ?? '' }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Jawaban essay akan dinilai oleh guru</p>
            @endif
        </div>
    @endforeach

    {{-- Tombol Submit --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-medium text-gray-900 dark:text-white">Siap mengumpulkan?</p>
                <p class="text-sm text-gray-500 mt-0.5">
                    Terjawab: <strong id="summary-answered">{{ $answeredCount }}</strong>/{{ $totalQuestions }} soal
                </p>
            </div>
            <button
                wire:click="submitExam"
                wire:confirm="Yakin ingin mengumpulkan ujian? Jawaban yang belum diisi akan dianggap kosong."
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-60">
                <span wire:loading.remove>
                    <x-heroicon-o-paper-airplane class="w-5 h-5 inline mr-1"/>
                    Kumpulkan Ujian
                </span>
                <span wire:loading>Mengumpulkan...</span>
            </button>
        </div>
    </div>

</div>

{{-- Timer JavaScript --}}
<script>
window.__takeExamTimer ??= {
    intervalId: null,
    sessionId: null,
};

function initTakeExamTimer() {
    const timerEl = document.getElementById('timer');

    if (! timerEl) {
        if (window.__takeExamTimer.intervalId) {
            clearInterval(window.__takeExamTimer.intervalId);
            window.__takeExamTimer.intervalId = null;
            window.__takeExamTimer.sessionId = null;
        }

        return;
    }

    const sessionId = timerEl.dataset.sessionId;

    if (window.__takeExamTimer.intervalId && window.__takeExamTimer.sessionId === sessionId) {
        return;
    }

    if (window.__takeExamTimer.intervalId) {
        clearInterval(window.__takeExamTimer.intervalId);
    }

    window.__takeExamTimer.sessionId = sessionId;

    let seconds = parseInt(timerEl.dataset.seconds, 10);

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function format(s) {
        const h = Math.floor(s / 3600);
        const m = Math.floor((s % 3600) / 60);
        const sec = s % 60;

        return h > 0
            ? `${pad(h)}:${pad(m)}:${pad(sec)}`
            : `${pad(m)}:${pad(sec)}`;
    }

    function updateColor(s) {
        if (s <= 60) {
            timerEl.classList.add('text-red-400');
            timerEl.classList.remove('text-yellow-400', 'text-white');
        } else if (s <= 300) {
            timerEl.classList.add('text-yellow-400');
            timerEl.classList.remove('text-red-400', 'text-white');
        } else {
            timerEl.classList.add('text-white');
            timerEl.classList.remove('text-red-400', 'text-yellow-400');
        }
    }

    timerEl.textContent = format(seconds);
    updateColor(seconds);

    window.__takeExamTimer.intervalId = setInterval(() => {
        seconds--;

        if (seconds <= 0) {
            clearInterval(window.__takeExamTimer.intervalId);
            window.__takeExamTimer.intervalId = null;
            timerEl.textContent = '00:00';
            @this.timeUp();
            return;
        }

        timerEl.textContent = format(seconds);
        updateColor(seconds);
    }, 1000);
}

document.addEventListener('DOMContentLoaded', initTakeExamTimer);
document.addEventListener('livewire:navigated', initTakeExamTimer);
document.addEventListener('livewire:navigating', () => {
    if (window.__takeExamTimer?.intervalId) {
        clearInterval(window.__takeExamTimer.intervalId);
        window.__takeExamTimer.intervalId = null;
        window.__takeExamTimer.sessionId = null;
    }
});
</script>

</x-filament-panels::page>
