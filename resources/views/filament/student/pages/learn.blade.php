<x-filament-panels::page>
<div class="flex gap-0 -mx-6 -mt-6" style="min-height: calc(100vh - 80px)">

    {{-- ── Sidebar kiri: daftar chapter & lesson ───────────────────── --}}
    <aside class="w-72 flex-shrink-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">

        {{-- Header kursus --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ \App\Filament\Student\Pages\MyCourses::getUrl() }}"
               class="text-xs text-blue-500 hover:underline mb-2 flex items-center gap-1">
                ← Kembali ke Kursus Saya
            </a>
            <h2 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2">
                {{ $course->title ?? '—' }}
            </h2>
            <p class="text-xs text-gray-400 mt-1">{{ $course->teacher->name ?? '' }}</p>

            {{-- Progress bar --}}
            <div class="mt-3">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Progress</span>
                    <span>{{ $enrollment->progress_percent ?? 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                    <div class="bg-blue-500 h-1.5 rounded-full"
                         style="width: {{ $enrollment->progress_percent ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Daftar chapter & lesson --}}
        <div class="py-2">
            @foreach($course->chapters->sortBy('order') as $chapter)
                <div class="mb-1">
                    {{-- Chapter header --}}
                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-800">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                            {{ $chapter->title }}
                        </p>
                    </div>

                    {{-- Lessons --}}
                    @foreach($chapter->lessons->sortBy('order') as $lesson)
                        <button
                            wire:click="loadLesson({{ $lesson->id }})"
                            class="w-full text-left px-4 py-2.5 flex items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors
                                {{ $currentLesson?->id === $lesson->id
                                    ? 'bg-blue-50 dark:bg-blue-900/20 border-r-2 border-blue-500'
                                    : '' }}">

                            {{-- Icon status --}}
                            @if(in_array($lesson->id, $completedIds))
                                <span class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-s-check class="w-3 h-3 text-green-600"/>
                                </span>
                            @elseif($currentLesson?->id === $lesson->id)
                                <span class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <x-heroicon-s-play class="w-3 h-3 text-blue-600"/>
                                </span>
                            @else
                                <span class="w-5 h-5 rounded-full border-2 border-gray-200 dark:border-gray-600 flex-shrink-0"></span>
                            @endif

                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-800 dark:text-gray-200 line-clamp-2
                                    {{ $currentLesson?->id === $lesson->id ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                    {{ $lesson->title }}
                                </p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    {{-- Tipe icon --}}
                                    <span class="text-xs text-gray-400">
                                        @if($lesson->type === 'video') 🎬
                                        @elseif($lesson->type === 'pdf') 📄
                                        @else 📝 @endif
                                    </span>
                                    @if($lesson->duration_minutes > 0)
                                        <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }} mnt</span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endforeach
        </div>
    </aside>

    {{-- ── Konten utama: tampilan lesson ──────────────────────────── --}}
    <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-950">

        @if($currentLesson)
            <div class="max-w-4xl mx-auto p-6">

                {{-- Breadcrumb --}}
                <p class="text-xs text-gray-400 mb-2">
                    {{ $course->title }} /
                    {{ $currentLesson->chapter->title ?? '' }}
                </p>

                {{-- Judul lesson --}}
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $currentLesson->title }}
                </h1>

                {{-- ── Konten berdasar tipe ────────────────────────── --}}

                @if($currentLesson->type === 'video' && $currentLesson->video_url)
                    {{-- Video embed --}}
                    <div class="aspect-video bg-black rounded-xl overflow-hidden mb-6">
                        @php
                            $url = $currentLesson->video_url;
                            // Convert YouTube URL ke embed
                            if (str_contains($url, 'youtube.com/watch')) {
                                $id = parse_url($url, PHP_URL_QUERY);
                                parse_str($id, $params);
                                $url = 'https://www.youtube.com/embed/' . ($params['v'] ?? '');
                            } elseif (str_contains($url, 'youtu.be/')) {
                                $id = basename(parse_url($url, PHP_URL_PATH));
                                $url = 'https://www.youtube.com/embed/' . $id;
                            }
                        @endphp
                        <iframe src="{{ $url }}"
                                class="w-full h-full"
                                frameborder="0"
                                allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                        </iframe>
                    </div>

                @elseif($currentLesson->type === 'pdf' && $currentLesson->attachment_url)
                    {{-- PDF viewer --}}
                    <div class="mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <iframe src="{{ $currentLesson->attachment_url }}"
                                    class="w-full rounded-xl"
                                    style="height: 600px">
                            </iframe>
                        </div>
                        <a href="{{ $currentLesson->attachment_url }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 mt-3 text-sm text-blue-500 hover:underline">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4"/>
                            Download PDF
                        </a>
                    </div>

                @elseif($currentLesson->type === 'text' && $currentLesson->content)
                    {{-- Teks / Rich text --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6 prose dark:prose-invert max-w-none">
                        {!! $currentLesson->content !!}
                    </div>

                @else
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-10 text-center mb-6">
                        <p class="text-gray-400">Konten belum tersedia.</p>
                    </div>
                @endif

                {{-- ── Action bar bawah ─────────────────────────────── --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">

                    {{-- Status selesai --}}
                    @if(in_array($currentLesson->id, $completedIds))
                        <span class="inline-flex items-center gap-2 text-sm text-green-600 font-medium">
                            <x-heroicon-s-check-circle class="w-5 h-5"/>
                            Materi sudah selesai
                        </span>
                    @else
                        {{-- Tombol Tandai Selesai --}}
                        <button
                            wire:click="markComplete({{ $currentLesson->id }})"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-60">
                            <x-heroicon-o-check-circle class="w-4 h-4"/>
                            <span wire:loading.remove>Tandai Selesai</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    @endif

                    {{-- Tombol Next lesson --}}
                    <button
                        wire:click="nextLesson"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Materi Berikutnya
                        <x-heroicon-o-arrow-right class="w-4 h-4"/>
                    </button>
                </div>

            </div>

        @else
            {{-- Belum ada lesson --}}
            <div class="flex flex-col items-center justify-center h-full py-32 text-center">
                <x-heroicon-o-document-text class="w-16 h-16 text-gray-300 mb-4"/>
                <p class="text-gray-500">Pilih materi dari sidebar untuk mulai belajar.</p>
            </div>
        @endif

    </main>

</div>
</x-filament-panels::page>
