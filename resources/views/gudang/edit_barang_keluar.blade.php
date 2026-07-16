@extends('layouts.app')

@section('content')
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Barang Keluar</h2>
        <a href="/gudang/barang-keluar" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="/gudang/barang-keluar/update/{{ $barangKeluar->id }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Produk</label>
                        <select name="produk_id" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            @foreach($produk as $p)
                                <option value="{{ $p->id }}" {{ $barangKeluar->produk_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_produk }} (Stok: {{ $p->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Qty (Kardus)</label>
                        <input type="number" name="qty" class="form-control" value="{{ $barangKeluar->qty }}" min="0" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah (Pcs)</label>
                        <input type="number" name="jumlah" class="form-control" value="{{ $barangKeluar->jumlah }}" min="1" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Keluar</label>
                        <input type="date" name="tanggal_keluar" class="form-control" value="{{ $barangKeluar->tanggal_keluar }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tujuan</label>
                        <input type="text" name="tujuan" class="form-control" value="{{ $barangKeluar->tujuan }}" placeholder="Tujuan">
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Update Barang Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
