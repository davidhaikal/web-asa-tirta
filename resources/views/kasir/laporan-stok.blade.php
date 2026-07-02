@extends('layouts.app', [
    'title' => 'Laporan Stok',
    'subtitle' => 'Kasir > Laporan > Stok',
])

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Laporan Stok Gudang</h2>
            <p class="text-muted mb-0">Data stok produk + kartu stok</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Produk</h6>
                    <h3 class="fw-bold text-primary">{{ $totalProduk }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Stok Rendah (<10)</h6>
                    <h3 class="fw-bold text-danger">{{ $totalStokRendah }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Nilai Total Stok</h6>
                    <h3 class="fw-bold text-success">Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Data Stok Produk</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Nilai Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produk as $idx => $p)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="fw-bold">{{ $p->nama_produk }}</td>
                                <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $p->stok > 50 ? 'bg-success' : ($p->stok > 10 ? 'bg-warning' : 'bg-danger') }} fs-6">{{ $p->stok }}</span>
                                </td>
                                <td>Rp {{ number_format($p->stok * $p->harga, 0, ',', '.') }}</td>
                                <td>
                                    @if ($p->stok > 50)
                                        <span class="badge bg-success">AMAN</span>
                                    @elseif ($p->stok > 10)
                                        <span class="badge bg-warning">MENIPIS</span>
                                    @else
                                        <span class="badge bg-danger">KRITIS</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('kasir.laporan-stok', ['produk_id' => $p->id]) }}" class="btn btn-sm btn-outline-info">
                                        Kartu Stok
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($produkTerpilih)
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold">Kartu Stok: {{ $produkTerpilih->nama_produk }}</h5>
                    <span class="badge bg-primary fs-6">Stok Saat Ini: {{ $produkTerpilih->stok }}</span>
                </div>

                @if ($kartuStok->isEmpty())
                    <p class="text-muted text-center py-3">Belum ada riwayat stok untuk produk ini.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $saldo = 0; @endphp
                                @foreach ($kartuStok as $ks)
                                    <tr>
                                        <td>{{ $ks->created_at ? \Carbon\Carbon::parse($ks->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            @if ($ks->jenis === 'masuk')
                                                <span class="badge bg-success">MASUK</span>
                                            @elseif ($ks->jenis === 'keluar')
                                                <span class="badge bg-danger">KELUAR</span>
                                            @else
                                                <span class="badge bg-secondary">{{ strtoupper($ks->jenis) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ks->jenis === 'masuk')
                                                <span class="text-success">+{{ $ks->jumlah }}</span>
                                            @else
                                                <span class="text-danger">-{{ $ks->jumlah }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $ks->keterangan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection