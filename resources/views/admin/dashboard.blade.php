@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h2 class="mb-4">📊 Dashboard Admin</h2>

    <div class="row">

        {{-- TOTAL PRODUK --}}
        <div class="col-md-3 mb-3">

            <div class="card bg-primary text-white">

                <div class="card-body">

                    <h5>Total Produk</h5>

                    <h2>{{ $totalProduk ?? 0 }}</h2>

                </div>

            </div>

        </div>

        {{-- TOTAL STOK --}}
        <div class="col-md-3 mb-3">

            <div class="card bg-success text-white">

                <div class="card-body">

                    <h5>Total Stok</h5>

                    <h2>{{ $totalStok ?? 0 }}</h2>

                </div>

            </div>

        </div>

        {{-- TOTAL PENJUALAN --}}
        <div class="col-md-3 mb-3">

            <div class="card bg-warning text-dark">

                <div class="card-body">

                    <h5>Total Penjualan</h5>

                    <h2>
                        Rp {{ number_format($totalPenjualan ?? 0) }}
                    </h2>

                </div>

            </div>

        </div>

        {{-- TOTAL TRANSAKSI --}}
        <div class="col-md-3 mb-3">

            <div class="card bg-danger text-white">

                <div class="card-body">

                    <h5>Total Transaksi</h5>

                    <h2>{{ $totalTransaksi ?? 0 }}</h2>

                </div>

            </div>

        </div>

    </div>

    {{-- MENU CEPAT --}}
    <div class="card mt-4">

        <div class="card-header">
            Menu Cepat
        </div>

        <div class="card-body">

            <a href="/produk" class="btn btn-primary">
                📦 Kelola Produk
            </a>

            <a href="/gudang" class="btn btn-success">
                🏭 Gudang
            </a>

            <a href="/kasir" class="btn btn-warning">
                💰 Kasir
            </a>

            <a href="/laporan" class="btn btn-danger">
                📄 Laporan
            </a>

        </div>

    </div>

</div>

@endsection