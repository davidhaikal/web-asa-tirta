@extends('layouts.app')

@section('content')
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Produk</h2>
        <a href="/gudang/produk" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">ID Produk</div>
                <div class="col-sm-9">{{ $produk->id }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Nama Produk</div>
                <div class="col-sm-9">{{ $produk->nama_produk }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Kode Produk</div>
                <div class="col-sm-9">{{ $produk->kode_produk ?? '-' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Qty (Kardus)</div>
                <div class="col-sm-9">{{ $produk->qty }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Stok Tersedia</div>
                <div class="col-sm-9">
                    @if($produk->stok <= 10)
                        <span class="badge bg-danger">{{ $produk->stok }} (Menipis)</span>
                    @else
                        <span class="badge bg-success">{{ $produk->stok }}</span>
                    @endif
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Harga</div>
                <div class="col-sm-9 text-success fw-bold">Rp {{ number_format($produk->harga,0,',','.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-3 fw-bold text-muted">Tanggal Ditambahkan</div>
                <div class="col-sm-9">{{ $produk->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
