@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold text-primary">
                📋 Laporan Quality Control
            </h2>

            <p class="text-muted mb-0">
                Riwayat hasil pemeriksaan produk ASA Tirta
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ url('/qc/export/pdf') }}" class="btn btn-danger">
                📄 Export PDF
            </a>
            <a href="{{ url('/qc/export/excel') }}" class="btn btn-success">
                📊 Export Excel
            </a>
        </div>

    </div>

    <!-- Statistik -->

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Pemeriksaan
                    </h6>

                    <h2 class="fw-bold text-primary">
                        {{ $totalQc ?? 0 }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body">

                    <h6 class="text-muted">
                        Produk Lolos
                    </h6>

                    <h2 class="fw-bold text-success">
                        {{ $totalLolos ?? 0 }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body">

                    <h6 class="text-muted">
                        Produk Reject
                    </h6>

                    <h2 class="fw-bold text-danger">
                        {{ $totalReject ?? 0 }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

    <!-- Filter -->

    <div class="card border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body">

            <form action="{{ url('/qc/laporan') }}" method="GET" class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        🔍 Filter Data
                    </button>
                    @if(request('tanggal_awal') || request('tanggal_akhir'))
                        <a href="{{ url('/qc/laporan') }}" class="btn btn-secondary ms-2 w-50">Reset</a>
                    @endif
                </div>

            </form>

        </div>

    </div>

    <!-- Tabel -->

    <div class="card border-0 shadow rounded-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">

                📊 Data Pemeriksaan QC

            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>No</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Hasil</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($dataQc as $q)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $q->produksi->produk->nama_produk ?? '-' }}
                            </td>

                            <td>
                                {{ $q->produksi->jumlah_produksi ?? 0 }}
                            </td>

                            <td>

                                @if($q->hasil == 'Layak')

                                    <span class="badge bg-success">

                                        ✅ Lolos

                                    </span>

                                @else

                                    <span class="badge bg-danger">

                                        ❌ Reject

                                    </span>

                                @endif

                            </td>

                            <td>

                                {{ $q->keterangan }}

                            </td>

                            <td>

                                {{ $q->created_at->format('d M Y') }}

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="6" class="text-center">

                                Belum ada data laporan QC

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection