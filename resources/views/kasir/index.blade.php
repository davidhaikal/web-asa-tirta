@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h2 class="fw-bold">Dashboard Kasir</h2>
        <p class="text-muted">
            Selamat datang di Dashboard Kasir ASA Tirta
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Total Transaksi</h6>
                    <h2>0</h2>
                    <small>Hari ini</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Pendapatan</h6>
                    <h2>Rp 0</h2>
                    <small>Hari ini</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Nota Dicetak</h6>
                    <h2>0</h2>
                    <small>Total nota</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Stok Menipis</h6>
                    <h2>0</h2>
                    <small>Perlu restock</small>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection