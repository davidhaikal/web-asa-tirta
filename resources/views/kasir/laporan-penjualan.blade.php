@extends('layouts.app', [
    'title' => 'Laporan Penjualan',
    'subtitle' => 'Kasir > Laporan > Penjualan',
])

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Laporan Penjualan</h2>
            <p class="text-muted mb-0">Filter: {{ $labelPeriode }}</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Transaksi</h6>
                    <h3 class="fw-bold text-primary">{{ $totalTransaksi }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Pendapatan</h6>
                    <h3 class="fw-bold text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Produk Terjual</h6>
                    <h3 class="fw-bold text-info">{{ $totalProdukTerjual }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Lunas / Pending</h6>
                    <h3 class="fw-bold">
                        <span class="text-success">{{ $lunasCount }}</span>
                        <span class="text-muted">/</span>
                        <span class="text-danger">{{ $pendingCount }}</span>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('kasir.laporan-penjualan') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Periode</label>
                    <select name="periode" class="form-select" onchange="this.form.submit()">
                        <option value="hari" {{ $periode === 'hari' ? 'selected' : '' }}>Harian</option>
                        <option value="minggu" {{ $periode === 'minggu' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulan" {{ $periode === 'bulan' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('kasir.laporan-penjualan') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold">Detail Penjualan — {{ $labelPeriode }}</h5>
                <button onclick="window.print()" class="btn btn-outline-primary btn-sm">Cetak Laporan</button>
            </div>

            @if ($penjualan->isEmpty())
                <p class="text-muted text-center py-4">Tidak ada data penjualan pada periode ini.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan as $p)
                                <tr>
                                    <td class="fw-bold">{{ $p->kode }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $p->pelanggan }}</td>
                                    <td>
                                        @foreach ($p->detailPenjualans as $d)
                                            <span class="badge bg-light text-dark">{{ $d->produk->nama_produk }} x{{ $d->jumlah }}</span>
                                        @endforeach
                                    </td>
                                    <td>Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-secondary">{{ strtoupper($p->metode) }}</span></td>
                                    <td>
                                        @if ($p->status === 'lunas')
                                            <span class="badge bg-success">LUNAS</span>
                                        @elseif ($p->status === 'pending')
                                            <span class="badge bg-danger">BELUM LUNAS</span>
                                        @else
                                            <span class="badge bg-dark">BATAL</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="4">TOTAL</td>
                                <td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $penjualan->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection