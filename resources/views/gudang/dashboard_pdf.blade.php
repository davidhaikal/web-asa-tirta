<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Gudang ASA Tirta</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Laporan Gudang ASA Tirta</h2>
    <p>Tanggal cetak: {{ date('d-m-Y H:i') }}</p>

    <h3>Data Produk</h3>
    <table>
        <thead>
            <tr><th>No</th><th>Nama Produk</th><th>Stok</th></tr>
        </thead>
        <tbody>
            @foreach($produk as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->nama_produk }}</td>
                <td>{{ $p->stok }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Barang Masuk</h3>
    <table>
        <thead>
            <tr><th>No</th><th>Produk</th><th>Jumlah</th><th>Tanggal</th></tr>
        </thead>
        <tbody>
            @foreach($barangMasuk as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ $item->tanggal_masuk }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Barang Keluar</h3>
    <table>
        <thead>
            <tr><th>No</th><th>Produk</th><th>Jumlah</th><th>Tanggal</th><th>Tujuan</th></tr>
        </thead>
        <tbody>
            @foreach($barangKeluar as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ $item->tanggal_keluar }}</td>
                <td>{{ $item->tujuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Barang Rusak</h3>
    <table>
        <thead>
            <tr><th>No</th><th>Produk</th><th>Jumlah</th><th>Keterangan</th><th>Tanggal</th></tr>
        </thead>
        <tbody>
            @foreach($barangRusak as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->produk->nama_produk ?? '-' }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ $item->tanggal_rusak }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>