@extends('layouts.app')

@section('content')

<div class="dashboard-content">

    {{-- CARD STATISTIK DIHAPUS --}}

    {{-- BOX PRODUK --}}
    <div class="produk-box">

        <div class="table-header d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Data Produk</h2>
            <button type="button"
                    class="tambah-btn btn"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahProduk">
                <i class="bi bi-plus-lg"></i>
                Tambah Produk
            </button>
        </div>

        {{-- SEARCH BOX --}}
        <div class="search-box">

            <form action="/gudang/produk"
                method="GET">

                <input type="text"
                    name="search"
                    placeholder="Cari produk..."
                    value="{{ $search }}">

                <button type="submit"
                        class="search-btn">

                    Cari

                </button>

            </form>

        </div>

        {{-- TABEL PRODUK --}}
        <div class="table-responsive">
            <table class="produk-table">

            <thead>

                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Kode Produk</th>
                    <th>Kategori</th>
                    <th>Qty (Kardus)</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                @foreach($produk as $p)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $p->nama_produk }}</td>

                    <td>{{ $p->kode_produk }}</td>

                    <!-- Kategori -->
                    <td>{{ $p->kategori ?? '-' }}</td>

                    <!-- Qty -->
                    <td>{{ $p->qty }}</td>

                    <!-- Stok -->
                    <td>{{ $p->stok }}</td>

                    <!-- Harga -->
                    <td class="fw-semibold text-success">
                        Rp {{ number_format($p->harga,0,',','.') }}
                    </td>

                    <!-- Status -->
                    <td>

                        @if($p->stok <= 10)
                            <span class="badge bg-danger">
                                Menipis
                            </span>
                        @else
                            <span class="badge bg-success">
                                Aktif
                            </span>
                        @endif

                    </td>

                    <!-- Aksi -->
                    <td class="text-center">

                        <div class="aksi-group">

                            {{-- Detail --}}
                            <a href="/gudang/produk/detail/{{ $p->id }}"
                            class="btn btn-primary btn-action"
                            title="Detail">

                                <i class="bi bi-eye-fill"></i>

                            </a>

                            {{-- Edit --}}
                            <a href="/gudang/produk/edit/{{ $p->id }}"
                            class="btn btn-warning btn-action"
                            title="Edit">

                                <i class="bi bi-pencil-fill"></i>

                            </a>

                            {{-- Hapus --}}
                            <form action="/gudang/produk/delete/{{ $p->id }}"
                                method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger btn-action"
                                        onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                        title="Hapus">

                                    <i class="bi bi-trash-fill"></i>

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

                @endforeach

            </tbody>

            </table>
        </div>

            <div class="pagination-box">
            {{ $produk->links() }}

        </div>

    </div>

</div>

{{-- MODAL TAMBAH PRODUK --}}
<div class="modal fade"
     id="modalTambahProduk"
     tabindex="-1"
     aria-labelledby="modalTambahProdukLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahProdukLabel">
                    <i class="bi bi-plus-circle text-success"></i> Tambah Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/gudang/produk/store" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" placeholder="Nama Produk" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Produk</label>
                        <input type="text" name="kode_produk" class="form-control" placeholder="Kode Produk" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Qty (Kardus)</label>
                            <input type="number" name="qty" class="form-control" placeholder="Qty (Kardus)" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok (Pcs)</label>
                            <input type="number" name="stok" class="form-control" placeholder="Stok Pcs" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="Harga Satuan" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="tambah-btn btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>

.dashboard-content{
    padding:20px;
}

.stats{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-bottom:25px;
}

.card{
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

.card h3{
    margin:0;
    font-size:28px;
}

.card p{
    color:gray;
}

.produk-box{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.produk-form{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:10px;
    margin-bottom:20px;
}

.produk-form input{
    padding:12px;
    border-radius:10px;
    border:1px solid #ccc;
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

/* RESPONSIVE */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .produk-form {
        grid-template-columns: 1fr;
    }
    .stats {
        grid-template-columns: 1fr;
    }
}



</style>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('modalTambahProduk');
            if (modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
    </script>
@endif

@endsection