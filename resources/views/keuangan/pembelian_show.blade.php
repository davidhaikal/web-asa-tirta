@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Detail Pembelian
            </h2>

            <p class="text-muted mb-0">
                {{ $pembelian->no_transaksi }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('pembelian.edit', $pembelian->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

    </div>

    <!-- Info Utama -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted mb-1">Nomor Pembelian</label>
                    <div class="fw-bold">{{ $pembelian->no_transaksi }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted mb-1">Status Pembayaran</label>
                    <div>
                        @if(strtolower($pembelian->status) === 'lunas')
                            <span class="badge bg-success">Lunas</span>
                        @else
                            <span class="badge bg-warning text-dark">Utang</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted mb-1">Tanggal Pembelian</label>
                    <div class="fw-semibold">
                        {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d M Y') }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted mb-1">Supplier</label>
                    <div class="fw-semibold">{{ $pembelian->supplier ?? '-' }}</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted mb-1">Jatuh Tempo</label>
                    <div class="fw-semibold">
                        {{ $pembelian->tanggal_jatuh_tempo
                            ? \Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo)->translatedFormat('d M Y')
                            : '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted mb-1">Keterangan</label>
                    <div class="text-muted">{{ $pembelian->keterangan ?? '-' }}</div>
                </div>

            </div>

            <hr>

            <div class="d-flex gap-4">
                <div>
                    <small class="text-muted">Total Harga</small>
                    <div class="h4 fw-bold mb-0">
                        Rp {{ number_format((float) $pembelian->total_harga, 0, ',', '.') }}
                    </div>
                </div>
                <div>
                    <small class="text-muted">Total Bayar</small>
                    <div class="h4 fw-bold mb-0">
                        Rp {{ number_format((float) $pembelian->total_bayar, 0, ',', '.') }}
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Detail Barang -->
    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-white">
            <h4 class="fw-bold mb-0">
                Daftar Barang
            </h4>
        </div>

        <div class="card-body">

            <table class="table table-bordered align-middle">

                <thead class="table-light">

                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end">Subtotal</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($pembelian->details as $detail)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td class="fw-semibold">
                                {{ $detail->produk->nama_produk ?? '-' }}
                            </td>

                            <td class="text-end">{{ number_format($detail->qty) }}</td>

                            <td class="text-end">
                                Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}
                            </td>

                            <td class="text-end fw-bold">
                                Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Belum ada detail barang.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
