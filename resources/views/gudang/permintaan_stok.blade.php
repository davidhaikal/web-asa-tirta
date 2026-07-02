@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Permintaan Stok</h2>

    {{-- FORM --}}
    <div class="card-form">

        <form action="/gudang/permintaan-stok/store"
              method="POST">

            @csrf

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

            <input type="number"
                   name="jumlah"
                   placeholder="Jumlah Permintaan"
                   required>

            <input type="date"
                   name="tanggal"
                   required>

            <button type="submit">
                Kirim Permintaan
            </button>

        </form>

    </div>


    {{-- TABEL --}}
    <div class="table-card">

        <table>

            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            @foreach($permintaan as $item)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>
                    {{ $item->produk->nama_produk }}
                </td>

                <td>
                    {{ $item->jumlah }}
                </td>

                <td>
                    {{ $item->tanggal }}
                </td>

                <td>

                    <span class="status">

                        {{ $item->status }}

                    </span>

                </td>

                </td>

                    <a href="/gudang/permintaan-stok/edit/{{ $item->id }}"
                    class="btn-edit">

                        Edit

                    </a>

                    <form action="/gudang/permintaan-stok/delete/{{ $item->id }}"
                        method="POST"
                        style="display:inline;">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn-delete">

                            Hapus

                        </button>

                    </form>

                </td>

            </tr>

            @endforeach

        </table>

    </div>

</div>

<style>

.container{
    padding:20px;
}

.card-form{
    background:white;
    padding:20px;
    border-radius:20px;
    margin-bottom:25px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.card-form form{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.card-form input,
.card-form select{
    padding:12px;
    border-radius:10px;
    border:1px solid #ddd;
    min-width:220px;
}

.card-form button{
    background:#2563eb;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    cursor:pointer;
}

.table-card{
    background:white;
    padding:20px;
    border-radius:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    padding:15px;
    border-bottom:1px solid #eee;
    text-align:left;
}

.status{
    background:orange;
    color:white;
    padding:8px 14px;
    border-radius:20px;
    font-size:13px;
}

.btn-edit{
    background:#2563eb;
    color:white;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:13px;
}

.btn-delete{
    background:red;
    color:white;
    border:none;
    padding:8px 14px;
    border-radius:8px;
    cursor:pointer;
    font-size:13px;
}

</style>

@endsection