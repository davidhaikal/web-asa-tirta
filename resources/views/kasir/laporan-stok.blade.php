@extends('layouts.kasir', [
    'title' => 'Laporan Stok',
    'subtitle' => 'Kasir > Laporan > Stok',
])

@section('content')

<div class="k-page-head">
    <div>
        <h1>Laporan Stok Gudang</h1>
        <p>Data stok produk + kartu stok</p>
    </div>
</div>

{{-- ============ RINGKASAN ============ --}}
<div class="row g-3">
    <div class="col-md-4">
        <div class="k-stat">
            <span class="k-stat-icon i-accent"><i class="bi bi-boxes"></i></span>
            <div>
                <div class="k-stat-label">Total Produk</div>
                <div class="k-stat-value v-accent">{{ $totalProduk }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="k-stat">
            <span class="k-stat-icon i-red"><i class="bi bi-exclamation-triangle"></i></span>
            <div>
                <div class="k-stat-label">Stok Rendah (&lt;10)</div>
                <div class="k-stat-value v-red">{{ $totalStokRendah }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="k-stat">
            <span class="k-stat-icon i-green"><i class="bi bi-currency-dollar"></i></span>
            <div>
                <div class="k-stat-label">Nilai Total Stok</div>
                <div class="k-stat-value v-green">Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ============ DATA STOK PRODUK ============ --}}
<div class="k-card" style="margin-top: 20px;">
    <div class="k-card-head">
        <div>
            <h2>Data Stok Produk</h2>
            <small>Aman &gt;50 · Menipis 11–50 · Kritis ≤10</small>
        </div>
    </div>
    <div class="table-responsive">
        <table class="k-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Produk</th>
                    <th class="t-right">Harga</th>
                    <th class="t-right">Stok</th>
                    <th class="t-right">Nilai Stok</th>
                    <th>Status</th>
                    <th class="t-right no-print">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produk as $idx => $p)
                    <tr>
                        <td class="text-muted">{{ $idx + 1 }}</td>
                        <td class="fw-semibold">{{ $p->nama_produk }}</td>
                        <td class="t-right">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                        <td class="t-right">
                            <span class="k-badge {{ $p->stok > 50 ? 'b-aman' : ($p->stok > 10 ? 'b-menipis' : 'b-kritis') }}">{{ $p->stok }}</span>
                        </td>
                        <td class="t-right">Rp {{ number_format($p->stok * $p->harga, 0, ',', '.') }}</td>
                        <td>
                            @if ($p->stok > 50)
                                <span class="k-badge b-aman">AMAN</span>
                            @elseif ($p->stok > 10)
                                <span class="k-badge b-menipis">MENIPIS</span>
                            @else
                                <span class="k-badge b-kritis">KRITIS</span>
                            @endif
                        </td>
                        <td class="t-right no-print" style="white-space: nowrap;">
                            <a href="{{ route('kasir.laporan-stok', ['produk_id' => $p->id]) }}" class="k-btn k-btn-ghost k-btn-sm">
                                <i class="bi bi-journal-text"></i> Kartu Stok
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ============ KARTU STOK ============ --}}
@if ($produkTerpilih)
<div class="k-card">
    <div class="k-card-head">
        <div>
            <h2>Kartu Stok: {{ $produkTerpilih->nama_produk }}</h2>
            <small>50 riwayat stok terbaru</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="k-badge b-accent">Stok Saat Ini: {{ $produkTerpilih->stok }}</span>
            <a href="{{ route('kasir.laporan-stok') }}" class="k-btn k-btn-ghost k-btn-sm no-print">Tutup</a>
        </div>
    </div>
    @if ($kartuStok->isEmpty())
        <p class="k-empty"><i class="bi bi-journal-x"></i>Belum ada riwayat stok untuk produk ini.</p>
    @else
        <div class="table-responsive">
            <table class="k-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th class="t-right">Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kartuStok as $ks)
                        <tr>
                            <td>{{ $ks->created_at ? \Carbon\Carbon::parse($ks->created_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @if ($ks->jenis === 'masuk')
                                    <span class="k-badge b-masuk">MASUK</span>
                                @elseif ($ks->jenis === 'keluar')
                                    <span class="k-badge b-keluar">KELUAR</span>
                                @else
                                    <span class="k-badge b-method">{{ strtoupper($ks->jenis) }}</span>
                                @endif
                            </td>
                            <td class="t-right fw-semibold">
                                @if ($ks->jenis === 'masuk')
                                    <span class="v-green">+{{ $ks->jumlah }}</span>
                                @else
                                    <span class="v-red">-{{ $ks->jumlah }}</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $ks->keterangan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endif

@endsection
