@extends('layouts.app')

@section('content')

<div class="row g-4">

    <div class="col-md-4">

        <div class="card-modern bg-blue">

            <h5>Total Penjualan</h5>

            <h2 class="fw-bold">
                Rp 250 JT
            </h2>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card-modern bg-green">

            <h5>Total Transaksi</h5>

            <h2 class="fw-bold">
                520
            </h2>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card-modern bg-orange">

            <h5>Penjualan Bulan Ini</h5>

            <h2 class="fw-bold">
                Rp 42 JT
            </h2>

        </div>

    </div>

</div>

<div class="chart-box">

    <h5 class="fw-bold mb-4">
        Grafik Penjualan
    </h5>

    <canvas id="chartPenjualan"></canvas>

</div>

<div class="table-modern mt-4 p-4">

    <div class="d-flex justify-content-between mb-4">

        <h4 class="fw-bold">
            Data Penjualan
        </h4>

        <button class="btn btn-success rounded-pill">
            Export Excel
        </button>

    </div>

    <table class="table table-hover align-middle">

        <thead class="table-light">

            <tr>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
            </tr>

        </thead>

        <tbody>

            <tr>

                <td>20 Mei 2026</td>

                <td>PT Maju Jaya</td>

                <td class="fw-bold">
                    Rp 5.000.000
                </td>

                <td>
                    <span class="badge bg-success">
                        Selesai
                    </span>
                </td>

            </tr>

            <tr>

                <td>22 Mei 2026</td>

                <td>CV Tirta Abadi</td>

                <td class="fw-bold">
                    Rp 8.500.000
                </td>

                <td>
                    <span class="badge bg-primary">
                        Diproses
                    </span>
                </td>

            </tr>

        </tbody>

    </table>

</div>

<script>

const ctxPenjualan = document.getElementById('chartPenjualan');

new Chart(ctxPenjualan, {

    type: 'bar',

    data: {

        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],

        datasets: [{

            label: 'Penjualan',

            data: [12, 19, 10, 15, 22, 30],

            borderWidth: 1

        }]
    },

    options: {
        responsive: true
    }

});

</script>

@endsection