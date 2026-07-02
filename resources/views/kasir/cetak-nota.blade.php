<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota {{ $penjualan->kode }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #333; padding: 20px; }
        .nota { max-width: 400px; margin: 0 auto; border: 2px dashed #ccc; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 18px; font-weight: bold; }
        .header p { font-size: 11px; color: #666; }
        .info { margin-bottom: 15px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .info-label { font-weight: bold; }
        .table-section { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table-section th { text-align: left; border-bottom: 1px solid #333; padding: 5px 0; font-size: 11px; }
        .table-section td { padding: 4px 0; font-size: 11px; }
        .table-section .right { text-align: right; }
        .total-section { border-top: 2px solid #333; padding-top: 10px; margin-top: 10px; }
        .total-row { display: flex; justify-content: space-between; font-size: 14px; font-weight: bold; margin-bottom: 5px; }
        .footer { text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px dashed #ccc; font-size: 10px; color: #999; }
        .status { display: inline-block; padding: 3px 10px; border-radius: 3px; font-weight: bold; font-size: 11px; }
        .status-lunas { background: #d4edda; color: #155724; }
        .status-pending { background: #f8d7da; color: #721c24; }
        .btn-print { display: block; width: 100%; padding: 10px; background: #0d6efd; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; margin-top: 15px; }
        @media print { .btn-print { display: none; } body { padding: 0; } .nota { border: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="nota">
        <div class="header">
            <h1>ASA TIRTA</h1>
            <p>Jl. Contoh No. 123, Kota, Provinsi<br>Telp: 0812-3456-7890</p>
            <p style="margin-top: 5px; font-size: 14px; font-weight: bold;">NOTA PENJUALAN</p>
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