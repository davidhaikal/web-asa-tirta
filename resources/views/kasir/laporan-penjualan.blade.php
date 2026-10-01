@extends('layouts.kasir', [
    'title' => 'Laporan Penjualan',
    'subtitle' => 'Kasir > Laporan > Penjualan',
])

@section('content')

<div class="k-page-head">
    <div>
        <h1>Laporan Penjualan</h1>
        <p>Filter: {{ $labelPeriode }}</p>
    </div>
    <button onclick="window.print()" class="k-btn k-btn-ghost no-print"><i class="bi bi-printer"></i> Cetak Laporan</button>
</div>

{{-- ============ FILTER ============ --}}
<div class="k-card print-hide">
    <div class="k-card-body">
        <form method="GET" action="{{ route('kasir.laporan-penjualan') }}" class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-semibold" for="periode">Periode</label>
                <select id="periode" name="periode" class="form-select" onchange="this.form.submit()">
                    <option value="hari" {{ $periode === 'hari' ? 'selected' : '' }}>Harian</option>
                    <option value="minggu" {{ $periode === 'minggu' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulan" {{ $periode === 'bulan' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-semibold" for="tanggal">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-3 col-sm-6">
                <button type="submit" class="k-btn k-btn-primary k-btn-block">Terapkan Filter</button>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('kasir.laporan-penjualan') }}" class="k-btn k-btn-ghost k-btn-block">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- ============ RINGKASAN ============ --}}
<div class="row g-3 print-hide">
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-accent"><i class="bi bi-receipt"></i></span>
            <div>
                <div class="k-stat-label">Total Transaksi</div>
                <div class="k-stat-value v-accent">{{ $totalTransaksi }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-green"><i class="bi bi-cash-stack"></i></span>
            <div>
                <div class="k-stat-label">Total Pendapatan</div>
                <div class="k-stat-value v-green">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-info"><i class="bi bi-box-seam"></i></span>
            <div>
                <div class="k-stat-label">Produk Terjual</div>
                <div class="k-stat-value v-info">{{ $totalProdukTerjual }}</div>
                <div class="k-stat-sub">unit</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-amber"><i class="bi bi-hourglass-split"></i></span>
            <div>
                <div class="k-stat-label">Lunas / Pending</div>
                <div class="k-stat-value"><span class="v-green">{{ $lunasCount }}</span> <span class="text-muted" style="font-size: 16px;">/</span> <span class="v-amber">{{ $pendingCount }}</span></div>
            </div>
        </div>
    </div>
</div>

{{-- ============ DETAIL ============ --}}
<div class="k-card print-area" style="margin-top: 20px;">
    <div class="k-card-head">
        <div>
            <h2>Detail Penjualan — {{ $labelPeriode }}</h2>
            <small>Transaksi berstatus batal tidak dihitung</small>
        </div>
    </div>
    <div class="table-responsive">
        @if ($penjualan->isEmpty())
            <p class="k-empty"><i class="bi bi-graph-up"></i>Tidak ada data penjualan pada periode ini.</p>
        @else
            <table class="k-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Items</th>
                        <th class="t-right">Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualan as $p)
                        <tr>
                            <td class="fw-semibold">{{ $p->kode }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $p->pelanggan }}</td>
                            <td>
                                @foreach ($p->detailPenjualans as $d)
                                    <span class="k-badge b-batal me-1 mb-1">{{ $d->produk->nama_produk }} x{{ $d->jumlah }}</span>
                                @endforeach
                            </td>
                            <td class="t-right fw-semibold">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                            <td><span class="k-badge b-method">{{ strtoupper($p->metode) }}</span></td>
                            <td>
                                @if ($p->status === 'lunas')
                                    <span class="k-badge b-lunas">LUNAS</span>
                                @elseif ($p->status === 'pending')
                                    <span class="k-badge b-pending">BELUM LUNAS</span>
                                @else
                                    <span class="k-badge b-batal">BATAL</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="t-right" style="color: var(--k-muted);">TOTAL</td>
                        <td class="t-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
            <div class="d-flex justify-content-center no-print" style="padding: 16px;">
                {{ $penjualan->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
