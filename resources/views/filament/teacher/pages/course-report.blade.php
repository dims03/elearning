<x-filament-panels::page>

    {{-- Filter kursus --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Pilih Kursus
            </label>
            <select wire:model.live="selectedCourseId"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">— Pilih kursus —</option>
                @foreach ($this->getCourses() as $course)
                    <option value="{{ $course->id }}">
                        {{ $course->title }}
                        @if($course->teacher)
                            — {{ $course->teacher->name }}
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @php $data = $this->getReportData(); @endphp

    @if (empty($data))
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <x-heroicon-o-chart-bar class="w-16 h-16 text-gray-300 mb-4" />
            <p class="text-gray-500">Pilih kursus untuk melihat laporan</p>
        </div>
    @else

        {{-- Info kursus + guru --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl px-5 py-3 mb-6 flex items-center gap-3">
            <x-heroicon-o-book-open class="w-5 h-5 text-blue-500 flex-shrink-0"/>
            <div>
                <p class="font-semibold text-blue-900 dark:text-blue-200 text-sm">{{ $data['course']->title }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400">
                    Guru: {{ $data['course']->teacher->name ?? '—' }} ·
                    {{ $data['course']->category->name ?? '—' }} ·
                    {{ ucfirst($data['course']->level) }}
                </p>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Total Siswa</p>
                <p class="text-3xl font-bold text-blue-600">{{ $data['totalStudents'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $data['completedStudents'] }} selesai</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Rata-rata Progress</p>
                <p class="text-3xl font-bold text-teal-600">{{ $data['avgProgress'] }}%</p>
                <p class="text-xs text-gray-400 mt-1">Semua siswa</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Rata-rata Nilai</p>
                <p class="text-3xl font-bold {{ $data['avgScore'] >= 70 ? 'text-green-600' : 'text-red-500' }}">
                    {{ $data['avgScore'] }}%
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $data['totalExamTakers'] }} peserta ujian</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center">
                <p class="text-xs text-gray-400 mb-1">Tingkat Kelulusan</p>
                <p class="text-3xl font-bold {{ $data['passRate'] >= 70 ? 'text-green-600' : 'text-orange-500' }}">
                    {{ $data['passRate'] }}%
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $data['passedStudents'] }} lulus</p>
            </div>
        </div>

        {{-- Tabel Progress Siswa --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 dark:text-white">Progress Siswa</h3>
                <div class="flex items-center gap-3">
                    <select wire:model.live="perPage"
                        class="text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-2 py-1">
                        <option value="10">10 / halaman</option>
                        <option value="15">15 / halaman</option>
                        <option value="25">25 / halaman</option>
                        <option value="50">50 / halaman</option>
                    </select>
                    <span class="text-xs text-gray-400">{{ $data['totalStudents'] }} siswa terdaftar</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Progress</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tgl Enroll</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Nilai Terbaik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($data['enrollments'] as $i => $enrollment)
                            @php
                                $userSessions = $data['examSessions']->get($enrollment->user_id);
                                $bestScore    = $userSessions?->max('score');
                                $hasPassed    = $userSessions?->where('is_passed', true)->isNotEmpty();
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $enrollment->user->name }}
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $enrollment->user->email }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $enrollment->progress_percent >= 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                                                style="width: {{ $enrollment->progress_percent }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 w-8">{{ $enrollment->progress_percent }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        {{ $enrollment->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $enrollment->status === 'active'    ? 'bg-blue-100 text-blue-700'  : '' }}
                                        {{ $enrollment->status === 'dropped'   ? 'bg-red-100 text-red-700'    : '' }}">
                                        {{ match ($enrollment->status) {
                                            'active'    => 'Aktif',
                                            'completed' => 'Selesai',
                                            'dropped'   => 'Keluar',
                                            default     => $enrollment->status,
                                        } }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                    {{ $enrollment->enrolled_at?->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($bestScore !== null)
                                        <span class="font-semibold {{ $hasPassed ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $bestScore }}% {{ $hasPassed ? '✓' : '✗' }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">Belum ujian</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                    Belum ada siswa yang terdaftar
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($data['enrollments']->hasPages())
                <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Menampilkan {{ $data['enrollments']->firstItem() }}–{{ $data['enrollments']->lastItem() }}
                        dari {{ $data['enrollments']->total() }} siswa
                    </p>
                    <div class="flex gap-2">
                        <button wire:click="$set('enrollmentPage', {{ $data['enrollments']->currentPage() - 1 }})"
                            @disabled($data['enrollments']->onFirstPage())
                            class="px-3 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-600
                                disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-700">
                            ← Prev
                        </button>
                        @foreach (range(1, $data['enrollments']->lastPage()) as $page)
                            <button wire:click="$set('enrollmentPage', {{ $page }})"
                                class="px-3 py-1.5 text-xs rounded-lg border
                                    {{ $page === $data['enrollments']->currentPage()
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        <button wire:click="$set('enrollmentPage', {{ $data['enrollments']->currentPage() + 1 }})"
                            @disabled(!$data['enrollments']->hasMorePages())
                            class="px-3 py-1.5 text-xs rounded-lg border border-gray-300 dark:border-gray-600
                                disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-700">
                            Next →
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Tabel Hasil Ujian per exam --}}
        @foreach ($data['course']->exams as $exam)
            @php
                $totalSessions = \App\Models\ExamSession::where('exam_id', $exam->id)
                    ->where('status', 'graded')->count();
                $limit = in_array($exam->id, $this->expandedExams) ? null : 5;
                $examSessions = \App\Models\ExamSession::with('user')
                    ->where('exam_id', $exam->id)
                    ->where('status', 'graded')
                    ->orderBy('user_id')->orderBy('attempt_number')
                    ->when($limit, fn ($q) => $q->limit($limit))
                    ->get();
            @endphp

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mb-4 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $exam->title }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Nilai lulus: {{ $exam->pass_score }}% · {{ $exam->duration_minutes }} menit
                        </p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $totalSessions }} peserta</span>
                </div>

                @if ($examSessions->isEmpty())
                    <div class="px-5 py-6 text-center text-gray-400 text-sm">
                        Belum ada yang mengerjakan ujian ini
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Percobaan</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Nilai</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Waktu Submit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($examSessions as $session)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50
                                        {{ $session->is_passed ? 'bg-green-50/30 dark:bg-green-900/5' : '' }}">
                                        <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                            {{ $session->user->name }}
                                        </td>
                                        <td class="px-4 py-2 text-center text-gray-500">ke-{{ $session->attempt_number }}</td>
                                        <td class="px-4 py-2 text-center font-bold
                                            {{ $session->is_passed ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $session->score }}%
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                                {{ $session->is_passed ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $session->is_passed ? 'Lulus' : 'Tidak Lulus' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center text-gray-500 text-xs">
                                            {{ $session->submitted_at?->format('d M Y H:i') ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($totalSessions > 5 && !in_array($exam->id, $this->expandedExams))
                        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700 text-center">
                            <button wire:click="toggleExam({{ $exam->id }})"
                                class="text-sm text-blue-600 hover:underline">
                                Tampilkan semua {{ $totalSessions }} hasil →
                            </button>
                        </div>
                    @elseif (in_array($exam->id, $this->expandedExams))
                        <div class="px-5 py-3 border-t border-gray-200 dark:border-gray-700 text-center">
                            <button wire:click="toggleExam({{ $exam->id }})"
                                class="text-sm text-gray-400 hover:underline">
                                Sembunyikan ↑
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        @endforeach

    @endif

</x-filament-panels::page>