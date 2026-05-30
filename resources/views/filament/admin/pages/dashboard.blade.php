@once
    @push('styles')
        <style>
            .admin-dashboard-hero {
                position: relative;
                overflow: hidden;
                border: 1px solid var(--admin-border);
                border-radius: 1.75rem;
                background:
                    radial-gradient(circle at top right, rgba(255, 255, 255, 0.7), transparent 24%),
                    linear-gradient(135deg, var(--admin-surface) 0%, var(--admin-surface-muted) 100%);
                box-shadow: var(--admin-card-shadow);
            }

            .admin-dashboard-hero::before,
            .admin-dashboard-hero::after {
                content: "";
                position: absolute;
                border-radius: 999px;
                pointer-events: none;
                opacity: 0.7;
            }

            .admin-dashboard-hero::before {
                top: -3rem;
                right: -2rem;
                width: 13rem;
                height: 13rem;
                background: rgba(255, 255, 255, 0.42);
            }

            .admin-dashboard-hero::after {
                bottom: -4.5rem;
                left: 28%;
                width: 16rem;
                height: 16rem;
                background: rgba(143, 131, 117, 0.08);
                pointer-events: none;
            }

            .admin-dashboard-hero-copy {
                max-width: 44rem;
            }

            .admin-dashboard-hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                width: fit-content;
                padding: 0.48rem 0.85rem;
                border: 1px solid var(--admin-border);
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.5);
                color: var(--admin-text);
                font-size: 0.78rem;
                font-weight: 600;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            .admin-dashboard-hero-badge::before {
                content: "";
                width: 0.55rem;
                height: 0.55rem;
                border-radius: 999px;
                background: #d3aa59;
                box-shadow: 0 0 0 0.25rem rgba(211, 170, 89, 0.12);
            }

            .admin-dashboard-hero-date {
                color: var(--admin-muted);
            }

            .admin-dashboard-hero-title {
                color: var(--admin-text);
            }

            .admin-dashboard-hero-subtitle {
                display: block;
                color: var(--admin-muted);
            }

            .admin-dashboard-figure-wrap {
                display: flex;
                align-items: flex-end;
                justify-content: center;
                height: 100%;
                min-height: 240px;
                padding: 1.5rem;
                border: 1px solid var(--admin-border);
                border-radius: 1.5rem;
                background:
                    radial-gradient(circle at top left, rgba(255, 255, 255, 0.5), transparent 26%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.36), rgba(255, 255, 255, 0.08));
            }

            .admin-dashboard-stat,
            .admin-dashboard-link,
            .admin-dashboard-feed {
                border: 1px solid var(--admin-border);
                border-radius: 1.4rem;
                background: var(--admin-surface);
                box-shadow: var(--admin-card-shadow);
            }

            .admin-dashboard-stat {
                min-height: 150px;
            }

            .admin-dashboard-link {
                transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
            }

            .admin-dashboard-link:hover {
                transform: translateY(-2px);
                border-color: rgba(143, 135, 241, 0.28);
                box-shadow: 0 18px 36px rgba(67, 57, 142, 0.12);
            }

            .admin-dashboard-link-arrow {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 999px;
                background: var(--admin-accent-soft);
                color: var(--admin-accent);
            }

            .admin-dashboard-figure {
                max-height: 290px;
                object-fit: contain;
                filter: drop-shadow(0 26px 36px rgba(12, 14, 30, 0.2));
            }

            .admin-dashboard-feed-item + .admin-dashboard-feed-item {
                border-top: 1px solid var(--admin-border);
            }

            html.dark .admin-dashboard-hero {
                background:
                    radial-gradient(circle at top right, rgba(255, 255, 255, 0.06), transparent 24%),
                    linear-gradient(135deg, var(--admin-surface) 0%, var(--admin-surface-muted) 100%);
                box-shadow: var(--admin-card-shadow);
            }

            html.dark .admin-dashboard-hero::before {
                background: rgba(255, 255, 255, 0.05);
            }

            html.dark .admin-dashboard-hero::after {
                background: rgba(255, 255, 255, 0.04);
            }

            html.dark .admin-dashboard-hero-badge {
                background: rgba(255, 255, 255, 0.04);
            }

            html.dark .admin-dashboard-figure-wrap {
                background:
                    radial-gradient(circle at top left, rgba(255, 255, 255, 0.06), transparent 26%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.01));
            }

            @media (max-width: 1024px) {
                .admin-dashboard-figure-wrap {
                    min-height: 220px;
                }
            }
        </style>
    @endpush
