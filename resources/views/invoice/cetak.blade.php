<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_no }} - ASA TIRTA</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f4f6f8;
            color: #1e293b;
            padding: 30px 15px;
            font-size: 13.5px;
        }
        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            padding: 40px;
            border: 1px solid #e2e8f0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .brand h1 {
            font-size: 26px;
            font-weight: 800;
            color: #0f766e;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .brand p {
            color: #64748b;
            font-size: 12px;
            line-height: 1.4;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .invoice-title .inv-badge {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            margin-top: 5px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
        }
        .meta-col h3 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .meta-label { color: #64748b; }
        .meta-value { font-weight: 600; color: #0f172a; }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table-items th {
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            border-bottom: 1px solid #cbd5e1;
            text-align: left;
        }
        .table-items th.text-right, .table-items td.text-right {
            text-align: right;
        }
        .table-items th.text-center, .table-items td.text-center {
            text-align: center;
        }
        .table-items td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
        }
        .table-items tbody tr:hover {
            background: #fafafa;
        }
        
        .calc-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .calc-box {
            width: 320px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
        }
        .calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 8px;
            color: #475569;
        }
        .calc-row.total-row {
            border-top: 2px solid #0f766e;
            padding-top: 10px;
            margin-top: 10px;
            margin-bottom: 0;
            font-size: 16px;
            font-weight: 800;
            color: #0f766e;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
        }
        .sig-box p { font-size: 12px; color: #64748b; }
        .sig-line {
            margin-top: 60px;
            border-bottom: 1px solid #94a3b8;
            width: 180px;
            margin-left: auto;
            margin-right: auto;
        }
        .sig-name {
            font-weight: 700;
            margin-top: 6px;
            color: #0f172a;
        }

        .action-bar {
            max-width: 800px;
            margin: 20px auto 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }
        .btn-print { background: #0f766e; color: #fff; }
        .btn-print:hover { background: #0d645e; }
        .btn-back { background: #e2e8f0; color: #334155; }
        .btn-back:hover { background: #cbd5e1; }

        @media print {
            body { background: #fff; padding: 0; }
            .invoice-box { box-shadow: none; border: none; padding: 0; }
            .action-bar { display: none; }
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <div class="header">
            <div class="brand">
                <h1>ASA TIRTA</h1>
                <p>Distributor Air Minum Dalam Kemasan (AMDK)<br>
                Jl. Industri Tirta No. 88, Jawa Timur<br>
                Hotline: (0341) 889900 | info@asat提rta.co.id</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <div style="font-weight: 700; font-size: 14px; margin-top: 3px; color: #0f766e;">{{ $invoice->invoice_no }}</div>
                <span class="inv-badge">&#10003; TERBIT &amp; LUNAS</span>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-col">
                <h3>Informasi Faktur</h3>
                <div class="meta-row">
                    <span class="meta-label">No. Transaksi</span>
                    <span class="meta-value">{{ $invoice->penjualan->kode ?? '-' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Tanggal Terbit</span>
                    <span class="meta-value">{{ \Carbon\Carbon::parse($invoice->tanggal)->format('d F Y') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Kasir / Petugas</span>
                    <span class="meta-value">{{ $invoice->penjualan->user->name ?? 'Kasir ASA TIRTA' }}</span>
                </div>
            </div>

            <div class="meta-col">
                <h3>Tujuan Tagihan</h3>
                <div class="meta-row">
                    <span class="meta-label">Nama Pelanggan</span>
                    <span class="meta-value">{{ $invoice->pelanggan }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Metode Pembayaran</span>
                    <span class="meta-value" style="color: #0f766e; text-transform: uppercase;">{{ $invoice->penjualan->metode ?? 'TUNAI' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Status Pengiriman</span>
                    <span class="meta-value">
                        @if ($invoice->penjualan && $invoice->penjualan->pengiriman)
                            {{ strtoupper($invoice->penjualan->pengiriman->status) }}
                        @else
                            SIAP / SELESAI
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <table class="table-items">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>Nama Produk / Barang</th>
                    <th class="text-center" style="width: 90px;">Jumlah</th>
                    <th class="text-right" style="width: 140px;">Harga Satuan</th>
                    <th class="text-right" style="width: 150px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @if ($invoice->penjualan && $invoice->penjualan->detailPenjualans->isNotEmpty())
                    @foreach ($invoice->penjualan->detailPenjualans as $idx => $detail)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td style="font-weight: 600;">{{ $detail->produk->nama_produk ?? 'Produk' }}</td>
                            <td class="text-center">{{ $detail->jumlah }} unit</td>
                            <td class="text-right">Rp {{ number_format($detail->produk->harga ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right" style="font-weight: 600;">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">1</td>
                        <td style="font-weight: 600;">Penjualan Produk ASA TIRTA ({{ $invoice->invoice_no }})</td>
                        <td class="text-center">1 paket</td>
                        <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        <td class="text-right" style="font-weight: 600;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="calc-section">
            <div class="calc-box">
                <div class="calc-row">
                    <span>Subtotal Barang:</span>
                    <span>Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                </div>
                <div class="calc-row">
                    <span>Diskon / Potongan:</span>
                    <span>Rp 0</span>
                </div>
                <div class="calc-row">
                    <span>Biaya Pengiriman:</span>
                    <span>Rp 0</span>
                </div>
                <div class="calc-row total-row">
                    <span>TOTAL DIBAYAR:</span>
                    <span>Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if ($invoice->penjualan && $invoice->penjualan->pengiriman && $invoice->penjualan->pengiriman->bukti_foto)
            <div style="margin-top: 15px; margin-bottom: 25px; border: 1px dashed #94a3b8; border-radius: 8px; padding: 14px 18px; background: #f8fafc; page-break-inside: avoid;">
                <div style="font-weight: 700; font-size: 11.5px; text-transform: uppercase; color: #475569; margin-bottom: 8px; letter-spacing: 0.5px;">
                    &#128247; Bukti Serah Terima Barang / Foto Pengiriman Driver:
                </div>
                <div style="display: flex; gap: 16px; align-items: center;">
                    <img src="{{ asset('storage/' . $invoice->penjualan->pengiriman->bukti_foto) }}" alt="Bukti Foto Pengiriman" style="max-height: 110px; max-width: 160px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">
                    <div style="font-size: 12px; color: #475569; line-height: 1.6;">
                        <div>Status Pengiriman: <strong style="color: #0f766e;">{{ strtoupper($invoice->penjualan->pengiriman->status) }}</strong></div>
                        <div>Tanggal Kirim: <strong>{{ \Carbon\Carbon::parse($invoice->penjualan->pengiriman->tanggal_kirim)->translatedFormat('d F Y') }}</strong></div>
                        <div>Waktu Dokumentasi: <strong>{{ \Carbon\Carbon::parse($invoice->penjualan->pengiriman->updated_at)->translatedFormat('d F Y, H:i') }} WIB</strong></div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 4px;"><em>*Dokumen foto resmi diunggah oleh driver saat penyerahan barang di alamat tujuan.</em></div>
                    </div>
                </div>
            </div>
        @endif

        <div class="signatures">
            <div class="sig-box">
                <p>Tanda Terima Pelanggan,</p>
                <div class="sig-line"></div>
                <p class="sig-name">{{ $invoice->pelanggan }}</p>
            </div>
            <div class="sig-box">
                <p>Hormat Kami (Kasir / Petugas),</p>
                <div class="sig-line"></div>
                <p class="sig-name">{{ $invoice->penjualan->user->name ?? 'Kasir ASA TIRTA' }}</p>
            </div>
        </div>
    </div>

    <div class="action-bar">
        <a href="javascript:window.history.back()" class="btn btn-back">&larr; Kembali</a>
        <button onclick="window.print()" class="btn btn-print">&#128438; Cetak Invoice Sekarang</button>
    </div>

</body>
</html>
