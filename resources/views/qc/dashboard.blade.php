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
            <div class="card border-0 shadow rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">
                                Total Dicek
                            </small>

                            <h3 class="fw-bold text-primary">
                                {{ $totalQc ?? 0 }}
                            </h3>
                        </div>

                        <div class="fs-1">
                            📊
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">
                                Produk Lolos
                            </small>

                            <h3 class="fw-bold text-success">
                                {{ $totalLolos ?? 0 }}
                            </h3>
                        </div>

                        <div class="fs-1">
                            ✅
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">
                                Produk Reject
                            </small>

                            <h3 class="fw-bold text-danger">
                                {{ $totalReject ?? 0 }}
                            </h3>
                        </div>

                        <div class="fs-1">
                            ❌
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">
                                Pemeriksaan Hari Ini
                            </small>

                            <h3 class="fw-bold text-warning">
                                {{ $hariIni ?? 0 }}
                            </h3>
                        </div>

                        <div class="fs-1">
                            🧪
                        </div>
                    </div>
                </div>
            </div>
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

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Produk</th>
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
                                <td>{{ $q->created_at->format('d M Y H:i') }}</td>

                                <!-- Produk -->
                                <td>{{ $q->produksi->produk->nama_produk ?? '-' }}</td>

                                <!-- Keterangan -->
                                <td>{{ $q->keterangan ?? '-' }}</td>

                                <!-- Pemeriksa -->
                                <td>QC User</td>

                                <!-- Status -->
                                <td>
                                    @if($q->hasil == 'Layak')
                                        <span class="badge bg-success">
                                            Lolos
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Reject
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

        <div class="col-lg-4">

            <div class="card border-0 shadow rounded-4">

                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">
                        🎯 Ringkasan QC
                    </h5>
                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <div class="d-flex justify-content-between">
                            <span>Produk Lolos</span>
                            <span class="text-success fw-bold">
                                {{ $totalLolos ?? 0 }}
                            </span>
                        </div>

                    </div>

                    <div class="mb-3">

                        <div class="d-flex justify-content-between">
                            <span>Produk Reject</span>
                            <span class="text-danger fw-bold">
                                {{ $totalReject ?? 0 }}
                            </span>
                        </div>

                    </div>

                    <hr>

                    <!-- Grafik Lingkaran -->
                    <div class="text-center mb-4">

                        <canvas id="qcPieChart" style="max-width:250px; margin:auto;"></canvas>

                    </div>

                    <div class="alert alert-success">

                        <strong>Target QC</strong><br>

                        Pastikan seluruh produk memenuhi standar kualitas sebelum masuk gudang.

                    </div>

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