<x-filament-panels::page>
<div x-data="{ mobileSidebarOpen: false }" class="relative">
    <div
        class="fixed inset-0 z-30 bg-slate-950/35 transition-opacity duration-200 lg:hidden"
        :class="mobileSidebarOpen ? 'pointer-events-auto opacity-100' : 'pointer-events-none opacity-0'"
        @click="mobileSidebarOpen = false"
    ></div>

    <div class="flex flex-col overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 lg:-mx-6 lg:-mt-6 lg:min-h-[calc(100vh-80px)] lg:flex-row">

        {{-- ── Sidebar kiri: daftar chapter & lesson ───────────────────── --}}
        <aside
            class="fixed inset-y-0 left-0 z-40 w-[min(22rem,88vw)] overflow-y-auto border-r border-gray-200 bg-white transition-transform duration-200 dark:border-gray-700 dark:bg-gray-900 lg:static lg:w-80 lg:flex-shrink-0 lg:translate-x-0"
            :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4 dark:border-gray-700 lg:hidden">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Daftar Materi</p>
                <button
                    type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 dark:border-gray-700 dark:text-gray-300"
                    @click="mobileSidebarOpen = false"
                >
                    <x-heroicon-o-x-mark class="h-5 w-5" />
                </button>
            </div>

            {{-- Header kursus --}}
            <div class="border-b border-gray-200 p-4 dark:border-gray-700">
                <a href="{{ \App\Filament\Student\Pages\MyCourses::getUrl() }}"
                   class="mb-2 flex items-center gap-1 text-xs text-blue-500 hover:underline">
                    ← Kembali ke Kursus Saya
                </a>
                <h2 class="line-clamp-2 text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $course->title ?? '—' }}
                </h2>
                <p class="mt-1 text-xs text-gray-400">{{ $course->teacher->name ?? '' }}</p>

                {{-- Progress bar --}}
                <div class="mt-3">
                    <div class="mb-1 flex justify-between text-xs text-gray-500">
                        <span>Progress</span>
                        <span>{{ $enrollment->progress_percent ?? 0 }}%</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-1.5 rounded-full bg-blue-500"
                             style="width: {{ $enrollment->progress_percent ?? 0 }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Daftar chapter & lesson --}}
            <div class="py-2">
                @foreach($course->chapters->sortBy('order') as $chapter)
                    <div class="mb-1">
                        {{-- Chapter header --}}
                        <div class="bg-gray-50 px-4 py-2 dark:bg-gray-800">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                {{ $chapter->title }}
                            </p>
                        </div>

                        {{-- Lessons --}}
                        @foreach($chapter->lessons->sortBy('order') as $lesson)
                            <button
                                wire:click="loadLesson({{ $lesson->id }})"
                                x-on:click="mobileSidebarOpen = false"
                                class="flex w-full items-center gap-3 px-4 py-2.5 text-left transition-colors hover:bg-gray-50 dark:hover:bg-gray-800
                                    {{ $currentLesson?->id === $lesson->id
                                        ? 'border-r-2 border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                        : '' }}"
                            >

                                {{-- Icon status --}}
                                @if(in_array($lesson->id, $completedIds))
                                    <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-100">
                                        <x-heroicon-s-check class="h-3 w-3 text-green-600"/>
                                    </span>
                                @elseif($currentLesson?->id === $lesson->id)
                                    <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-blue-100">
                                        <x-heroicon-s-play class="h-3 w-3 text-blue-600"/>
                                    </span>
                                @else
                                    <span class="h-5 w-5 flex-shrink-0 rounded-full border-2 border-gray-200 dark:border-gray-600"></span>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-xs font-medium text-gray-800 dark:text-gray-200
                                        {{ $currentLesson?->id === $lesson->id ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                        {{ $lesson->title }}
                                    </p>
                                    <div class="mt-0.5 flex items-center gap-2">
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
        <main class="min-w-0 flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-950">

            @if($currentLesson)
                <div class="mx-auto max-w-4xl p-4 sm:p-6">
                    <div class="mb-4 flex flex-col gap-3 sm:mb-5 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            {{-- Breadcrumb --}}
                            <p class="mb-2 text-xs text-gray-400">
                                {{ $course->title }} /
                                {{ $currentLesson->chapter->title ?? '' }}
                            </p>

                            {{-- Judul lesson --}}
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
                                {{ $currentLesson->title }}
                            </h1>
                        </div>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 lg:hidden"
                            @click="mobileSidebarOpen = true"
                        >
                            <x-heroicon-o-bars-3 class="h-4 w-4" />
                            Daftar Materi
                        </button>
                    </div>

                    {{-- ── Konten berdasar tipe ────────────────────────── --}}

                    @if($currentLesson->type === 'video' && $currentLesson->video_url)
                        {{-- Video embed --}}
                        <div class="mb-6 aspect-video overflow-hidden rounded-xl bg-black">
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
                                    class="h-full w-full"
                                    frameborder="0"
                                    allowfullscreen
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                            </iframe>
                        </div>

                    @elseif($currentLesson->type === 'pdf' && $currentLesson->attachment_url)
                        {{-- PDF viewer --}}
                        <div class="mb-6">
                            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                <iframe src="{{ $currentLesson->attachment_url }}"
                                        class="h-[60vh] w-full rounded-xl sm:h-[600px]">
                                </iframe>
                            </div>
                            <a href="{{ $currentLesson->attachment_url }}"
                               target="_blank"
                               class="mt-3 inline-flex items-center gap-2 text-sm text-blue-500 hover:underline">
                                <x-heroicon-o-arrow-down-tray class="h-4 w-4"/>
                                Download PDF
                            </a>
                        </div>

                    @elseif($currentLesson->type === 'text' && $currentLesson->content)
                        {{-- Teks / Rich text --}}
                        <div class="learn-lesson-content prose mb-6 max-w-none overflow-hidden rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 dark:prose-invert sm:p-6">
                            {!! $currentLesson->content !!}
                        </div>

                    @else
                        <div class="mb-6 rounded-xl bg-gray-100 p-8 text-center dark:bg-gray-800 sm:p-10">
                            <p class="text-gray-400">Konten belum tersedia.</p>
                        </div>
                    @endif

                    {{-- ── Action bar bawah ─────────────────────────────── --}}
                    <div class="flex flex-col gap-3 border-t border-gray-200 pt-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">

                        {{-- Status selesai --}}
                        @if(in_array($currentLesson->id, $completedIds))
                            <span class="inline-flex items-center gap-2 text-sm font-medium text-green-600">
                                <x-heroicon-s-check-circle class="h-5 w-5"/>
                                Materi sudah selesai
                            </span>
                        @else
                            {{-- Tombol Tandai Selesai --}}
                            <button
                                wire:click="markComplete({{ $currentLesson->id }})"
                                wire:loading.attr="disabled"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-sm font-medium text-white transition-colors hover:bg-green-700 disabled:opacity-60 sm:w-auto">
                                <x-heroicon-o-check-circle class="h-4 w-4"/>
                                <span wire:loading.remove>Tandai Selesai</span>
                                <span wire:loading>Menyimpan...</span>
                            </button>
                        @endif

                        {{-- Tombol Next lesson --}}
                        <button
                            wire:click="nextLesson"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-medium text-white transition-colors hover:bg-blue-700 sm:w-auto">
                            Materi Berikutnya
                            <x-heroicon-o-arrow-right class="h-4 w-4"/>
                        </button>
                    </div>

                </div>

            @else
                {{-- Belum ada lesson --}}
                <div class="flex h-full flex-col items-center justify-center px-4 py-24 text-center sm:py-32">
                    <x-heroicon-o-document-text class="mb-4 h-16 w-16 text-gray-300"/>
                    <p class="text-gray-500">Pilih materi dari daftar untuk mulai belajar.</p>
                    <button
                        type="button"
                        class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 lg:hidden"
                        @click="mobileSidebarOpen = true"
                    >
                        <x-heroicon-o-bars-3 class="h-4 w-4" />
                        Buka Daftar Materi
                    </button>
                </div>
            @endif

        </main>

    </div>
</div>
</x-filament-panels::page>
