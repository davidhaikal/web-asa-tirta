@extends('layouts.app')

@section('title', 'Dashboard Driver')

@section('content')
<div class="container-fluid py-4">

    <div class="page-header mb-4">
        <h2>Dashboard</h2>
        <p>Ringkasan aktivitas pengiriman kamu hari ini</p>
    </div>

    <!-- Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-blue">
                <div class="stat-icon">🚚</div>
                <div>
                    <h3>{{ $pengirimanHariIni }}</h3>
                    <span>Pengiriman Hari Ini</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-orange">
                <div class="stat-icon">📦</div>
                <div>
                    <h3>{{ $sedangDikirim }}</h3>
                    <span>Sedang Dikirim</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-green">
                <div class="stat-icon">✅</div>
                <div>
                    <h3>{{ $selesaiHariIni }}</h3>
                    <span>Selesai Hari Ini</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card stat-purple">
                <div class="stat-icon">🔔</div>
                <div>
                    <h3>{{ $menungguKonfirmasi }}</h3>
                    <span>Menunggu Konfirmasi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengiriman Terbaru -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Pengiriman Terbaru</h5>
            <a href="{{ route('driver.pengiriman') }}" class="small text-decoration-none">Lihat Semua</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Alamat</th>
                        <th>Total Barang</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengirimanTerbaru as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->penjualan->kode ?? '-' }}</td>
                            <td>{{ $item->penjualan->pelanggan ?? '-' }}</td>
                            <td class="text-muted small">-</td>
                            <td>{{ $item->penjualan->detailPenjualans->sum('jumlah') ?? '-' }} Dus</td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB</td>
                            <td>
                                <span class="badge-status status-{{ $item->status }}">
                                    {{ \App\Http\Controllers\DriverController::LABEL_STATUS[$item->status] ?? ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('driver.pengiriman', $item->id) }}" class="btn btn-sm btn-primary rounded-pill">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada pengiriman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    .page-header h2 {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
    }

    .page-header p {
        color: #64748b;
        margin-bottom: 0;
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
        height: 100%;
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .stat-blue .stat-icon   { background: #dbeafe; }
    .stat-orange .stat-icon { background: #ffedd5; }
    .stat-green .stat-icon  { background: #dcfce7; }
    .stat-purple .stat-icon { background: #ede9fe; }

    .stat-card h3 {
        margin: 0;
        font-weight: 700;
        font-size: 24px;
        color: #1e293b;
    }

    .stat-card span {
        color: #64748b;
        font-size: 13px;
    }

    .content-card {
        background: #fff;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
    }

    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-baru      { background: #fef3c7; color: #b45309; }
    .status-siap       { background: #dbeafe; color: #1d4ed8; }
    .status-berangkat  { background: #dbeafe; color: #1d4ed8; }
    .status-sampai     { background: #fef9c3; color: #854d0e; }
    .status-selesai    { background: #dcfce7; color: #15803d; }
</style>
@endsection