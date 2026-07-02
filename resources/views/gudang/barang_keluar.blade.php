@extends('layouts.app')

@section('content')

<div class="dashboard-content">

    <h2>Barang Keluar</h2>

    {{-- FORM BARANG KELUAR --}}
    <div class="produk-box">

        <form action="/gudang/barang-keluar/store"
              method="POST"
              class="produk-form">

            @csrf

            {{-- PILIH PRODUK --}}
            <select name="produk_id" required>

                <option value="">
                    Pilih Produk
                </option>

                @foreach($produk as $p)

                    <option value="{{ $p->id }}">
                        {{ $p->nama_produk }}
                    </option>

                @endforeach

            </select>

            {{-- JUMLAH --}}
            <input type="number"
                   name="jumlah"
                   placeholder="Jumlah Barang"
                   required>

            {{-- TANGGAL --}}
            <input type="date"
                   name="tanggal_keluar"
                   required>

            {{-- TUJUAN --}}
            <input type="text"
                   name="tujuan"
                   placeholder="Tujuan Pengiriman">

            <button type="submit"
                    class="tambah-btn">

                Simpan

            </button>

        </form>

    </div>

    <br>

    {{-- TABEL RIWAYAT --}}
    <div class="produk-box">

        <h3>Riwayat Barang Keluar</h3>

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

                @foreach($barangKeluar as $bk)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>
                        {{ $bk->produk->nama_produk }}
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

                    <td>

                        <a href="/gudang/barang-keluar/edit/{{ $bk->id }}"
                        class="edit-btn">

                            Edit

                        </a>

                        <form action="/gudang/barang-keluar/delete/{{ $bk->id }}"
                            method="POST"
                            style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="hapus-btn">

                                Hapus

                            </button>

                        </form>

                    </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

<style>

.dashboard-content{
    padding:20px;
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
}

.produk-form input,
.produk-form select{
    padding:12px;
    border-radius:10px;
    border:1px solid #ccc;
}

.tambah-btn{
    background:#ef4444;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
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
    border-bottom:1px solid #eee;
}

.edit-btn{
    background:#facc15;
    color:black;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
}

.hapus-btn{
    background:#ef4444;
    color:white;
    border:none;
    padding:8px 14px;
    border-radius:8px;
    cursor:pointer;
}

</style>

@endsection