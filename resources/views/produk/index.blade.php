<!DOCTYPE html>
<html>
<head>
    <title>Data Produk</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <h2>📦 Data Produk</h2>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM TAMBAH PRODUK --}}
    <div class="card mb-4">
        <div class="card-header">
            Tambah Produk
        </div>

        <div class="card-body">

            <form method="POST" action="/produk/store">
                @csrf

                <div class="mb-3">
                    <label>Nama Produk</label>

                    <input type="text"
                           name="nama_produk"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label>Harga</label>

                    <input type="number"
                           name="harga"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-primary">
                    Simpan
                </button>

            </form>

        </div>
    </div>

    {{-- TABEL PRODUK --}}
    <div class="card">

        <div class="card-header">
            List Produk
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>

                @foreach($produk as $key => $p)

                <tr>
                    <td>{{ $key + 1 }}</td>

                    <td>{{ $p->nama_produk }}</td>

                    <td>
                        Rp {{ number_format($p->harga) }}
                    </td>

                    <td>{{ $p->stok }}</td>

                    <td>

                        {{-- EDIT --}}
                        <a href="/produk/edit/{{ $p->id }}"
                           class="btn btn-warning btn-sm">

                            Edit
                        </a>

                        {{-- HAPUS --}}
                        <form action="/produk/delete/{{ $p->id }}"
                              method="POST"
                              style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Hapus produk?')">

                                Hapus
                            </button>

                        </form>

                    </td>
                </tr>

                @endforeach

            </table>

        </div>

    </div>

</div>

</body>
</html>