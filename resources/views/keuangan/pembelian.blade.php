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

            <form action="{{ url('/pembelian') }}" method="GET" class="row g-3">

                <div class="col-md-6">

                    <input
                        type="text"
                        class="form-control"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nomor pembelian...">

                </div>

                <div class="col-md-3">

                    <button class="btn btn-success w-100">
                        Filter
                    </button>

                </div>

                <div class="col-md-3 text-end">

                    <a href="{{ url('/pembelian') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>

                </div>

            </form>

        </div>

    </div>

    <!-- Table -->

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>No Pembelian</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th width="180">Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($pembelians as $pembelian)

                        <tr>

                            <td>{{ $loop->iteration + (($pembelians->currentPage() - 1) * $pembelians->perPage()) }}</td>

                            <td class="fw-semibold">
                                {{ $pembelian->no_transaksi }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d M Y') }}
                            </td>

                            <td>{{ $pembelian->supplier ?? '-' }}</td>

                            <td class="fw-bold">
                                Rp {{ number_format((float) $pembelian->total_harga, 0, ',', '.') }}
                            </td>

                            <td>
                                @if(strtolower($pembelian->status) === 'lunas')
                                    <span class="badge bg-success">
                                        Lunas
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        Utang
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">

                                <a href="{{ route('pembelian.show', $pembelian->id) }}"
                                   class="btn btn-info btn-sm text-white"
                                   title="Detail">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                <a href="{{ route('pembelian.edit', $pembelian->id) }}"
                                   class="btn btn-warning btn-sm"
                                   title="Edit">

                                    <i class="bi bi-pencil-square"></i>

                                </a>

                                <form action="{{ route('pembelian.destroy', $pembelian->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-danger btn-sm">

                                        <i class="bi bi-trash-fill"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data pembelian.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="d-flex justify-content-end">
                {{ $pembelians->links() }}
            </div>

        </div>

    </div>

</div>

@endsection
