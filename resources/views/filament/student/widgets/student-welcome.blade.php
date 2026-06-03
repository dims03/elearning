<x-filament-widgets::widget>
    <section
        class="relative overflow-hidden rounded-[1.75rem] border border-[var(--admin-border)] bg-[linear-gradient(135deg,#fffaf3_0%,#f6ecdd_52%,#efe4d7_100%)] shadow-[var(--admin-card-shadow)] dark:bg-[linear-gradient(135deg,#1d2438_0%,#1b2031_52%,#151a28_100%)]"
    >
        <div class="relative z-10 grid items-center gap-4 p-5 sm:p-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(220px,0.65fr)] lg:gap-6 lg:p-6">
            <div class="max-w-3xl">
                <p class="text-sm font-medium text-[var(--admin-muted)]">{{ $todayLabel }}</p>

                <h2 class="mt-2 text-[clamp(1.75rem,3vw,2.45rem)] font-bold leading-[1.12] text-[var(--admin-text)]">
                    {{ $greeting }}, {{ $studentName }}.
                </h2>

                <p class="mt-3 max-w-2xl text-base leading-7 text-[var(--admin-muted)]">
                    Lanjutkan belajar, cek progres kursus, dan pantau hasil ujian Anda dari satu dashboard.
                </p>

                <div class="mt-4 flex flex-wrap gap-3">
                    <a
                        href="{{ $coursesUrl }}"
                        wire:navigate.hover
                        class="inline-flex min-h-[2.9rem] items-center justify-center gap-2 rounded-full bg-[var(--admin-accent)] px-4.5 py-3 text-sm font-semibold text-white shadow-[var(--admin-primary-shadow)] transition duration-150 hover:-translate-y-0.5"
                    >
                        <x-heroicon-o-book-open class="h-5 w-5" />
                        <span>My Course</span>
                    </a>

                    <a
                        href="{{ $examUrl }}"
                        wire:navigate.hover
                        class="inline-flex min-h-[2.9rem] items-center justify-center gap-2 rounded-full border border-[rgba(143,135,241,0.18)] bg-white/60 px-4.5 py-3 text-sm font-semibold text-[var(--admin-text)] transition duration-150 hover:-translate-y-0.5 dark:bg-white/5"
                    >
                        <x-heroicon-o-clipboard-document-list class="h-5 w-5" />
                        <span>My Exam</span>
                    </a>
                </div>
            </div>

            <div class="flex min-h-[150px] items-center justify-center p-2 lg:min-h-[170px]">
                <img
                    src="{{ asset('storage/asset/student-welcome.png') }}"
                    alt="Student welcome illustration"
                    class="w-full max-w-[220px] object-contain drop-shadow-[0_18px_26px_rgba(47,43,39,0.16)] lg:max-w-[250px]"
                >
            </div>
        </div>
    </section>
</x-filament-widgets::widget>
