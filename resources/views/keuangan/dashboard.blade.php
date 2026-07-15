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
        background: linear-gradient(135deg, #0d6efd, #3b8bff) !important;
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
        background-color: #0d6efd !important;
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

            <p class="text-muted mb-0">
                Ringkasan Pembelian dan Pembayaran Utang
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
            <div class="card-modern bg-blue">
                <div class="d-flex justify-content-between">
                    <div>
                        <p>Total Pendapatan</p>
                        <h3 class="fw-bold">Rp {{ $totalPendapatan ?? '125 JT' }}</h3>
                    </div>
                    <div>
                        <i class="bi bi-cash-coin fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-modern bg-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1">Total Lunas</p>
                        <h3 class="fw-bold">Rp {{ $totalLunas ?? '100' }}</h3>
                    </div>
                    <div>
                        <i class="bi bi-check-circle-fill fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-modern bg-red">
                <div class="d-flex justify-content-between">
                    <div>
                        <p>Total Piutang</p>
                        <h3 class="fw-bold">Rp {{ $totalPiutang ?? '32 JT' }}</h3>
                    </div>
                    <div>
                        <i class="bi bi-wallet2 fs-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-modern bg-orange">
                <div class="d-flex justify-content-between">
                    <div>
                        <p>Tagihan Pending</p>
                        <h3 class="fw-bold">{{ $tagihanPending ?? 18 }}</h3>
                    </div>
                    <div>
                        <i class="bi bi-receipt-cutoff fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="chart-box">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h5 class="fw-bold mb-1">
                    Grafik Pembelian Barang
                </h5>

                <small class="text-muted">
                    Statistik pembelian setiap bulan
                </small>

            </div>

            <div>

                <span class="badge bg-primary">
                    Tahun {{ date('Y') }}
                </span>

            </div>

        </div>

        <canvas id="chartPembelian"></canvas>

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

                <tr>

                    <td>PB-0001</td>

                    <td>PT Tirta Abadi</td>

                    <td>Rp 5.000.000</td>

                    <td>12 Juli 2026</td>

                    <td>

                        <span class="badge bg-danger">

                            Belum Lunas

                        </span>

                    </td>

                    <td>

                        <button class="btn btn-sm btn-primary rounded-pill">

                            Detail

                        </button>

                    </td>

                </tr>

                <tr>

                    <td>PB-0002</td>

                    <td>CV Maju Jaya</td>

                    <td>Rp 2.300.000</td>

                    <td>18 Juli 2026</td>

                    <td>

                        <span class="badge bg-warning text-dark">

                            Sebagian

                        </span>

                    </td>

                    <td>

                        <button class="btn btn-sm btn-success rounded-pill">

                            Bayar

                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

<script>
    const ctxPembelian = document.getElementById('chartPembelian');
        new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'Mei',
                'Jun'
            ],

            datasets: [{

                label: 'Total Pembelian',

                data: [
                    15,
                    18,
                    12,
                    20,
                    17,
                    25
                ],

                borderWidth: 1,
                borderRadius: 8

            }]
        },

        options: {

            responsive: true,

            plugins: {

                legend: {
                    display: true
                }

            },

            scales: {

                y: {
                    beginAtZero: true
                }

            }

        }

    });
</script>

@endsection