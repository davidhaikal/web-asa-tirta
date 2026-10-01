@extends('layouts.kasir', [
    'title' => 'Dashboard Kasir',
    'subtitle' => 'Ringkasan aktivitas penjualan hari ini | ' . now()->format('d M Y'),
])

@section('content')

@php
    // Agregat read-only untuk strip "Perlu ditangani" (tanpa mengubah controller)
    $pendingTransaksi = \App\Models\Penjualan::where('status', 'pending')->count();
    $poBelumBayar = \App\Models\PurchaseOrder::where('status', 'menunggu')->count();
@endphp

<div class="k-page-head">
    <div>
        <h1>Dashboard Kasir</h1>
        <p>Ringkasan aktivitas penjualan hari ini</p>
    </div>
    <a href="{{ route('kasir.transaksi') }}" class="k-btn k-btn-primary"><i class="bi bi-plus-lg"></i> Transaksi Baru</a>
</div>

{{-- ============ STATISTIK HARI INI ============ --}}
<div class="row g-3 mb-1">
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-accent"><i class="bi bi-receipt"></i></span>
            <div>
                <div class="k-stat-label">Total Transaksi Hari Ini</div>
                <div class="k-stat-value v-accent">{{ $totalTransaksi }}</div>
                <div class="k-stat-sub">transaksi</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-green"><i class="bi bi-cash-stack"></i></span>
            <div>
                <div class="k-stat-label">Pendapatan Hari Ini</div>
                <div class="k-stat-value v-green">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
                <div class="k-stat-sub">total penjualan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-amber"><i class="bi bi-box-seam"></i></span>
            <div>
                <div class="k-stat-label">Produk Terjual</div>
                <div class="k-stat-value v-amber">{{ $produkTerjual }}</div>
                <div class="k-stat-sub">unit</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-info"><i class="bi bi-printer"></i></span>
            <div>
                <div class="k-stat-label">Nota Dicetak</div>
                <div class="k-stat-value v-info">{{ $notaDicetak }}</div>
                <div class="k-stat-sub">nota</div>
            </div>
        </div>
    </div>
</div>

{{-- ============ PERLU DITANGANI ============ --}}
<div class="k-card" style="margin-top: 20px;">
    <div class="k-card-body d-flex align-items-center flex-wrap gap-3" style="padding: 16px 20px;">
        <span class="k-stat-icon i-{{ ($pendingTransaksi + $poBelumBayar) > 0 ? 'amber' : 'green' }}" style="width: 38px; height: 38px; font-size: 17px;">
            <i class="bi {{ ($pendingTransaksi + $poBelumBayar) > 0 ? 'bi-bell' : 'bi-check2-circle' }}"></i>
        </span>
        <div class="me-auto">
            <div class="fw-bold" style="font-size: 14.5px;">
                @if ($pendingTransaksi + $poBelumBayar > 0)
                    Perlu ditangani
                @else
                    Semua beres — tidak ada yang tertunda
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2" style="margin-top: 5px;">
                @if ($pendingTransaksi > 0)
                    <span class="k-badge b-pending">{{ $pendingTransaksi }} transaksi belum bayar</span>
                @endif
                @if ($poBelumBayar > 0)
                    <span class="k-badge b-keluar">{{ $poBelumBayar }} PO belum bayar</span>
                @endif
            </div>
        </div>
        @if ($pendingTransaksi + $poBelumBayar > 0)
            <a href="{{ route('kasir.transaksi') }}" class="k-btn k-btn-primary k-btn-sm">Buka Transaksi <i class="bi bi-arrow-right"></i></a>
        @endif
    </div>
</div>

{{-- ============ GRAFIK & STOK ============ --}}
<div class="row g-3 mt-2">
    <div class="col-lg-8">
        <div class="k-card" style="margin-bottom: 0;">
            <div class="k-card-head">
                <div>
                    <h2>Grafik Penjualan 7 Hari</h2>
                    <small>Nilai penjualan per hari</small>
                </div>
            </div>
            <div class="k-card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="k-card" style="margin-bottom: 0;">
            <div class="k-card-head">
                <div>
                    <h2>Stok Produk</h2>
                    <small>Stok kritis (≤10) ditandai merah</small>
                </div>
            </div>
            <div style="max-height: 320px; overflow-y: auto; padding: 10px 20px 14px;">
                @foreach ($produkStok as $item)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2" style="{{ $item->stok <= 10 ? 'background:#fff7f5;margin:0 -12px;padding-left:12px;padding-right:12px;border-radius:8px;' : '' }}">
                        <div>
                            <strong style="font-size: 14px;">{{ $item->nama_produk }}</strong><br>
                            <small class="text-muted">Rp {{ number_format($item->harga, 0, ',', '.') }}</small>
                        </div>
                        <span class="k-badge {{ $item->stok > 50 ? 'b-aman' : ($item->stok > 10 ? 'b-menipis' : 'b-kritis') }}">{{ $item->stok }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ============ FORM PO & KEBUTUHAN PRODUKSI ============ --}}
