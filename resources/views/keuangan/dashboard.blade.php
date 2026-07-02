@extends('keuangan.layout')

@section('content')

<div class="row g-4">

    <div class="col-md-3">

        <div class="card-modern bg-blue">

            <div class="d-flex justify-content-between">

                <div>
                    <p>Total Pendapatan</p>

                    <h3 class="fw-bold">
                        Rp 125 JT
                    </h3>
                </div>

                <div>
                    <i class="bi bi-cash-coin fs-1"></i>
                </div>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card-modern bg-green">

            <div class="d-flex justify-content-between">

                <div>
                    <p>Total Customer</p>

                    <h3 class="fw-bold">
                        120
                    </h3>
                </div>

                <div>
                    <i class="bi bi-people-fill fs-1"></i>
                </div>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card-modern bg-red">

            <div class="d-flex justify-content-between">

                <div>
                    <p>Total Piutang</p>

                    <h3 class="fw-bold">
                        Rp 32 JT
                    </h3>
                </div>

                <div>
                    <i class="bi bi-wallet2 fs-1"></i>
                </div>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card-modern bg-orange">

            <div class="d-flex justify-content-between">

                <div>
                    <p>Tagihan Pending</p>

                    <h3 class="fw-bold">
                        18
                    </h3>
                </div>

                <div>
                    <i class="bi bi-receipt-cutoff fs-1"></i>
                </div>

            </div>

        </div>

    </div>

</div>

<div class="chart-box">

    <h5 class="fw-bold mb-4">
        Grafik Pendapatan
    </h5>

    <canvas id="chartPendapatan"></canvas>

</div>

<div class="table-modern mt-4 p-4">

    <div class="d-flex justify-content-between mb-3">

        <h5 class="fw-bold">
            Tagihan Customer
        </h5>

        <button class="btn btn-primary rounded-pill">
            + Tambah
        </button>

    </div>

    <table class="table align-middle">

        <thead>

            <tr>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>

        </thead>

        <tbody>

            <tr>
                <td>PT Maju Jaya</td>
                <td>Rp 5.000.000</td>

                <td>
                    <span class="badge bg-warning">
                        Pending
                    </span>
                </td>

                <td>20 Mei 2026</td>
            </tr>

            <tr>
                <td>CV Tirta Abadi</td>
                <td>Rp 8.500.000</td>

                <td>
                    <span class="badge bg-success">
                        Lunas
                    </span>
                </td>

                <td>22 Mei 2026</td>
            </tr>

        </tbody>

    </table>

</div>

<script>

const ctx = document.getElementById('chartPendapatan');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
        datasets: [{
            label: 'Pendapatan',
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