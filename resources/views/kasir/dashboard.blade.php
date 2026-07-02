@extends('layouts.app', [
    'title' => 'Dashboard Kasir',
    'subtitle' => 'Ringkasan aktivitas penjualan hari ini | ' . now()->format('d M Y'),
])

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Dashboard Kasir</h2>
            <p class="text-muted mb-0">Ringkasan aktivitas penjualan hari ini</p>
        </div>
        <a href="{{ route('kasir.transaksi') }}" class="btn btn-primary px-4 py-2 rounded-4 shadow-sm">
            + Transaksi Baru
        </a>
    </div>

    <!-- Statistik -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Transaksi Hari Ini</h6>
                    <h2 class="fw-bold text-primary">{{ $totalTransaksi }}</h2>
                    <small class="text-muted">transaksi</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted">Pendapatan Hari Ini</h6>
                    <h2 class="fw-bold text-success">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h2>
                    <small class="text-muted">total penjualan</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted">Produk Terjual</h6>
                    <h2 class="fw-bold text-warning">{{ $produkTerjual }}</h2>
                    <small class="text-muted">unit</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted">Nota Dicetak</h6>
                    <h2 class="fw-bold text-info">{{ $notaDicetak }}</h2>
                    <small class="text-muted">nota</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik & Stok -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Grafik Penjualan 7 Hari</h5>
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Stok Produk</h5>
                    <div style="max-height: 300px; overflow-y: auto;">
                        @foreach ($produkStok as $item)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <strong>{{ $item->nama_produk }}</strong><br>
                                    <small class="text-muted">Rp {{ number_format($item->harga, 0, ',', '.') }}</small>
                                </div>
                                <span class="badge {{ $item->stok > 50 ? 'bg-success' : ($item->stok > 10 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $item->stok }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form PO & Kebutuhan Produksi -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">📝 Buat Purchase Order (PO)</h5>
                    <p class="text-muted small">Buat PO untuk kebutuhan produksi. PO baru berstatus <strong>BELUM BAYAR</strong>.</p>
                    <form action="{{ route('kasir.po.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Produk</label>
                            <select name="produk_id" class="form-select" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($produkStok as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->nama_produk }} (Stok: {{ $item->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Dibutuhkan</label>
                            <input type="number" name="jumlah" class="form-control" min="1" required
                                   placeholder="Misal: 200 karton">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Dibutuhkan</label>
                            <input type="date" name="tanggal_butuh" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"
                                      placeholder="Catatan tambahan..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Kirim Purchase Order</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">🏭 Kebutuhan Produksi Bulan Ini</h5>
                        @if ($totalKebutuhanProduksi > 0)
                            <span class="badge bg-primary fs-6">Total: {{ $totalKebutuhanProduksi }} unit</span>
                        @endif
                    </div>
                    @if ($kebutuhanProduksi->isEmpty())
                        <p class="text-muted">Belum ada PO untuk bulan ini.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kode PO</th>
                                        <th>Produk</th>
                                        <th>Jumlah</th>
                                        <th>Tgl Butuh</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kebutuhanProduksi as $po)
                                        <tr>
                                            <td>{{ $po->kode_po }}</td>
                                            <td>{{ $po->produk->nama_produk }}</td>
                                            <td>{{ $po->jumlah }}</td>
                                            <td>{{ \Carbon\Carbon::parse($po->tanggal_butuh)->format('d M Y') }}</td>
                                            <td>
                                                @if ($po->status === 'menunggu')
                                                    <span class="badge bg-danger">BELUM BAYAR</span>
                                                @elseif ($po->status === 'disetujui')
                                                    <span class="badge bg-info">Disetujui</span>
                                                @elseif ($po->status === 'selesai')
                                                    <span class="badge bg-success">LUNAS</span>
                                                @else
                                                    <span class="badge bg-dark">{{ strtoupper($po->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="2">TOTAL</td>
                                        <td>{{ $totalKebutuhanProduksi }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Transaksi Terbaru</h5>
            @if ($transaksiTerbaru->isEmpty())
                <p class="text-muted">Belum ada transaksi hari ini.</p>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksiTerbaru as $trx)
                                <tr>
                                    <td>{{ $trx->kode }}</td>
                                    <td>{{ $trx->pelanggan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ strtoupper($trx->metode) }}</span>
                                    </td>
                                    <td>
                                        @if ($trx->status === 'lunas')
                                            <span class="badge bg-success">LUNAS</span>
                                        @elseif ($trx->status === 'pending')
                                            <span class="badge bg-danger">BELUM LUNAS</span>
                                        @else
                                            <span class="badge bg-dark">BATAL</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');
const chartDays = @json($chartDays);
const chartData = @json($chartData);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartDays,
        datasets: [{
            label: 'Penjualan (Rp)',
            data: chartData,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

@endsection