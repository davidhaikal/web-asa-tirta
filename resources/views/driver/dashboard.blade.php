@extends('layouts.app')


@section('content')

<style>
body{
    background:#f4f7fb;
}

.dashboard-header{
    background: linear-gradient(135deg,#2563eb,#4f46e5);
    color:#fff;
    border-radius:25px;
    padding:35px;
    overflow:hidden;
    position:relative;
}

.dashboard-header::after{
    content:'';
    position:absolute;
    width:250px;
    height:250px;
    background:rgba(255,255,255,.08);
    border-radius:50%;
    right:-70px;
    top:-70px;
}

.hero-icon{
    width:160px;
    height:160px;
    background:rgba(255,255,255,.15);
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:auto;
}

.hero-icon i{
    font-size:70px;
    color:white;
}

.stat-card{
    background:#fff;
    border:none;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    transition:.3s;
    overflow:hidden;
}

.stat-card:hover{
    transform:translateY(-6px);
}

.icon-box{
    width:70px;
    height:70px;
    border-radius:18px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
}

.quick-card{
    border:none;
    border-radius:22px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    transition:.3s;
}

.quick-card:hover{
    transform:translateY(-6px);
}

.activity-card{
    border:none;
    border-radius:22px;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.badge-soft{
    background:#e0f2fe;
    color:#0284c7;
    padding:8px 14px;
    border-radius:30px;
}
</style>

<div class="container-fluid py-4">

    {{-- HERO --}}
    <div class="dashboard-header mb-4">

        <div class="row align-items-center">

            <div class="col-lg-8">

                <h2 class="fw-bold mb-3">
                    Selamat Datang Driver 👋
                </h2>

                <p class="mb-4">
                    Kelola penerimaan invoice, upload bukti pengiriman,
                    serta pantau aktivitas pengiriman barang
                    melalui Dashboard Driver ASA Tirta.
                </p>

                <button class="btn btn-light rounded-pill px-4">
                    <i class="fas fa-truck me-2"></i>
                    Mulai Bekerja
                </button>

            </div>

            <div class="col-lg-4 text-center">

                <div class="hero-icon">

                    <i class="fas fa-truck-moving"></i>

                </div>

            </div>

        </div>

    </div>



    {{-- STATISTIK --}}

    <div class="row g-4 mb-4">

        <div class="col-lg-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">
                                Invoice
                            </small>

                            <h2 class="fw-bold">
                                24
                            </h2>

                        </div>

                        <div class="icon-box bg-primary-subtle">

                            <i class="fas fa-file-invoice text-primary"></i>

                        </div>

                    </div>

                    <small class="text-success">

                        +8 Hari ini

                    </small>

                </div>

            </div>

        </div>



        <div class="col-lg-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">

                                Pengiriman

                            </small>

                            <h2 class="fw-bold">

                                18

                            </h2>

                        </div>

                        <div class="icon-box bg-success-subtle">

                            <i class="fas fa-truck text-success"></i>

                        </div>

                    </div>

                    <small class="text-success">

                        +5 Hari ini

                    </small>

                </div>

            </div>

        </div>



        <div class="col-lg-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">

                                Upload Bukti

                            </small>

                            <h2 class="fw-bold">

                                15

                            </h2>

                        </div>

                        <div class="icon-box bg-warning-subtle">

                            <i class="fas fa-cloud-upload-alt text-warning"></i>

                        </div>

                    </div>

                    <small class="text-success">

                        +3 Hari ini

                    </small>

                </div>

            </div>

        </div>



        <div class="col-lg-3">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <small class="text-muted">

                                Selesai

                            </small>

                            <h2 class="fw-bold">

                                96%

                            </h2>

                        </div>

                        <div class="icon-box bg-danger-subtle">

                            <i class="fas fa-circle-check text-danger"></i>

                        </div>

                    </div>

                    <small class="text-success">

                        Semua berjalan baik

                    </small>

                </div>

            </div>

        </div>

    </div>

{{-- ================= QUICK ACTION ================= --}}

<div class="row">

    <!-- Quick Action -->
    <div class="col-lg-8">

        <div class="row g-4">

            <!-- Terima Invoice -->
            <div class="col-md-6">

                <div class="card quick-card h-100">

                    <div class="card-body p-4">

                        <div class="icon-box bg-primary-subtle mb-4">

                            <i class="fas fa-file-invoice text-primary"></i>

                        </div>

                        <h4 class="fw-bold">
                            Terima Invoice
                        </h4>

                        <p class="text-muted mb-4">
                            Lihat dan konfirmasi invoice yang telah dibuat oleh bagian gudang.
                        </p>

                        <ul class="list-unstyled">

                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Cek nomor invoice
                            </li>

                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Pastikan barang sesuai
                            </li>

                            <li class="mb-4">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Konfirmasi penerimaan
                            </li>

                        </ul>

                        <a href="{{ route('driver.invoice.index') }}"
                            class="btn btn-primary rounded-pill w-100">

                            <i class="fas fa-arrow-right me-2"></i>

                            Buka Invoice

                        </a>

                    </div>

                </div>

            </div>



            <!-- Upload Pengiriman -->

            <div class="col-md-6">

                <div class="card quick-card h-100">

                    <div class="card-body p-4">

                        <div class="icon-box bg-success-subtle mb-4">

                            <i class="fas fa-truck-fast text-success"></i>

                        </div>

                        <h4 class="fw-bold">

                            Upload Pengiriman

                        </h4>

                        <p class="text-muted mb-4">

                            Upload bukti pengiriman barang yang sudah selesai dikirim.

                        </p>

                        <ul class="list-unstyled">

                            <li class="mb-2">

                                <i class="fas fa-check-circle text-success me-2"></i>

                                Foto Bukti

                            </li>

                            <li class="mb-2">

                                <i class="fas fa-check-circle text-success me-2"></i>

                                Status Pengiriman

                            </li>

                            <li class="mb-4">

                                <i class="fas fa-check-circle text-success me-2"></i>

                                Simpan Data

                            </li>

                        </ul>

                        <a href="{{ route('driver.pengiriman.index') }}"
                            class="btn btn-success rounded-pill w-100">

                            <i class="fas fa-upload me-2"></i>

                            Upload Sekarang

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>





    <!-- Progress -->

    <div class="col-lg-4">

        <div class="card activity-card">

            <div class="card-body">

                <h5 class="fw-bold mb-4">

                    Progress Pengiriman

                </h5>

                <div class="text-center">

                    <div style="width:170px;height:170px;
                    margin:auto;
                    border-radius:50%;
                    background:conic-gradient(#2563eb 82%,#e9ecef 0);
                    display:flex;
                    justify-content:center;
                    align-items:center;">

                        <div style="width:125px;
                        height:125px;
                        background:white;
                        border-radius:50%;
                        display:flex;
                        justify-content:center;
                        align-items:center;
                        flex-direction:column;">

                            <h2 class="fw-bold">

                                82%

                            </h2>

                            <small>

                                Selesai

                            </small>

                        </div>

                    </div>

                </div>

                <hr>

                <div class="d-flex justify-content-between">

                    <span>

                        Pengiriman Hari Ini

                    </span>

                    <strong>

                        18

                    </strong>

                </div>

                <div class="d-flex justify-content-between mt-2">

                    <span>

                        Belum Dikirim

                    </span>

                    <strong class="text-danger">

                        4

                    </strong>

                </div>

                <div class="d-flex justify-content-between mt-2">

                    <span>

                        Selesai

                    </span>

                    <strong class="text-success">

                        14

                    </strong>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- ================= AKTIVITAS ================= --}}

<div class="card activity-card mt-4">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="fw-bold mb-0">

                Aktivitas Terbaru

            </h5>

            <span class="badge-soft">

                Hari Ini

            </span>

        </div>

    </div>

    <div class="table-responsive">

        <table class="table align-middle mb-0">

            <thead>

                <tr>

                    <th>Invoice</th>

                    <th>Pengiriman</th>

                    <th>Status</th>

                    <th>Jam</th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>INV-00125</td>

                    <td>Malang</td>

                    <td>

                        <span class="badge bg-success">

                            Selesai

                        </span>

                    </td>

                    <td>08.30 WIB</td>

                </tr>

                <tr>

                    <td>INV-00126</td>

                    <td>Surabaya</td>

                    <td>

                        <span class="badge bg-warning text-dark">

                            Proses

                        </span>

                    </td>

                    <td>10.15 WIB</td>

                </tr>

                <tr>

                    <td>INV-00127</td>

                    <td>Kediri</td>

                    <td>

                        <span class="badge bg-primary">

                            Diterima

                        </span>

                    </td>

                    <td>11.20 WIB</td>

                </tr>

            </tbody>

        </table>

    </div>

</div>
</div>

@endsection