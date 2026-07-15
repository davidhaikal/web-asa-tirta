@extends('layouts.app')

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

        <a href="{{ route('penagihan.form.kirim') }}"
            class="btn btn-danger rounded-pill px-4">

            <i class="bi bi-send"></i>
            Kirim Tagihan

        </a>

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

                        <a href="{{ route('penagihan.show',1) }}"
                            class="btn btn-sm btn-primary rounded-pill">

                                <i class="bi bi-eye"></i>

                                Detail

                        </a>

                        <form action="{{ route('penagihan.kirim',1) }}"
                            method="POST"
                            class="d-inline">

                            @csrf

                            <button type="submit"
                                    class="btn btn-sm btn-success rounded-pill">

                                <i class="bi bi-send-check"></i>

                                Kirim

                            </button>

                        </form>

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

                    <form action="{{ route('penagihan.tagih',3) }}"
                        method="POST"
                        class="d-inline">

                        @csrf

                        <button type="submit"
                                class="btn btn-sm btn-warning rounded-pill">

                            <i class="bi bi-cash-coin"></i>

                            Tagih

                        </button>

                    </form>

                </tr>

            </tbody>

        </table>

    </div>

</div>

@endsection