<div class="row g-3 mt-2">
    <div class="col-lg-6">
        <div class="k-card" style="margin-bottom: 0;">
            <div class="k-card-head">
                <div>
                    <h2><i class="bi bi-cart3 me-2" style="color: var(--k-accent);"></i>Buat Purchase Order (PO)</h2>
                    <small>Untuk kebutuhan produksi. PO baru berstatus <strong>BELUM BAYAR</strong>.</small>
                </div>
            </div>
            <div class="k-card-body">
                <form action="{{ route('kasir.po.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="po_produk">Produk</label>
                        <select id="po_produk" name="produk_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($produkStok as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->nama_produk }} (Stok: {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="po_jumlah">Jumlah Dibutuhkan</label>
                            <input type="number" id="po_jumlah" name="jumlah" class="form-control" min="1" required placeholder="Misal: 200 karton">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="po_tanggal">Tanggal Dibutuhkan</label>
                            <input type="date" id="po_tanggal" name="tanggal_butuh" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="po_catatan">Catatan</label>
                        <textarea id="po_catatan" name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                    <button type="submit" class="k-btn k-btn-primary k-btn-block"><i class="bi bi-send"></i> Kirim Purchase Order</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="k-card" style="margin-bottom: 0;">
            <div class="k-card-head">
                <div>
                    <h2><i class="bi bi-activity me-2" style="color: var(--k-accent);"></i>Kebutuhan Produksi Bulan Ini</h2>
                    <small>PO yang belum selesai untuk {{ now()->format('F Y') }}</small>
                </div>
                @if ($totalKebutuhanProduksi > 0)
                    <span class="k-badge b-accent">Total: {{ $totalKebutuhanProduksi }} unit</span>
                @endif
            </div>
            <div class="table-responsive">
                @if ($kebutuhanProduksi->isEmpty())
                    <p class="k-empty"><i class="bi bi-clipboard-check"></i>Belum ada PO untuk bulan ini.</p>
                @else
                    <table class="k-table">
                        <thead>
                            <tr>
                                <th>Kode PO</th>
                                <th>Produk</th>
                                <th class="t-right">Jumlah</th>
                                <th>Tgl Butuh</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kebutuhanProduksi as $po)
                                <tr>
                                    <td class="fw-semibold">{{ $po->kode_po }}</td>
                                    <td>{{ $po->produk->nama_produk }}</td>
                                    <td class="t-right">{{ $po->jumlah }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->tanggal_butuh)->format('d M Y') }}</td>
                                    <td>
                                        @if ($po->status === 'menunggu')
                                            <span class="k-badge b-pending">BELUM BAYAR</span>
                                        @elseif ($po->status === 'selesai')
                                            <span class="k-badge b-lunas">LUNAS</span>
                                        @else
                                            <span class="k-badge b-batal">{{ strtoupper($po->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="t-right" style="color: var(--k-muted);">TOTAL</td>
                                <td class="t-right">{{ $totalKebutuhanProduksi }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============ TRANSAKSI TERBARU ============ --}}
<div class="k-card" style="margin-top: 20px;">
    <div class="k-card-head">
        <div>
            <h2>Transaksi Terbaru</h2>
            <small>5 transaksi terakhir</small>
        </div>
        <a href="{{ route('kasir.transaksi') }}" class="k-btn k-btn-ghost k-btn-sm no-print">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="table-responsive">
        <table class="k-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th class="t-right">Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th class="t-right no-print">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksiTerbaru as $trx)
                    <tr class="{{ $trx->status === 'pending' ? 'tr-pending' : '' }}">
                        <td class="fw-semibold">{{ $trx->kode }}</td>
                        <td>{{ $trx->pelanggan }}</td>
                        <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}</td>
                        <td class="t-right fw-semibold">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                        <td><span class="k-badge b-method">{{ strtoupper($trx->metode) }}</span></td>
                        <td>
                            @if ($trx->status === 'lunas')
                                <span class="k-badge b-lunas">LUNAS</span>
                            @elseif ($trx->status === 'pending')
                                <span class="k-badge b-pending">BELUM LUNAS</span>
                            @else
                                <span class="k-badge b-batal">BATAL</span>
                            @endif
                        </td>
                        <td class="t-right no-print" style="white-space: nowrap;">
                            @if ($trx->status === 'pending')
                                <form action="{{ route('kasir.transaksi.bayar', $trx->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="metode" value="{{ $trx->metode }}">
                                    <button type="submit" class="k-btn k-btn-success k-btn-sm js-confirm"><i class="bi bi-cash-coin"></i> Konfirmasi Bayar</button>
                                </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="k-empty"><i class="bi bi-receipt"></i>Belum ada transaksi hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    var ctx = document.getElementById('salesChart');
    var chartDays = @json($chartDays);
    var chartData = @json($chartData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartDays,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: chartData,
                borderColor: '#127369',
                backgroundColor: 'rgba(18, 115, 105, 0.12)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#127369'
            }]
        },
        options: {
            plugins: { legend: { display: true } },
            scales: { y: { beginAtZero: true } }
        }
    });
})();
</script>
@endpush

@endsection
