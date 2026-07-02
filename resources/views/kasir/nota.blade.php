@extends('layouts.app', [
    'title' => 'Nota Penjualan',
    'subtitle' => 'Kasir > Nota',
])

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Nota Penjualan</h2>
            <p class="text-muted mb-0">Daftar invoice transaksi yang sudah selesai</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('kasir.nota') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Cari Nota</label>
                    <input type="text" name="search" class="form-control" placeholder="Kode transaksi atau nama pelanggan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('kasir.nota') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            @if ($nota->isEmpty())
                <p class="text-muted text-center py-4">Belum ada nota.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($nota as $n)
                                <tr>
                                    <td class="fw-bold">{{ $n->kode }}</td>
                                    <td>{{ $n->pelanggan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($n->tanggal)->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($n->total, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-secondary">{{ strtoupper($n->metode) }}</span></td>
                                    <td>
                                        @if ($n->status === 'lunas')
                                            <span class="badge bg-success">LUNAS</span>
                                        @elseif ($n->status === 'pending')
                                            <span class="badge bg-danger">BELUM LUNAS</span>
                                        @else
                                            <span class="badge bg-dark">BATAL</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('kasir.nota.cetak', $n->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-printer"></i> Cetak Nota
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $nota->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection