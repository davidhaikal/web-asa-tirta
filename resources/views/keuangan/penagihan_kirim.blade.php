@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-send-fill text-danger"></i>
                    Kirim Tagihan Customer
                </h3>

                <small class="text-muted">
                    Pilih customer yang akan dikirimkan tagihan.
                </small>
            </div>

            <a href="{{ route('penagihan.index') }}"
               class="btn btn-secondary rounded-pill">

                <i class="bi bi-arrow-left"></i>
                Kembali

            </a>

        </div>

        <div class="card-body">

            <form action="{{ route('penagihan.proses.kirim') }}"
                  method="POST">

                @csrf

                {{-- Filter --}}

                <div class="row mb-4">

                    <div class="col-md-5">

                        <label class="form-label fw-semibold">

                            Cari Customer

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            placeholder="Masukkan nama customer">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">

                            Status

                        </label>

                        <select class="form-select">

                            <option>Semua</option>
                            <option>Pending</option>
                            <option>Menunggak</option>

                        </select>

                    </div>

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">

                            Metode Pengiriman

                        </label>

                        <select class="form-select"
                                name="metode">

                            <option>Email</option>
                            <option>WhatsApp</option>
                            <option>Cetak PDF</option>

                        </select>

                    </div>

                </div>

                {{-- Ringkasan --}}

                <div class="row mb-4">

                    <div class="col-md-4">

                        <div class="card border-primary">

                            <div class="card-body">

                                <small>Total Customer</small>

                                <h3 class="fw-bold text-primary">

                                    {{ count($tagihans) }}

                                </h3>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card border-danger">

                            <div class="card-body">

                                <small>Total Tagihan</small>

                                <h3 class="fw-bold text-danger">

                                    Rp17.000.000

                                </h3>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="card border-success">

                            <div class="card-body">

                                <small>Metode</small>

                                <h3 class="fw-bold text-success">

                                    Email

                                </h3>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Tabel --}}

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="50">

                                    <input type="checkbox">

                                </th>

                                <th>Customer</th>

                                <th>Invoice</th>

                                <th>Total</th>

                                <th>Jatuh Tempo</th>

                                <th>Status</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($tagihans as $tagihan)

                            <tr>

                                <td>

                                    <input
                                        type="checkbox"
                                        name="tagihan[]"
                                        value="{{ $tagihan->id }}">

                                </td>

                                <td class="fw-semibold">

                                    {{ $tagihan->customer }}

                                </td>

                                <td>

                                    {{ $tagihan->invoice }}

                                </td>

                                <td class="text-danger fw-bold">

                                    Rp {{ number_format($tagihan->total,0,',','.') }}

                                </td>

                                <td>

                                    {{ $tagihan->jatuh_tempo }}

                                </td>

                                <td>

                                    @if($tagihan->status=="Pending")

                                        <span class="badge bg-warning">

                                            Pending

                                        </span>

                                    @elseif($tagihan->status=="Menunggak")

                                        <span class="badge bg-danger">

                                            Menunggak

                                        </span>

                                    @else

                                        <span class="badge bg-success">

                                            Lunas

                                        </span>

                                    @endif

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <hr>

                <div class="d-flex justify-content-end">

                    <button
                        type="submit"
                        class="btn btn-danger rounded-pill px-4">

                        <i class="bi bi-send-fill"></i>

                        Kirim Tagihan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection