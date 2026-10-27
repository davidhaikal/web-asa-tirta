@extends('layouts.app')

@section('title', 'Edit Barang Rusak')

@section('content')
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Edit Barang Rusak</h2>
            <small class="text-muted">Perbarui data pencatatan barang rusak di gudang</small>
        </div>
        <a href="/gudang/barang-rusak" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-2"></i> Periksa kembali data input:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form action="/gudang/barang-rusak/update/{{ $barangRusak->id }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Produk <span class="text-danger">*</span></label>
                        <select name="produk_id" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            @foreach($produk as $p)
                                <option value="{{ $p->id }}" {{ $barangRusak->produk_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_produk }} (Stok Saat Ini: {{ $p->stok }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Qty (Kardus)</label>
                        <input type="number" name="qty" class="form-control" value="{{ $barangRusak->qty }}" min="0" placeholder="0" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah Rusak (Pcs) <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" class="form-control" value="{{ $barangRusak->jumlah }}" min="1" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Rusak <span class="text-danger">*</span></label>
                        @php
                            $tanggalVal = $barangRusak->tanggal_rusak ?? $barangRusak->tanggal;
                            try {
                                $formattedDate = $tanggalVal ? \Carbon\Carbon::parse($tanggalVal)->format('Y-m-d') : date('Y-m-d');
                            } catch (\Throwable $e) {
                                $formattedDate = date('Y-m-d');
                            }
                        @endphp
                        <input type="date" name="tanggal" class="form-control" value="{{ $formattedDate }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Penyebab / Keterangan Kerusakan <span class="text-danger">*</span></label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Jelaskan penyebab kerusakan produk..." required>{{ $barangRusak->penyebab ?? $barangRusak->keterangan }}</textarea>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <a href="/gudang/barang-rusak" class="btn btn-secondary rounded-pill px-4 me-2">Batal</a>
                    <button type="submit" class="btn btn-warning rounded-pill px-4">
                        <i class="bi bi-save me-1"></i> Update Barang Rusak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
