@extends('layouts.app')

@section('content')

<div class="dashboard-content">

    <h2>Barang Masuk</h2>

    {{-- FILTER --}}
    <div class="content-card mt-4">
        <div class="mb-3">

            <h5 class="fw-bold mb-1">
                <i class="bi bi-funnel-fill text-primary"></i>
                Filter Barang Masuk
            </h5>
        </div>

        <form action="/gudang/barang-masuk" method="GET">

            <div class="row g-3">

                <div class="col-md-4">
                    <input type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="🔍 Cari Produk...">
                </div>

                <div class="col-md-3">
                    <select name="supplier"
                            class="form-select">

                        <option value="">
                            Semua Supplier
                        </option>

                    </select>
                </div>

                <div class="col-md-3">
                    <input type="date"
                        name="tanggal"
                        class="form-control"
                        value="{{ request('tanggal') }}">
                </div>

                <div class="col-md-2 d-flex gap-2">

                    <button class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                        Filter
                    </button>

                    <a href="/gudang/barang-masuk"
                    class="btn btn-outline-secondary">

                        <i class="bi bi-arrow-clockwise"></i>

                    </a>

                </div>

            </div>

        </form>

    </div>

    <br>

    {{-- TABEL RIWAYAT --}}
    <div class="produk-box">

        <div class="table-header d-flex justify-content-between align-items-center">

            <h3 class="mb-0">Riwayat Barang Masuk</h3>

            {{-- TOMBOL BUKA MODAL TAMBAH --}}
            <button type="button"
                    class="tambah-btn btn"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahBarangMasuk">
                <i class="bi bi-plus-lg"></i>
                Tambah Barang
            </button>

        </div>

        <div class="table-responsive">
            <table class="produk-table">

            <thead>

                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Qty (Kardus)</th>
                    <th>Jumlah (Pcs)</th>
                    <th>Tanggal</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                @forelse($barangMasuk as $bm)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>
                        {{ $bm->produk->nama_produk ?? '-' }}
                    </td>

                    <td>
                        {{ $bm->qty }}
                    </td>

                    <td>
                        {{ $bm->jumlah }}
                    </td>

                    <td>
                        {{ $bm->tanggal_masuk }}
                    </td>

                    <td>
                        {{ $bm->catatan }}
                    </td>

                    {{-- Aksi --}}
                    <td class="text-center">

                        <div class="aksi-group">

                            <a href="/gudang/barang-masuk/detail/{{ $bm->id }}"
                            class="btn btn-primary btn-action"
                            title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>

                            <a href="/gudang/barang-masuk/edit/{{ $bm->id }}"
                            class="btn btn-warning btn-action"
                            title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>

                            <form action="/gudang/barang-masuk/delete/{{ $bm->id }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Yakin ingin menghapus data barang masuk ini?');">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger btn-action"
                                        title="Hapus">

                                    <i class="bi bi-trash-fill"></i>

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Belum ada data barang masuk.
                    </td>
                </tr>

                @endforelse

            </tbody>

            </table>
        </div>

    </div>

</div>

{{-- ============================
     MODAL TAMBAH BARANG MASUK
============================ --}}
<div class="modal fade"
     id="modalTambahBarangMasuk"
     tabindex="-1"
     aria-labelledby="modalTambahBarangMasukLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahBarangMasukLabel">
                    <i class="bi bi-plus-circle text-success"></i>
                    Tambah Barang Masuk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="/gudang/barang-masuk/store"
                  method="POST">

                @csrf

                <div class="modal-body">

                    <div class="row g-3">

                        {{-- PILIH PRODUK --}}
                        <div class="col-md-6">
                            <label class="form-label">Produk</label>
                            <select name="produk_id"
                                    class="form-select @error('produk_id') is-invalid @enderror"
                                    required>

                                <option value="">
                                    Pilih Produk
                                </option>

                                @foreach($produk as $p)

                                    <option value="{{ $p->id }}"
                                        {{ old('produk_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama_produk }}
                                    </option>

                                @endforeach

                            </select>
                            @error('produk_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- QTY --}}
                        <div class="col-md-6">
                            <label class="form-label">Qty (Kardus)</label>
                            <input type="number"
                                   name="qty"
                                   class="form-control @error('qty') is-invalid @enderror"
                                   value="{{ old('qty') }}"
                                   min="0"
                                   placeholder="Qty Kardus"
                                   required>
                            @error('qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- JUMLAH --}}
                        <div class="col-md-6">
                            <label class="form-label">Jumlah (Pcs)</label>
                            <input type="number"
                                   name="jumlah"
                                   class="form-control @error('jumlah') is-invalid @enderror"
                                   value="{{ old('jumlah') }}"
                                   min="1"
                                   placeholder="Total Barang (Pcs)"
                                   required>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TANGGAL --}}
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date"
                                   name="tanggal_masuk"
                                   class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                   value="{{ old('tanggal_masuk') }}"
                                   required>
                            @error('tanggal_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- CATATAN --}}
                        <div class="col-md-6">
                            <label class="form-label">Catatan</label>
                            <input type="text"
                                   name="catatan"
                                   class="form-control @error('catatan') is-invalid @enderror"
                                   value="{{ old('catatan') }}"
                                   placeholder="Catatan (opsional)">
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="tambah-btn btn">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<style>

.page-header h2{
    font-weight:700;
}

.page-header p{
    color:#64748b;
}

.table-header{
    margin-bottom:15px;
}

.dashboard-content{
    padding:20px;
}

.produk-box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.tambah-btn{
    background:#22c55e;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
    padding:10px 18px;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.tambah-btn:hover{
    background:#16a34a;
    color:white;
}

.produk-table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

.produk-table th,
.produk-table td{
    padding:15px;
    border-bottom:1px solid #e5e7eb;
    vertical-align:middle;
}

.produk-table th{
    background:#f8fafc;
    font-weight:600;
    color:#334155;
}

.produk-table td:last-child,
.produk-table th:last-child{
    width:150px;
    text-align:center;
}

/* ===========================
   BUTTON AKSI
=========================== */

.aksi-group{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:8px;
    flex-wrap:nowrap;
}

.aksi-group form{
    display:inline-block;
    margin:0;
}

.btn-action{
    width:40px;
    height:40px;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:0;
    border-radius:10px;
}

.btn-action i{
    font-size:15px;
    line-height:1;
}

/* RESPONSIVE */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .row.g-3 > .col-md-4,
    .row.g-3 > .col-md-3,
    .row.g-3 > .col-md-2 {
        width: 100%;
    }
}


</style>

{{-- Buka modal otomatis kalau validasi gagal, supaya input & pesan error tetap terlihat --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('modalTambahBarangMasuk');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    </script>
@endif

@endsection