@extends('layouts.app')

@section('title', 'Laporan Manajemen')

@section('content')

<div class="produk-box mb-4">

    <h5 class="fw-bold mb-3">
        <i class="bi bi-funnel-fill text-primary"></i>
        Filter Laporan
    </h5>

    <form action="{{ route('manajemen.laporan') }}" method="GET" class="row g-3 align-items-end">

        <div class="col-md-3">
            <label class="form-label small text-muted">Jenis Laporan</label>
            <select name="jenis" class="form-select" onchange="this.form.submit()">
                <option value="masuk"     {{ $jenis == 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="keluar"    {{ $jenis == 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
                <option value="rusak"     {{ $jenis == 'rusak' ? 'selected' : '' }}>Barang Rusak</option>
                <option value="penjualan" {{ $jenis == 'penjualan' ? 'selected' : '' }}>Penjualan</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted">Dari Tanggal</label>
            <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control">
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted">Sampai Tanggal</label>
            <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control">
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted">Produk</label>
            <select name="produk" class="form-select">
                <option value="">Semua Produk</option>
                @foreach($daftarProduk as $p)
                    <option value="{{ $p }}" {{ request('produk') == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label small text-muted">Satuan</label>
            <select name="satuan" class="form-select">
                <option value="">Semua Satuan</option>
                @foreach($daftarSatuan as $s)
                    <option value="{{ $s }}" {{ request('satuan') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-1 d-flex gap-2">
            <button type="submit" class="btn btn-primary" title="Filter">
                <i class="bi bi-funnel"></i>
            </button>
            <a href="{{ route('manajemen.laporan', ['jenis' => $jenis]) }}" class="btn btn-outline-secondary" title="Reset">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>

        <div class="col-12">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control"
                   placeholder="🔍 Cari produk atau nomor transaksi...">
        </div>

    </form>

</div>

{{-- ============================
     RINGKASAN — dihitung langsung dari hasil filter, bukan angka statis
============================ --}}
<div class="ringkasan-bar mb-4">

    <div class="ringkasan-item">
        <div class="r-label">Total Transaksi</div>
        <div class="r-value">{{ number_format($totalTransaksi) }}</div>
        <div class="r-sub">Transaksi</div>
    </div>

    <div class="ringkasan-item">
        <div class="r-label">Total {{ $labelJenis }}</div>
        <div class="r-value">{{ number_format($totalJumlah) }}</div>
        <div class="r-sub">Item</div>
    </div>

    <div class="ringkasan-item">
        <div class="r-label">Total Nilai</div>
        <div class="r-value">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
        <div class="r-sub">Nilai Transaksi</div>
    </div>

    <div class="ringkasan-item">
        <div class="r-label">Rata-rata per Transaksi</div>
        <div class="r-value">Rp {{ number_format($rataRata, 0, ',', '.') }}</div>
        <div class="r-sub">Per Transaksi</div>
    </div>

</div>

{{-- ============================
     TABEL HASIL
============================ --}}
<div class="produk-box">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

        <h5 class="fw-bold mb-0">Hasil Laporan {{ $labelJenis }}</h5>

        <div class="d-flex gap-2">

            <a href="{{ route('manajemen.laporan.export.excel', request()->query()) }}"
               class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>

            <a href="{{ route('manajemen.laporan.export.pdf', request()->query()) }}"
               class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>

            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>

        </div>

    </div>

    <div class="table-responsive">

        <table class="produk-table">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>No. Transaksi</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>{{ $labelPihak }}</th>
                    <th>Nilai (Rp)</th>
                    <th>Catatan</th>
                </tr>
            </thead>

            <tbody>

                @forelse($items as $i => $row)
                <tr>
                    <td>{{ $items->firstItem() + $i }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d M Y') }}</td>
                    <td>{{ $row['no_transaksi'] }}</td>
                    <td>{{ $row['produk'] }}</td>
                    <td>{{ number_format($row['jumlah']) }}</td>
                    <td>{{ $row['satuan'] }}</td>
                    <td>{{ $row['pihak'] }}</td>
                    <td>Rp {{ number_format($row['nilai'], 0, ',', '.') }}</td>
                    <td>{{ $row['catatan'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td>
                </tr>
                @endforelse

            </tbody>

            @if($items->count())
            <tfoot>
                <tr>
                    <td colspan="4" class="fw-bold">TOTAL</td>
                    <td class="fw-bold">{{ number_format($totalJumlah) }}</td>
                    <td colspan="2"></td>
                    <td class="fw-bold">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif

        </table>

    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
        <div class="text-muted small">
            Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} results
        </div>

        {{ $items->links() }}
    </div>

</div>

<style>

.produk-box{
    background:#fff;
    padding:25px;
    border-radius:20px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

.ringkasan-bar{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
}

.ringkasan-item{
    background:#fff;
    border-radius:16px;
    padding:18px 20px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

.r-label{
    font-size:13px;
    color:#64748b;
    font-weight:600;
    margin-bottom:6px;
}

.r-value{
    font-size:22px;
    font-weight:800;
    color:#0f172a;
}

.r-sub{
    font-size:12px;
    color:#94a3b8;
    margin-top:2px;
}

.produk-table{
    width:100%;
    border-collapse:collapse;
}

.produk-table th{
    text-align:left;
    padding:12px;
    background:#f8fafc;
    color:#334155;
    font-size:13px;
    font-weight:600;
    border-bottom:1px solid #e5e7eb;
    white-space:nowrap;
}

.produk-table td{
    padding:12px;
    border-bottom:1px solid #f1f5f9;
    font-size:13.5px;
    color:#1f2937;
    white-space:nowrap;
}

.produk-table tfoot td{
    background:#f8fafc;
    border-top:2px solid #e5e7eb;
}

@media print{
    form, .btn, .pagination, nav{
        display:none !important;
    }
}

</style>

@endsection