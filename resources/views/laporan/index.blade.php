@extends('layouts.app')

@section('title', 'Laporan Sistem')

@section('content')

<div class="k-page-head">
    <div>
        <h1>Laporan Sistem</h1>
        <p>Penjualan &amp; mutasi stok terkini — Gudang · Kasir · Keuangan</p>
    </div>
    <div class="d-flex gap-2 no-print">
        <button onclick="window.print()" class="k-btn k-btn-primary">
            <i class="bi bi-printer"></i> Cetak
        </button>
    </div>
</div>

{{-- ============ RINGKASAN SISTEM ============ --}}
<div class="row g-3 print-hide">
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-green"><i class="bi bi-cash-stack"></i></span>
            <div>
                <div class="k-stat-label">Total Penjualan</div>
                <div class="k-stat-value v-green">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                <div class="k-stat-sub">Transaksi non-batal</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-amber"><i class="bi bi-hourglass-split"></i></span>
            <div>
                <div class="k-stat-label">Piutang Pending</div>
                <div class="k-stat-value v-amber">Rp {{ number_format($piutangPending, 0, ',', '.') }}</div>
                <div class="k-stat-sub">Transaksi belum lunas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-accent"><i class="bi bi-box-seam"></i></span>
            <div>
                <div class="k-stat-label">Total Stok</div>
                <div class="k-stat-value v-accent">{{ number_format($totalStok) }}</div>
                <div class="k-stat-sub">Unit tersedia saat ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-info"><i class="bi bi-arrow-left-right"></i></span>
            <div>
                <div class="k-stat-label">Mutasi Stok</div>
                <div class="k-stat-value v-info">{{ number_format($jumlahMutasi) }}</div>
                <div class="k-stat-sub">Catatan masuk &amp; keluar</div>
            </div>
        </div>
    </div>
</div>

{{-- ============ PILIH LAPORAN ============ --}}
<div class="btn-group print-hide" role="group" aria-label="Pilih laporan" style="margin-top: 20px;">
    <a href="{{ url('laporan?tab=penjualan') }}" class="btn {{ $tab === 'penjualan' ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="bi bi-receipt"></i> Penjualan
    </a>
    <a href="{{ url('laporan?tab=stok') }}" class="btn {{ $tab === 'stok' ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="bi bi-box-arrow-in-down"></i> Mutasi Stok
    </a>
</div>

@if ($tab === 'penjualan')

    {{-- ============ PENJUALAN ============ --}}
    <div class="k-card print-area" style="margin-top: 20px;">
        <div class="k-card-head">
            <div>
                <h2>Transaksi Penjualan</h2>
                <small>Transaksi yang terinput sistem (termasuk pending &amp; batal)</small>
            </div>
        </div>
        <div class="table-responsive">
            @if ($penjualan->isEmpty())
                <p class="k-empty"><i class="bi bi-graph-up"></i>Tidak ada data transaksi.</p>
            @else
                <table class="k-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Metode</th>
                            <th class="t-right">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan as $p)
                            <tr>
                                <td>{{ $penjualan->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $p->kode ?: '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $p->pelanggan ?: 'Umum' }}</td>
                                <td><span class="k-badge b-method">{{ strtoupper($p->metode ?? '-') }}</span></td>
                                <td class="t-right fw-semibold">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                <td>
                                    @if ($p->status === 'lunas')
                                        <span class="k-badge b-lunas">LUNAS</span>
                                    @elseif ($p->status === 'pending')
                                        <span class="k-badge b-pending">BELUM LUNAS</span>
                                    @else
                                        <span class="k-badge b-batal">{{ strtoupper($p->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center no-print" style="padding: 16px;">
                    {{ $penjualan->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

@else

    {{-- ============ MUTASI STOK ============ --}}
    <div class="k-card print-area" style="margin-top: 20px;">
        <div class="k-card-head">
            <div>
                <h2>Mutasi Stok</h2>
                <small>Riwayat barang masuk &amp; keluar (kartu stok)</small>
            </div>
        </div>
        <div class="table-responsive">
            @if ($stok->isEmpty())
                <p class="k-empty"><i class="bi bi-box-seam"></i>Tidak ada data mutasi stok.</p>
            @else
                <table class="k-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Jenis</th>
                            <th class="t-right">Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stok as $s)
                            <tr>
                                <td>{{ $stok->firstItem() + $loop->index }}</td>
                                <td>{{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $s->produk->nama_produk ?? '-' }}</td>
                                <td>
                                    @if ($s->jenis === 'masuk')
                                        <span class="k-badge b-masuk">MASUK</span>
                                    @else
                                        <span class="k-badge b-keluar">KELUAR</span>
                                    @endif
                                </td>
                                <td class="t-right fw-semibold">{{ $s->jenis === 'masuk' ? '+' : '-' }}{{ number_format($s->jumlah, 0, ',', '.') }}</td>
                                <td>{{ $s->keterangan ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center no-print" style="padding: 16px;">
                    {{ $stok->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

@endif

@endsection
