@extends('layouts.app')

@section('content')
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Barang Keluar</h2>
        <a href="/gudang/barang-keluar" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">ID Transaksi</div>
                <div class="col-sm-9">{{ $barangKeluar->id }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Nama Produk</div>
                <div class="col-sm-9">{{ $barangKeluar->produk->nama_produk ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Qty (Kardus)</div>
                <div class="col-sm-9 fw-bold text-success">{{ $barangKeluar->qty }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Jumlah (Pcs)</div>
                <div class="col-sm-9 fw-bold text-warning">{{ $barangKeluar->jumlah }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Tanggal Keluar</div>
                <div class="col-sm-9">{{ $barangKeluar->tanggal_keluar }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Tujuan</div>
                <div class="col-sm-9">{{ $barangKeluar->tujuan ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Waktu Pencatatan</div>
                <div class="col-sm-9">{{ $barangKeluar->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
