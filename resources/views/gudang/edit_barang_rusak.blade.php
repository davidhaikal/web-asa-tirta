@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Edit Barang Rusak</h2>

    <div class="card-form">

        <form action="/gudang/barang-rusak/update/{{ $barangRusak->id }}"
              method="POST">

            @csrf
            @method('PUT')

            <select name="produk_id">

                @foreach($produk as $p)

                    <option value="{{ $p->id }}"
                        {{ $barangRusak->produk_id == $p->id ? 'selected' : '' }}>

                        {{ $p->nama_produk }}

                    </option>

                @endforeach

            </select>

            <input type="number"
                   name="jumlah"
                   value="{{ $barangRusak->jumlah }}">

            <input type="text"
                   name="keterangan"
                   value="{{ $barangRusak->keterangan }}">

            <input type="date"
                   name="tanggal"
                   value="{{ $barangRusak->tanggal }}">

            <button type="submit">
                Update
            </button>

        </form>

    </div>

</div>

@endsection