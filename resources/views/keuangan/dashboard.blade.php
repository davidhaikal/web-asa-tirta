@extends('layouts.app')

@section('title', 'Dashboard Keuangan')

@section('content')

<style>
    /* ========================== Card Statistik ========================== */
    .card-modern {
        border-radius: 18px;
        padding: 24px;
        color: #fff !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
        height: 100%;
    }

    .card-modern p {
        margin-bottom: 4px;
        opacity: .9;
        color: #fff !important;
    }

    .card-modern h3 {
        margin-bottom: 0;
        color: #fff !important;
    }

    .card-modern i {
        color: #fff !important;
    }

    .bg-blue {
        background: linear-gradient(135deg, #127369, #2ba397) !important;
    }

    .bg-green {
        background: linear-gradient(135deg, #198754, #2fb673) !important;
    }

    .bg-red {
        background: linear-gradient(135deg, #dc3545, #ef5b6b) !important;
    }

    .bg-orange {
        background: linear-gradient(135deg, #fd7e14, #ffa347) !important;
    }

    /* ========================== Chart Box ========================== */
    .chart-box {
        background: #fff;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
    }

    /* ========================== Table ========================== */
    .table-modern {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        overflow: hidden;
    }

    .table-header-keuangan th {
        background-color: #127369 !important;
        color: #fff !important;
        font-weight: 600;
        border: none;
    }

    .table-modern tbody tr:hover {
        background: #f5f8ff;
    }
</style>

<div class="container-fluid py-4">

    <!-- export laporan -->
    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Dashboard Keuangan
            </h2>

            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('keuangan.export.pdf') }}"
            class="btn btn-danger rounded-pill shadow-sm">

                <i class="bi bi-file-earmark-pdf-fill"></i>
                Export PDF

            </a>

            <a href="{{ route('keuangan.export.excel') }}"
            class="btn btn-success rounded-pill shadow-sm">

                <i class="bi bi-file-earmark-excel-fill"></i>
                Export Excel

            </a>

        </div>

    </div>

    <!-- Statistik -->
    <div class="row g-4">
        <div class="col-md-3">
            <a href="{{ route('keuangan.piutang', ['tab' => 'pendapatan', 'periode' => 'hari']) }}" class="text-decoration-none">
                <div class="card-modern bg-blue">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p>Pendapatan (Hari Ini)</p>
                            <h3 class="fw-bold">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div>
                            <i class="bi bi-cash-coin fs-1"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('keuangan.piutang', ['tab' => 'pendapatan', 'periode' => 'hari']) }}" class="text-decoration-none">
                <div class="card-modern bg-green">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">Lunas (Hari Ini)</p>
                            <h3 class="fw-bold">{{ $totalLunas ?? 0 }} Transaksi</h3>
                        </div>
                        <div>
                            <i class="bi bi-check-circle-fill fs-1"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('keuangan.piutang', ['tab' => 'piutang']) }}" class="text-decoration-none">
                <div class="card-modern bg-red">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p>Total Piutang</p>
                            <h3 class="fw-bold">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div>
                            <i class="bi bi-wallet2 fs-1"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('keuangan.penagihan') }}" class="text-decoration-none">
                <div class="card-modern bg-orange">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p>Tagihan Pending</p>
                            <h3 class="fw-bold">{{ $tagihanPending ?? 0 }} Tagihan</h3>
                        </div>
                        <div>
                            <i class="bi bi-receipt-cutoff fs-1"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mt-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Grafik Arus Kas 7 Hari Terakhir</h5>
                            <small class="text-muted">Perbandingan Tren Pendapatan vs Pengeluaran</small>
                        </div>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            <i class="bi bi-calendar3 me-1"></i> 7 Hari Terakhir
                        </span>
                    </div>
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Tagihan Customer -->
    <div class="table-modern mt-4 p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h5 class="fw-bold mb-1">
                Tagihan Utang Jatuh Tempo
            </h5>

            <small class="text-muted">
                Daftar pembelian yang belum lunas dan mendekati jatuh tempo
            </small>
        </div>

        <button class="btn btn-outline-primary rounded-pill">
            <i class="bi bi-arrow-clockwise"></i>
            Refresh
        </button>

    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead>

                <tr>

                    <th>No Pembelian</th>

                    <th>Supplier</th>

                    <th>Total Utang</th>

                    <th>Jatuh Tempo</th>

                    <th>Status</th>

                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>
                @forelse($pembelianJatuhTempo ?? [] as $item)
                <tr>
                    <td>{{ $item->no_transaksi ?? 'PB-' . str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $item->supplier->nama_supplier ?? $item->nama_supplier ?? '-' }}</td>
                    <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    <td>{{ $item->tanggal_pembelian ? \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y') : '-' }}</td>
                    <td>
                        <span class="badge {{ $item->status == 'Belum Lunas' ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('keuangan.penagihan') }}" class="btn btn-sm btn-primary rounded-pill">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Tidak ada tagihan jatuh tempo</td>
                </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctxFinance = document.getElementById('financeChart');
        if (!ctxFinance) return;

        const chartDays = @json($chartDays ?? []);
        const pendapatanData = @json($pendapatanData ?? []);
        const pengeluaranData = @json($pengeluaranData ?? []);

        new Chart(ctxFinance, {
            type: 'line',
            data: {
                labels: chartDays,
                datasets: [
                    {
                        label: 'Pendapatan (Kas Masuk)',
                        data: pendapatanData,
                        borderColor: '#127369',
                        backgroundColor: 'rgba(18, 115, 105, 0.12)',
                        pointBackgroundColor: '#127369',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#127369',
                        pointRadius: 4,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35
                    },
                    {
                        label: 'Pengeluaran (Operasional)',
                        data: pengeluaranData,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.08)',
                        pointBackgroundColor: '#dc3545',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#dc3545',
                        pointRadius: 4,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Space Grotesk', size: 13, weight: '600' },
                            usePointStyle: true,
                            padding: 18
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1d2433',
                        titleFont: { family: 'Space Grotesk', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Space Grotesk', size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + Number(context.parsed.y).toLocaleString('id-ID');
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Space Grotesk', size: 12 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0ec' },
                        ticks: {
                            font: { family: 'Space Grotesk', size: 12 },
                            callback: function(value) {
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>

@endsection