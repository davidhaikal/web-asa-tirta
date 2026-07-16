@extends('layouts.app')

@section('content')
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Barang Rusak</h2>
        <a href="/gudang/barang-rusak" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">ID Transaksi</div>
                <div class="col-sm-9">{{ $barangRusak->id }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Nama Produk</div>
                <div class="col-sm-9">{{ $barangRusak->produk->nama_produk ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Qty (Kardus)</div>
                <div class="col-sm-9 fw-bold text-success">{{ $barangRusak->qty }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Jumlah Rusak (Pcs)</div>
                <div class="col-sm-9 fw-bold text-danger">{{ $barangRusak->jumlah }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Tanggal Rusak</div>
                <div class="col-sm-9">{{ $barangRusak->tanggal_rusak ?? $barangRusak->tanggal }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Keterangan</div>
                <div class="col-sm-9">{{ $barangRusak->keterangan ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Waktu Pencatatan</div>
                <div class="col-sm-9">{{ $barangRusak->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
