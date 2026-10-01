@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold text-primary">
                📋 Laporan Quality Control
            </h2>

            <p class="text-muted">
                Riwayat hasil pemeriksaan produk ASA Tirta
            </p>

        </div>

        <div>

            <button class="btn btn-success shadow-sm">

                🖨️ Cetak Laporan

            </button>

        </div>

    </div>

    <!-- Statistik -->

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4">

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

            <div class="card border-0 shadow rounded-4">

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

            <div class="card border-0 shadow rounded-4">

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

    <!-- Tabel Laporan -->

    <div class="card border-0 shadow rounded-4">

        <div class="card-header bg-white">

            <h5 class="mb-0 fw-bold">

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
                            <th>Hasil QC</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($dataLolos as $q)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $q->produksi->produk->nama_produk ?? '-' }}
                            </td>

                            <td>

                                @if($q->hasil == 'Layak')

                                    <span class="badge bg-success">

                                        ✅ LOLOS

                                    </span>

                                @else

                                    <span class="badge bg-danger">

                                        ❌ REJECT

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

                            <td colspan="5" class="text-center">

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