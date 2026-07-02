<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f3f6fb;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --primary: #1f6feb;
            --accent: #f59e0b;
            --nav: #101826;
            --nav-soft: #1b2536;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: "Space Grotesk", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: radial-gradient(900px circle at 80% 0%, #dfe7ff 0, #f3f6fb 40%, #eef2f7 100%);
            color: var(--ink);
            min-height: 100vh;
        }
        .layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }
        aside {
            background: linear-gradient(160deg, var(--nav) 0%, var(--nav-soft) 100%);
            color: #e2e8f0;
            padding: 20px 18px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow-y: auto;
        }
        .brand {
            margin-bottom: 12px;
        }
        .brand h1 { font-size: 20px; margin: 0; }
        .brand p { font-size: 12px; color: #94a3b8; margin: 4px 0 0; }
        nav { display: flex; flex-direction: column; gap: 4px; }
        nav a {
            text-decoration: none;
            color: #e2e8f0;
            padding: 8px 12px;
            border-radius: 8px;
            background: transparent;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }
        nav a:hover { background: rgba(148, 163, 184, 0.12); }
        nav a.active { background: rgba(255,255,255,0.16); }
        main {
            padding: 28px;
            display: block;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .welcome h2 { font-size: 26px; margin: 0; }
        .welcome p { color: var(--muted); font-size: 14px; margin: 4px 0 0; }
        .menu-title {
            margin-top: 12px;
            margin-bottom: 4px;
            font-weight: bold;
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        @media (max-width: 1000px) {
            .layout { grid-template-columns: 1fr; }
            aside { position: sticky; top: 0; z-index: 5; max-height: 100vh; }
        }
        @media (max-width: 700px) {
            main { padding: 20px; }
        }
    </style>
</head>
<body>
    @php($role = auth()->check() ? auth()->user()->role : 'guest')
    <div class="layout">
        <aside>
            <div class="brand">
                <h1>ASA Tirta</h1>
                <p>Sistem Manajemen</p>
            </div>

            <?php
                $dashboardRoutes = [
                    'admin' => '/admin',
                    'produksi' => '/produksi',
                    'marketing' => '/dashboard',
                    'gudang' => '/gudang/dashboard',
                    'qc' => '/qc/dashboard',
                    'keuangan' => '/keuangan/dashboard',
                    'kasir' => '/kasir/dashboard',
                    'driver' => '/driver/dashboard',
                ];
                $currentDashboard = $dashboardRoutes[$role] ?? '/login';
            ?>
            <nav>
                @if ($role !== 'kasir')
                <a href="{{ $currentDashboard }}">📊 Dashboard</a>
                @endif

                @if ($role === 'admin' || $role === 'produksi')
                    <a href="/produksi">🏭 Produksi</a>
                @endif

                {{-- Marketing --}}
                @if ($role === 'admin' || $role === 'marketing')
                    <div class="menu-title">Marketing</div>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Dashboard Marketing</a>
                    <a href="{{ route('po.index') }}" class="{{ request()->routeIs('po.index') ? 'active' : '' }}">🛒 Purchase Order</a>
                    <a href="{{ route('stok.index') }}" class="{{ request()->routeIs('stok.index') ? 'active' : '' }}">📦 Stok Produk</a>
                    <a href="{{ route('invoice.index') }}" class="{{ request()->routeIs('invoice.index') ? 'active' : '' }}">🧾 Invoice</a>
                    <a href="{{ route('permintaan-uang.index') }}" class="{{ request()->routeIs('permintaan-uang.index') ? 'active' : '' }}">💵 Permintaan Uang</a>
                @endif

                {{-- Gudang --}}
                @if ($role === 'admin' || $role === 'gudang')
                    <div class="menu-title">Gudang</div>
                    <a href="/gudang/dashboard" class="{{ request()->is('gudang/dashboard') ? 'active' : '' }}">📊 Dashboard Gudang</a>
                    <a href="/gudang/produk" class="{{ request()->is('gudang/produk') ? 'active' : '' }}">📦 Data Produk</a>
                    <a href="/gudang/barang-masuk" class="{{ request()->is('gudang/barang-masuk') ? 'active' : '' }}">📥 Barang Masuk</a>
                    <a href="/gudang/barang-keluar" class="{{ request()->is('gudang/barang-keluar') ? 'active' : '' }}">📤 Barang Keluar</a>
                    <a href="/gudang/permintaan-stok" class="{{ request()->is('gudang/permintaan-stok') ? 'active' : '' }}">📋 Permintaan Stok</a>
                    <a href="/gudang/barang-rusak" class="{{ request()->is('gudang/barang-rusak') ? 'active' : '' }}">⚠️ Barang Rusak</a>
                    <a href="/gudang/laporan" class="{{ request()->is('gudang/laporan') ? 'active' : '' }}">📈 Laporan Gudang</a>
                @endif

                @if ($role === 'admin' || $role === 'qc')
                    <div class="menu-title">Quality Control</div>
                    <a href="/qc/dashboard" class="{{ request()->is('qc/dashboard') ? 'active' : '' }}">📊 Dashboard QC</a>
                    <a href="/qc/pemeriksaan" class="{{ request()->is('qc/pemeriksaan') ? 'active' : '' }}">🔍 Pemeriksaan Produk</a>
                    <a href="/qc/lolos" class="{{ request()->is('qc/lolos') ? 'active' : '' }}">✅ Produk Lolos</a>
                    <a href="/qc/reject" class="{{ request()->is('qc/reject') ? 'active' : '' }}">❌ Produk Reject</a>
                    <a href="/qc/laporan" class="{{ request()->is('qc/laporan') ? 'active' : '' }}">📈 Laporan QC</a>
                @endif

                @if ($role === 'admin' || $role === 'keuangan')
                    <div class="menu-title">Keuangan</div>
                    <a href="/keuangan/dashboard" class="{{ request()->is('keuangan/dashboard') ? 'active' : '' }}">📊 Dashboard Keuangan</a>
                    <a href="/keuangan/pelanggan" class="{{ request()->is('keuangan/pelanggan*') ? 'active' : '' }}">👤 Pelanggan</a>
                    <a href="/keuangan/piutang" class="{{ request()->is('keuangan/piutang*') ? 'active' : '' }}">💳 Piutang</a>
                    <a href="/keuangan/penagihan" class="{{ request()->is('keuangan/penagihan*') ? 'active' : '' }}">📩 Penagihan</a>
                    <a href="/keuangan/laporan" class="{{ request()->is('keuangan/laporan*') ? 'active' : '' }}">📄 Laporan Keuangan</a>
                @endif

                @if ($role === 'admin' || $role === 'kasir')
                    <div class="menu-title">Kasir</div>
                    <a href="/kasir/dashboard" class="{{ request()->is('kasir/dashboard') ? 'active' : '' }}">📊 Dashboard Kasir</a>
                    <a href="/kasir/transaksi" class="{{ request()->is('kasir/transaksi') ? 'active' : '' }}">🛒 Transaksi Penjualan</a>
                    <a href="/kasir/nota" class="{{ request()->is('kasir/nota') ? 'active' : '' }}">🖨️ Cetak Nota</a>
                    <a href="/kasir/laporan-penjualan" class="{{ request()->is('kasir/laporan-penjualan') ? 'active' : '' }}">📈 Laporan Penjualan</a>
                    <a href="/kasir/laporan-stok" class="{{ request()->is('kasir/laporan-stok') ? 'active' : '' }}">📦 Laporan Stok</a>
                @endif

                {{-- Driver --}}
                @if ($role === 'admin' || $role === 'driver')
                    <div class="menu-title">Driver</div>
                    <a href="{{ route('driver.dashboard') }}" class="{{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">📊 Dashboard Driver</a>
                    <a href="{{ route('driver.invoice.index') }}" class="{{ request()->routeIs('driver.invoice.*') ? 'active' : '' }}">📄 Terima Invoice</a>
                    <a href="{{ route('driver.pengiriman.index') }}" class="{{ request()->routeIs('driver.pengiriman.*') ? 'active' : '' }}">🚚 Data Pengiriman</a>
                @endif

                <div class="menu-title">Akun</div>
                <a href="/logout" onclick="return confirm('Yakin ingin logout?')">🚪 Logout</a>
            </nav>
        </aside>

        <main>
            <div class="topbar">
                <div class="welcome">
                    <h2>{{ $title ?? 'Dashboard' }}</h2>
                    <p>{{ $subtitle ?? '' }}</p>
                </div>
                <span class="badge bg-secondary fs-6">
                    {{ auth()->check() ? auth()->user()->name : 'Guest' }}
                </span>
            </div>

            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>