@extends('layouts.app')

@section('content')

<div class="dashboard-content">

    <h2>Barang Keluar</h2>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    {{-- filter --}}
    <div class="content-card mt-4">

        <div class="mb-3">

            <h5 class="fw-bold mb-1">

                <i class="bi bi-funnel-fill text-primary"></i>

                Filter Barang Keluar

            </h5>

        </div>

        <form action="/gudang/barang-keluar" method="GET" class="row g-3">

            <div class="col-md-4">

                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Cari Produk...">

            </div>

            <div class="col-md-3">

                <select name="tujuan" class="form-select">

                    <option value="">Semua Tujuan</option>

                </select>

            </div>

            <div class="col-md-3">

                <input type="date"
                    name="tanggal"
                    value="{{ request('tanggal') }}"
                    class="form-control">

            </div>

            <div class="col-md-2 d-flex gap-2">

                <button type="submit" class="btn btn-primary w-100">

                    <i class="bi bi-search"></i>

                    Filter

                </button>

                <a href="/gudang/barang-keluar"
                   class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-clockwise"></i>

                </a>

            </div>

        </form>

    </div>


    <br>

    {{-- TABEL RIWAYAT --}}
    <div class="produk-box">

        <div class="table-header d-flex justify-content-between align-items-center">

            <h3 class="mb-0">Riwayat Barang Keluar</h3>

            {{-- TOMBOL BUKA MODAL TAMBAH --}}
            <button type="button"
                    class="tambah-btn btn"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahBarangKeluar">
                <i class="bi bi-plus-lg"></i>
                Tambah Barang
            </button>

        </div>

        <table class="produk-table">

            <thead>

                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Tujuan</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                @forelse($barangKeluar as $bk)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>
                        {{ $bk->produk->nama_produk ?? '-' }}
                    </td>

                    <td>
                        {{ $bk->jumlah }}
                    </td>

                    <td>
                        {{ $bk->tanggal_keluar }}
                    </td>

                    <td>
                        {{ $bk->tujuan }}
                    </td>

                    {{--Aksi--}}
                    <td class="text-center align-middle">

                            <div class="aksi-group">

                                {{-- Detail --}}
                                <a href="/gudang/barang-keluar/detail/{{ $bk->id }}"
                                class="btn btn-primary btn-action"
                                title="Detail">

                                    <i class="bi bi-eye-fill"></i>

                                </a>

                                {{-- Edit --}}
                                <a href="/gudang/barang-keluar/edit/{{ $bk->id }}"
                                class="btn btn-warning btn-action"
                                title="Edit">

                                    <i class="bi bi-pencil-fill"></i>

                                </a>

                                {{-- Hapus --}}
                                <form action="/gudang/barang-keluar/delete/{{ $bk->id }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">

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
                        Belum ada data barang keluar.
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- ============================
     MODAL TAMBAH BARANG KELUAR
============================ --}}
<div class="modal fade"
     id="modalTambahBarangKeluar"
     tabindex="-1"
     aria-labelledby="modalTambahBarangKeluarLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahBarangKeluarLabel">
                    <i class="bi bi-plus-circle text-danger"></i>
                    Tambah Barang Keluar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="/gudang/barang-keluar/store"
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

                        {{-- JUMLAH --}}
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Barang</label>
                            <input type="number"
                                   name="jumlah"
                                   class="form-control @error('jumlah') is-invalid @enderror"
                                   value="{{ old('jumlah') }}"
                                   min="1"
                                   placeholder="Jumlah Barang"
                                   required>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TANGGAL --}}
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Keluar</label>
                            <input type="date"
                                   name="tanggal_keluar"
                                   class="form-control @error('tanggal_keluar') is-invalid @enderror"
                                   value="{{ old('tanggal_keluar') }}"
                                   required>
                            @error('tanggal_keluar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TUJUAN --}}
                        <div class="col-md-6">
                            <label class="form-label">Tujuan Pengiriman</label>
                            <input type="text"
                                   name="tujuan"
                                   class="form-control @error('tujuan') is-invalid @enderror"
                                   value="{{ old('tujuan') }}"
                                   placeholder="Tujuan Pengiriman">
                            @error('tujuan')
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
    background:#ef4444;
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
    background:#dc2626;
    color:white;
}

.produk-table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

.produk-table th{
    background:#f3f4f6;
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
}

.aksi-group form{
    margin:0;
}

.btn-action{
    width:40px;
    height:40px;
    border-radius:10px;
    display:inline-flex;
    justify-content:center;
    align-items:center;
    padding:0;
    transition:.25s ease;
}

.btn-action:hover{
    transform:translateY(-2px);
    box-shadow:0 4px 12px rgba(0,0,0,.15);
}

.btn-action i{
    font-size:15px;
    line-height:1;
}


</style>

{{-- Buka modal otomatis kalau validasi gagal --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('modalTambahBarangKeluar');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    </script>
@endif

@endsection