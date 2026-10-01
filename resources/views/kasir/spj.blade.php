@extends('layouts.kasir', [
    'title' => 'Laporan SPJ',
    'subtitle' => 'Kasir > Laporan > SPJ Bulanan',
])

@section('content')

<div class="k-page-head">
    <div>
        <h1>Laporan SPJ Bulanan</h1>
        <p>Periode: <strong>{{ $labelPeriode }}</strong> — rekapitulasi transaksi &amp; pendapatan</p>
    </div>
    <button onclick="window.print()" class="k-btn k-btn-ghost no-print"><i class="bi bi-printer"></i> Cetak SPJ</button>
</div>

{{-- ============ FILTER ============ --}}
<div class="k-card print-hide">
    <div class="k-card-body">
        <form method="GET" action="{{ route('kasir.spj') }}" class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-semibold" for="tanggal">Bulan Periode</label>
                <input type="month" id="tanggal" name="tanggal" class="form-control"
                       value="{{ \Carbon\Carbon::parse($tanggal)->format('Y-m') }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('kasir.spj') }}" class="k-btn k-btn-ghost k-btn-block">Reset</a>
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
            <span class="k-stat-icon i-info"><i class="bi bi-check2-circle"></i></span>
            <div>
                <div class="k-stat-label">Lunas</div>
                <div class="k-stat-value v-info">{{ $lunasCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-amber"><i class="bi bi-hourglass-split"></i></span>
            <div>
                <div class="k-stat-label">Belum Lunas</div>
                <div class="k-stat-value v-amber">{{ $pendingCount }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ============ REKAP METODE ============ --}}
<div class="k-card print-area" style="margin-top: 20px;">
    <div class="k-card-body">
        <h3 class="k-card-title">Rekap Metode Pembayaran</h3>
        <table class="k-table" style="max-width: 520px;">
            <thead>
                <tr>
                    <th>Metode</th>
                    <th class="t-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><i class="bi bi-cash-coin me-1"></i>Tunai</td>
                    <td class="t-right">Rp {{ number_format($metodeRekap['tunai'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><i class="bi bi-bank me-1"></i>Transfer</td>
                    <td class="t-right">Rp {{ number_format($metodeRekap['transfer'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><i class="bi bi-qr-code me-1"></i>QRIS</td>
                    <td class="t-right">Rp {{ number_format($metodeRekap['qris'], 0, ',', '.') }}</td>
                </tr>
                <tfoot>
                    <tr>
                        <td class="t-right" style="color: var(--k-muted);">TOTAL</td>
                        <td class="t-right"><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </tbody>
        </table>
    </div>
</div>

{{-- ============ REKAP PRODUK ============ --}}
<div class="k-card print-area" style="margin-top: 20px;">
    <div class="k-card-body">
        <h3 class="k-card-title">Rekap Produk Terjual</h3>
        <table class="k-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th class="t-right">Jumlah (Unit)</th>
                    <th class="t-right">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produkRekap as $idx => $rek)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $rek['produk'] }}</td>
                        <td class="t-right">{{ number_format($rek['jumlah']) }}</td>
                        <td class="t-right">Rp {{ number_format($rek['nilai'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center" style="color: var(--k-muted);">Tidak ada transaksi pada periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="t-right" style="color: var(--k-muted);">TOTAL</td>
                    <td class="t-right"><strong>{{ number_format($produkRekap->sum('jumlah')) }}</strong></td>
                    <td class="t-right"><strong>Rp {{ number_format($produkRekap->sum('nilai'), 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ============ RINCIAN TRANSAKSI ============ --}}
<div class="k-card print-area" style="margin-top: 20px;">
    <div class="k-card-body">
        <h3 class="k-card-title">Rincian Transaksi ({{ $totalTransaksi }})</h3>
        <table class="k-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th class="t-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $idx => $t)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</td>
                        <td>{{ $t->kode }}</td>
                        <td>{{ $t->pelanggan ?? 'Walk-in' }}</td>
                        <td>{{ ucfirst($t->metode) }}</td>
                        <td>
                            @if($t->status === 'lunas')
                                <span class="k-badge b-lunas">Lunas</span>
                            @else
                                <span class="k-badge b-pending">Pending</span>
                            @endif
                        </td>
                        <td class="t-right">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center" style="color: var(--k-muted);">Tidak ada transaksi pada periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="t-right" style="color: var(--k-muted);">TOTAL</td>
                    <td class="t-right"><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
