@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Barang Rusak</h2>

    {{-- Filter --}}
    <div class="produk-box">

        <div class="table-header">

            <div>

                <h4>
                    <i class="bi bi-funnel-fill text-primary"></i>
                    Filter Barang Rusak
                </h4>

            </div>

        </div>

        <form action="/gudang/barang-rusak"
            method="GET"
            class="filter-form">

            {{-- Cari Produk --}}
            <input type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="🔍 Cari Produk...">

            {{-- Penyebab --}}
            <select name="penyebab">

                <option value="">
                    Semua Penyebab
                </option>

                <option value="Pecah" {{ request('penyebab') == 'Pecah' ? 'selected' : '' }}>
                    Pecah
                </option>

                <option value="Bocor" {{ request('penyebab') == 'Bocor' ? 'selected' : '' }}>
                    Bocor
                </option>

                <option value="Kemasan Rusak" {{ request('penyebab') == 'Kemasan Rusak' ? 'selected' : '' }}>
                    Kemasan Rusak
                </option>

                <option value="Kadaluarsa" {{ request('penyebab') == 'Kadaluarsa' ? 'selected' : '' }}>
                    Kadaluarsa
                </option>

                <option value="Cacat Produksi" {{ request('penyebab') == 'Cacat Produksi' ? 'selected' : '' }}>
                    Cacat Produksi
                </option>

                <option value="Lainnya" {{ request('penyebab') == 'Lainnya' ? 'selected' : '' }}>
                    Lainnya
                </option>

            </select>

            {{-- Tanggal --}}
            <input type="date"
                name="tanggal"
                value="{{ request('tanggal') }}">

            {{-- Tombol --}}
            <button type="submit"
                    class="filter-btn">

                <i class="bi bi-search"></i>

                Filter

            </button>

            <a href="/gudang/barang-rusak"
            class="reset-btn">

                <i class="bi bi-arrow-clockwise"></i>

                Reset

            </a>

        </form>

    </div>

    {{-- TABEL --}}
    <div class="produk-box">

        <div class="table-header d-flex justify-content-between align-items-center">

            <div>

                <h4>
                    <i class="bi bi-clock-history text-danger"></i>
                    Riwayat Barang Rusak
                </h4>

                <p>
                    Daftar seluruh barang yang mengalami kerusakan.
                </p>

            </div>

            {{-- TOMBOL BUKA MODAL TAMBAH --}}
            <button type="button"
                    class="tambah-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahBarangRusak">
                <i class="bi bi-plus-lg"></i>
                Tambah Barang
            </button>

        </div>

        <table class="produk-table">

            <thead>

                <tr>

                    <th>No</th>

                    <th>Produk</th>

                    <th>Jumlah Rusak</th>

                    <th>Penyebab</th>

                    <th>Tanggal</th>

                    <th class="text-center">Aksi</th>

                </tr>

            </thead>

            <tbody>

                @forelse($barangRusak as $item)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $item->produk->nama_produk ?? '-' }}</td>

                    <td>{{ $item->jumlah }}</td>

                    <td>{{ $item->penyebab ?? $item->keterangan }}</td>

                    <td>{{ \Carbon\Carbon::parse($item->tanggal_rusak)->format('d M Y') }}</td>

                    <td class="text-center">

                        <div class="aksi-group">

                            <a href="/gudang/barang-rusak/detail/{{ $item->id }}"
                            class="btn btn-primary btn-action"
                            title="Detail">

                                <i class="bi bi-eye-fill"></i>

                            </a>

                            <a href="/gudang/barang-rusak/edit/{{ $item->id }}"
                            class="btn btn-warning btn-action"
                            title="Edit">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                            <form action="/gudang/barang-rusak/delete/{{ $item->id }}"
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

                    <td colspan="6" class="text-center">

                        Belum ada data barang rusak.

                    </td>

                </tr>

                @endforelse

            </tbody>
        </table>

    </div>
</div>

