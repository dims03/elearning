@push('styles')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet" />

    <style>
        .admin-login-body {
            --admin-accent: #24211d;
            --admin-accent-soft: #3a342d;
            --admin-accent-contrast: #f8f2e7;
            --admin-shell-shadow: 0 26px 80px rgba(36, 33, 29, 0.12);
            --admin-page-bg:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.7), transparent 22%),
                linear-gradient(180deg, #f8f2e7 0%, #f5ecd9 100%);
            --admin-card-bg: rgba(255, 250, 242, 0.92);
            --admin-card-text: #24211d;
            --admin-card-muted: #6f685d;
            --admin-card-line: rgba(36, 33, 29, 0.14);
            --admin-input-text: #24211d;
            --admin-input-placeholder: rgba(36, 33, 29, 0.42);
            --admin-panel-text: #24211d;
            --admin-panel-muted: #6f685d;
            --admin-panel-bg:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.28), transparent 24%),
                radial-gradient(circle at right center, rgba(255, 255, 255, 0.18), transparent 22%),
                linear-gradient(180deg, #fbf5ea 0%, #f3e7d2 100%);
            --admin-panel-border: rgba(36, 33, 29, 0.08);
            --admin-panel-orb: rgba(36, 33, 29, 0.04);
            --admin-illustration-filter: none;
            --admin-illustration-blend: multiply;
            --admin-illustration-opacity: 0.96;
            --admin-back-btn-bg: rgba(255, 255, 255, 0.42);
            --admin-back-btn-line: rgba(36, 33, 29, 0.12);
            background: var(--admin-page-bg);
            color: var(--admin-card-text);
            font-family: "Outfit", sans-serif;
            height: 100dvh;
            overflow: hidden;
            transition: background 180ms ease, color 180ms ease;
        }

        html.dark .admin-login-body {
            --admin-accent: #f6ead6;
            --admin-accent-soft: #e8d7bc;
            --admin-accent-contrast: #1d1b18;
            --admin-shell-shadow: 0 26px 80px rgba(7, 9, 18, 0.34);
            --admin-page-bg:
                radial-gradient(circle at top left, rgba(255, 244, 225, 0.05), transparent 22%),
                linear-gradient(180deg, #171513 0%, #12110f 100%);
            --admin-card-bg: #1d1f24;
            --admin-card-text: #f4f0fb;
            --admin-card-muted: rgba(244, 240, 251, 0.6);
            --admin-card-line: rgba(255, 255, 255, 0.16);
            --admin-input-text: #ffffff;
            --admin-input-placeholder: rgba(244, 240, 251, 0.34);
            --admin-panel-text: #f6ead6;
            --admin-panel-muted: rgba(246, 234, 214, 0.72);
            --admin-panel-bg:
                radial-gradient(circle at top left, rgba(246, 234, 214, 0.08), transparent 24%),
                radial-gradient(circle at right center, rgba(246, 234, 214, 0.05), transparent 22%),
                linear-gradient(180deg, #221f1a 0%, #171411 100%);
            --admin-panel-border: rgba(246, 234, 214, 0.08);
            --admin-panel-orb: rgba(246, 234, 214, 0.05);
            --admin-illustration-filter: invert(1) grayscale(1) contrast(0.9);
            --admin-illustration-blend: screen;
            --admin-illustration-opacity: 0.84;
            --admin-back-btn-bg: rgba(255, 255, 255, 0.08);
            --admin-back-btn-line: rgba(255, 255, 255, 0.08);
        }

        .admin-login-body .fi-simple-layout {
            height: 100dvh;
            min-height: 100dvh;
        }

        .admin-login-body .fi-simple-main-ctn {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 16px;
        }

        .admin-login-body .fi-simple-main {
            width: min(1320px, calc(100% - 24px)) !important;
            max-width: min(1320px, calc(100% - 24px)) !important;
            height: 100%;
            display: flex;
            align-items: center;
            background: transparent !important;
            box-shadow: none !important;
            border: 0 !important;
        }

        .admin-login-body .fi-simple-page,
        .admin-login-body .fi-simple-page-content {
            background: transparent !important;
            box-shadow: none !important;
            border: 0 !important;
        }

        .admin-login-shell {
            display: grid;
            grid-template-columns: minmax(500px, 0.96fr) minmax(580px, 1.04fr);
            width: 100%;
            height: min(720px, calc(100dvh - 28px));
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--admin-shell-shadow);
        }

        .admin-login-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100%;
            padding: 42px 56px;
            background: var(--admin-card-bg);
        }

        .admin-login-topbar {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            width: min(100%, 460px);
            margin-bottom: 24px;
        }

        .admin-login-theme-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            height: 42px;
            border: 1px solid var(--admin-card-line);
            border-radius: 999px;
            background: transparent;
            color: var(--admin-card-text);
            cursor: pointer;
            transition: background-color 160ms ease, border-color 160ms ease, transform 160ms ease;
        }

        .admin-login-theme-toggle:hover {
            transform: translateY(-1px);
            background: rgba(36, 33, 29, 0.06);
            border-color: rgba(36, 33, 29, 0.18);
        }

        html.dark .admin-login-theme-toggle:hover {
            background: rgba(246, 234, 214, 0.08);
            border-color: rgba(246, 234, 214, 0.18);
        }

        .admin-login-theme-toggle svg {
            width: 18px;
            height: 18px;
        }

        .admin-login-card-copy {
            max-width: 460px;
        }

        .admin-login-card-copy h2 {
            margin: 0 0 10px;
            font-size: clamp(2.2rem, 4vw, 2.9rem);
            line-height: 1.05;
            font-weight: 600;
            color: var(--admin-card-text);
        }

        .admin-login-form {
            width: min(100%, 460px);
        }

        .admin-login-card .fi-page-simple-header {
            display: none;
        }

        .admin-login-card .fi-simple-page {
            width: 100%;
        }

        .admin-login-card .fi-simple-page-content {
            gap: 0;
        }

        .admin-login-card .fi-fo-component-ctn {
            gap: 0.65rem;
        }

        .admin-login-card .fi-fo-field {
            margin-bottom: 0;
        }

        .admin-login-card .fi-fo-field-label {
            color: var(--admin-card-muted);
            font-size: 0.8rem;
            font-weight: 400;
        }

        .admin-login-card .fi-fo-field-label-required-mark {
            color: var(--admin-card-muted);
        }

        .admin-login-card .fi-input-wrp {
            padding-inline: 0;
            border: 0;
            border-bottom: 1px solid var(--admin-card-line);
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            transition: border-color 160ms ease;
        }

        .admin-login-card .fi-input-wrp:focus-within {
            border-color: rgba(36, 33, 29, 0.42);
            transform: none;
            box-shadow: none;
        }

        html.dark .admin-login-card .fi-input-wrp:focus-within {
            border-color: rgba(246, 234, 214, 0.42);
        }

        .admin-login-card .fi-input {
            min-height: 44px;
            padding-inline: 0;
            color: var(--admin-input-text);
            font-size: 0.95rem;
            background: transparent;
        }

        .admin-login-card .fi-input::placeholder {
            color: var(--admin-input-placeholder);
        }

        .admin-login-card .fi-input-wrp-actions .fi-btn {
            color: var(--admin-card-muted);
            background: transparent;
        }

        .admin-login-card .fi-checkbox-input {
            border-color: var(--admin-card-line);
            background: transparent;
        }

        .admin-login-card .fi-checkbox-input:checked {
            border-color: var(--admin-accent);
            background: var(--admin-accent);
        }

        .admin-login-card .fi-fo-checkbox {
            margin-top: 4px;
        }

        .admin-login-card .fi-fo-checkbox label,
        .admin-login-card .fi-fo-checkbox .fi-fo-field-label {
            color: var(--admin-card-muted);
            font-size: 0.88rem;
        }

        .admin-login-card .fi-ac {
            margin-top: 16px;
        }

        .admin-login-card .fi-ac .fi-btn.fi-color-custom,
        .admin-login-card .fi-ac .fi-btn.fi-color-primary,
        .admin-login-card .fi-ac .fi-btn {
            min-height: 46px;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(180deg, var(--admin-accent-soft), var(--admin-accent));
            color: var(--admin-accent-contrast);
            font-weight: 500;
            box-shadow: 0 12px 24px rgba(36, 33, 29, 0.18);
            transition: transform 160ms ease, box-shadow 160ms ease, opacity 160ms ease;
        }

        .admin-login-card .fi-ac .fi-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(36, 33, 29, 0.24);
        }

        .admin-login-card .fi-ac .fi-btn:focus-visible {
            outline: 2px solid rgba(36, 33, 29, 0.24);
            outline-offset: 2px;
        }

        html.dark .admin-login-card .fi-ac .fi-btn:focus-visible {
            outline: 2px solid rgba(246, 234, 214, 0.28);
        }

        .admin-login-intro {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100%;
            padding: 36px 42px;
            background: var(--admin-panel-bg);
            border-left: 1px solid var(--admin-panel-border);
        }

        .admin-login-intro::before,
        .admin-login-intro::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: var(--admin-panel-orb);
        }

        .admin-login-intro::before {
            top: -40px;
            left: -60px;
            width: 260px;
            height: 260px;
        }

        .admin-login-intro::after {
            right: -70px;
            bottom: 120px;
            width: 280px;
            height: 280px;
        }

        .admin-login-intro-copy,
        .admin-login-illustration {
            position: relative;
            z-index: 1;
        }

        .admin-login-intro-copy {
            max-width: 560px;
        }

        .admin-login-intro-copy h1 {
            margin: 0 0 10px;
            font-size: clamp(2.6rem, 4.4vw, 4.3rem);
            line-height: 0.96;
            font-weight: 700;
            color: var(--admin-panel-text);
        }

        .admin-login-intro-copy span {
            display: block;
            font-weight: 400;
        }

        .admin-login-intro-copy p {
            margin: 0;
            color: var(--admin-panel-muted);
            font-size: 0.9rem;
        }

        .admin-login-illustration {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            min-height: 360px;
            background: transparent;
        }

        .admin-login-illustration img {
            width: min(100%, 860px);
            max-height: 390px;
            object-fit: contain;
            display: block;
            mix-blend-mode: var(--admin-illustration-blend);
            filter: var(--admin-illustration-filter);
            opacity: var(--admin-illustration-opacity);
        }

        @media (max-width: 980px) {
            .admin-login-body {
                height: auto;
                overflow: auto;
            }

            .admin-login-body .fi-simple-layout {
                height: auto;
                min-height: 100dvh;
            }

            .admin-login-body .fi-simple-main {
                height: auto;
            }

            .admin-login-shell {
                grid-template-columns: 1fr;
                height: auto;
            }

            .admin-login-card,
            .admin-login-intro {
                min-height: auto;
            }

            .admin-login-card {
                order: 2;
                padding: 34px 24px;
            }

            .admin-login-intro {
                order: 1;
                padding: 30px 24px;
            }

            .admin-login-illustration {
                min-height: 220px;
                margin-top: 18px;
            }
        }

        @media (max-width: 640px) {
            .admin-login-body .fi-simple-main-ctn {
                padding: 16px 12px;
            }

            .admin-login-shell {
                min-height: auto;
                border-radius: 22px;
            }

            .admin-login-card {
                padding: 32px 20px;
            }

            .admin-login-intro {
                padding: 28px 20px;
            }

            .admin-login-topbar {
                margin-bottom: 24px;
            }

            .admin-login-intro-copy h1 {
                font-size: 2.4rem;
            }
        }
    </style>
