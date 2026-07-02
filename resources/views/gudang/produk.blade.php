@extends('layouts.app')

@section('content')

<div class="dashboard-content">

    <h2>Dashboard Gudang</h2>

    {{-- CARD STATISTIK --}}
    <div class="stats">

        <div class="card">
            <h3>{{ $produk->count() }}</h3>
            <p>Total Produk</p>
        </div>

        <div class="card">
            <h3>0</h3>
            <p>Produk Habis</p>
        </div>

        <div class="card">
            <h3>0</h3>
            <p>Pengiriman Pending</p>
        </div>

        <div class="card">
            <h3>23</h3>
            <p>Pengiriman Selesai</p>
        </div>

    </div>

    {{-- BOX PRODUK --}}
    <div class="produk-box">

        <h2>Data Produk</h2>

        {{-- FORM TAMBAH PRODUK --}}
        <form action="/gudang/produk/store"
              method="POST"
              class="produk-form">

            @csrf

            <input type="text"
                   name="nama_produk"
                   placeholder="Nama Produk"
                   required>

            <input type="text"
                   name="kode_produk"
                   placeholder="Kode Produk"
                   required>

            <input type="number"
                   name="stok"
                   placeholder="Stok"
                   required>

            <input type="number"
                   name="harga"
                   placeholder="Harga"
                   required>

            <button type="submit"
                    class="tambah-btn">

                Tambah Produk

            </button>

        </form>

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
        <table class="produk-table">

            <thead>

                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Kode Produk</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                @foreach($produk as $p)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $p->nama_produk }}</td>

                    <td>{{ $p->kode_produk }}</td>

                    <td>{{ $p->stok }}</td>

                    <td>
                        Rp {{ number_format($p->harga) }}
                    </td>

                    <td>

                        <a href="/gudang/produk/detail/{{ $p->id }}"
                        class="btn btn-info btn-sm">
                            Detail
                        </a>

                        <a href="/gudang/produk/edit/{{ $p->id }}"
                        class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <form action="/gudang/produk/delete/{{ $p->id }}"
                            method="POST"
                            style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm">
                                Hapus
                            </button>

                        </form>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

            <div class="pagination-box">
            {{ $produk->links() }}

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
}

.produk-table{
    width:100%;
    background:white;
    border-collapse:collapse;
    border-radius:15px;
    overflow:hidden;
}

.produk-table th{
    background:#f3f4f6;
}

.produk-table th,
.produk-table td{
    padding:15px;
    border-bottom:1px solid #eee;
}

.edit-btn{
    background:#3b82f6;
    color:white;
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
}

.hapus-btn{
    background:#ef4444;
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:8px;
    cursor:pointer;
}

.search-box{
    margin-bottom:20px;
    display:flex;
    justify-content:end;
}

.search-box form{
    display:flex;
    gap:10px;
}

.search-box input{
    padding:12px;
    border-radius:10px;
    border:1px solid #ccc;
    width:250px;
}

.search-btn{
    background:#2563eb;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    cursor:pointer;
}

.pagination-box{
    margin-top:20px;
}

</style>

@endsection