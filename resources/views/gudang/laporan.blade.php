@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="/gudang/dashboard" class="text-decoration-none text-muted">Gudang</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">Laporan Gudang</li>
                </ol>
            </nav>
            <h2 class="h3 fw-bold text-dark mb-0">Laporan Gudang</h2>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex gap-2">
            <a href="{{ route('gudang.laporan.print', request()->query()) }}" target="_blank" class="btn btn-outline-secondary btn-action d-flex align-items-center gap-2">
                <i class="bi bi-printer"></i> Print
            </a>
            <a href="{{ route('gudang.laporan.excel', request()->query()) }}" class="btn btn-outline-success btn-action d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('gudang.laporan.pdf', request()->query()) }}" class="btn btn-danger btn-action d-flex align-items-center gap-2 text-white">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- 5 Kartu Statistik -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-4">
        
        <!-- Total Produk -->
        <div class="col">
            <div class="stat-card border-0 shadow-sm d-flex align-items-center p-3 h-100">
                <div class="stat-icon-wrapper bg-blue-soft text-blue-deep me-3">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <div>
                    <span class="stat-label text-muted d-block small">Total Produk</span>
                    <strong class="stat-value text-dark d-block h4 mb-0">{{ $stats['total_produk'] }}</strong>
                    <span class="stat-subtext text-muted d-block small">Semua produk</span>
                </div>
            </div>
        </div>

        <!-- Barang Masuk -->
        <div class="col">
            <div class="stat-card border-0 shadow-sm d-flex align-items-center p-3 h-100">
                <div class="stat-icon-wrapper bg-green-soft text-green-deep me-3">
                    <i class="bi bi-box-arrow-in-down fs-4"></i>
                </div>
                <div>
                    <span class="stat-label text-muted d-block small">Barang Masuk</span>
                    <strong class="stat-value text-dark d-block h4 mb-0">{{ number_format($stats['total_masuk'], 0, ',', '.') }} Pcs</strong>
                    <span class="stat-subtext text-muted d-block small">Total masuk</span>
                </div>
            </div>
        </div>

        <!-- Barang Keluar -->
        <div class="col">
            <div class="stat-card border-0 shadow-sm d-flex align-items-center p-3 h-100">
                <div class="stat-icon-wrapper bg-orange-soft text-orange-deep me-3">
                    <i class="bi bi-box-arrow-up fs-4"></i>
                </div>
                <div>
                    <span class="stat-label text-muted d-block small">Barang Keluar</span>
                    <strong class="stat-value text-dark d-block h4 mb-0">{{ number_format($stats['total_keluar'], 0, ',', '.') }} Pcs</strong>
                    <span class="stat-subtext text-muted d-block small">Total keluar</span>
                </div>
            </div>
        </div>

        <!-- Barang Rusak -->
        <div class="col">
            <div class="stat-card border-0 shadow-sm d-flex align-items-center p-3 h-100">
                <div class="stat-icon-wrapper bg-red-soft text-red-deep me-3">
                    <i class="bi bi-exclamation-triangle fs-4"></i>
                </div>
                <div>
                    <span class="stat-label text-muted d-block small">Barang Rusak</span>
                    <strong class="stat-value text-dark d-block h4 mb-0">{{ number_format($stats['total_rusak'], 0, ',', '.') }} Pcs</strong>
                    <span class="stat-subtext text-muted d-block small">Total rusak</span>
                </div>
            </div>
        </div>

        <!-- Permintaan Stok -->
        <div class="col">
            <div class="stat-card border-0 shadow-sm d-flex align-items-center p-3 h-100">
                <div class="stat-icon-wrapper bg-purple-soft text-purple-deep me-3">
                    <i class="bi bi-clipboard fs-4"></i>
                </div>
                <div>
                    <span class="stat-label text-muted d-block small">Permintaan Stok</span>
                    <strong class="stat-value text-dark d-block h4 mb-0">{{ $stats['total_permintaan'] }}</strong>
                    <span class="stat-subtext text-muted d-block small">Total permintaan</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Card Filter Data -->
    <div class="card border-0 shadow-sm mb-4 filter-card">
        <div class="card-header bg-white border-0 py-3 d-flex align-items-center gap-2">
            <i class="bi bi-funnel text-primary fs-5"></i>
            <h5 class="mb-0 fw-bold text-dark">Filter Data</h5>
        </div>
        <div class="card-body pt-0">
            <form action="{{ route('gudang.laporan') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-2">
                    <label for="tanggal_awal" class="form-label small fw-bold text-muted mb-1">Tanggal Awal</label>
                    <input type="date" class="form-control form-control-custom" id="tanggal_awal" name="tanggal_awal" value="{{ request('tanggal_awal') }}">
                </div>
                <div class="col-md-2">
                    <label for="tanggal_akhir" class="form-label small fw-bold text-muted mb-1">Tanggal Akhir</label>
                    <input type="date" class="form-control form-control-custom" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                </div>
                <div class="col-md-2">
                    <label for="jenis_laporan" class="form-label small fw-bold text-muted mb-1">Jenis Laporan</label>
                    <select class="form-select form-control-custom" id="jenis_laporan" name="jenis_laporan">
                        <option value="Semua" {{ request('jenis_laporan') == 'Semua' ? 'selected' : '' }}>Semua</option>
                        <option value="Barang Masuk" {{ request('jenis_laporan') == 'Barang Masuk' ? 'selected' : '' }}>Barang Masuk</option>
                        <option value="Barang Keluar" {{ request('jenis_laporan') == 'Barang Keluar' ? 'selected' : '' }}>Barang Keluar</option>
                        <option value="Permintaan Stok" {{ request('jenis_laporan') == 'Permintaan Stok' ? 'selected' : '' }}>Permintaan Stok</option>
                        <option value="Barang Rusak" {{ request('jenis_laporan') == 'Barang Rusak' ? 'selected' : '' }}>Barang Rusak</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="produk_id" class="form-label small fw-bold text-muted mb-1">Produk</label>
                    <select class="form-select form-control-custom" id="produk_id" name="produk_id">
                        <option value="">Cari produk...</option>
                        @foreach ($produk as $p)
                            <option value="{{ $p->id }}" {{ request('produk_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_produk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <a href="{{ route('gudang.laporan') }}" class="btn btn-light btn-action flex-fill d-flex align-items-center justify-content-center gap-2 border">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary btn-action flex-fill d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data Laporan -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="asa-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 12%">Tanggal</th>
                        <th style="width: 18%">No Referensi</th>
                        <th style="width: 15%">Jenis Laporan</th>
                        <th style="width: 20%">Produk</th>
                        <th style="width: 10%" class="text-end">Qty (Kardus)</th>
                        <th style="width: 10%" class="text-end">Jumlah (Pcs)</th>
                        <th style="width: 10%" class="text-center">Status</th>
                        <th style="width: 10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paginatedData as $key => $item)
                        @php
                            // Format tanggal ke Bahasa Indonesia (misal: 30 Jul 2026)
                            $dateStr = date('d M Y', strtotime($item->tanggal));
                            $dateStrIndo = strtr($dateStr, [
                                'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
                                'Jul' => 'Jul', 'Aug' => 'Ags', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
                            ]);

                            // Set warna badge berdasarkan jenis laporan
                            $badgeTypeClass = 'bg-light text-dark';
                            if ($item->jenis_laporan === 'Barang Masuk') {
                                $badgeTypeClass = 'badge-masuk-theme';
                            } elseif ($item->jenis_laporan === 'Barang Keluar') {
                                $badgeTypeClass = 'badge-keluar-theme';
                            } elseif ($item->jenis_laporan === 'Permintaan Stok') {
                                $badgeTypeClass = 'badge-permintaan-theme';
                            } elseif ($item->jenis_laporan === 'Barang Rusak') {
                                $badgeTypeClass = 'badge-rusak-theme';
                            }

                            // Set link detail aksi
                            $detailUrl = '#';
                            if ($item->jenis_laporan === 'Barang Masuk') {
                                $detailUrl = "/gudang/barang-masuk/detail/{$item->id}";
                            } elseif ($item->jenis_laporan === 'Barang Keluar') {
                                $detailUrl = "/gudang/barang-keluar/detail/{$item->id}";
                            } elseif ($item->jenis_laporan === 'Barang Rusak') {
                                $detailUrl = "/gudang/barang-rusak/detail/{$item->id}";
                            } elseif ($item->jenis_laporan === 'Permintaan Stok') {
                                $detailUrl = "/gudang/permintaan-stok/edit/{$item->id}";
                            }
                        @endphp
                        <tr>
                            <td>{{ ($paginatedData->currentPage() - 1) * $paginatedData->perPage() + $loop->iteration }}</td>
                            <td class="text-muted">{{ $dateStrIndo }}</td>
                            <td class="fw-semibold text-dark">{{ $item->no_referensi }}</td>
                            <td>
                                <span class="badge badge-custom {{ $badgeTypeClass }}">
                                    {{ $item->jenis_laporan }}
                                </span>
                            </td>
                            <td class="text-dark">{{ $item->produk }}</td>
                            <td class="text-end text-dark">{{ $item->qty ?? 0 }}</td>
                            <td class="text-end fw-bold text-dark">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if (strtolower($item->status) === 'selesai')
                                    <span class="badge badge-custom badge-status-selesai">Selesai</span>
                                @elseif (strtolower($item->status) === 'menunggu')
                                    <span class="badge badge-custom badge-status-menunggu">Menunggu</span>
                                @else
                                    <span class="badge badge-custom bg-secondary">{{ $item->status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ $detailUrl }}" class="btn btn-action-view btn-sm d-inline-flex align-items-center justify-content-center">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                Tidak ada data laporan gudang untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($paginatedData->total() > 0)
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="small text-muted">
                    Menampilkan {{ $paginatedData->firstItem() ?? 0 }}–{{ $paginatedData->lastItem() ?? 0 }} dari {{ $paginatedData->total() ?? 0 }} data
                </span>
                <div>
                    {{ $paginatedData->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

<style>
    /* CSS Kustom Premium untuk Laporan Gudang */
    .btn-action {
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
    }

    /* Kartu Statistik */
    .stat-card {
        background-color: #ffffff;
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(148, 163, 184, 0.15) !important;
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* HSL Colors */
    .bg-blue-soft { background-color: #eff6ff; }
    .text-blue-deep { color: #2563eb; }
    
    .bg-green-soft { background-color: #f0fdf4; }
    .text-green-deep { color: #16a34a; }
    
    .bg-orange-soft { background-color: #fff7ed; }
    .text-orange-deep { color: #ea580c; }
    
    .bg-red-soft { background-color: #fef2f2; }
    .text-red-deep { color: #dc2626; }
    
    .bg-purple-soft { background-color: #f5f3ff; }
    .text-purple-deep { color: #7c3aed; }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.2px;
    }

    .stat-value {
        font-size: 20px;
        letter-spacing: -0.5px;
    }

    .stat-subtext {
        font-size: 11px;
    }

    /* Card Filter */
    .filter-card {
        border-radius: 16px;
    }

    .form-control-custom {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control-custom:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    /* Badges */
    .badge-custom {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.2px;
        display: inline-block;
    }

    /* Jenis Laporan Badges */
    .badge-masuk-theme {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-keluar-theme {
        background-color: #ffedd5;
        color: #9a3412;
    }

    .badge-permintaan-theme {
        background-color: #f3e8ff;
        color: #5b21b6;
    }

    .badge-rusak-theme {
        background-color: #fee2e2;
        color: #991b1b;
    }

    /* Status Badges */
    .badge-status-selesai {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-status-menunggu {
        background-color: #fef3c7;
        color: #92400e;
    }

    /* Tombol Aksi */
    .btn-action-view {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background-color: #ffffff;
        color: #2563eb;
        transition: all 0.2s ease;
    }

    .btn-action-view:hover {
        background-color: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
        transform: scale(1.05);
    }

    /* Pagination Override */
    .pagination {
        margin-bottom: 0;
        gap: 4px;
    }

    .page-item .page-link {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-weight: 500;
        padding: 8px 12px;
    }

    .page-item.active .page-link {
        background-color: #2563eb;
        border-color: #2563eb;
        color: #ffffff;
    }
</style>
@endsection
