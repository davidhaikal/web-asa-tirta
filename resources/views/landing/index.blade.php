<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ASA Tirta — Sistem Manajemen Air Minum</title>
    <meta name="description" content="Sistem manajemen ASA Tirta: pencatatan produksi, QC, gudang, penjualan &amp; invoice, keuangan, dan pengiriman air minum dalam satu platform dengan akses sesuai 5 peran.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script>document.documentElement.classList.add('js');</script>
    <style>
        :root {
            --bg: #f7f6f2;
            --card: #ffffff;
            --ink: #1d2433;
            --muted: #6b7280;
            --line: #e5e7eb;
            --accent: #127369;
            --accent-dark: #0e5b53;
            --accent-soft: #e8f3f1;
            --navy: #0f1f2b;
            --navy-2: #18344a;
            --navy-muted: #9fb3c8;
            --danger: #b42318;
            --radius: 16px;
            --shadow-sm: 0 1px 2px rgba(16, 24, 40, 0.05);
            --shadow: 0 10px 30px rgba(16, 24, 40, 0.08);
            --shadow-lg: 0 24px 60px rgba(17, 24, 39, 0.16);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 84px;
        }

        body {
            font-family: "Space Grotesk", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg);
            color: var(--ink);
            font-size: 16px;
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
        }

        ::selection { background: var(--accent); color: #fff; }

        img, svg { display: block; max-width: 100%; }

        a { color: var(--accent); }

        .container {
            width: min(1120px, 100% - 48px);
            margin-inline: auto;
        }

        /* ---------- Reveal on scroll (hanya bila JS aktif) ---------- */
        .js .reveal {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.6s ease, transform 0.6s ease;
            transition-delay: var(--d, 0s);
        }
        .js .reveal.in { opacity: 1; transform: translateY(0); }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .js .reveal { opacity: 1; transform: none; transition: none; }
            .float-chip { animation: none !important; }
            * { transition-duration: 0.01ms !important; animation-duration: 0.01ms !important; }
        }

        /* ---------- Tombol ---------- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: 10px;
            border: 1px solid transparent;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
        .btn:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        .btn i { font-size: 14px; }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover {
            background: var(--accent-dark);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(18, 115, 105, 0.25);
        }
        .btn-ghost { background: transparent; color: var(--ink); border-color: var(--line); }
        .btn-ghost:hover { border-color: var(--accent); color: var(--accent); background: rgba(232, 243, 241, 0.6); }
        .btn-light { background: #fff; color: var(--navy); }
        .btn-light:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25); }
        .btn-sm { padding: 9px 16px; font-size: 14px; }

        /* ---------- Navbar ---------- */
        .nav {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 50;
            background: rgba(247, 246, 242, 0.82);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid transparent;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .nav.scrolled {
            border-bottom-color: var(--line);
            box-shadow: 0 4px 20px rgba(16, 24, 40, 0.06);
        }
        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            height: 68px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--ink);
        }
        .brand-mark {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: var(--accent);
            color: #fff;
            font-size: 18px;
        }
        .brand-text {
            display: grid;
            line-height: 1.15;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .brand-text small {
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }
        .nav-links > a:not(.btn) {
            font-size: 14.5px;
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.2s ease, color 0.2s ease;
        }
        .nav-links > a:not(.btn):hover { opacity: 1; color: var(--accent); }
        .nav-toggle {
            display: none;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--ink);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        /* ---------- Hero ---------- */
        .hero {
            position: relative;
            padding: 150px 0 96px;
            overflow: hidden;
            background:
                radial-gradient(1000px circle at 12% -10%, #e3efec 0%, rgba(247, 246, 242, 0) 55%),
                var(--bg);
        }
        .hero-droplet {
            position: absolute;
            top: 96px;
            right: -70px;
            width: 420px;
            color: var(--accent);
            opacity: 0.07;
            pointer-events: none;
        }
        .hero-grid {
            position: relative;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 56px;
            align-items: center;
        }
        .eyebrow {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent);
            background: var(--accent-soft);
            padding: 6px 12px;
            border-radius: 999px;
            margin-bottom: 18px;
        }
        .hero h1 {
            font-size: clamp(34px, 4.6vw, 46px);
            line-height: 1.1;
            letter-spacing: -0.02em;
            font-weight: 700;
            margin-bottom: 18px;
        }
        .hero h1 em {
            font-style: normal;
            color: var(--accent);
        }
        .hero-sub {
            font-size: 17px;
            color: var(--muted);
            max-width: 520px;
            margin-bottom: 28px;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 28px;
        }
        .hero-points {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 22px;
            list-style: none;
            font-size: 14px;
            font-weight: 500;
            color: var(--muted);
        }
        .hero-points i { color: var(--accent); margin-right: 6px; }

        /* ---------- Mock dashboard ---------- */
        .hero-visual { position: relative; }
        .mock {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        .mock-bar {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 12px 16px;
            background: #fbfbf9;
            border-bottom: 1px solid var(--line);
        }
        .mock-bar .dot { width: 10px; height: 10px; border-radius: 50%; background: #d9d9d4; }
        .mock-title { margin-left: 8px; font-size: 12.5px; font-weight: 600; color: var(--muted); }
        .mock-body { padding: 18px; display: grid; gap: 14px; }
        .mock-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .mock-tile {
            background: #fbfbf9;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
        }
        .mock-tile span { display: block; font-size: 11.5px; font-weight: 600; color: var(--muted); }
        .mock-tile strong { display: block; font-size: 20px; font-weight: 700; letter-spacing: -0.01em; margin-top: 2px; }
        .mock-tile small { font-size: 11px; color: var(--muted); }
        .mock-cols { display: grid; grid-template-columns: 1.2fr 1fr; gap: 10px; }
        .mock-card {
            background: #fbfbf9;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
        }
        .mock-label { font-size: 11.5px; font-weight: 600; color: var(--muted); display: block; margin-bottom: 10px; }
        .mock-chart {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            height: 96px;
            padding-top: 4px;
        }
        .mock-chart .bar {
            flex: 1;
            border-radius: 6px 6px 2px 2px;
            background: linear-gradient(180deg, var(--accent) 0%, #3aa094 100%);
            opacity: 0.9;
        }
        .mock-chart .bar:nth-child(odd) { opacity: 0.65; }
        .mock-days {
            display: flex;
            gap: 8px;
            margin-top: 6px;
        }
        .mock-days span { flex: 1; text-align: center; font-size: 10.5px; color: var(--muted); }
        .mock-list { list-style: none; display: grid; gap: 9px; }
        .mock-list li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: var(--ink);
        }
        .mock-list li::before {
            content: "";
            flex: none;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--accent);
        }
        .mock-list time { margin-left: auto; font-size: 11px; color: var(--muted); }
        .float-chip {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--card);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 600;
            animation: floaty 6s ease-in-out infinite;
        }
        .float-chip i { color: var(--accent); font-size: 16px; }
        .float-chip small { display: block; font-size: 11px; font-weight: 500; color: var(--muted); line-height: 1.2; }
        .chip-qc { top: -18px; left: -22px; }
        .chip-stok { bottom: -18px; right: -14px; animation-delay: -3s; }
        @keyframes floaty {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* ---------- Section umum ---------- */
        .section { padding: 96px 0; }
        .section-head {
            max-width: 640px;
            margin: 0 auto 56px;
            text-align: center;
        }
        .section-head h2 {
            font-size: clamp(26px, 3.4vw, 32px);
            line-height: 1.2;
            letter-spacing: -0.015em;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .section-head p { color: var(--muted); font-size: 16px; }

        /* ---------- About / Akses ---------- */
        .about-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 56px;
            align-items: center;
        }
        .about-copy h2 {
            font-size: clamp(26px, 3.4vw, 32px);
            line-height: 1.2;
            letter-spacing: -0.015em;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .about-copy p { color: var(--muted); margin-bottom: 14px; font-size: 16px; }
        .about-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            padding: 28px;
        }
        .about-card .eyebrow { margin-bottom: 16px; }
        .role-chips {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }
        .role-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--line);
            background: #fbfbf9;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            font-weight: 600;
        }
        .role-chip i {
            display: grid;
            place-items: center;
            flex: none;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 15px;
        }
        .about-note {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed var(--line);
            font-size: 13px;
            color: var(--muted);
        }

        /* ---------- Modul ---------- */
        .modul { background: #fff; border-block: 1px solid var(--line); }
        .modul-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }
        .modul-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .modul-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
            border-color: #c9e2de;
        }
        .modul-icon {
            display: grid;
            place-items: center;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 20px;
        }
        .modul-card h3 { font-size: 17px; font-weight: 700; margin: 16px 0 10px; letter-spacing: -0.01em; }
        .modul-card ul { list-style: none; display: grid; gap: 7px; }
        .modul-card li {
            position: relative;
            padding-left: 22px;
            font-size: 13.5px;
            color: var(--muted);
            line-height: 1.5;
        }
        .modul-card li::before {
            content: "\F26E"; /* bi-check2 */
            font-family: "bootstrap-icons";
            position: absolute;
            left: 0;
            top: 1px;
            color: var(--accent);
            font-size: 13px;
        }

        /* ---------- Alur kerja ---------- */
        .alur-steps {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 18px;
            counter-reset: langkah;
        }
        .alur-step { position: relative; }
        .alur-num {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: var(--accent);
            margin-bottom: 10px;
        }
        .alur-icon {
            display: grid;
            place-items: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--accent-soft);
            border: 1px solid #cfe4e0;
            color: var(--accent);
            font-size: 20px;
            margin-bottom: 14px;
        }
        .alur-step h3 { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
        .alur-step p { font-size: 13.5px; color: var(--muted); line-height: 1.55; }
        .alur-step:not(:last-child)::after {
            content: "";
            position: absolute;
            top: 24px;
            right: -13px;
            width: 8px;
            height: 8px;
            border-top: 2px solid #b9c2cf;
            border-right: 2px solid #b9c2cf;
            transform: rotate(45deg);
        }

        /* ---------- Statistik ---------- */
        .stats {
            background: linear-gradient(145deg, var(--navy) 0%, var(--navy-2) 55%, var(--navy) 100%);
            padding: 64px 0;
            color: #fff;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            text-align: center;
        }
        .stat { display: grid; gap: 4px; }
        .stat + .stat { border-left: 1px solid rgba(255, 255, 255, 0.12); }
        .stat strong {
            font-size: clamp(32px, 4vw, 42px);
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1;
        }
        .stat span { font-size: 14px; color: var(--navy-muted); }

        /* ---------- CTA ---------- */
        .cta-panel {
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, var(--navy) 0%, var(--navy-2) 55%, var(--navy) 100%);
            border-radius: 24px;
            padding: 64px 48px;
            text-align: center;
            color: #f8fafc;
            box-shadow: var(--shadow-lg);
        }
        .cta-panel::before {
            content: "";
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(145deg, rgba(255,255,255,0.25), rgba(255,255,255,0.05) 40%, rgba(255,255,255,0.18));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
        .cta-panel h2 {
            font-size: clamp(26px, 3.6vw, 34px);
            font-weight: 700;
            letter-spacing: -0.015em;
            margin-bottom: 12px;
        }
        .cta-panel p { color: #c3d2df; max-width: 480px; margin: 0 auto 28px; }
        .cta-note { margin-top: 18px; font-size: 13px !important; color: var(--navy-muted) !important; }

        /* ---------- Footer ---------- */
        .footer {
            background: #0c1923;
            color: #b9c8d6;
            padding: 56px 0 24px;
            margin-top: 96px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr;
            gap: 40px;
            padding-bottom: 32px;
        }
        .footer .brand-text { color: #fff; }
        .footer .brand-text small { color: var(--navy-muted); }
        .footer-desc { font-size: 14px; color: #8fa5b8; margin-top: 14px; max-width: 320px; }
        .footer h4 {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #7d93a8;
            margin-bottom: 14px;
        }
        .footer ul { list-style: none; display: grid; gap: 9px; }
        .footer ul a {
            color: #b9c8d6;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s ease;
        }
        .footer ul a:hover { color: #fff; }
        .footer-bottom {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 20px;
            font-size: 13px;
            color: #7d93a8;
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 1024px) {
            .hero { padding: 130px 0 72px; }
            .hero-grid { gap: 40px; }
            .modul-grid { grid-template-columns: repeat(2, 1fr); }
            .alur-steps { grid-template-columns: repeat(2, 1fr); gap: 28px; }
            .alur-step:nth-child(2)::after { display: none; }
            .alur-step::after { display: none; }
        }

        @media (max-width: 900px) {
            .hero-grid { grid-template-columns: 1fr; }
            .hero-copy { text-align: center; }
            .hero-sub { margin-inline: auto; }
            .hero-actions, .hero-points { justify-content: center; }
            .hero-visual { max-width: 560px; margin-inline: auto; width: 100%; }
            .chip-qc { left: -8px; }
            .chip-stok { right: -6px; }
            .about-grid { grid-template-columns: 1fr; gap: 36px; }
            .stats-grid { grid-template-columns: 1fr; gap: 32px; }
            .stat + .stat { border-left: 0; padding-top: 32px; border-top: 1px solid rgba(255, 255, 255, 0.12); }
            .footer-grid { grid-template-columns: 1fr; gap: 28px; }
        }

        @media (max-width: 700px) {
            .section { padding: 64px 0; }
            .section-head { margin-bottom: 40px; }
            .hero { padding: 116px 0 56px; }
            .hero-droplet { display: none; }
            .nav-links {
                position: fixed;
                top: 68px;
                left: 0;
                right: 0;
                flex-direction: column;
                align-items: stretch;
                gap: 4px;
                background: var(--card);
                border-bottom: 1px solid var(--line);
                box-shadow: var(--shadow);
                padding: 16px 24px 20px;
                display: none;
            }
            .nav.open .nav-links { display: flex; }
            .nav-links > a:not(.btn) { padding: 10px 4px; font-size: 15px; }
            .nav-links .btn { margin-top: 10px; }
            .nav-toggle { display: inline-grid; place-items: center; }
            .mock-stats { grid-template-columns: repeat(2, 1fr); }
            .mock-cols { grid-template-columns: 1fr; }
            .modul-grid { grid-template-columns: 1fr; }
            .alur-steps { grid-template-columns: 1fr; gap: 24px; }
            .cta-panel { padding: 48px 24px; }
            .footer { margin-top: 64px; }
        }
    </style>
</head>
<body>

    {{-- ======================= NAVBAR ======================= --}}
    <header class="nav" id="top">
        <div class="container nav-inner">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-mark"><i class="bi bi-droplet-fill"></i></span>
                <span class="brand-text">ASA Tirta<small>Sistem Manajemen</small></span>
            </a>
            <nav class="nav-links" id="navLinks" aria-label="Navigasi utama">
                <a href="#modul">Modul</a>
                <a href="#alur">Alur Kerja</a>
                <a href="#akses">Akses</a>
                <a class="btn btn-primary btn-sm" href="{{ route('login') }}">Masuk <i class="bi bi-arrow-right"></i></a>
            </nav>
            <button class="nav-toggle" id="navToggle" aria-label="Buka menu" aria-expanded="false" aria-controls="navLinks">
                <i class="bi bi-list" id="navToggleIcon"></i>
            </button>
        </div>
    </header>

    <main>
        {{-- ======================= HERO ======================= --}}
        <section class="hero">
            <svg class="hero-droplet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true">
                <path d="M12 2.7c3.4 4.1 7 8.2 7 12.3a7 7 0 1 1-14 0c0-4.1 3.6-8.2 7-12.3Z"/>
            </svg>
            <div class="container hero-grid">
                <div class="hero-copy reveal">
                    <span class="eyebrow">Sistem Manajemen Air Minum</span>
                    <h1>Kelola produksi air minum, <em>dari hulu ke hilir</em>, dalam satu sistem.</h1>
                    <p class="hero-sub">
                        Satu platform untuk seluruh alur operasional ASA Tirta — dari pencatatan
                        produksi &amp; QC, gudang, penjualan, keuangan, hingga pengiriman — dengan
                        akses yang menyesuaikan peran masing-masing tim.
                    </p>
                    <div class="hero-actions">
                        <a class="btn btn-primary" href="{{ route('login') }}">Masuk ke Sistem <i class="bi bi-box-arrow-in-right"></i></a>
                        <a class="btn btn-ghost" href="#modul">Lihat Modul</a>
                    </div>
                    <ul class="hero-points">
                        <li><i class="bi bi-check2-circle"></i>Akses sesuai 5 peran</li>
                        <li><i class="bi bi-check2-circle"></i>Ekspor laporan PDF &amp; Excel</li>
                        <li><i class="bi bi-check2-circle"></i>Pantau stok &amp; pengiriman</li>
                    </ul>
                </div>

                <div class="hero-visual reveal" style="--d:.15s">
                    <div class="mock" role="img" aria-label="Pratinjau dashboard ASA Tirta">
                        <div class="mock-bar">
                            <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                            <span class="mock-title">Dashboard — Ringkasan Harian</span>
                        </div>
                        <div class="mock-body">
                            <div class="mock-stats">
                                <div class="mock-tile"><span>Transaksi Hari Ini</span><strong>12</strong><small>dibayar kasir</small></div>
                                <div class="mock-tile"><span>Pengiriman</span><strong>26</strong><small>dalam proses</small></div>
                                <div class="mock-tile"><span>Invoice</span><strong>18</strong><small>bulan ini</small></div>
                                <div class="mock-tile"><span>QC</span><strong>32</strong><small>pemeriksaan</small></div>
                            </div>
                            <div class="mock-cols">
                                <div class="mock-card">
                                    <span class="mock-label">Grafik Penjualan 7 Hari</span>
                                    <div class="mock-chart" aria-hidden="true">
                                        <div class="bar" style="height:42%"></div>
                                        <div class="bar" style="height:58%"></div>
                                        <div class="bar" style="height:36%"></div>
                                        <div class="bar" style="height:72%"></div>
                                        <div class="bar" style="height:60%"></div>
                                        <div class="bar" style="height:88%"></div>
                                        <div class="bar" style="height:50%"></div>
                                    </div>
                                    <div class="mock-days" aria-hidden="true">
                                        <span>S</span><span>S</span><span>R</span><span>K</span><span>J</span><span>S</span><span>M</span>
                                    </div>
                                </div>
                                <div class="mock-card">
                                    <span class="mock-label">Aktivitas Terbaru</span>
                                    <ul class="mock-list">
                                        <li>TRX-1042 dibayar — invoice dibuat <time>09:12</time></li>
                                        <li>QC lolos — produksi 118 unit <time>10:05</time></li>
                                        <li>Pengiriman #218 dimulai <time>13:40</time></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="float-chip chip-qc">
                        <i class="bi bi-clipboard-check"></i>
                        <span>QC Lolos<small>Pemeriksaan hari ini</small></span>
                    </div>
                    <div class="float-chip chip-stok">
                        <i class="bi bi-box-seam"></i>
                        <span>Stok Aman<small>Gudang terpantau</small></span>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================= TENTANG / AKSES ======================= --}}
        <section class="section" id="akses">
            <div class="container about-grid">
                <div class="about-copy reveal">
                    <span class="eyebrow">Tentang Sistem</span>
                    <h2>Satu sistem untuk seluruh operasional ASA Tirta</h2>
                    <p>
                        Setiap tim bekerja di modulnya sendiri dengan data yang saling terhubung:
                        QC mencatat produksi &amp; memverifikasi kualitas, gudang memantau stok,
                        kasir melayani penjualan &amp; invoice, keuangan merapikan piutang, dan
                        driver mengirim barang — semua dalam satu sistem.
                    </p>
                    <p>
                        Peran (role) menentukan menu dan izin akses, sehingga setiap orang hanya
                        melihat bagian yang menjadi tanggung jawabnya.
                    </p>
                </div>
                <div class="about-card reveal" style="--d:.12s">
                    <span class="eyebrow">Akses Berdasarkan Peran</span>
                    <div class="role-chips">
                        <div class="role-chip"><i class="bi bi-box-seam"></i>Admin Gudang</div>
                        <div class="role-chip"><i class="bi bi-cash-coin"></i>Kasir</div>
                        <div class="role-chip"><i class="bi bi-wallet2"></i>Admin Keuangan</div>
                        <div class="role-chip"><i class="bi bi-clipboard-check"></i>Quality Control</div>
                        <div class="role-chip"><i class="bi bi-truck"></i>Driver</div>
                    </div>
                    <p class="about-note">Butuh akun? Hubungi administrator sistem untuk akses awal.</p>
                </div>
            </div>
        </section>

        {{-- ======================= MODUL ======================= --}}
        <section class="section modul" id="modul">
            <div class="container">
                <div class="section-head reveal">
                    <span class="eyebrow">Modul</span>
                    <h2>Semua fungsi operasional dalam satu tempat</h2>
                    <p>Dari pencatatan produksi hingga laporan keuangan — setiap modul terhubung dengan yang lain.</p>
                </div>
                <div class="modul-grid">
                    <article class="modul-card reveal">
                        <span class="modul-icon"><i class="bi bi-clipboard-check"></i></span>
                        <h3>Quality Control</h3>
                        <ul>
                            <li>Pencatatan produksi harian</li>
                            <li>Pemeriksaan produk</li>
                            <li>Status layak / reject</li>
                            <li>Laporan &amp; ekspor QC</li>
                        </ul>
                    </article>
                    <article class="modul-card reveal" style="--d:.05s">
                        <span class="modul-icon"><i class="bi bi-box-seam"></i></span>
                        <h3>Gudang &amp; Stok</h3>
                        <ul>
                            <li>Barang masuk &amp; keluar</li>
                            <li>Permintaan stok</li>
                            <li>Catatan barang rusak</li>
                            <li>Riwayat stok</li>
                        </ul>
                    </article>
                    <article class="modul-card reveal" style="--d:.1s">
                        <span class="modul-icon"><i class="bi bi-cash-coin"></i></span>
                        <h3>Kasir &amp; Penjualan</h3>
                        <ul>
                            <li>Transaksi &amp; pembayaran</li>
                            <li>Invoice otomatis &amp; cetak nota</li>
                            <li>Laporan penjualan, stok &amp; SPJ</li>
                        </ul>
                    </article>
                    <article class="modul-card reveal" style="--d:.15s">
                        <span class="modul-icon"><i class="bi bi-cart3"></i></span>
                        <h3>Pembelian &amp; PO</h3>
                        <ul>
                            <li>Purchase order</li>
                            <li>Pembelian barang &amp; detail</li>
                            <li>Tersambung ke modul keuangan</li>
                        </ul>
                    </article>
                    <article class="modul-card reveal">
                        <span class="modul-icon"><i class="bi bi-wallet2"></i></span>
                        <h3>Keuangan</h3>
                        <ul>
                            <li>Data pelanggan</li>
                            <li>Pembukuan piutang</li>
                            <li>Penagihan &amp; laporan keuangan</li>
                        </ul>
                    </article>
                    <article class="modul-card reveal" style="--d:.05s">
                        <span class="modul-icon"><i class="bi bi-truck"></i></span>
                        <h3>Pengiriman &amp; Driver</h3>
                        <ul>
                            <li>Terima invoice &rarr; kirim &rarr; selesai</li>
                            <li>Upload bukti foto</li>
                            <li>Status pengiriman terpantau</li>
                        </ul>
                    </article>
                    <article class="modul-card reveal" style="--d:.1s">
                        <span class="modul-icon"><i class="bi bi-bar-chart"></i></span>
                        <h3>Laporan</h3>
                        <ul>
                            <li>Laporan per modul (gudang, penjualan, stok, keuangan)</li>
                            <li>Filter berdasarkan periode</li>
                            <li>Ekspor PDF &amp; Excel</li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        {{-- ======================= ALUR KERJA ======================= --}}
        <section class="section" id="alur">
            <div class="container">
                <div class="section-head reveal">
                    <span class="eyebrow">Alur Kerja</span>
                    <h2>Dari produksi sampai ke pelanggan</h2>
                    <p>Data mengalir antar modul — setiap langkah tercatat dan bisa dilacak.</p>
                </div>
                <ol class="alur-steps">
                    <li class="alur-step reveal">
                        <span class="alur-num">01</span>
                        <span class="alur-icon"><i class="bi bi-gear-wide-connected"></i></span>
                        <h3>Pencatatan Produksi</h3>
                        <p>QC mencatat pelaksanaan produksi harian beserta kuantitasnya.</p>
                    </li>
                    <li class="alur-step reveal" style="--d:.08s">
                        <span class="alur-num">02</span>
                        <span class="alur-icon"><i class="bi bi-clipboard-check"></i></span>
                        <h3>Quality Control</h3>
                        <p>Produk diperiksa; hasil layak atau reject tercatat.</p>
                    </li>
                    <li class="alur-step reveal" style="--d:.16s">
                        <span class="alur-num">03</span>
                        <span class="alur-icon"><i class="bi bi-box-seam"></i></span>
                        <h3>Stok &amp; Gudang</h3>
                        <p>Hasil QC masuk ke stok; barang masuk dan keluar terpantau.</p>
                    </li>
                    <li class="alur-step reveal" style="--d:.24s">
                        <span class="alur-num">04</span>
                        <span class="alur-icon"><i class="bi bi-cash-coin"></i></span>
                        <h3>Penjualan</h3>
                        <p>Kasir memproses transaksi; sistem membuat invoice &amp; nota otomatis.</p>
                    </li>
                    <li class="alur-step reveal" style="--d:.32s">
                        <span class="alur-num">05</span>
                        <span class="alur-icon"><i class="bi bi-truck"></i></span>
                        <h3>Pengiriman</h3>
                        <p>Driver menerima invoice, mengirim, dan mengunggah bukti.</p>
                    </li>
                </ol>
            </div>
        </section>

        {{-- ======================= STATISTIK ======================= --}}
        <section class="stats">
            <div class="container stats-grid">
                <div class="stat reveal"><strong>5</strong><span>Peran akses</span></div>
                <div class="stat reveal" style="--d:.08s"><strong>7</strong><span>Modul terintegrasi</span></div>
                <div class="stat reveal" style="--d:.16s"><strong>2</strong><span>Format ekspor — PDF &amp; Excel</span></div>
            </div>
        </section>

        {{-- ======================= CTA ======================= --}}
        <section class="section">
            <div class="container">
                <div class="cta-panel reveal">
                    <h2>Siap mulai kelola operasional ASA Tirta?</h2>
                    <p>Masuk ke dashboard Anda — menu dan akses menyesuaikan peran secara otomatis.</p>
                    <a class="btn btn-light" href="{{ route('login') }}">Masuk ke Dashboard <i class="bi bi-arrow-right"></i></a>
                    <p class="cta-note">Butuh akun? Hubungi administrator sistem untuk akses awal.</p>
                </div>
            </div>
        </section>
    </main>

    {{-- ======================= FOOTER ======================= --}}
    <footer class="footer">
        <div class="container footer-grid">
            <div>
                <a class="brand" href="{{ url('/') }}">
                    <span class="brand-mark"><i class="bi bi-droplet-fill"></i></span>
                    <span class="brand-text">ASA Tirta<small>Sistem Manajemen</small></span>
                </a>
                <p class="footer-desc">
                    Sistem manajemen produksi, penjualan, dan distribusi air minum
                    ASA Tirta — dari hulu ke hilir dalam satu platform.
                </p>
            </div>
            <div>
                <h4>Menu</h4>
                <ul>
                    <li><a href="#modul">Modul</a></li>
                    <li><a href="#alur">Alur Kerja</a></li>
                    <li><a href="#akses">Akses</a></li>
                </ul>
            </div>
            <div>
                <h4>Akses Sistem</h4>
                <ul>
                    <li><a href="{{ route('login') }}">Halaman Login</a></li>
                </ul>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>&copy; {{ date('Y') }} ASA Tirta. Seluruh hak cipta dilindungi.</p>
            <p>Gudang &middot; Kasir &middot; Keuangan &middot; QC &middot; Driver</p>
        </div>
    </footer>

    <script>
        (function () {
            var nav = document.getElementById('top');
            var toggle = document.getElementById('navToggle');
            var toggleIcon = document.getElementById('navToggleIcon');
            var links = document.getElementById('navLinks');

            window.addEventListener('scroll', function () {
                nav.classList.toggle('scrolled', window.scrollY > 8);
            }, { passive: true });

            toggle.addEventListener('click', function () {
                var open = nav.classList.toggle('open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
                toggleIcon.className = open ? 'bi bi-x' : 'bi bi-list';
            });

            links.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () {
                    nav.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                    toggleIcon.className = 'bi bi-list';
                });
            });

            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('in');
                            io.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12 });
                document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
            } else {
                document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('in'); });
            }
        })();
    </script>
</body>
</html>
