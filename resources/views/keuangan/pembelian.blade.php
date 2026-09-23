@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Pembelian Barang
            </h2>

            <p class="text-muted mb-0">
                Kelola data pembelian barang dari supplier
            </p>
        </div>

        <a href="{{ route('pembelian.create') }}" class="btn btn-primary">
            + Tambah Pembelian
        </a>

    </div>

    <!-- Filter -->

    <div class="card shadow-sm border-0 rounded-4 mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Cari nomor pembelian atau supplier...">

                </div>

                <div class="col-md-3">

                    <select class="form-select">

                        <option>Semua Status</option>
                        <option>Lunas</option>
                        <option>Utang</option>

                    </select>

                </div>

                <div class="col-md-3">

                    <input
                        type="date"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <button class="btn btn-success w-100">

                        Filter

                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- Table -->

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-body">

            <table class="asa-table table table-hover align-middle">

                <thead>

                <tr>

                    <th>No</th>
                    <th>No Pembelian</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th width="180" class="text-center">Aksi</th>

                </tr>

                </thead>

                <tbody>

                @forelse($pembelians as $p)
                <tr>

                    <td>{{ ($pembelians->currentPage() - 1) * $pembelians->perPage() + $loop->iteration }}</td>

                    <td>{{ $p->no_transaksi }}</td>

                    <td>{{ \Carbon\Carbon::parse($p->tanggal_pembelian)->format('d M Y') }}</td>

                    <td>{{ $p->supplier }}</td>

                    <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>

                    <td>
                        @if($p->status == 'Lunas')
                            <span class="badge bg-success">
                                Lunas
                            </span>
                        @else
                            <span class="badge bg-danger">
                                {{ $p->status }}
                            </span>
                        @endif
                    </td>

                    <td class="text-center">

                        <a href="{{ route('pembelian.show', $p->id) }}"
                        class="btn btn-info btn-sm text-white"
                        title="Detail">

                            <i class="bi bi-eye-fill"></i>

                        </a>

                        <a href="{{ route('pembelian.edit', $p->id) }}"
                        class="btn btn-warning btn-sm"
                        title="Edit">

                            <i class="bi bi-pencil-square"></i>

                        </a>

                        <form action="{{ route('pembelian.destroy', $p->id) }}"
                            method="POST"
                            class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')"
                                    title="Hapus">

                                <i class="bi bi-trash-fill"></i>

                            </button>

                        </form>

                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada data pembelian.</td>
                </tr>
                @endforelse

                </tbody>

            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                Menampilkan {{ $pembelians->firstItem() ?? 0 }}–{{ $pembelians->lastItem() ?? 0 }} dari {{ $pembelians->total() ?? 0 }} data
            </div>
            <div>
                {{ $pembelians->withQueryString()->links() }}
            </div>
        </div>

    </div>

</div>

</div>

@endsection