<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Eduix') }}</title>

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

            .admin-notice {
                margin-top: 22px;
                max-width: 540px;
                padding: 14px 16px;
                border: 1px solid var(--line-strong);
                background: rgba(255, 255, 255, 0.46);
                color: var(--muted);
                line-height: 1.65;
            }

            .hero-visual {
                position: relative;
                min-height: 520px;
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
                height: 100%;
                border: 1px solid rgba(36, 33, 29, 0.08);
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.04));
                overflow: hidden;
            }

            .hero-illustration img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                display: block;
                mix-blend-mode: multiply;
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
                    width: min(100% - 20px, 1180px);
                    padding-top: 10px;
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
                }

                .hero-actions,
                .portal-actions {
                    flex-direction: column;
                }

                .button {
                    width: 100%;
                }

                .stat-card,
                .course-card,
                .portal-card,
                .about-panel,
                .about-highlight {
                    padding: 24px;
                }
            }
        </style>
    </head>
    <body>
        <div class="page">
            <header class="site-header" id="home">
                <a href="#home" class="brand">E-Learning Platform</a>

                <nav class="nav" aria-label="Primary navigation">
                    <a href="#home">Home</a>
                    <a href="#courses">Course List</a>
                    <a href="#about">About us</a>
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

                    @if (session('adminShortcutNotice'))
                        <div class="admin-notice">{{ session('adminShortcutNotice') }}</div>
                    @endif
                </div>

                <div class="hero-visual" aria-hidden="true">
                    <div class="hero-orb"></div>
                    <div class="hero-illustration">
                        <img src="{{ asset('storage/asset/image.webp') }}" alt="E-learning illustration">
                    </div>
                </div>
            </section>

            <section class="section">
                <div class="stats-grid">
                    <article class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 6H19V18H5V6Z" stroke="#24211D" stroke-width="1.6"/>
                                <path d="M8 10L11 13L16 8" stroke="#24211D" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="stat-value">1200+</span>
                        <span class="stat-label">Online Courses</span>
                        <p class="stat-copy">Structured learning paths for classes, self-paced lessons, and course completion tracking.</p>
                    </article>

                    <article class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 18C7 15.8 8.8 14 11 14H13C15.2 14 17 15.8 17 18" stroke="#24211D" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M12 11C13.7 11 15 9.7 15 8C15 6.3 13.7 5 12 5C10.3 5 9 6.3 9 8C9 9.7 10.3 11 12 11Z" stroke="#24211D" stroke-width="1.6"/>
                                <path d="M4 4H20V20H4V4Z" stroke="#24211D" stroke-width="1.6"/>
                            </svg>
                        </div>
                        <span class="stat-value">220</span>
                        <span class="stat-label">Certified Teacher</span>
                        <p class="stat-copy">A focused teaching panel to manage material, quizzes, student performance, and class delivery.</p>
                    </article>

                    <article class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 17H17" stroke="#24211D" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M9 14H15" stroke="#24211D" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M12 6L17 9V14C17 16.8 14.8 19 12 19C9.2 19 7 16.8 7 14V9L12 6Z" stroke="#24211D" stroke-width="1.6" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="stat-value">30K+</span>
                        <span class="stat-label">Students</span>
                        <p class="stat-copy">From enrollment to progress reviews, every student can keep learning activity in one place.</p>
                    </article>
                </div>
            </section>

            <section class="section" id="courses">
                <div class="section-heading">
                    <h2>Course list built for real learning flow.</h2>
                    <p>Use the landing page as a clean public front, then guide each role into the panel they actually need.</p>
                </div>

                <div class="course-grid">
                    <article class="course-card">
                        <span>Design Basics</span>
                        <h3>Visual Course Delivery</h3>
                        <p>Organize modules, lessons, and assignments so students can move from introduction to completion without confusion.</p>
                        <div class="course-meta">
                            <strong>12 lessons</strong>
                            <span>Beginner</span>
                        </div>
                    </article>

                    <article class="course-card">
                        <span>Teaching Tools</span>
                        <h3>Assessment and Quiz Setup</h3>
                        <p>Create objective tests, structured evaluations, and score tracking directly from the teacher workflow.</p>
                        <div class="course-meta">
                            <strong>8 modules</strong>
                            <span>Intermediate</span>
                        </div>
                    </article>

                    <article class="course-card">
                        <span>Student Growth</span>
                        <h3>Progress Monitoring</h3>
                        <p>Track enrollments, lesson completion, and participation data to keep each class measurable and actionable.</p>
                        <div class="course-meta">
                            <strong>Live reports</strong>
                            <span>All levels</span>
                        </div>
                    </article>
                </div>
            </section>

            <section class="section" id="portal-access">
                <div class="section-heading">
                    <h2>Portal access for each learning role.</h2>
                    <p>Student and teacher stay visible from the landing page. Admin remains hidden behind the keyboard shortcut.</p>
                </div>

                <div class="portal-grid">
                    <article class="portal-card">
                        <span>Student access</span>
                        <h3>Continue learning</h3>
                        <p>Masuk ke panel student untuk melihat course aktif, lesson progress, dan hasil evaluasi belajar.</p>
                        <div class="portal-meta">Akses login tersedia dari tombol header bagian kanan atas.</div>
                    </article>

                    <article class="portal-card">
                        <span>Teacher access</span>
                        <h3>Manage your class</h3>
                        <p>Masuk ke panel teacher untuk kelola materi, ujian, chapter, dan performa peserta didik.</p>
                        <div class="portal-meta">Teacher login juga dipindahkan ke header agar tampilan landing lebih rapi.</div>
                    </article>

                    <article class="portal-card">
                        <span>Internal access</span>
                        <h3>Admin via hotkey</h3>
                        {{-- <p>Shortcut admin tetap aktif. Dari landing page tekan <strong>Ctrl + Shift + L</strong> untuk membuka login admin.</p> --}}
                        <div class="portal-actions">
                            <a href="#home" class="button">Back to top</a>
                        </div>
                    </article>
                </div>
            </section>

            <section class="section" id="about">
                <div class="section-heading">
                    <h2>About this platform.</h2>
                    <p>A simple front page with a stronger visual identity, while the actual teaching and learning work still happens inside Filament panels.</p>
                </div>

                <div class="about-box">
                    <article class="about-panel">
                        <p>
                            Landing ini didesain seperti referensi yang kamu kirim: ringan, editorial, dominan warna krem,
                            dan terasa seperti halaman promosi platform pendidikan. Dengan begitu halaman depan project ini
                            tidak lagi terlihat default Laravel, tapi langsung terasa sebagai produk e-learning.
                        </p>
                    </article>

                    <article class="about-highlight">
                        <p>Yang tetap dipertahankan dari implementasi sebelumnya:</p>
                        <div class="about-points">
                            <div>
                                <strong>Student login</strong>
                                Arah masuk langsung ke panel siswa.
                            </div>
                            <div>
                                <strong>Teacher login</strong>
                                Arah masuk langsung ke panel pengajar.
                            </div>
                            <div>
                                <strong>Admin shortcut</strong>
                                Tetap tersembunyi dan dibuka lewat hotkey.
                            </div>
                        </div>
                    </article>
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
