@extends('keuangan.layout')

@section('content')

<div class="table-modern p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-bold">
                Penagihan Customer
            </h4>

            <small class="text-muted">
                Monitoring penagihan dan pembayaran customer
            </small>

        </div>

        <button class="btn btn-danger rounded-pill px-4">

            <i class="bi bi-send"></i>
            Kirim Tagihan

        </button>

    </div>

    <div class="row mb-4">

        <div class="col-md-4">

            <input
                type="text"
                class="form-control rounded-pill"
                placeholder="Cari customer..."
            >

        </div>

    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

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

                    <td class="fw-bold">
                        PT Maju Jaya
                    </td>

                    <td class="text-danger fw-bold">
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
                            Kirim
                        </button>

                    </td>

                </tr>

                <tr>

                    <td class="fw-bold">
                        CV Tirta Abadi
                    </td>

                    <td class="text-success fw-bold">
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

                    <td class="fw-bold">
                        PT Sumber Rejeki
                    </td>

                    <td class="text-danger fw-bold">
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

                        <button class="btn btn-sm btn-warning rounded-pill">
                            Tagih
                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection