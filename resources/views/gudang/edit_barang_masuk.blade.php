@extends('layouts.app')

@section('content')
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Barang Masuk</h2>
        <a href="/gudang/barang-masuk" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="/gudang/barang-masuk/update/{{ $barangMasuk->id }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Produk</label>
                        <select name="produk_id" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            @foreach($produk as $p)
                                <option value="{{ $p->id }}" {{ $barangMasuk->produk_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_produk }} (Stok: {{ $p->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Qty (Kardus)</label>
                        <input type="number" name="qty" class="form-control" value="{{ $barangMasuk->qty }}" min="0" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah (Pcs)</label>
                        <input type="number" name="jumlah" class="form-control" value="{{ $barangMasuk->jumlah }}" min="1" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="{{ $barangMasuk->tanggal_masuk }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Catatan</label>
                        <input type="text" name="catatan" class="form-control" value="{{ $barangMasuk->catatan }}" placeholder="Opsional">
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update Barang Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
