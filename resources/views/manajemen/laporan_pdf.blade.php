<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Manajemen ASA Tirta</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Laporan Manajemen ASA Tirta</h2>
    <p>Tanggal cetak: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No Transaksi</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>{{ $labelPihak ?? 'Pihak' }}</th>
                <th>Nilai (Rp)</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item['tanggal'] }}</td>
                <td>{{ $item['no_transaksi'] }}</td>
                <td>{{ $item['produk'] }}</td>
                <td>{{ $item['jumlah'] }}</td>
                <td>{{ $item['satuan'] }}</td>
                <td>{{ $item['pihak'] }}</td>
                <td>{{ number_format($item['nilai'], 0, ',', '.') }}</td>
                <td>{{ $item['catatan'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
