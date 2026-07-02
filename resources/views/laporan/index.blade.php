<!DOCTYPE html>
<html>
<head>
    <title>Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <h2>📊 Laporan Penjualan</h2>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Total</th>
        </tr>

        @foreach($penjualan as $key => $p)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $p->tanggal }}</td>
            <td>Rp {{ number_format($p->total) }}</td>
        </tr>
        @endforeach

    </table>

    <hr>

    <h2>📦 Laporan Stok</h2>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Produk</th>
            <th>Jenis</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
        </tr>

        @foreach($stok as $key => $s)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $s->produk->nama_produk ?? '-' }}</td>
            <td>{{ $s->jenis }}</td>
            <td>{{ $s->jumlah }}</td>
            <td>{{ $s->keterangan }}</td>
        </tr>
        @endforeach

    </table>

</div>

</body>
</html>