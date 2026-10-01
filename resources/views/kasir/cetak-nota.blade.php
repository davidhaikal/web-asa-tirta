<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota {{ $penjualan->kode }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #222;
            background: #f2f1ec;
            padding: 24px 12px;
            -webkit-font-smoothing: antialiased;
        }
        .nota {
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            border: 1px dashed #bbb;
            padding: 24px 20px;
        }
        .header { text-align: center; border-bottom: 2px solid #222; padding-bottom: 12px; margin-bottom: 14px; }
        .header h1 { font-size: 19px; font-weight: 700; letter-spacing: 0.06em; }
        .header p { font-size: 10.5px; color: #666; line-height: 1.5; }
        .header .doc { margin-top: 8px; font-size: 13px; font-weight: 700; letter-spacing: 0.12em; }
        .info { margin-bottom: 14px; }
        .info-row { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 4px; font-size: 11.5px; }
        .info-label { font-weight: 700; }
        .table-section { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .table-section th {
            text-align: left;
            border-bottom: 2px solid #222;
            padding: 6px 0;
            font-size: 11px;
            letter-spacing: 0.04em;
        }
        .table-section td { padding: 5px 0; font-size: 11.5px; border-bottom: 1px dotted #ccc; }
        .table-section .right { text-align: right; }
        .total-section { border-top: 2px solid #222; padding-top: 10px; margin-top: 12px; }
        .total-row { display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; margin-bottom: 5px; }
        .status { display: inline-block; padding: 2px 10px; border-radius: 3px; font-weight: 700; font-size: 10.5px; letter-spacing: 0.05em; }
        .status-lunas { background: #d4edda; color: #155724; }
        .status-pending { background: #f8d7da; color: #721c24; }
        .footer { text-align: center; margin-top: 18px; padding-top: 10px; border-top: 1px dashed #ccc; font-size: 10px; color: #888; line-height: 1.6; }
        .btn-print {
            display: block;
            width: 100%;
            max-width: 400px;
            margin: 14px auto 0;
            padding: 12px;
            background: #127369;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: "Space Grotesk", sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .btn-print:hover { background: #0e5b53; }
        @media print {
            body { background: #fff; padding: 0; }
            .btn-print { display: none; }
            .nota { border: none; max-width: 76mm; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="nota">
        <div class="header">
            <h1>ASA TIRTA</h1>
            <p>Jl. Contoh No. 123, Kota, Provinsi<br>Telp: 0812-3456-7890</p>
            <p class="doc">NOTA PENJUALAN</p>
        </div>

        <div class="info">
            <div class="info-row">
                <span class="info-label">No. Nota:</span>
                <span>{{ $penjualan->kode }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal:</span>
                <span>{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pelanggan:</span>
                <span>{{ $penjualan->pelanggan }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir:</span>
                <span>{{ $penjualan->user?->name ?? 'System' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Metode:</span>
                <span>{{ strtoupper($penjualan->metode) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="status {{ $penjualan->status === 'lunas' ? 'status-lunas' : 'status-pending' }}">
                    {{ $penjualan->status === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                </span>
            </div>
        </div>

        <table class="table-section">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="right">Qty</th>
                    <th class="right">Harga</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan->detailPenjualans as $detail)
                    <tr>
                        <td>{{ $detail->produk->nama_produk }}</td>
                        <td class="right">{{ $detail->jumlah }}</td>
                        <td class="right">{{ number_format($detail->produk->harga, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih atas pembelian Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan.</p>
        </div>
    </div>

    <button class="btn-print" onclick="window.print()">Cetak Nota</button>
</body>
</html>
