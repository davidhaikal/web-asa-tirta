@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold text-danger">
                ❌ Produk Reject
            </h2>

            <p class="text-muted">
                Daftar produk yang tidak lolos pemeriksaan Quality Control
            </p>

        </div>

    </div>

    <!-- Statistik -->

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Produk Reject
                    </h6>

                    <h2 class="fw-bold text-danger">
                        {{ $dataReject->count() }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

    <!-- Tabel -->

    <div class="card border-0 shadow rounded-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">
                🚫 Daftar Produk Reject
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>No</th>
                            <th>Produk</th>
                            <th>Jumlah Produksi</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Tanggal QC</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($dataReject as $q)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $q->produksi->produk->nama_produk ?? '-' }}
                            </td>

                            <td>
                                {{ $q->produksi->jumlah_produksi ?? '-' }}
                            </td>

                            <td>

                                <span class="badge bg-danger">

                                    ❌ Reject

                                </span>

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

                                Belum ada produk reject

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