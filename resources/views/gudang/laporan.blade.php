@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Laporan Gudang
            </h2>

            <p class="text-muted mb-0">
                Ringkasan stok per produk — total mutasi masuk, keluar, dan barang rusak (akumulatif)
            </p>
        </div>

        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak
        </button>

    </div>

    <!-- Ringkasan -->
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Jumlah Produk</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($ringkasan['totalProduk']) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Stok</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($ringkasan['totalStok']) }} Unit</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Nilai Stok</h6>
                    <h3 class="fw-bold mb-0 text-success">
                        Rp {{ number_format($ringkasan['totalNilai'], 0, ',', '.') }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Barang Rusak (Akumulatif)</h6>
                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($ringkasan['totalRusak']) }} Unit</h3>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabel -->
    <div class="card shadow-sm border-0 rounded-4 print-area">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th class="text-end">Masuk</th>
                        <th class="text-end">Keluar</th>
                        <th class="text-end">Rusak</th>
                        <th class="text-end">Stok Saat Ini</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Nilai Stok</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($data as $item)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td class="fw-semibold">
                                {{ $item['nama'] }}
                            </td>

                            <td class="text-end text-success">
                                +{{ number_format($item['masuk']) }}
                            </td>

                            <td class="text-end text-warning">
                                -{{ number_format($item['keluar']) }}
                            </td>

                            <td class="text-end text-danger">
                                -{{ number_format($item['rusak']) }}
                            </td>

                            <td class="text-end">
                                @if($item['stok'] > 50)
                                    <span class="badge bg-success">{{ number_format($item['stok']) }} — AMAN</span>
                                @elseif($item['stok'] > 10)
                                    <span class="badge bg-warning text-dark">{{ number_format($item['stok']) }} — MENIPIS</span>
                                @else
                                    <span class="badge bg-danger">{{ number_format($item['stok']) }} — KRITIS</span>
                                @endif
                            </td>

                            <td class="text-end">
                                Rp {{ number_format($item['harga'], 0, ',', '.') }}
                            </td>

                            <td class="text-end fw-bold">
                                Rp {{ number_format($item['nilai'], 0, ',', '.') }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data produk.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

                @if($data->isNotEmpty())

                    <tfoot>

                        <tr class="table-light fw-bold">
                            <td colspan="5" class="text-end">TOTAL</td>
                            <td class="text-end">{{ number_format($ringkasan['totalStok']) }} Unit</td>
                            <td></td>
                            <td class="text-end">
                                Rp {{ number_format($ringkasan['totalNilai'], 0, ',', '.') }}
                            </td>
                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>

</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .print-area, .print-area * { visibility: visible; }
        .print-area { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>

@endsection
