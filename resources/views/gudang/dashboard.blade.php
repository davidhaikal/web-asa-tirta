@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h2>Dashboard Gudang</h2>
            <p>Monitoring stok dan aktivitas gudang ASA Tirta</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('gudang.export.pdf') }}" class="btn btn-export btn-pdf">
                📄 Export PDF
            </a>
            <a href="{{ route('gudang.export.excel') }}" class="btn btn-export btn-excel">
                📊 Export Excel
            </a>
        </div>
    </div>

    <!-- Statistik -->
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

        <!-- Grafik Aktivitas Gudang -->
        <div class="col-lg-8">

            <div class="content-card modern-card">

                <h4>📈 Grafik Aktivitas Gudang</h4>

                <canvas id="aktivitasChart" height="110"></canvas>

            </div>

        </div>

        <!-- Stok Menipis -->
        <div class="col-lg-4">

            <div class="content-card modern-card">

                <h4>⚠️ Stok Menipis</h4>

                <div class="stock-box">

                    <h1>{{ $stokMenipis ?? 0 }}</h1>

                    <p>Produk perlu restock</p>

                </div>

            </div>

        </div>

    </div>

    <div class="row mt-4">

        <!-- Aktivitas -->
        <div class="col-lg-12">

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

                        @forelse ($aktivitas ?? [] as $item)
                            <tr>
                                <td>{{ $item->tanggal ?? '-' }}</td>
                                <td>{{ $item->aktivitas ?? '-' }}</td>
                                <td>{{ $item->produk ?? '-' }}</td>
                                <td>{{ $item->jumlah ?? '-' }}</td>
                                <td>{{ $item->status ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada aktivitas</td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

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
    margin-bottom:0;
}

.btn-export{
    border-radius:12px;
    padding:10px 18px;
    font-weight:600;
    color:#fff;
    border:none;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.btn-pdf{
    background:linear-gradient(135deg,#dc2626,#ef4444);
}

.btn-pdf:hover{
    color:#fff;
    opacity:.9;
}

.btn-excel{
    background:linear-gradient(135deg,#16a34a,#22c55e);
}

.btn-excel:hover{
    color:#fff;
    opacity:.9;
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
    margin-bottom:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.content-card h4{
    margin-bottom:20px;
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
    text-align:center;
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

.table td,
.table th{
    vertical-align:middle;
    padding:14px;
}

</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctxAktivitas = document.getElementById('aktivitasChart');

new Chart(ctxAktivitas, {

    type: 'bar',

    data: {

        labels: [
            'Barang Masuk',
            'Barang Keluar',
            'Barang Rusak'
        ],

        datasets: [{

            label: 'Jumlah',

            data: [
                {{ $barangMasuk }},
                {{ $barangKeluar }},
                {{ $barangRusak }}
            ],

            backgroundColor: [
                '#22c55e',
                '#f59e0b',
                '#ef4444'
            ],

            borderRadius: 8

        }]

    },

    options: {

        responsive: true,

        plugins: {

            legend: {

                display: false

            }

        },

        scales: {

            y: {

                beginAtZero: true

            }

        }

    }

});

</script>

@endsection