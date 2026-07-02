@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h2 class="page-title">Dashboard Marketing 👋</h2>
            <div class="page-subtitle">
                Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>.
                Semoga pekerjaan hari ini berjalan lancar.
            </div>
        </div>

        <div class="col-lg-4 text-end">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-calendar-week fs-3 text-primary"></i>
                    <h6 class="mt-2 mb-0">{{ now()->translatedFormat('l') }}</h6>
                    <small>{{ now()->translatedFormat('d F Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row g-4 mb-4">
        <!-- PO -->
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary-subtle text-primary">
                            <i class="bi bi-cart3 fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <small class="text-muted">PO Hari Ini</small>
                            <h2 class="fw-bold mb-0">{{ $totalPO ?? 12 }}</h2>
                            <small class="text-success">+12%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk -->
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-success-subtle text-success">
                            <i class="bi bi-box-seam fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <small class="text-muted">Produk</small>
                            <h2 class="fw-bold mb-0">{{ $totalProduk ?? 245 }}</h2>
                            <small class="text-success">Tersedia</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice -->
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-warning-subtle text-warning">
                            <i class="bi bi-receipt fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <small class="text-muted">Invoice</small>
                            <h2 class="fw-bold mb-0">{{ $totalInvoice ?? 8 }}</h2>
                            <small class="text-warning">Bulan Ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permintaan -->
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-danger-subtle text-danger">
                            <i class="bi bi-cash-stack fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <small class="text-muted">Permintaan Uang</small>
                            <h2 class="fw-bold mb-0">{{ $permintaanUang ?? 5 }}</h2>
                            <small class="text-danger">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== BANNER ====================== -->
    <div class="card border-0 dashboard-card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="fw-bold">Sistem Informasi Purchase Order</h3>
                    <p class="text-muted">
                        Kelola Purchase Order, stok produk, invoice, serta permintaan uang
                        secara cepat dan efisien.
                    </p>
                </div>
                <div class="col-lg-4 text-end">
                    <i class="bi bi-bar-chart-line-fill" style="font-size:90px;color:#0d6efd;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== WIDGET INFORMASI ====================== -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-primary-subtle">
                            <i class="bi bi-clock-history text-primary fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <small class="text-muted">Jam Server</small>
                            <h5 id="clock" class="fw-bold mb-0">00:00:00</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-success-subtle">
                            <i class="bi bi-person-check text-success fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <small class="text-muted">Login Sebagai</small>
                            <h5 class="fw-bold mb-0">{{ Auth::user()->role ?? 'Marketing' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-warning-subtle">
                            <i class="bi bi-calendar-event text-warning fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <small class="text-muted">Hari Ini</small>
                            <h5 class="fw-bold mb-0">{{ now()->translatedFormat('d F Y') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
        RINGKASAN PROGRESS
    ========================================== -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Progress Purchase Order</h6>
                            <h3 class="fw-bold">78%</h3>
                        </div>
                        <div class="icon-circle bg-primary-subtle">
                            <i class="bi bi-graph-up text-primary fs-3"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height:8px;">
                        <div class="progress-bar bg-primary" style="width:78%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Produk Terjual</h6>
                            <h3 class="fw-bold">1.245</h3>
                        </div>
                        <div class="icon-circle bg-success-subtle">
                            <i class="bi bi-box-seam text-success fs-3"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height:8px;">
                        <div class="progress-bar bg-success" style="width:92%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Target Penjualan</h6>
                            <h3 class="fw-bold">65%</h3>
                        </div>
                        <div class="icon-circle bg-warning-subtle">
                            <i class="bi bi-trophy text-warning fs-3"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height:8px;">
                        <div class="progress-bar bg-warning" style="width:65%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== CONTENT: AKTIVITAS & QUICK ACTION ====================== -->
    <div class="row mt-4">
        <!-- Aktivitas -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm dashboard-card">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold">Aktivitas Terbaru</h5>
                        <a href="#" class="text-decoration-none">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="activity-item">
                        <div class="activity-icon bg-primary-subtle">
                            <i class="bi bi-cart-check text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">PO-0012 berhasil dibuat</h6>
                            <small class="text-muted">5 menit yang lalu</small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon bg-success-subtle">
                            <i class="bi bi-box text-success"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Stok produk diperbarui</h6>
                            <small class="text-muted">15 menit yang lalu</small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon bg-warning-subtle">
                            <i class="bi bi-receipt text-warning"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Invoice berhasil dibuat</h6>
                            <small class="text-muted">1 jam yang lalu</small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon bg-danger-subtle">
                            <i class="bi bi-cash text-danger"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Permintaan uang menunggu validasi</h6>
                            <small class="text-muted">2 jam yang lalu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Action -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm dashboard-card">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold">⚡ Quick Action</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ url('/po') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus"></i> Kirim PO
                        </a>
                        <a href="{{ url('/stok') }}" class="btn btn-success btn-lg">
                            <i class="bi bi-box"></i> Cek Stok
                        </a>
                        <a href="{{ url('/invoice') }}" class="btn btn-warning btn-lg text-white">
                            <i class="bi bi-receipt"></i> Buat Invoice
                        </a>
                        <a href="{{ url('/permintaan-uang') }}" class="btn btn-danger btn-lg">
                            <i class="bi bi-cash-stack"></i> Permintaan Uang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================== GRAFIK & STATUS PO ====================== -->
    <div class="row mt-4">
        <!-- Grafik PO -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm dashboard-card">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-graph-up-arrow text-primary"></i>
                            Grafik Purchase Order
                        </h5>
                        <span class="badge bg-primary">Tahun {{ date('Y') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="poChart" height="90"></canvas>
                </div>
            </div>
        </div>

        <!-- Donut -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm dashboard-card">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold">Status PO</h5>
                </div>
                <div class="card-body text-center">
                    <canvas id="statusChart" height="220"></canvas>
                    <hr>
                    <div class="row">
                        <div class="col">
                            <h5 class="text-success fw-bold">70%</h5>
                            <small class="text-muted">Selesai</small>
                        </div>
                        <div class="col">
                            <h5 class="text-warning fw-bold">20%</h5>
                            <small class="text-muted">Diproses</small>
                        </div>
                        <div class="col">
                            <h5 class="text-danger fw-bold">10%</h5>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
        TRANSAKSI TERBARU & NOTIFIKASI
    ========================================== -->
    <div class="row mt-4">
        <!-- Tabel PO -->
        <div class="col-lg-8">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-table"></i>
                            Purchase Order Terbaru
                        </h5>
                        <a href="#" class="btn btn-sm btn-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No PO</th>
                                    <th>Customer</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PO-00125</td>
                                    <td>PT ABC</td>
                                    <td>20 Juli 2026</td>
                                    <td><span class="badge bg-success">Selesai</span></td>
                                    <td>Rp 5.000.000</td>
                                </tr>
                                <tr>
                                    <td>PO-00126</td>
                                    <td>PT Maju Jaya</td>
                                    <td>21 Juli 2026</td>
                                    <td><span class="badge bg-warning text-dark">Diproses</span></td>
                                    <td>Rp 8.200.000</td>
                                </tr>
                                <tr>
                                    <td>PO-00127</td>
                                    <td>CV Berkah</td>
                                    <td>22 Juli 2026</td>
                                    <td><span class="badge bg-danger">Pending</span></td>
                                    <td>Rp 3.600.000</td>
                                </tr>
                                <tr>
                                    <td>PO-00128</td>
                                    <td>PT Tirta Abadi</td>
                                    <td>23 Juli 2026</td>
                                    <td><span class="badge bg-success">Selesai</span></td>
                                    <td>Rp 6.900.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifikasi -->
        <div class="col-lg-4">
            <div class="card dashboard-card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold">🔔 Notifikasi</h5>
                </div>
                <div class="card-body">
                    <div class="notification-item">
                        <h6>Invoice Baru</h6>
                        <small class="text-muted">Invoice INV-00012 berhasil dibuat.</small>
                    </div>
                    <hr>
                    <div class="notification-item">
                        <h6>Permintaan Uang</h6>
                        <small class="text-muted">Menunggu persetujuan Finance.</small>
                    </div>
                    <hr>
                    <div class="notification-item">
                        <h6>Stok Menipis</h6>
                        <small class="text-muted">Aqua 600ml tersisa 8 karton.</small>
                    </div>
                    <hr>
                    <div class="notification-item">
                        <h6>PO Baru</h6>
                        <small class="text-muted">Marketing membuat PO-00129.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // ====================== GRAFIK PURCHASE ORDER ======================
    const poChartCtx = document.getElementById('poChart');
    new Chart(poChartCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Purchase Order',
                data: [12, 19, 15, 25, 22, 31, 28, 34, 29, 40, 38, 45],
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                backgroundColor: 'rgba(13,110,253,.15)',
                borderColor: '#0d6efd',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // ====================== STATUS PO (DONUT) ======================
    const statusChartCtx = document.getElementById('statusChart');
    new Chart(statusChartCtx, {
        type: 'doughnut',
        data: {
            labels: ['Selesai', 'Diproses', 'Pending'],
            datasets: [{
                data: [70, 20, 10],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // ====================== JAM SERVER ======================
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').innerHTML = now.toLocaleTimeString('id-ID');
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endpush

@push('styles')
<style>
    /* ========================== Dashboard Background ========================== */
    body {
        background: #f4f7fc;
    }

    /* ========================== Card ========================== */
    .dashboard-card {
        border: none;
        border-radius: 20px;
        background: #fff;
        transition: .35s;
        overflow: hidden;
    }

    .dashboard-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(0, 0, 0, .12);
    }

    /* ========================== Icon ========================== */
    .icon-circle {
        width: 70px;
        height: 70px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ========================== Activity ========================== */
    .activity-item {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 15px 0;
        border-bottom: 1px solid #ececec;
    }

    .activity-item:last-child {
        border: none;
    }

    .activity-icon {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* ========================== Button ========================== */
    .btn {
        border-radius: 15px;
    }

    .btn-lg {
        padding: 14px;
    }

    /* ========================== Header ========================== */
    .page-title {
        font-weight: 700;
        color: #22304a;
    }

    .page-subtitle {
        color: #7d879c;
    }

    /* ========================== Card Header ========================== */
    .card-header {
        border-radius: 20px 20px 0 0 !important;
    }

    /* ========================== Shadow ========================== */
    .shadow-sm {
        box-shadow: 0 10px 30px rgba(0, 0, 0, .05) !important;
    }

    /* ========================== Table ========================== */
    .table thead {
        background: #f7f9fc;
    }

    .table-hover tbody tr {
        transition: .3s;
    }

    .table-hover tbody tr:hover {
        background: #f5f8ff;
    }

    /* ========================== Notification ========================== */
    .notification-item {
        transition: .3s;
        padding: 8px;
        border-radius: 12px;
    }

    .notification-item:hover {
        background: #f5f7ff;
    }

    .notification-item h6 {
        font-weight: 600;
        margin-bottom: 5px;
    }
</style>
@endpush