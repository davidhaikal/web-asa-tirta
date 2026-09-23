@extends('layouts.app')

@section('content')

<div class="dashboard-content px-4">

    <!-- Header Halaman -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">📋 Laporan Quality Control</h2>
            <p class="text-muted mb-0">Riwayat dan statistik hasil pemeriksaan produk ASA Tirta</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('/qc/export/pdf') }}" class="btn btn-danger btn-export px-3 py-2 shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
            </a>
            <a href="{{ url('/qc/export/excel') }}" class="btn btn-success btn-export px-3 py-2 shadow-sm">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Statistik Panel (Desain Premium) -->
    <div class="row g-3 mb-4">
        <!-- Total Pemeriksaan -->
        <div class="col-md-4">
            <div class="stat-card total border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1">Total Pemeriksaan</h6>
                        <h2 class="fw-bold text-primary mb-0">{{ $totalQc ?? 0 }}</h2>
                    </div>
                    <div class="icon-box bg-primary-subtle text-primary rounded-4">
                        <i class="bi bi-clipboard-data-fill"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <!-- Produk Lolos -->
        <div class="col-md-4">
            <div class="stat-card lolos border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1">Produk Lolos</h6>
                        <h2 class="fw-bold text-success mb-0">{{ $totalLolos ?? 0 }}</h2>
                    </div>
                    <div class="icon-box bg-success-subtle text-success rounded-4">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    @php
                        $persenLolos = ($totalQc ?? 0) > 0 ? (($totalLolos ?? 0) / $totalQc) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persenLolos }}%" aria-valuenow="{{ $persenLolos }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <!-- Produk Reject -->
        <div class="col-md-4">
            <div class="stat-card reject border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1">Produk Reject</h6>
                        <h2 class="fw-bold text-danger mb-0">{{ $totalReject ?? 0 }}</h2>
                    </div>
                    <div class="icon-box bg-danger-subtle text-danger rounded-4">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 6px;">
                    @php
                        $persenReject = ($totalQc ?? 0) > 0 ? (($totalReject ?? 0) / $totalQc) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $persenReject }}%" aria-valuenow="{{ $persenReject }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel Filter Tanggal -->
    <div class="content-card mb-4">
        <div class="mb-3">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-funnel-fill text-primary"></i> Rentang Tanggal Laporan
            </h5>
        </div>
        <form action="{{ url('/qc/laporan') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold text-muted mb-1">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" class="form-control py-2 rounded-3 border-secondary-subtle" value="{{ request('tanggal_awal') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold text-muted mb-1">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control py-2 rounded-3 border-secondary-subtle" value="{{ request('tanggal_akhir') }}">
            </div>

            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-search"></i> Filter Data
                </button>
                @if(request('tanggal_awal') || request('tanggal_akhir'))
                    <a href="{{ url('/qc/laporan') }}" class="btn btn-outline-secondary py-2 rounded-3 px-3">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Laporan QC -->
    <div class="content-card p-0 overflow-hidden shadow-sm">
        <div class="table-responsive">
            <table class="asa-table qc-table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="60px" class="text-center">No</th>
                        <th>Produk</th>
                        <th>Jumlah (Pcs)</th>
                        <th>Hasil Pemeriksaan</th>
                        <th>Catatan / Keterangan</th>
                        <th>Tanggal QC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataQc as $q)
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ ($dataQc->currentPage() - 1) * $dataQc->perPage() + $loop->iteration }}</td>
                            <td class="fw-semibold text-slate-800">
                                {{ $q->produksi && $q->produksi->produk ? $q->produksi->produk->nama_produk : '-' }}
                            </td>
                            <td>
                                <span class="fw-bold">{{ $q->produksi ? $q->produksi->jumlah_produksi : 0 }}</span>
                            </td>
                            <td>
                                @if($q->hasil == 'Layak')
                                    <span class="badge-status lolos">
                                        <i class="bi bi-check-circle-fill"></i> Lolos
                                    </span>
                                @else
                                    <span class="badge-status reject">
                                        <i class="bi bi-x-circle-fill"></i> Reject
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted fs-7">{{ $q->keterangan ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $q->created_at->format('Y-m-d') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    Belum ada data laporan pemeriksaan QC.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-4 border-top bg-white">
            <div class="text-muted small">
                Menampilkan {{ $dataQc->firstItem() ?? 0 }}–{{ $dataQc->lastItem() ?? 0 }} dari {{ $dataQc->total() ?? 0 }} data
            </div>
            <div>
                {{ $dataQc->withQueryString()->links() }}
            </div>
        </div>
    </div>

</div>

<style>
/* CSS Kustom Untuk Tampilan Premium ASA-TIRTA Laporan */
.dashboard-content {
    padding-top: 20px;
    padding-bottom: 40px;
}

.content-card {
    background: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    border: none;
}

.btn-export {
    font-weight: 500;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.btn-export:hover {
    transform: translateY(-1px);
}

/* Card Statistik */
.stat-card {
    background: white;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 16px;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
}

.icon-box {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

/* Tabel */
.qc-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.qc-table th {
    background: #1f6feb !important;
    color: white !important;
    font-weight: 600;
    padding: 16px;
    text-transform: capitalize;
    font-size: 13px;
    letter-spacing: 0.2px;
}

.qc-table th:first-child {
    border-top-left-radius: 12px;
}

.qc-table th:last-child {
    border-top-right-radius: 12px;
}

.qc-table td {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
    font-size: 14px;
}

.qc-table tbody tr {
    transition: all 0.2s ease;
}

.qc-table tbody tr:hover {
    background-color: #f8fafc;
}

.qc-table tbody tr:last-child td {
    border-bottom: none;
}

/* Badge Status Styling */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 13px;
    border: 1px solid transparent;
}

.badge-status.lolos {
    background-color: #e8f5e9 !important;
    color: #2e7d32 !important;
    border-color: #c8e6c9 !important;
}

.badge-status.reject {
    background-color: #ffebee !important;
    color: #c62828 !important;
    border-color: #ffcdd2 !important;
}

.fs-7 {
    font-size: 0.85rem;
}
</style>

@endsection