{{-- ============================
     MODAL TAMBAH BARANG RUSAK
============================ --}}
<div class="modal fade"
     id="modalTambahBarangRusak"
     tabindex="-1"
     aria-labelledby="modalTambahBarangRusakLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahBarangRusakLabel">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    Tambah Barang Rusak
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="/gudang/barang-rusak/store"
                method="POST">

                @csrf

                <div class="modal-body">

                    <div class="row g-3">

                        {{-- Produk --}}
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

                        {{-- Jumlah --}}
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Rusak</label>
                            <input type="number"
                                name="jumlah"
                                class="form-control @error('jumlah') is-invalid @enderror"
                                value="{{ old('jumlah') }}"
                                min="1"
                                placeholder="Jumlah Rusak"
                                required>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Penyebab --}}
                        <div class="col-md-6">
                            <label class="form-label">Penyebab</label>
                            <select name="penyebab"
                                    class="form-select @error('penyebab') is-invalid @enderror"
                                    required>

                                <option value="">
                                    Pilih Penyebab
                                </option>

                                <option value="Pecah" {{ old('penyebab') == 'Pecah' ? 'selected' : '' }}>Pecah</option>
                                <option value="Bocor" {{ old('penyebab') == 'Bocor' ? 'selected' : '' }}>Bocor</option>
                                <option value="Kemasan Rusak" {{ old('penyebab') == 'Kemasan Rusak' ? 'selected' : '' }}>Kemasan Rusak</option>
                                <option value="Kadaluarsa" {{ old('penyebab') == 'Kadaluarsa' ? 'selected' : '' }}>Kadaluarsa</option>
                                <option value="Cacat Produksi" {{ old('penyebab') == 'Cacat Produksi' ? 'selected' : '' }}>Cacat Produksi</option>
                                <option value="Lainnya" {{ old('penyebab') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>

                            </select>
                            @error('penyebab')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date"
                                name="tanggal"
                                class="form-control @error('tanggal') is-invalid @enderror"
                                value="{{ old('tanggal') }}"
                                required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="tambah-btn">
                        <i class="bi bi-save"></i>
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<style>

/* ==========================
    CONTAINER
========================== */

.container{
    padding:20px;
}

/* ==========================
    CARD
========================== */

.produk-box{
    background:#fff;
    border-radius:20px;
    padding:25px;
    margin-bottom:25px;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
}

/* ==========================
    HEADER
========================== */

.table-header{
    margin-bottom:18px;
}

.table-header h4{
    font-size:22px;
    font-weight:700;
    color:#1f2937;
    margin-bottom:5px;
}

.table-header p{
    color:#6b7280;
    font-size:14px;
    margin:0;
}

/* ==========================
    TAMBAH BTN
========================== */

.tambah-btn{

    height:48px;
    padding:0 28px;

    border:none;
    border-radius:10px;

    background:#2563eb;

    color:#fff;

    font-weight:600;

    cursor:pointer;

    display:inline-flex;
    align-items:center;
    gap:6px;

}

.tambah-btn:hover{

    background:#1d4ed8;
    color:#fff;

}

/* ==========================
    FILTER
========================== */

.filter-form{

    display:grid;

    grid-template-columns:
        2fr
        1.5fr
        1.4fr
        auto
        auto;

    gap:15px;

    align-items:center;

}

.filter-form input,
.filter-form select{

    width:100%;

    height:48px;

    padding:0 15px;

    border:1px solid #d1d5db;

    border-radius:10px;

}

.filter-btn{

    height:48px;

    padding:0 28px;

    background:#2563eb;

    color:#fff;

    border:none;

    border-radius:10px;

    font-weight:600;

}

.filter-btn:hover{

    background:#1d4ed8;

}

.reset-btn{

    height:48px;

    padding:0 28px;

    display:flex;

    align-items:center;

    justify-content:center;

    background:#6b7280;

    color:#fff;

    border-radius:10px;

    text-decoration:none;

}

.reset-btn:hover{

    background:#4b5563;

    color:#fff;

}

/* ==========================
    TABLE
========================== */

.produk-table{

    width:100%;

    border-collapse:collapse;

    margin-top:20px;

}

.produk-table th{

    background:#f3f4f6;

    padding:16px;

    text-align:left;

    font-weight:600;

    border-bottom:1px solid #e5e7eb;

}

.produk-table td{

    padding:16px;

    border-bottom:1px solid #e5e7eb;

    vertical-align:middle;

}

.produk-table tbody tr:hover{

    background:#f9fafb;

}

/* ==========================
    AKSI
========================== */

.aksi-group{

    display:flex;

    justify-content:center;

    align-items:center;

    gap:8px;

    flex-wrap:nowrap;

}

.aksi-group form{

    margin:0;

}

.btn-action{

    width:40px;

    height:40px;

    border-radius:10px;

    display:flex;

    justify-content:center;

    align-items:center;

    padding:0;

}

.btn-action i{

    font-size:15px;

}

</style>

{{-- Buka modal otomatis kalau validasi gagal --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('modalTambahBarangRusak');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    </script>
@endif

@endsection