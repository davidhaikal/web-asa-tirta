@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary">
                🧪 Dashboard Quality Control
            </h2>
            <p class="text-muted mb-0">
                Monitoring pemeriksaan kualitas produk ASA Tirta
            </p>
        </div>

        <div>
            <span class="badge bg-primary fs-6">
                {{ date('d M Y') }}
            </span>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row">

        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ url('/qc/pemeriksaan') }}" class="text-decoration-none">
                <div class="card border-0 shadow rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">
                                    Total Dicek
                                </small>

                                <h3 class="fw-bold text-primary mb-0 mt-1">
                                    {{ $totalQc ?? 0 }}
                                </h3>
                            </div>

                            <div class="fs-1">
                                📊
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ url('/qc/lolos') }}" class="text-decoration-none">
                <div class="card border-0 shadow rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">
                                    Produk Lolos
                                </small>

                                <h3 class="fw-bold text-success mb-0 mt-1">
                                    {{ $totalLolos ?? 0 }}
                                </h3>
                            </div>

                            <div class="fs-1">
                                ✅
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ url('/qc/reject') }}" class="text-decoration-none">
                <div class="card border-0 shadow rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">
                                    Produk Reject
                                </small>

                                <h3 class="fw-bold text-danger mb-0 mt-1">
                                    {{ $totalReject ?? 0 }}
                                </h3>
                            </div>

                            <div class="fs-1">
                                ❌
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ url('/qc/pemeriksaan?tanggal=' . now()->toDateString()) }}" class="text-decoration-none">
                <div class="card border-0 shadow rounded-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">
                                    Pemeriksaan Hari Ini
                                </small>

                                <h3 class="fw-bold text-warning mb-0 mt-1">
                                    {{ $hariIni ?? 0 }}
                                </h3>
                            </div>

                            <div class="fs-1">
                                🧪
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <!-- Filter -->   
    <div class="card shadow-sm border-0 rounded-4 mb-4">

        <div class="card-body">

            <form action="{{ url('/qc/dashboard') }}" method="GET">

                <div class="row align-items-end">

                    <!-- Filter Tanggal -->
                    <div class="col-lg-3 col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            📅 Tanggal
                        </label>

                        <input type="date"
                            name="tanggal"
                            class="form-control"
                            value="{{ request('tanggal') }}">

                    </div>

                    <!-- Filter Bulan -->
                    <div class="col-lg-3 col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            📆 Bulan
                        </label>

                        <input type="month"
                            name="bulan"
                            class="form-control"
                            value="{{ request('bulan') }}">

                    </div>

                    <!-- Tombol Filter -->
                    <div class="col-lg-2 col-md-6 mb-3 d-flex gap-2">

                        <button type="submit"
                                class="btn btn-primary w-100">

                            🔍 Filter

                        </button>

                        @if(request('tanggal') || request('bulan'))
                            <a href="{{ url('/qc/dashboard') }}" class="btn btn-outline-secondary" title="Reset Filter">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        @endif

                    </div>

                    <!-- Export -->
                    <div class="col-lg-4 col-md-6 mb-3 text-lg-end">

                        <a href="{{ url('/qc/export/excel?' . http_build_query(request()->only(['tanggal', 'bulan']))) }}"
                        class="btn btn-success">

                            📊 Excel

                        </a>

                        <a href="{{ url('/qc/export/pdf?' . http_build_query(request()->only(['tanggal', 'bulan']))) }}"
                        class="btn btn-danger">

                            📄 PDF

                        </a>

                        <a href="{{ url('/qc/cetak?' . http_build_query(request()->only(['tanggal', 'bulan']))) }}"
                        target="_blank"
                        class="btn btn-secondary">

                            🖨 Cetak

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- Aktivitas -->
    <div class="row">

        <div class="col-lg-8 mb-4">

            <div class="card border-0 shadow rounded-4">

                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        📋 Pemeriksaan Terbaru
                    </h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

                            <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                                <th>Pemeriksa</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($dataQc ?? [] as $q)

                            <tr>

                                <!-- No -->
                                <td>{{ $loop->iteration }}</td>

                                <!-- Tanggal -->
                                <td>{{ $q->created_at ? $q->created_at->format('d M Y H:i') : '-' }}</td>

                                <!-- Produk -->
                                <td class="fw-semibold">{{ $q->produksi->produk->nama_produk ?? '-' }}</td>

                                <!-- Jumlah -->
                                <td>{{ $q->produksi->jumlah_produksi ?? 0 }}</td>

                                <!-- Keterangan -->
                                <td>{{ $q->keterangan ?? '-' }}</td>

                                <!-- Pemeriksa -->
                                <td>{{ auth()->check() ? auth()->user()->name : 'QC User' }}</td>

                                <!-- Status -->
                        <td>

                            @if($q->hasil == 'Layak')

                                <span class="badge bg-success text-white">
                                    Lolos
                                </span>

                            @elseif($q->hasil == 'Tidak Layak')

                                <span class="badge bg-danger text-white">
                                    Reject
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    -
                                </span>

                            @endif

                        </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="6" class="text-center">
                                Belum ada data pemeriksaan
                            </td>
                        </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow rounded-4 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">📊 Proporsi Kualitas</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="qcPieChart"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('qcPieChart');

new Chart(ctx, {

    type: 'pie',

    data: {

        labels: [
            'Produk Lolos',
            'Produk Reject'
        ],

        datasets: [{

            data: [
                {{ $totalLolos ?? 0 }},
                {{ $totalReject ?? 0 }}
            ],

            backgroundColor: [
                '#198754',
                '#dc3545'
            ],

            borderColor: '#ffffff',

            borderWidth: 3

        }]

    },

    options: {

        responsive: true,

        plugins: {

            legend: {

                position: 'bottom'

            }

        }

    }

});

</script>

@endsection