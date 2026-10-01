@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')

<div class="k-page-head">
    <div>
        <h1>Laporan Keuangan</h1>
        <p>{{ $labelPeriode }} — rekap pendapatan, transaksi &amp; piutang</p>
    </div>
    <div class="d-flex gap-2 no-print">
        <a href="{{ route('keuangan.export.pdf', ['periode' => $periode, 'tanggal' => $tanggal]) }}" class="k-btn k-btn-ghost">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
        <a href="{{ route('keuangan.export.excel', ['periode' => $periode, 'tanggal' => $tanggal]) }}" class="k-btn k-btn-ghost">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <button onclick="window.print()" class="k-btn k-btn-primary">
            <i class="bi bi-printer"></i> Cetak Laporan
        </button>
    </div>
</div>

{{-- ============ FILTER PERIODE ============ --}}
<div class="k-card print-hide">
    <div class="k-card-body">
        <form method="GET" action="{{ route('keuangan.laporan') }}" class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="form-label fw-semibold" for="periode">Periode</label>
                <select id="periode" name="periode" class="form-select" onchange="this.form.submit()">
                    <option value="bulan" {{ $periode === 'bulan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="hari" {{ $periode === 'hari' ? 'selected' : '' }}>Harian</option>
                    <option value="semua" {{ $periode === 'semua' ? 'selected' : '' }}>Semua Periode</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6" id="wrapTanggal">
                <label class="form-label fw-semibold" for="tanggal">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-3 col-sm-6">
                <button type="submit" class="k-btn k-btn-primary k-btn-block">Terapkan Filter</button>
            </div>
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('keuangan.laporan') }}" class="k-btn k-btn-ghost k-btn-block">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- ============ RINGKASAN ============ --}}
<div class="row g-3 print-hide">
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-green"><i class="bi bi-cash-stack"></i></span>
            <div>
                <div class="k-stat-label">Total Pendapatan</div>
                <div class="k-stat-value v-green">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="k-stat-sub">Transaksi lunas · {{ $labelPeriode }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-amber"><i class="bi bi-hourglass-split"></i></span>
            <div>
                <div class="k-stat-label">Piutang Berjalan</div>
                <div class="k-stat-value v-amber">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</div>
                <div class="k-stat-sub">{{ $piutangCount }} transaksi pending (kondisi terkini)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-accent"><i class="bi bi-receipt"></i></span>
            <div>
                <div class="k-stat-label">Total Transaksi</div>
                <div class="k-stat-value v-accent">{{ number_format($totalTransaksi) }}</div>
                <div class="k-stat-sub">Lunas {{ $lunasCount }} · Pending {{ $pendingCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-info"><i class="bi bi-bar-chart"></i></span>
            <div>
                <div class="k-stat-label">Rata-rata per Transaksi</div>
                <div class="k-stat-value v-info">Rp {{ number_format($rataRata, 0, ',', '.') }}</div>
                <div class="k-stat-sub">Nilai transaksi ÷ jumlah transaksi</div>
            </div>
        </div>
    </div>
</div>

{{-- ============ REKAP METODE PEMBAYARAN ============ --}}
<div class="k-card print-hide" style="margin-top: 20px;">
    <div class="k-card-head">
        <div>
            <h2>Rekap Metode Pembayaran</h2>
            <small>{{ $labelPeriode }}</small>
        </div>
    </div>
    <div class="k-card-body">
        <div class="row g-3">
            @foreach (['tunai' => 'bi-cash', 'transfer' => 'bi-bank', 'qris' => 'bi-qr-code'] as $metode => $ikon)
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="k-stat-icon i-accent"><i class="bi {{ $ikon }}"></i></span>
                        <div>
                            <div class="k-stat-label">{{ strtoupper($metode) }}</div>
                            <div class="fw-bold">Rp {{ number_format($metodeRekap[$metode]['total'], 0, ',', '.') }}</div>
                            <div class="k-stat-sub">{{ $metodeRekap[$metode]['jumlah'] }} transaksi</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ============ GRAFIK ============ --}}
<div class="k-card print-hide">
    <div class="k-card-head">
        <div>
            <h2>Grafik Penjualan — 6 Bulan Terakhir</h2>
            <small>Tren keseluruhan (tidak mengikuti filter periode)</small>
        </div>
    </div>
    <div class="k-card-body">
        <canvas id="chartPenjualan" height="110"></canvas>
    </div>
</div>

{{-- ============ DETAIL TRANSAKSI ============ --}}
<div class="k-card print-area">
    <div class="k-card-head">
        <div>
            <h2>Detail Transaksi — {{ $labelPeriode }}</h2>
            <small>Transaksi berstatus batal tidak dihitung</small>
        </div>
    </div>
    <div class="table-responsive">
        @if ($penjualans->isEmpty())
            <p class="k-empty"><i class="bi bi-graph-up"></i>Tidak ada data transaksi pada periode ini.</p>
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
                    @foreach ($penjualans as $p)
                        <tr>
                            <td>{{ $penjualans->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $p->kode ?: '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $p->pelanggan ?? 'Umum' }}</td>
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
                <tfoot>
                    <tr>
                        <td colspan="5" class="t-right" style="color: var(--k-muted);">TOTAL — {{ number_format($totalTransaksi) }} transaksi</td>
                        <td class="t-right">Rp {{ number_format($totalNilaiPeriode, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <div class="d-flex justify-content-center no-print" style="padding: 16px;">
                {{ $penjualans->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<script>
    // Sembunyikan input tanggal saat mode "Semua Periode"
    const elPeriode = document.getElementById('periode');
    const elTanggal = document.getElementById('tanggal');
    const wrapTanggal = document.getElementById('wrapTanggal');

    const terapkanTanggal = function () {
        const sembunyi = elPeriode.value === 'semua';
        elTanggal.disabled = sembunyi;
        wrapTanggal.style.opacity = sembunyi ? '.45' : '1';
    };
    elPeriode.addEventListener('change', terapkanTanggal);
    terapkanTanggal();

    const ctxPenjualan = document.getElementById('chartPenjualan');

    new Chart(ctxPenjualan, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Penjualan',
                data: @json($chartData),
                backgroundColor: '#127369',
                borderRadius: 6,
                maxBarThickness: 42
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return 'Rp ' + Number(context.parsed.y).toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>

@endsection
