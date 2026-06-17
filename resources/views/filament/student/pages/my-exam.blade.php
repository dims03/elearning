<x-filament-panels::page>

@if($exams->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <x-heroicon-o-clipboard-document-list class="w-16 h-16 text-gray-300 mb-4"/>
        <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400">Belum ada ujian tersedia</h3>
        <p class="text-sm text-gray-400 mt-1">Ikuti kursus terlebih dahulu untuk melihat ujian</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($exams as $item)
            @php
                $exam       = $item['exam'];
                $canAttempt = $item['canAttempt'];
                $hasPassed  = $item['hasPassed'];
                $latestScore = $item['latestScore'];
                $inProgress = $item['inProgress'];
                $attempts   = $item['attemptCount'];
                $resultSession = $item['resultSession'];
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-start justify-between gap-4">

                    <div class="flex-1">
                        {{-- Kursus badge --}}
                        <p class="text-xs text-gray-400 mb-1">{{ $exam->course->title }}</p>

                        {{-- Judul ujian --}}
                        <h3 class="font-semibold text-gray-900 dark:text-white text-base mb-2">
                            {{ $exam->title }}
                        </h3>

                        {{-- Info ujian --}}
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-3">
                            <span class="flex items-center gap-1">
                                <x-heroicon-o-clock class="w-4 h-4"/>
                                {{ $exam->duration_minutes }} menit
                            </span>
                            <span class="flex items-center gap-1">
                                <x-heroicon-o-question-mark-circle class="w-4 h-4"/>
                                {{ $exam->questions_count ?? $exam->questions->count() }} soal
                            </span>
                            <span class="flex items-center gap-1">
                                <x-heroicon-o-trophy class="w-4 h-4"/>
                                Nilai lulus: {{ $exam->pass_score }}%
                            </span>
                            <span class="flex items-center gap-1">
                                <x-heroicon-o-arrow-path class="w-4 h-4"/>
                                {{ $attempts }}/{{ $exam->max_attempts }} percobaan
                            </span>
                        </div>

                        {{-- Status & best score --}}
                        @if($hasPassed)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full font-medium">
                                <x-heroicon-s-check-circle class="w-4 h-4"/> Lulus — Attempt {{ $resultSession->attempt_number }}: {{ $latestScore }}%
                            </span>
                        @elseif($latestScore !== null)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-700 text-sm rounded-full font-medium">
                                <x-heroicon-s-x-circle class="w-4 h-4"/> Belum lulus — Attempt {{ $resultSession->attempt_number }}: {{ $latestScore }}%
                            </span>
                        @endif

                        {{-- Jadwal --}}
                        @if($exam->start_at || $exam->end_at)
                            <div class="mt-2 text-xs text-gray-400">
                                @if($exam->start_at) Mulai: {{ $exam->start_at->format('d M Y H:i') }} @endif
                                @if($exam->end_at) · Selesai: {{ $exam->end_at->format('d M Y H:i') }} @endif
                            </div>
                        @endif
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="flex flex-col gap-2 flex-shrink-0">
                        @if($inProgress)
                            {{-- Lanjutkan ujian yang belum selesai --}}
                            @php $examUrl = \App\Filament\Student\Pages\TakeExam::getUrl(['exam' => $exam->id]); @endphp
                            <a href="{{ $examUrl }}"
                               wire:navigate.hover
                               class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg">
                                <x-heroicon-o-play class="w-4 h-4"/>
                                Lanjutkan
                            </a>
                        @elseif($canAttempt)
                            @php $examUrl = \App\Filament\Student\Pages\TakeExam::getUrl(['exam' => $exam->id]); @endphp
                            <a href="{{ $examUrl }}"
                               wire:navigate.hover
                               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                                <x-heroicon-o-pencil-square class="w-4 h-4"/>
                                {{ $attempts > 0 ? 'Coba Lagi' : 'Mulai Ujian' }}
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed">
                                <x-heroicon-o-lock-closed class="w-4 h-4"/>
                                Batas Tercapai
                            </span>
                        @endif

                        {{-- Lihat hasil --}}
                        @if($item['resultSession'])
                            @php $resultUrl = \App\Filament\Student\Pages\ExamResult::getUrl(['session' => $item['resultSession']->id]); @endphp
                            <a href="{{ $resultUrl }}"
                               wire:navigate.hover
                               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg">
                                <x-heroicon-o-chart-bar class="w-4 h-4"/>
                                Lihat Hasil
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

</x-filament-panels::page>
