<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan ASA Tirta</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Laporan Keuangan ASA Tirta</h2>
    <p>Tanggal cetak: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item['tanggal'] }}</td>
                <td>{{ $item['customer'] }}</td>
                <td>{{ $item['total'] }}</td>
                <td>{{ $item['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