@endonce

<x-filament-panels::page>
    <div class="space-y-6">
        <section class="admin-dashboard-hero">
            <div class="relative z-10 grid gap-8 px-6 py-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(260px,0.85fr)] lg:px-8 lg:py-8">
                <div class="flex flex-col justify-between gap-6">
                    <div class="admin-dashboard-hero-copy space-y-5">
                        <div class="admin-dashboard-hero-badge">
                            <span>Admin Dashboard</span>
                        </div>

                        <div class="space-y-3">
                            <p class="admin-dashboard-hero-date text-sm font-medium">{{ $todayLabel }}</p>
                            <h1 class="admin-dashboard-hero-title max-w-2xl text-3xl font-semibold leading-tight sm:text-4xl">
                                {{ $greeting }}, {{ $adminName }}.
                                <span class="admin-dashboard-hero-subtitle">Kelola kelas, pengguna, dan aktivitas pembelajaran dari satu dashboard.</span>
                            </h1>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-end justify-center">
                    <div class="admin-dashboard-figure-wrap w-full">
                        <img
                            src="{{ asset('storage/asset/welcome.png') }}"
                            alt="Welcome illustration"
                            class="admin-dashboard-figure w-full max-w-sm"
                        >
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($stats as $stat)
                <article class="admin-dashboard-stat p-5">
                    <p class="text-sm font-medium text-[var(--admin-muted)]">{{ $stat['label'] }}</p>
                    <p class="mt-4 text-3xl font-semibold tracking-tight text-[var(--admin-text)]">{{ $stat['value'] }}</p>
                    <p class="mt-3 max-w-xs text-sm leading-6 text-[var(--admin-muted)]">{{ $stat['description'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-[var(--admin-text)]">Quick Actions</h2>
                        <p class="text-sm text-[var(--admin-muted)]">Shortcut ke area administrasi yang paling sering dipakai.</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($quickLinks as $link)
                        <a href="{{ $link['url'] }}" class="admin-dashboard-link flex h-full items-start justify-between gap-4 p-5">
                            <div>
                                <h3 class="text-base font-semibold text-[var(--admin-text)]">{{ $link['label'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-[var(--admin-muted)]">{{ $link['description'] }}</p>
                            </div>
                            <span class="admin-dashboard-link-arrow inline-flex items-center justify-center shrink-0">
                                <x-heroicon-o-arrow-up-right class="h-4 w-4" />
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <aside class="admin-dashboard-feed overflow-hidden">
                <div class="border-b border-[var(--admin-border)] px-5 py-4">
                    <h2 class="text-lg font-semibold text-[var(--admin-text)]">Recent Enrollments</h2>
                    <p class="text-sm text-[var(--admin-muted)]">Aktivitas pendaftaran kursus terbaru di platform.</p>
                </div>

                @forelse ($recentEnrollments as $enrollment)
                    <div class="admin-dashboard-feed-item flex items-start justify-between gap-4 px-5 py-4">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-[var(--admin-text)]">
                                {{ $enrollment->user?->name ?? 'Siswa' }}
                            </p>
                            <p class="mt-1 text-sm text-[var(--admin-muted)]">
                                Mendaftar ke <span class="font-medium text-[var(--admin-text)]">{{ $enrollment->course?->title ?? 'Kursus' }}</span>
                            </p>
                            <p class="mt-2 text-xs uppercase tracking-[0.18em] text-[var(--admin-muted)]">
                                {{ strtoupper($enrollment->status ?? 'active') }}
                            </p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-sm font-semibold text-[var(--admin-text)]">{{ $enrollment->progress_percent ?? 0 }}%</p>
                            <p class="mt-1 text-xs text-[var(--admin-muted)]">
                                {{ optional($enrollment->enrolled_at)->format('d M Y') ?? 'Baru saja' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <p class="text-sm text-[var(--admin-muted)]">Belum ada enrollment terbaru untuk ditampilkan.</p>
                    </div>
                @endforelse
            </aside>
        </section>
    </div>
</x-filament-panels::page>
