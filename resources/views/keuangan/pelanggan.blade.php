@extends('keuangan.layout')

@section('content')

<div class="row g-4">

    <!-- Card Total Piutang -->
    <div class="col-md-4">

        <div class="card-modern bg-red">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <p class="mb-1">Total Piutang</p>

                    <h2 class="fw-bold">
                        Rp 32 JT
                    </h2>
                </div>

                <div>
                    <i class="bi bi-wallet2 fs-1"></i>
                </div>

            </div>

        </div>

    </div>

    <!-- Card Belum Bayar -->
    <div class="col-md-4">

        <div class="card-modern bg-orange">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <p class="mb-1">Belum Dibayar</p>

                    <h2 class="fw-bold">
                        18 Customer
                    </h2>
                </div>

                <div>
                    <i class="bi bi-exclamation-circle fs-1"></i>
                </div>

            </div>

        </div>

    </div>

    <!-- Card Lunas -->
    <div class="col-md-4">

        <div class="card-modern bg-green">

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <p class="mb-1">Sudah Lunas</p>

                    <h2 class="fw-bold">
                        102 Customer
                    </h2>
                </div>

                <div>
                    <i class="bi bi-check-circle fs-1"></i>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- Tabel Piutang -->

<div class="table-modern mt-4 p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                Data Piutang Customer
            </h4>

            <small class="text-muted">
                Monitoring pembayaran customer
            </small>
        </div>

        <button class="btn btn-danger rounded-pill px-4">
            <i class="bi bi-file-earmark-pdf"></i>
            Export PDF
        </button>

    </div>

    <!-- Search -->

    <div class="row mb-4">

        <div class="col-md-4">

            <input
                type="text"
                class="form-control rounded-pill"
                placeholder="Cari customer..."
            >

        </div>

        <div class="col-md-3">

            <select class="form-select rounded-pill">

                <option>Semua Status</option>
                <option>Pending</option>
                <option>Lunas</option>

            </select>

        </div>

    </div>

    <!-- Table -->

    <div class="table-responsive">

        <table class="table align-middle table-hover">

            <thead class="table-light">

                <tr>
                    <th>Customer</th>
                    <th>Total Tagihan</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>

                        <div class="fw-bold">
                            PT Maju Jaya
                        </div>

                        <small class="text-muted">
                            Malang
                        </small>

                    </td>

                    <td class="fw-bold text-danger">
                        Rp 5.000.000
                    </td>

                    <td>
                        25 Mei 2026
                    </td>

                    <td>

                        <span class="badge bg-warning">
                            Pending
                        </span>

                    </td>

                    <td>

                        <button class="btn btn-sm btn-primary rounded-pill">
                            Detail
                        </button>

                        <button class="btn btn-sm btn-success rounded-pill">
                            Bayar
                        </button>

                    </td>

                </tr>

                <tr>

                    <td>

                        <div class="fw-bold">
                            CV Tirta Abadi
                        </div>

                        <small class="text-muted">
                            Surabaya
                        </small>

                    </td>

                    <td class="fw-bold text-success">
                        Rp 8.500.000
                    </td>

                    <td>
                        20 Mei 2026
                    </td>

                    <td>

                        <span class="badge bg-success">
                            Lunas
                        </span>

                    </td>

                    <td>

                        <button class="btn btn-sm btn-primary rounded-pill">
                            Detail
                        </button>

                    </td>

                </tr>

                <tr>

                    <td>

                        <div class="fw-bold">
                            PT Sumber Rejeki
                        </div>

                        <small class="text-muted">
                            Jakarta
                        </small>

                    </td>

                    <td class="fw-bold text-danger">
                        Rp 12.000.000
                    </td>

                    <td>
                        30 Mei 2026
                    </td>

                    <td>

                        <span class="badge bg-danger">
                            Menunggak
                        </span>

                    </td>

                    <td>

                        <button class="btn btn-sm btn-primary rounded-pill">
                            Detail
                        </button>

                        <button class="btn btn-sm btn-warning rounded-pill">
                            Tagih
                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

<!-- Grafik Piutang -->

<div class="chart-box">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h5 class="fw-bold">
            Grafik Piutang Bulanan
        </h5>

        <span class="badge bg-primary">
            Tahun 2026
        </span>

    </div>

    <canvas id="chartPiutang"></canvas>

</div>

<script>

const ctxPiutang = document.getElementById('chartPiutang');

new Chart(ctxPiutang, {

    type: 'line',

    data: {

        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],

        datasets: [{

            label: 'Total Piutang',

            data: [12, 19, 10, 15, 22, 30],

            borderWidth: 3,
            tension: 0.4,
            fill: true

        }]
    },

    options: {
        responsive: true
    }

});

</script>

@endsection