@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Stok Produk
            </h2>

            <p class="text-muted mb-0">
                Riwayat mutasi stok barang (masuk &amp; keluar)
            </p>
        </div>

    </div>

    <!-- Filter Jenis -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">

        <div class="card-body">

            <div class="btn-group" role="group" aria-label="Filter jenis stok">

                <a href="{{ url('/stok') }}"
                   class="btn {{ (!isset($jenis)) ? 'btn-primary' : 'btn-outline-primary' }}">
                    Semua
                </a>

                <a href="{{ url('/stok/masuk') }}"
                   class="btn {{ (isset($jenis) && $jenis === 'masuk') ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi bi-box-arrow-in-down"></i> Masuk
                </a>

                <a href="{{ url('/stok/keluar') }}"
                   class="btn {{ (isset($jenis) && $jenis === 'keluar') ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi bi-box-arrow-up"></i> Keluar
                </a>

            </div>

        </div>

    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($stok as $item)

                        <tr>

                            <td>{{ $loop->iteration + (($stok->currentPage() - 1) * $stok->perPage()) }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y H:i') }}
                            </td>

                            <td class="fw-semibold">
                                {{ $item->produk->nama_produk ?? '-' }}
                            </td>

                            <td>
                                @if($item->jenis === 'masuk')
                                    <span class="badge bg-success">
                                        <i class="bi bi-box-arrow-in-down"></i> Masuk
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-box-arrow-up"></i> Keluar
                                    </span>
                                @endif
                            </td>

                            <td class="fw-bold">
                                {{ number_format($item->jumlah) }}
                            </td>

                            <td class="text-muted">
                                {{ $item->keterangan ?? '-' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada riwayat stok.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="d-flex justify-content-end">
                {{ $stok->links() }}
            </div>

        </div>

    </div>

</div>

@endsection
