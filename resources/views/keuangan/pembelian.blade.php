@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Pembelian Barang
            </h2>

            <p class="text-muted mb-0">
                Kelola data pembelian barang dari supplier
            </p>
        </div>

        <a href="{{ route('pembelian.create') }}" class="btn btn-primary">
            + Tambah Pembelian
        </a>

    </div>

    <!-- Filter -->

    <div class="card shadow-sm border-0 rounded-4 mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <input
                        type="text"
                        class="form-control"
                        placeholder="Cari nomor pembelian atau supplier...">

                </div>

                <div class="col-md-3">

                    <select class="form-select">

                        <option>Semua Status</option>
                        <option>Lunas</option>
                        <option>Utang</option>

                    </select>

                </div>

                <div class="col-md-3">

                    <input
                        type="date"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <button class="btn btn-success w-100">

                        Filter

                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- Table -->

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead>

                <tr>

                    <th>No</th>
                    <th>No Pembelian</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th width="180">Aksi</th>

                </tr>

                </thead>

                <tbody>

                {{-- Data Dummy 1 --}}
                <tr>

                    <td>1</td>

                    <td>PB-0001</td>

                    <td>07 Juli 2026</td>

                    <td>PT Tirta Abadi</td>

                    <td>Rp5.000.000</td>

                    <td>
                        <span class="badge bg-success">
                            Lunas
                        </span>
                    </td>

                    <td class="text-center">

                        <a href="{{ route('pembelian.show', 1) }}"
                        class="btn btn-info btn-sm text-white"
                        title="Detail">

                            <i class="bi bi-eye-fill"></i>

                        </a>

                        <a href="{{ route('pembelian.edit', 1) }}"
                        class="btn btn-warning btn-sm"
                        title="Edit">

                            <i class="bi bi-pencil-square"></i>

                        </a>

                        <form action="{{ route('pembelian.destroy', 1) }}"
                            method="POST"
                            class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')"
                                    title="Hapus">

                                <i class="bi bi-trash-fill"></i>

                            </button>

                        </form>

                    </td>

                </tr>

                {{-- Data Dummy 2 --}}
                <tr>

                    <td>2</td>

                    <td>PB-0002</td>

                    <td>08 Juli 2026</td>

                    <td>CV Maju Bersama</td>

                    <td>Rp3.200.000</td>

                    <td>
                        <span class="badge bg-danger">
                            Utang
                        </span>
                    </td>

                    <td class="text-center">

                        <a href="{{ route('pembelian.show', 2) }}"
                        class="btn btn-info btn-sm text-white">

                            <i class="bi bi-eye-fill"></i>

                        </a>

                        <a href="{{ route('pembelian.edit', 2) }}"
                        class="btn btn-warning btn-sm">

                            <i class="bi bi-pencil-square"></i>

                        </a>

                        <form action="{{ route('pembelian.destroy', 2) }}"
                            method="POST"
                            class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">

                                <i class="bi bi-trash-fill"></i>

                            </button>

                        </form>

                    </td>

                </tr>

            </tbody>

@endsection