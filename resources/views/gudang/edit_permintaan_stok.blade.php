@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Edit Permintaan Stok</h2>

    <div class="card-form">

        <form action="/gudang/permintaan-stok/update/{{ $permintaan->id }}"
              method="POST">

            @csrf
            @method('PUT')

            <select name="produk_id">

                @foreach($produk as $p)

                    <option value="{{ $p->id }}"
                        {{ $permintaan->produk_id == $p->id ? 'selected' : '' }}>

                        {{ $p->nama_produk }}

                    </option>

                @endforeach

            </select>

            <input type="number"
                   name="qty"
                   placeholder="Qty (Kardus)"
                   value="{{ $permintaan->qty }}" min="0" required>

            <input type="number"
                   name="jumlah"
                   placeholder="Total Permintaan (Pcs)"
                   value="{{ $permintaan->jumlah }}" min="1" required>

            <input type="date"
                   name="tanggal"
                   value="{{ $permintaan->tanggal }}">

            <select name="status">

                <option value="Menunggu">
                    Menunggu
                </option>

                <option value="Disetujui">
                    Disetujui
                </option>

                <option value="Ditolak">
                    Ditolak
                </option>

            </select>

            <button type="submit">
                Update
            </button>

        </form>

    </div>

</div>

@endsection