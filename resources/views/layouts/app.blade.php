<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $title ?? 'Dashboard') | ASA Tirta</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --k-bg: #f7f6f2;
            --k-card: #ffffff;
            --k-ink: #1d2433;
            --k-muted: #6b7280;
            --k-line: #e5e7eb;
            --k-accent: #127369;
            --k-accent-dark: #0e5b53;
            --k-accent-soft: #e8f3f1;
            --k-navy: #0f1f2b;
            --k-green: #15803d;
            --k-green-soft: #e7f6ec;
            --k-amber: #b45309;
            --k-amber-soft: #fdf3e0;
            --k-red: #b42318;
            --k-red-soft: #fdeceb;
            --k-info: #0e7490;
            --k-info-soft: #e0f2f8;
            --k-radius: 14px;
            --k-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
            --k-shadow-lg: 0 12px 32px rgba(16, 24, 40, 0.10);

            /* Kompatibilitas variabel layout lama (view modules/*) */
            --muted: #6b7280;
            --ink: #1d2433;
            --card: #ffffff;
            --bg: #f7f6f2;
            --primary: #127369;

            /* Tema kasir (teal) diadopsi sebagai primary Bootstrap
               supaya view modul lama otomatis ikut tema */
            --bs-primary: #127369;
            --bs-primary-rgb: 18, 115, 105;
            --bs-link-color-rgb: 18, 115, 105;
            --bs-link-hover-color-rgb: 14, 91, 83;
        }

        * { box-sizing: border-box; }

        body {
            font-family: "Space Grotesk", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: var(--k-bg);
            color: var(--k-ink);
            font-size: 15px;
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
        }

        ::selection { background: var(--k-accent); color: #fff; }

        a { color: var(--k-accent); }

        .form-control:focus, .form-select:focus {
            border-color: var(--k-accent);
            box-shadow: 0 0 0 0.25rem rgba(18, 115, 105, 0.12);
        }

        /* Tombol primary Bootstrap -> teal (nilai hover/active dihitung di build SCSS,
           jadi harus di-override eksplisit via variabel --bs-btn-*) */
        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: var(--k-accent);
            --bs-btn-border-color: var(--k-accent);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: var(--k-accent-dark);
            --bs-btn-hover-border-color: var(--k-accent-dark);
            --bs-btn-focus-shadow-rgb: 18, 115, 105;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: var(--k-accent-dark);
            --bs-btn-active-border-color: var(--k-accent-dark);
            --bs-btn-disabled-color: #fff;
            --bs-btn-disabled-bg: var(--k-accent);
            --bs-btn-disabled-border-color: var(--k-accent);
        }
        .btn-outline-primary {
            --bs-btn-color: var(--k-accent);
            --bs-btn-border-color: var(--k-accent);
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: var(--k-accent);
            --bs-btn-hover-border-color: var(--k-accent);
            --bs-btn-focus-shadow-rgb: 18, 115, 105;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: var(--k-accent);
            --bs-btn-active-border-color: var(--k-accent);
        }

        /* Kartu Bootstrap disamakan dengan kartu tema */
        .card { border-color: var(--k-line); border-radius: 12px; box-shadow: var(--k-shadow); }
        .card-header { border-bottom-color: var(--k-line); }
        .modal-content { border: none; border-radius: 16px; }

        /* ---------- Shell: sidebar kiri + konten ---------- */
        .k-app {
            display: grid;
            grid-template-columns: 252px minmax(0, 1fr);
            min-height: 100vh;
        }
        .k-sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            background: var(--k-card);
            border-right: 1px solid var(--k-line);
            padding: 18px 14px 14px;
            z-index: 1060;
        }
        .k-side-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 2px 8px 16px;
            border-bottom: 1px solid var(--k-line);
            margin-bottom: 10px;
        }
        .k-brand-mark {
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--k-accent);
            color: #fff;
            font-size: 17px;
            flex: none;
        }
        .k-brand-text { display: grid; line-height: 1.15; font-size: 16.5px; font-weight: 700; letter-spacing: -0.01em; color: var(--k-ink); }
        .k-brand-text small {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--k-muted);
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .k-side-nav { display: flex; flex-direction: column; gap: 2px; flex: 1; }
        .k-side-title {
            margin: 12px 8px 4px;
            font-size: 11px;
            font-weight: 700;
            color: var(--k-muted);
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }
        .k-side-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--k-ink);
            opacity: 0.78;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.2s ease, color 0.2s ease, opacity 0.2s ease;
        }
        .k-side-nav a i { font-size: 15.5px; width: 20px; text-align: center; flex: none; }
        .k-side-nav a:hover { opacity: 1; background: #efeee9; }
        .k-side-nav a.active { opacity: 1; background: var(--k-accent-soft); color: var(--k-accent); }
        .k-side-foot {
            display: flex;
            align-items: center;
            gap: 10px;
            border-top: 1px solid var(--k-line);
            margin-top: 12px;
            padding-top: 12px;
        }
        .k-user {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--k-ink);
            background: var(--k-bg);
            border: 1px solid var(--k-line);
            border-radius: 999px;
            padding: 6px 14px 6px 10px;
            flex: 1;
            min-width: 0;
        }
        .k-user i { color: var(--k-accent); }
        .k-user span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .k-logout {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            flex: none;
            border-radius: 10px;
            border: 1px solid var(--k-line);
            background: var(--k-card);
            color: var(--k-muted);
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .k-logout:hover { color: var(--k-red); border-color: var(--k-red); background: var(--k-red-soft); }

        /* Kontrol mobile (off-canvas) */
        .k-menu-toggle { display: none; }
        .k-backdrop { display: none; }

        /* ---------- Konten ---------- */
        .k-main {
            width: min(1400px, 100% - 48px);
            margin: 0 auto;
            padding: 24px 0 48px;
        }
        .k-page-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .k-page-head h1 { font-size: 24px; font-weight: 700; letter-spacing: -0.015em; margin: 0; }
        .k-page-head p { color: var(--k-muted); font-size: 14px; margin: 4px 0 0; }

        /* ---------- Kartu ---------- */
        .k-card {
            background: var(--k-card);
            border: 1px solid var(--k-line);
            border-radius: var(--k-radius);
            box-shadow: var(--k-shadow);
            margin-bottom: 20px;
        }
        .k-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 15px 20px;
            border-bottom: 1px solid var(--k-line);
        }
        .k-card-head h2 { font-size: 16px; font-weight: 700; margin: 0; letter-spacing: -0.01em; }
        .k-card-head small { display: block; color: var(--k-muted); font-size: 12.5px; margin-top: 2px; }
        .k-card-body { padding: 20px; }

        /* ---------- Stat ---------- */
        .k-stat {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--k-card);
            border: 1px solid var(--k-line);
            border-radius: var(--k-radius);
            box-shadow: var(--k-shadow);
            padding: 16px 18px;
            height: 100%;
        }
        .k-stat-icon {
            display: grid;
            place-items: center;
            flex: none;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            font-size: 19px;
        }
        .k-stat-icon.i-accent { background: var(--k-accent-soft); color: var(--k-accent); }
        .k-stat-icon.i-green { background: var(--k-green-soft); color: var(--k-green); }
        .k-stat-icon.i-amber { background: var(--k-amber-soft); color: var(--k-amber); }
        .k-stat-icon.i-red { background: var(--k-red-soft); color: var(--k-red); }
        .k-stat-icon.i-info { background: var(--k-info-soft); color: var(--k-info); }
        .k-stat-label { font-size: 12.5px; font-weight: 600; color: var(--k-muted); }
        .k-stat-value { font-size: 23px; font-weight: 700; letter-spacing: -0.015em; line-height: 1.15; }
        .k-stat-value.v-accent { color: var(--k-accent); }
        .k-stat-value.v-green { color: var(--k-green); }
        .k-stat-value.v-amber { color: var(--k-amber); }
        .k-stat-value.v-red { color: var(--k-red); }
        .k-stat-value.v-info { color: var(--k-info); }
        .k-stat-sub { font-size: 11.5px; color: var(--k-muted); }

        /* ---------- Badge ---------- */
        .k-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.02em;
            white-space: nowrap;
        }
        .k-badge.b-lunas, .k-badge.b-masuk, .k-badge.b-aman { background: var(--k-green-soft); color: var(--k-green); }
        .k-badge.b-pending, .k-badge.b-menipis { background: var(--k-amber-soft); color: var(--k-amber); }
        .k-badge.b-batal, .k-badge.b-method { background: #f0f0ec; color: #57606e; }
        .k-badge.b-keluar, .k-badge.b-kritis { background: var(--k-red-soft); color: var(--k-red); }
        .k-badge.b-info { background: var(--k-info-soft); color: var(--k-info); }
        .k-badge.b-accent { background: var(--k-accent-soft); color: var(--k-accent); }

        /* ---------- Tombol ---------- */
        .k-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: 10px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }
        .k-btn:focus-visible { outline: 2px solid var(--k-accent); outline-offset: 2px; }
        .k-btn i { font-size: 14px; }
        .k-btn-primary { background: var(--k-accent); color: #fff; }
        .k-btn-primary:hover { background: var(--k-accent-dark); transform: translateY(-1px); box-shadow: 0 8px 16px rgba(18, 115, 105, 0.22); }
        .k-btn-success { background: var(--k-green); color: #fff; }
        .k-btn-success:hover { background: #116231; transform: translateY(-1px); box-shadow: 0 8px 16px rgba(21, 128, 61, 0.2); }
        .k-btn-danger-soft { background: transparent; color: var(--k-red); border-color: #f3c1bc; }
        .k-btn-danger-soft:hover { background: var(--k-red-soft); border-color: var(--k-red); }
        .k-btn-ghost { background: var(--k-card); color: var(--k-ink); border-color: var(--k-line); }
        .k-btn-ghost:hover { border-color: var(--k-accent); color: var(--k-accent); background: var(--k-accent-soft); }
        .k-btn-sm { padding: 6px 12px; font-size: 12.5px; border-radius: 8px; }
        .k-btn-block { width: 100%; }
        .k-btn-lg { padding: 13px 22px; font-size: 15px; }
        .k-confirming { background: var(--k-red) !important; color: #fff !important; border-color: var(--k-red) !important; }

        /* ---------- Tabel ---------- */
        .k-table { width: 100%; border-collapse: collapse; }
        .k-table thead th {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--k-muted);
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid var(--k-line);
            white-space: nowrap;
        }
        .k-table tbody td {
            padding: 11px 12px;
            font-size: 14px;
            border-bottom: 1px solid #f0f0ec;
            vertical-align: middle;
        }
        .k-table tbody tr:last-child td { border-bottom: none; }
        .k-table tbody tr:hover td { background: #fafaf7; }
        .k-table .t-right { text-align: right; }
        .k-table tfoot td {
            padding: 12px;
            font-weight: 700;
            background: #fbfbf9;
            border-top: 2px solid var(--k-line);
        }
        .tr-pending td { background: #fffaf0; }
        .tr-pending:hover td { background: #fdf4e2 !important; }

        .k-empty { text-align: center; color: var(--k-muted); padding: 32px 16px !important; font-size: 14px; }
        .k-empty i { display: block; font-size: 26px; margin-bottom: 8px; color: #c9c9c2; }

        /* ---------- Alert ---------- */
        .k-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .k-alert i.alert-ico { margin-top: 1px; }
        .k-alert .btn-close { margin-left: auto; padding: 8px; }
        .k-alert-success { background: var(--k-green-soft); border-color: #bfe3cb; color: #14532d; }
        .k-alert-success i.alert-ico { color: var(--k-green); }
        .k-alert-danger { background: var(--k-red-soft); border-color: #f3c1bc; color: #7f1d1d; }
        .k-alert-danger i.alert-ico { color: var(--k-red); }

        /* ---------- Misc ---------- */
        .k-sticky { position: sticky; top: 16px; }
        .k-qty { max-width: 92px; }
        .k-grand { font-size: 22px; font-weight: 700; color: var(--k-green); letter-spacing: -0.01em; }
        .k-item-remove {
            display: grid;
            place-items: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: var(--k-muted);
            cursor: pointer;
            font-size: 14px;
        }
        .k-item-remove:hover { background: var(--k-red-soft); color: var(--k-red); }
        .k-warn-row td { background: #fff7ed !important; }
        .k-search-wrap { position: relative; }
        .k-search-wrap i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--k-muted);
            font-size: 14px;
        }
        .k-search-wrap .form-control { padding-left: 36px; }

        /* ---------- Responsive (sidebar off-canvas di mobile) ---------- */
        @media (max-width: 991px) {
            .k-app { grid-template-columns: 1fr; }
            .k-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 284px;
                height: 100vh;
                transform: translateX(-103%);
                transition: transform 0.25s ease;
            }
            .k-sidebar.open { transform: translateX(0); box-shadow: var(--k-shadow-lg); }
            .k-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(15, 31, 43, 0.45);
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.25s ease;
                z-index: 1050;
            }
            .k-backdrop.show { opacity: 1; visibility: visible; }
            .k-menu-toggle {
                display: grid;
                place-items: center;
                position: fixed;
                top: 12px;
                left: 12px;
                z-index: 1040;
                width: 40px;
                height: 40px;
                border-radius: 10px;
                border: 1px solid var(--k-line);
                background: var(--k-card);
                color: var(--k-ink);
                font-size: 19px;
                cursor: pointer;
                box-shadow: var(--k-shadow);
            }
            .k-main { width: calc(100% - 28px); padding-top: 64px; }
        }
        @media (max-width: 575px) {
            .k-main { width: calc(100% - 28px); padding-top: 64px; }
            .k-page-head h1 { font-size: 20px; }
            .k-card-body { padding: 14px; }
            .k-stat-value { font-size: 20px; }
            .k-user span { display: none; }
            .k-user { padding: 6px 10px; flex: none; }
        }

        /* ---------- Print ---------- */
        @media print {
            .k-sidebar, .k-menu-toggle, .k-backdrop, .k-alert, .no-print, .print-hide { display: none !important; }
            body { background: #fff; }
            .k-app { display: block; }
            .k-main { width: 100%; padding: 0; }
            .k-card { border: none; box-shadow: none; margin-bottom: 0; }
            .k-table tbody tr:hover td { background: transparent; }
        }
    </style>
</head>
<body>
    @php
        $role = auth()->check() ? auth()->user()->role : 'guest';
        // 5 role sesuai use case skripsi: Gudang, Kasir, Keuangan, QC, Driver
        $roleLabel = [
            'gudang' => 'Gudang',
            'kasir' => 'Kasir',
            'keuangan' => 'Keuangan',
            'qc' => 'Quality Control',
            'driver' => 'Driver',
        ][$role] ?? 'ASA Tirta';
        $dashboardRoutes = [
            'gudang' => '/gudang/dashboard',
            'qc' => '/qc/dashboard',
            'keuangan' => '/keuangan/dashboard',
            'kasir' => '/kasir/dashboard',
            'driver' => '/driver/dashboard',
        ];
        $currentDashboard = $dashboardRoutes[$role] ?? url('login');
        $dashMatch = ltrim($currentDashboard, '/');
    @endphp

    <div class="k-app">
        <aside class="k-sidebar" id="kSidebar">
            <div class="k-side-brand">
                <span class="k-brand-mark"><i class="bi bi-droplet-fill"></i></span>
                <span class="k-brand-text">ASA Tirta<small>{{ $roleLabel }}</small></span>
            </div>

            <nav class="k-side-nav" aria-label="Menu utama">
                <a href="{{ $currentDashboard }}" class="{{ request()->is($dashMatch) ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>

                {{-- Gudang (Admin Gudang) --}}
                @if ($role === 'gudang')
                    <div class="k-side-title">Gudang</div>
                    <a href="/gudang/produk" class="{{ request()->is('gudang/produk') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Data Produk
                    </a>
                    <a href="/gudang/barang-masuk" class="{{ request()->is('gudang/barang-masuk') ? 'active' : '' }}">
                        <i class="bi bi-box-arrow-in-down"></i> Barang Masuk
                    </a>
                    <a href="/gudang/barang-keluar" class="{{ request()->is('gudang/barang-keluar') ? 'active' : '' }}">
                        <i class="bi bi-box-arrow-up"></i> Barang Keluar
                    </a>
                    <a href="/gudang/permintaan-stok" class="{{ request()->is('gudang/permintaan-stok') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i> Permintaan Stok
                    </a>
                    <a href="/gudang/barang-rusak" class="{{ request()->is('gudang/barang-rusak') ? 'active' : '' }}">
                        <i class="bi bi-exclamation-octagon"></i> Barang Rusak
                    </a>
                    <a href="{{ route('stok.index') }}" class="{{ request()->routeIs('stok.index') ? 'active' : '' }}">
                        <i class="bi bi-archive"></i> Stok Produk
                    </a>
                    <a href="{{ route('gudang.laporan') }}" class="{{ request()->is('gudang/laporan') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Laporan Gudang
                    </a>
                @endif

                {{-- QC --}}
                @if ($role === 'qc')
                    <div class="k-side-title">Quality Control</div>
                    <a href="/qc/pemeriksaan" class="{{ request()->is('qc/pemeriksaan') ? 'active' : '' }}">
                        <i class="bi bi-search"></i> Pemeriksaan Produk
                    </a>
                    <a href="/qc/laporan" class="{{ request()->is('qc/laporan') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data"></i> Laporan QC
                    </a>
                @endif

                {{-- Keuangan (Admin Keuangan) --}}
                @if ($role === 'keuangan')
                    <div class="k-side-title">Keuangan</div>
                    <a href="{{ route('keuangan.pelanggan') }}" class="{{ request()->routeIs('keuangan.pelanggan') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Data Pelanggan
                    </a>
                    <a href="{{ route('keuangan.piutang') }}" class="{{ request()->routeIs('keuangan.piutang') ? 'active' : '' }}">
                        <i class="bi bi-credit-card"></i> Piutang
                    </a>
                    <a href="{{ route('pembelian.index') }}" class="{{ request()->routeIs('pembelian.*') ? 'active' : '' }}">
                        <i class="bi bi-cart"></i> Pembelian Barang
                    </a>
                    <a href="{{ route('keuangan.penagihan') }}" class="{{ request()->routeIs('keuangan.penagihan') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i> Penagihan Utang
                    </a>
                    <a href="{{ route('keuangan.laporan') }}" class="{{ request()->routeIs('keuangan.laporan') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Laporan Keuangan
                    </a>
                @endif

                {{-- Kasir --}}
                @if ($role === 'kasir')
                    <div class="k-side-title">Kasir</div>
                    <a href="/kasir/transaksi" class="{{ request()->is('kasir/transaksi') ? 'active' : '' }}">
                        <i class="bi bi-receipt-cutoff"></i> Transaksi Penjualan
                    </a>
                    <a href="{{ route('invoice.index') }}" class="{{ request()->routeIs('invoice.index') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i> Invoice
                    </a>
                    <a href="/kasir/nota" class="{{ request()->is('kasir/nota') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Cetak Nota
                    </a>
                    <a href="/kasir/laporan-penjualan" class="{{ request()->is('kasir/laporan-penjualan') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Laporan Penjualan
                    </a>
                    <a href="/kasir/laporan-stok" class="{{ request()->is('kasir/laporan-stok') ? 'active' : '' }}">
                        <i class="bi bi-box"></i> Laporan Stok
                    </a>
                    <a href="{{ route('kasir.spj') }}" class="{{ request()->routeIs('kasir.spj') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data"></i> Laporan SPJ
                    </a>
                @endif

                {{-- Driver --}}
                @if ($role === 'driver')
                    <div class="k-side-title">Driver</div>
                    <a href="{{ route('driver.pengiriman') }}" class="{{ request()->routeIs('driver.pengiriman*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Pengiriman
                    </a>
                @endif

                {{-- Laporan Sistem (use case: Gudang, Kasir, Keuangan) --}}
                @if (in_array($role, ['gudang', 'kasir', 'keuangan']))
                    <div class="k-side-title">Laporan</div>
                    <a href="/laporan" class="{{ request()->is('laporan') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i> Laporan Sistem
                    </a>
                @endif
            </nav>

            <div class="k-side-foot">
                <span class="k-user"><i class="bi bi-person-circle"></i><span>{{ auth()->check() ? auth()->user()->name : 'Guest' }}</span></span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="k-logout" title="Logout" aria-label="Logout"><i class="bi bi-box-arrow-right"></i></button>
                </form>
            </div>
        </aside>

        <div class="k-backdrop" id="kBackdrop"></div>
        <button type="button" class="k-menu-toggle" id="kMenuToggle" aria-label="Buka menu"><i class="bi bi-list"></i></button>

        <main class="k-main">
            @if (session('success'))
                <div class="k-alert k-alert-success" role="alert">
                    <i class="bi bi-check-circle-fill alert-ico"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="k-alert k-alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill alert-ico"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            // Auto-dismiss flash alert
            document.querySelectorAll('.k-alert').forEach(function (a) {
                setTimeout(function () {
                    a.style.opacity = '0';
                    a.style.transform = 'translateY(-6px)';
                    setTimeout(function () { a.remove(); }, 300);
                }, 4500);
            });

            // Konfirmasi dua langkah (menggantikan confirm() native)
            function bindTwoStep(btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (btn.classList.contains('k-confirming')) {
                        clearTimeout(btn._kTimer);
                        btn.closest('form').submit();
                        return;
                    }
                    btn._kOrig = btn.innerHTML;
                    btn.classList.add('k-confirming');
                    btn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Yakin? Klik lagi';
                    btn._kTimer = setTimeout(function () {
                        btn.classList.remove('k-confirming');
                        btn.innerHTML = btn._kOrig;
                    }, 4000);
                });
            }
            document.querySelectorAll('.js-confirm').forEach(bindTwoStep);

            // Sidebar mobile (off-canvas)
            var sidebar = document.getElementById('kSidebar');
            var backdrop = document.getElementById('kBackdrop');
            var toggle = document.getElementById('kMenuToggle');
            function closeSide() {
                sidebar.classList.remove('open');
                backdrop.classList.remove('show');
            }
            if (toggle) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.add('open');
                    backdrop.classList.add('show');
                });
            }
            if (backdrop) {
                backdrop.addEventListener('click', closeSide);
            }
            sidebar.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', closeSide);
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
