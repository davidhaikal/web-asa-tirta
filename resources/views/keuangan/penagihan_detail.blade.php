@extends('layouts.app')

@section('content')

<div class="table-modern p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div class="container">

            <div class="card shadow">

                <div class="card-header">

                    <h4 class="fw-bold">
                        Detail Penagihan
                    </h4>

                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th width="220">ID Tagihan</th>
                            <td>{{ $tagihan->id }}</td>
                        </tr>

                        <tr>
                            <th>Customer</th>
                            <td>{{ $tagihan->customer }}</td>
                        </tr>

                        <tr>
                            <th>Total Tagihan</th>
                            <td>
                                Rp {{ number_format($tagihan->total_tagihan,0,',','.') }}
                            </td>
                        </tr>

                        <tr>
                            <th>Jatuh Tempo</th>
                            <td>{{ $tagihan->jatuh_tempo }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>{{ $tagihan->status }}</td>
                        </tr>

                        <tr>
                            <th>Alamat</th>
                            <td>{{ $tagihan->alamat }}</td>
                        </tr>

                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $tagihan->telepon }}</td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td>{{ $tagihan->email }}</td>
                        </tr>

                    </table>

                    <a href="{{ route('penagihan.index') }}"
                    class="btn btn-secondary">

                        Kembali

                    </a>

                </div>

            </div>

        </div>
</div>

@endsection