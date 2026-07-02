@extends('layouts.app')

@section('content')

<div class="edit-container">

    <div class="edit-box">

        <h2>Edit Produk</h2>

        <form action="/gudang/produk/update/{{ $produk->id }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="form-group">

                <label>Nama Produk</label>

                <input type="text"
                       name="nama_produk"
                       value="{{ $produk->nama_produk }}"
                       required>

            </div>

            <div class="form-group">

                <label>Kode Produk</label>

                <input type="text"
                       name="kode_produk"
                       value="{{ $produk->kode_produk }}"
                       required>

            </div>

            <div class="form-group">

                <label>Stok</label>

                <input type="number"
                       name="stok"
                       value="{{ $produk->stok }}"
                       required>

            </div>

            <div class="form-group">

                <label>Harga</label>

                <input type="number"
                       name="harga"
                       value="{{ $produk->harga }}"
                       required>

            </div>

            <button type="submit"
                    class="update-btn">

                Update Produk

            </button>

        </form>

    </div>

</div>

<style>

.edit-container{
    padding:30px;
}

.edit-box{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
    max-width:700px;
}

.edit-box h2{
    margin-bottom:25px;
}

.form-group{
    margin-bottom:20px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
}

.form-group input{
    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:10px;
}

.update-btn{
    background:#22c55e;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    cursor:pointer;
}

</style>

@endsection