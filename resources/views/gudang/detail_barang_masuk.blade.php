@extends('layouts.app')

@section('content')
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Barang Masuk</h2>
        <a href="/gudang/barang-masuk" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">ID Transaksi</div>
                <div class="col-sm-9">{{ $barangMasuk->id }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Nama Produk</div>
                <div class="col-sm-9">{{ $barangMasuk->produk->nama_produk ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Qty (Kardus)</div>
                <div class="col-sm-9 fw-bold text-success">{{ $barangMasuk->qty }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Jumlah (Pcs)</div>
                <div class="col-sm-9 fw-bold text-primary">{{ $barangMasuk->jumlah }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Tanggal Masuk</div>
                <div class="col-sm-9">{{ $barangMasuk->tanggal_masuk }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Catatan</div>
                <div class="col-sm-9">{{ $barangMasuk->catatan ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Waktu Pencatatan</div>
                <div class="col-sm-9">{{ $barangMasuk->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
