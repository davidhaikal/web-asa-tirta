@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="page-header">
        <h2>Dashboard Gudang</h2>
        <p>Monitoring stok dan aktivitas gudang ASA Tirta</p>
    </div>

    <div class="row">

        <div class="col-md-3">
            <div class="dashboard-card card-blue">
                <h6>Total Produk</h6>
                <h2>{{ $totalProduk }}</h2>
                <span>Produk tersedia</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card card-green">
                <h6>Barang Masuk</h6>
                <h2>{{ $barangMasuk }}</h2>
                <span>Total transaksi masuk</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card card-orange">
                <h6>Barang Keluar</h6>
                <h2>{{ $barangKeluar }}</h2>
                <span>Total transaksi keluar</span>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card card-red">
                <h6>Barang Rusak</h6>
                <h2>{{ $barangRusak }}</h2>
                <span>Total barang rusak</span>
            </div>
        </div>

    </div>

    <div class="row mt-4">

    <!-- Ringkasan Stok -->
    <div class="col-lg-8">

        <div class="content-card modern-card">

            <h4>📦 Ringkasan Stok</h4>

            <div class="row align-items-center">

                <div class="col-md-7">

                    <table class="table table-borderless">

                        <tr>
                            <td>Total Produk</td>
                            <td class="text-end fw-bold">
                                {{ $totalProduk }}
                            </td>
                        </tr>

                        <tr>
                            <td>Total Stok</td>
                            <td class="text-end fw-bold">
                                {{ $totalStok }}
                            </td>
                        </tr>

                        <tr>
                            <td>Barang Masuk</td>
                            <td class="text-end fw-bold text-success">
                                {{ $barangMasuk }}
                            </td>
                        </tr>

                        <tr>
                            <td>Barang Keluar</td>
                            <td class="text-end fw-bold text-warning">
                                {{ $barangKeluar }}
                            </td>
                        </tr>

                    </table>

                </div>

                <div class="col-md-5">

                    <canvas id="stokChart"></canvas>

                </div>

            </div>

        </div>

    </div>

    <!-- Stok Menipis -->
    <div class="col-lg-4">

        <div class="content-card modern-card">

            <h4>⚠️ Stok Menipis</h4>

            <div class="stock-box">

                <h1>0</h1>

                <p>Produk perlu restock</p>

            </div>

        </div>

    </div>

</div>

<div class="row mt-4">

    <!-- Aktivitas -->
    <div class="col-lg-8">

        <div class="content-card modern-card">

            <h4>📝 Aktivitas Gudang Terbaru</h4>

            <table class="table table-hover">

                <thead>

                    <tr>
                        <th>Tanggal</th>
                        <th>Aktivitas</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>-</td>
                        <td>Belum ada aktivitas</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Informasi -->
    <div class="col-lg-4">

        <div class="content-card modern-card">

            <h4>ℹ️ Informasi Gudang</h4>

            <ul class="info-list">

                <li>📦 Kelola Data Produk</li>

                <li>📥 Monitoring Barang Masuk</li>

                <li>📤 Monitoring Barang Keluar</li>

                <li>⚠️ Kelola Barang Rusak</li>

                <li>📋 Permintaan Stok</li>

            </ul>

        </div>

    </div>

</div>

<style>

.page-header{
    margin-bottom:25px;
}

.page-header h2{
    font-weight:700;
    color:#1e293b;
}

.page-header p{
    color:#64748b;
}

.dashboard-card{
    padding:25px;
    border-radius:18px;
    color:white;
    margin-bottom:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
}

.dashboard-card h6{
    margin-bottom:10px;
}

.dashboard-card h2{
    font-size:32px;
    font-weight:bold;
}

.card-blue{
    background:linear-gradient(135deg,#2563eb,#3b82f6);
}

.card-green{
    background:linear-gradient(135deg,#16a34a,#22c55e);
}

.card-orange{
    background:linear-gradient(135deg,#ea580c,#f97316);
}

.card-red{
    background:linear-gradient(135deg,#dc2626,#ef4444);
}

.content-card{
    background:white;
    border-radius:18px;
    padding:25px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.content-card h4{
    margin-bottom:20px;
}

.info-list{
    list-style:none;
    padding:0;
}

.info-list li{
    padding:12px 0;
    border-bottom:1px solid #eee;
}

.stock-alert{
    text-align:center;
    padding:25px;
}

.stock-alert p{
    font-size:20px;
    font-weight:600;
    color:#dc2626;
}

.content-card{
    background:white;
    border-radius:18px;
    padding:25px;
    margin-bottom:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.table td,
.table th{
    vertical-align:middle;
}

.mb-4{
    margin-bottom:25px;
}

.modern-card{
    background:#fff;
    border:none;
    border-radius:20px;
    padding:25px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    transition:0.3s;
}

.modern-card:hover{
    transform:translateY(-3px);
}

.stock-box{
    padding:20px;
}

.stock-box h1{
    font-size:60px;
    font-weight:700;
    color:#ef4444;
}

.stock-box p{
    color:#64748b;
    margin-bottom:15px;
}

.info-list{
    list-style:none;
    padding-left:0;
}

.info-list li{
    padding:12px 0;
    border-bottom:1px solid #eee;
}

.table td{
    padding:14px;
}

#stokChart{
    max-width:220px;
    margin:auto;
}

</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('stokChart');

new Chart(ctx, {

    type: 'doughnut',

    data: {

        labels: [
            'Barang Masuk',
            'Barang Keluar',
            'Stok Saat Ini'
        ],

        datasets: [{

            data: [
                {{ $barangMasuk }},
                {{ $barangKeluar }},
                {{ $totalStok }}
            ],

            backgroundColor: [
                '#22c55e',
                '#f59e0b',
                '#3b82f6'
            ],

            borderWidth: 0

        }]

    },

    options: {

        responsive: true,

        plugins: {

            legend: {

                position: 'bottom'

            }

        }

    }

});

</script>

@endsection