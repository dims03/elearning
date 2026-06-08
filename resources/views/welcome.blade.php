<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Eduix') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('storage/asset/favicon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700|cormorant-garamond:500,600,700" rel="stylesheet" />

        <style>
            :root {
                --bg: #f7f0e2;
                --surface: #fbf5ea;
                --surface-strong: #fffaf2;
                --ink: #24211d;
                --muted: #6f685d;
                --line: rgba(36, 33, 29, 0.14);
                --line-strong: rgba(36, 33, 29, 0.24);
                --accent: #24211d;
                --shadow: 0 22px 60px rgba(36, 33, 29, 0.06);
            }

            * {
                box-sizing: border-box;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                margin: 0;
                min-height: 100vh;
                background:
                    radial-gradient(circle at top right, rgba(255, 255, 255, 0.7), transparent 22%),
                    linear-gradient(180deg, #f8f2e7 0%, #f5ecd9 100%);
                color: var(--ink);
                font-family: "Outfit", sans-serif;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .page {
                width: min(1180px, calc(100% - 32px));
                margin: 0 auto;
                padding: 18px 0 56px;
            }

            .site-header {
                display: grid;
                grid-template-columns: 1fr auto 1fr;
                align-items: center;
                gap: 18px;
                padding: 10px 0 20px;
                border-bottom: 1px solid var(--line-strong);
            }

            .brand {
                font-size: clamp(1.85rem, 3vw, 2.4rem);
                font-weight: 500;
                letter-spacing: -0.05em;
            }

            .nav {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 18px;
                color: var(--muted);
                font-size: 1rem;
            }

            .nav a {
                position: relative;
                padding: 0 12px;
            }

            .nav a + a::before {
                content: "";
                position: absolute;
                left: -9px;
                top: 50%;
                width: 1px;
                height: 18px;
                background: var(--line);
                transform: translateY(-50%);
            }

            .header-cta {
                justify-self: end;
                display: inline-flex;
                align-items: center;
                gap: 0;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 54px;
                padding: 0 24px;
                border: 1px solid var(--line-strong);
                background: transparent;
                color: var(--accent);
                font-size: 0.98rem;
                font-weight: 500;
                transition: transform 160ms ease, background-color 160ms ease, box-shadow 160ms ease;
            }

            .header-cta .button + .button {
                border-left: 0;
            }

            .button:hover {
                transform: translateY(-2px);
                background: rgba(255, 255, 255, 0.5);
                box-shadow: var(--shadow);
            }

            .button-dark {
                background: var(--ink);
                color: #f8f2e7;
                border-color: var(--ink);
            }

            .hero {
                display: grid;
                grid-template-columns: minmax(0, 1.05fr) minmax(360px, 0.95fr);
                gap: 28px;
                align-items: center;
                padding: 48px 0 36px;
            }

            .hero-copy {
                padding-right: 18px;
            }

            .hero-copy h1 {
                margin: 0;
                font-family: "Cormorant Garamond", serif;
                font-size: clamp(4rem, 9vw, 6.7rem);
                line-height: 0.9;
                letter-spacing: -0.05em;
                font-weight: 600;
            }

            .hero-copy p {
                margin: 24px 0 0;
                color: var(--muted);
                font-size: clamp(1.15rem, 2vw, 1.7rem);
                line-height: 1.55;
            }

            .hero-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 14px;
                margin-top: 34px;
            }

            .hero-note {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin-top: 22px;
                color: var(--muted);
                font-size: 0.96rem;
            }

            .hero-note::before {
                content: "";
                width: 42px;
                height: 1px;
                background: var(--line-strong);
            }

            .hero-visual {
                position: relative;
                display: flex;
                align-items: center;
                min-height: 480px;
                margin-top: 0;
            }

            .hero-orb {
                position: absolute;
                inset: 26px 36px auto auto;
                width: 180px;
                height: 180px;
                border-radius: 999px;
                background: radial-gradient(circle, rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0));
                filter: blur(10px);
            }

            .hero-illustration {
                position: relative;
                width: 100%;
                height: 410px;
                border: 1px solid rgba(36, 33, 29, 0.08);
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.04));
                overflow: hidden;
            }

            .hero-illustration img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                object-position: center 52%;
                display: block;
                mix-blend-mode: multiply;
                transform: scale(1.01);
            }

            .section {
                padding-top: 18px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                margin-top: 18px;
                border-top: 1px solid var(--line-strong);
                border-left: 1px solid var(--line-strong);
                background: rgba(255, 252, 245, 0.58);
            }

            .stat-card {
                padding: 34px 36px 30px;
                border-right: 1px solid var(--line-strong);
                border-bottom: 1px solid var(--line-strong);
            }

            .stat-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 46px;
                height: 46px;
                border: 1px solid var(--line-strong);
                margin-bottom: 28px;
                background: rgba(255, 255, 255, 0.46);
            }

            .stat-value {
                display: block;
                font-family: "Cormorant Garamond", serif;
                font-size: clamp(2.8rem, 4vw, 4rem);
                line-height: 0.95;
                letter-spacing: -0.05em;
            }

            .stat-label {
                display: block;
                margin-top: 10px;
                font-size: 1.55rem;
                line-height: 1.2;
                letter-spacing: -0.03em;
            }

            .stat-copy {
                margin-top: 16px;
                color: var(--muted);
                font-size: 0.98rem;
                line-height: 1.75;
            }

            .section-heading {
                display: flex;
                align-items: end;
                justify-content: space-between;
                gap: 18px;
                margin-top: 56px;
                margin-bottom: 18px;
            }

            .section-heading h2 {
                margin: 0;
                font-family: "Cormorant Garamond", serif;
                font-size: clamp(2.4rem, 5vw, 3.8rem);
                line-height: 0.96;
                font-weight: 600;
                letter-spacing: -0.05em;
            }

            .section-heading p {
                max-width: 480px;
                margin: 0;
                color: var(--muted);
                line-height: 1.75;
                text-align: right;
            }

            .course-grid,
            .portal-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }

            .course-card,
            .portal-card {
                border: 1px solid var(--line-strong);
                background: rgba(255, 252, 245, 0.62);
                box-shadow: var(--shadow);
            }

            .course-card {
                padding: 28px;
            }

            .course-card span,
            .portal-card span {
                color: var(--muted);
                font-size: 0.95rem;
                letter-spacing: 0.02em;
                text-transform: uppercase;
            }

            .course-card h3,
            .portal-card h3 {
                margin: 14px 0 12px;
                font-family: "Cormorant Garamond", serif;
                font-size: 2rem;
                line-height: 1;
                font-weight: 600;
                letter-spacing: -0.04em;
            }

            .course-card p,
            .portal-card p {
                margin: 0;
                color: var(--muted);
                line-height: 1.75;
            }

            .course-meta {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                margin-top: 22px;
                padding-top: 18px;
                border-top: 1px solid var(--line);
                color: var(--muted);
                font-size: 0.92rem;
            }

            .portal-card {
                padding: 30px;
            }

            .portal-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                margin-top: 22px;
            }

            .portal-meta {
                margin-top: 20px;
                padding-top: 16px;
                border-top: 1px solid var(--line);
                color: var(--muted);
                font-size: 0.95rem;
                line-height: 1.7;
            }

            .about-box {
                display: grid;
                grid-template-columns: 1.15fr 0.85fr;
                gap: 18px;
                margin-top: 18px;
            }

            .about-panel,
            .about-highlight {
                padding: 30px;
                border: 1px solid var(--line-strong);
                background: rgba(255, 252, 245, 0.62);
                box-shadow: var(--shadow);
            }

            .about-panel p,
            .about-highlight p {
                margin: 0;
                color: var(--muted);
                line-height: 1.85;
            }

            .about-points {
                display: grid;
                gap: 16px;
                margin-top: 22px;
            }

            .about-points div {
                padding-top: 16px;
                border-top: 1px solid var(--line);
            }

            .about-points strong {
                display: block;
                margin-bottom: 6px;
                font-size: 1rem;
                font-weight: 600;
            }

            .shortcut-toast {
                position: fixed;
                left: 50%;
                bottom: 24px;
                transform: translateX(-50%) translateY(140%);
                min-width: min(360px, calc(100% - 32px));
                padding: 15px 18px;
                border: 1px solid rgba(36, 33, 29, 0.2);
                background: rgba(36, 33, 29, 0.92);
                color: #f8f2e7;
                text-align: center;
                transition: transform 180ms ease;
                z-index: 30;
            }

            .shortcut-toast.is-visible {
                transform: translateX(-50%) translateY(0);
            }

            @media (max-width: 980px) {
                .site-header {
                    grid-template-columns: 1fr;
                    justify-items: start;
                    gap: 14px;
                }

                .header-cta {
                    justify-self: start;
                    flex-wrap: wrap;
                }

                .hero,
                .about-box {
                    grid-template-columns: 1fr;
                }

                .hero-copy {
                    padding-right: 0;
                }

                .hero-visual {
                    min-height: 440px;
                }

                .stats-grid,
                .course-grid,
                .portal-grid {
                    grid-template-columns: 1fr;
                }

                .section-heading {
                    align-items: start;
                    flex-direction: column;
                }

                .section-heading p {
                    text-align: left;
                }
            }

            @media (max-width: 680px) {
                .page {
                    width: min(100% - 24px, 1180px);
                    padding-top: 8px;
                }

                .site-header {
                    padding: 8px 0 18px;
                }

                .brand {
                    max-width: 9ch;
                    font-size: clamp(1.8rem, 10vw, 2.4rem);
                    line-height: 0.95;
                }

                .nav {
                    flex-wrap: wrap;
                    gap: 10px 4px;
                }

                .nav a + a::before {
                    display: none;
                }

                .hero {
                    padding-top: 32px;
                    gap: 20px;
                }

                .hero-copy h1 {
                    font-size: clamp(3.2rem, 17vw, 5rem);
                    line-height: 0.92;
                }

                .hero-copy p {
                    margin-top: 18px;
                    font-size: 1.05rem;
                    line-height: 1.6;
                }

                .hero-visual {
                    min-height: 280px;
                }

                .hero-orb {
                    inset: 12px 16px auto auto;
                    width: 120px;
                    height: 120px;
                    filter: blur(8px);
                }

                .hero-illustration {
                    height: 260px;
                }

                .hero-illustration img {
                    object-position: center center;
                }

                .hero-actions,
                .portal-actions {
                    flex-direction: column;
                }

                .header-cta {
                    width: 100%;
                    flex-direction: column;
                    align-items: stretch;
                }

                .button {
                    width: 100%;
                    min-height: 52px;
                }

                .header-cta .button + .button {
                    border-left: 1px solid var(--line-strong);
                    margin-top: -1px;
                }

                .stat-card,
                .course-card,
                .portal-card,
                .about-panel,
                .about-highlight {
                    padding: 24px;
                }
            }

            @media (max-width: 480px) {
                .page {
                    width: min(100% - 16px, 1180px);
                }

                .hero {
                    padding-top: 24px;
                }

                .hero-copy h1 {
                    font-size: clamp(2.8rem, 15vw, 4rem);
                }

                .hero-copy p {
                    font-size: 1rem;
                }

                .hero-visual {
                    min-height: 220px;
                }

                .hero-illustration {
                    height: 220px;
                }

                .stat-card,
                .course-card,
                .portal-card,
                .about-panel,
                .about-highlight {
                    padding: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="page">
            <header class="site-header" id="home">
                <a href="#home" class="brand">E-Learning Platform</a>

                <nav class="nav" aria-label="Primary navigation">
                    {{-- <a href="#home">Home</a>
                    <a href="#courses">Course List</a>
                    <a href="#about">About us</a> --}}
                </nav>

                <div class="header-cta">
                    <a href="{{ route('filament.student.auth.login') }}" class="button">Login Students</a>
                    <a href="{{ route('filament.teacher.auth.login') }}" class="button">Login Teachers</a>
                </div>
            </header>

            <section class="hero">
                <div class="hero-copy">
                    <h1>Teach Anything<br>Learn Anytime</h1>
                    <p>30k+ students trust us to learn faster, teach better, and keep every class in one clean platform.</p>

                </div>

                <div class="hero-visual" aria-hidden="true">
                    <div class="hero-orb"></div>
                    <div class="hero-illustration">
                        <img src="{{ asset('storage/asset/image.webp') }}" alt="E-learning illustration">
                    </div>
                </div>
            </section>
        </div>

        <div class="shortcut-toast" id="shortcut-toast" aria-live="polite">Opening admin login...</div>

        <script>
            const adminShortcutUrl = @json(route('admin.shortcut'));
            const shortcutToast = document.getElementById('shortcut-toast');

            window.addEventListener('keydown', function (event) {
                const isShortcut = event.ctrlKey && event.shiftKey && event.key.toLowerCase() === 'l';

                if (! isShortcut) {
                    return;
                }

                event.preventDefault();
                shortcutToast.classList.add('is-visible');

                window.setTimeout(function () {
                    window.location.assign(adminShortcutUrl);
                }, 180);
            });
        </script>
    </body>
</html>
