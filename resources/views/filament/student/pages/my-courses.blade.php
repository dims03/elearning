<x-filament-panels::page>


    @if ($enrollments->isEmpty())
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <x-heroicon-o-book-open class="w-16 h-16 text-gray-300 mb-4" />
            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                Kamu belum mengikuti kursus apapun
            </h3>
            <p class="text-sm text-gray-400 mt-1 mb-6">
                Klik tombol "Enroll Kursus" untuk mulai belajar
            </p>
        </div>
    @else
        {{-- Grid kursus yang diikuti --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($enrollments as $enrollment)
                @php $course = $enrollment->course; @endphp
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">

                    {{-- Thumbnail --}}
                    <div class="relative h-40 bg-gray-100 dark:bg-gray-700">
                        @if ($course->thumbnail_url)
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                                class="w-full h-full object-cover" />
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <x-heroicon-o-academic-cap class="w-16 h-16 text-gray-300" />
                            </div>
                        @endif

                        {{-- Status badge --}}
                        <div class="absolute top-2 right-2">
                            @if ($enrollment->status === 'completed')
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                    ✓ Selesai
                                </span>
                            @elseif($enrollment->status === 'active')
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">
                                    Aktif
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-1">
                            @if ($course->category)
                                <span class="text-xs text-gray-400">{{ $course->category->name }}</span>
                            @endif
                            <span
                                class="text-xs px-2 py-0.5 rounded-full
                                {{ $course->level === 'beginner' ? 'bg-green-100 text-green-600' : '' }}
                                {{ $course->level === 'intermediate' ? 'bg-yellow-100 text-yellow-600' : '' }}
                                {{ $course->level === 'advanced' ? 'bg-red-100 text-red-600' : '' }}">
                                {{ ucfirst($course->level) }}
                            </span>
                        </div>

                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-1 line-clamp-2">
                            {{ $course->title }}
                        </h3>

                        <p class="text-xs text-gray-400 mb-3">
                            Guru: {{ $course->teacher->name ?? '—' }}
                        </p>

                        {{-- Progress bar --}}
                        <div class="mb-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress</span>
                                <span>{{ $enrollment->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full transition-all"
                                    style="width: {{ $enrollment->progress_percent }}%"></div>
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="flex gap-3 text-xs text-gray-400 mb-4">
                            <span>
                                <x-heroicon-o-squares-2x2 class="w-3 h-3 inline" />
                                {{ $course->chapters->count() }} chapter
                            </span>
                            <span>
                                <x-heroicon-o-document-text class="w-3 h-3 inline" />
                                {{ $course->chapters->sum(fn($c) => $c->lessons->count()) }} materi
                            </span>
                        </div>

                        {{-- Action button --}}
                        <a href="{{ \App\Filament\Student\Pages\Learn::getUrl(['course' => $course->id]) }}"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <x-heroicon-o-play-circle class="w-4 h-4" />
                            {{ $enrollment->progress_percent > 0 ? 'Continue Learning' : 'Start Learning' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Kursus tersedia (belum diikuti) --}}
    @if ($availableCourses->isNotEmpty())
        <div class="mt-10">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                Kursus Tersedia
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($availableCourses as $course)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex gap-4 items-start hover:shadow-sm transition-shadow">

                        {{-- Thumbnail kecil --}}
                        <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                            @if ($course->thumbnail_url)
                                <img src="{{ $course->thumbnail_url }}" class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <x-heroicon-o-academic-cap class="w-8 h-8 text-gray-300" />
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-sm text-gray-900 dark:text-white truncate">
                                {{ $course->title }}
                            </h4>
                            <p class="text-xs text-gray-400">{{ $course->teacher->name ?? '—' }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs text-gray-400">
                                    {{ $course->enrollments_count ?? 0 }} siswa
                                </span>
                                <span
                                    class="text-xs px-2 py-0.5 rounded-full
                                    {{ $course->is_free ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $course->is_free ? 'Gratis' : 'Berbayar' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</x-filament-panels::page>
