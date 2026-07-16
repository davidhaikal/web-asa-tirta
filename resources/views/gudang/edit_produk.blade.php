@extends('layouts.app')

@section('content')
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Produk</h2>
        <a href="/gudang/produk" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="/gudang/produk/update/{{ $produk->id }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" value="{{ $produk->nama_produk }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kode Produk</label>
                        <input type="text" name="kode_produk" class="form-control" value="{{ $produk->kode_produk }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Qty (Kardus)</label>
                        <input type="number" name="qty" class="form-control" value="{{ $produk->qty }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Stok (Pcs)</label>
                        <input type="number" name="stok" class="form-control" value="{{ $produk->stok }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Harga</label>
                        <input type="number" name="harga" class="form-control" value="{{ $produk->harga }}" required>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