@endpush

<div>
    <div class="admin-login-shell">
        <section class="admin-login-card" aria-label="Teacher login form">
            <div class="admin-login-form">
                <div class="admin-login-topbar">
                    <button type="button" class="admin-login-theme-toggle" data-theme-toggle aria-label="Toggle color theme">
                        <svg data-theme-icon="sun" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3V5.5M12 18.5V21M5.64 5.64L7.4 7.4M16.6 16.6L18.36 18.36M3 12H5.5M18.5 12H21M5.64 18.36L7.4 16.6M16.6 7.4L18.36 5.64M15.5 12A3.5 3.5 0 1 1 8.5 12A3.5 3.5 0 0 1 15.5 12Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <div class="admin-login-card-copy">
                    <h2>Login</h2>
                </div>

                {{ $this->content }}
            </div>
        </section>

        <section class="admin-login-intro" aria-label="Portal introduction">
            <div class="admin-login-intro-copy">
                <h1>Welcome to <span>Teacher portal</span></h1>
                <p>Login to access your account</p>
            </div>

            <div class="admin-login-illustration" aria-hidden="true">
                <img src="{{ asset('storage/asset/teacher.png') }}" alt="">
            </div>
        </section>
    </div>

    <x-filament-actions::modals />
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.querySelector('[data-theme-toggle]');

            if (! toggle) {
                return;
            }

            const syncTheme = () => {
                const isDark = document.documentElement.classList.contains('dark');
                toggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
                toggle.setAttribute('title', isDark ? 'Light mode' : 'Dark mode');
            };

            toggle.addEventListener('click', function () {
                const isDark = document.documentElement.classList.contains('dark');

                if (isDark) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }

                syncTheme();
            });

            syncTheme();
        });
    </script>
@endpush
