<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Gudang - ASA Tirta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            color: #111;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #555;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .meta-info td {
            padding: 2px 0;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-data th {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: bold;
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
        }
        .table-data td {
            padding: 8px 6px;
            border: 1px solid #e2e8f0;
        }
        .table-data tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-masuk {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-keluar {
            background-color: #ffedd5;
            color: #9a3412;
        }
        .badge-permintaan {
            background-color: #f3e8ff;
            color: #5b21b6;
        }
        .badge-rusak {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-status-selesai {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-status-menunggu {
            background-color: #fef3c7;
            color: #92400e;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #777;
        }
        /* Style khusus cetak */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body @if(isset($is_print) && $is_print) onload="window.print(); window.onafterprint = function() { window.close(); }" @endif>

    <div class="header">
        <h2>ASA TIRTA</h2>
        <p>Laporan Gudang Terpadu</p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%"><strong>Periode</strong></td>
            <td style="width: 35%">: 
                {{ request('tanggal_awal') ? date('d-m-Y', strtotime(request('tanggal_awal'))) : 'Semua' }} 
                s/d 
                {{ request('tanggal_akhir') ? date('d-m-Y', strtotime(request('tanggal_akhir'))) : 'Semua' }}
            </td>
            <td style="width: 15%"><strong>Tanggal Cetak</strong></td>
            <td style="width: 35%">: {{ date('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Jenis Laporan</strong></td>
            <td>: {{ request('jenis_laporan', 'Semua') }}</td>
            <td><strong>Dicetak Oleh</strong></td>
            <td>: {{ auth()->check() ? auth()->user()->name : 'Sistem' }}</td>
        </tr>
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%" class="text-center">No</th>
                <th style="width: 12%">Tanggal</th>
                <th style="width: 18%">No Referensi</th>
                <th style="width: 15%">Jenis Laporan</th>
                <th style="width: 25%">Produk</th>
                <th style="width: 10%" class="text-right">Qty (Kardus)</th>
                <th style="width: 10%" class="text-right">Jumlah (Pcs)</th>
                <th style="width: 10%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $key => $item)
                @php
                    $dateStr = date('d M Y', strtotime($item->tanggal));
                    $dateStrIndo = strtr($dateStr, [
                        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
                        'Jul' => 'Jul', 'Aug' => 'Ags', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
                    ]);

                    // Badge class
                    $badgeClass = '';
                    if ($item->jenis_laporan === 'Barang Masuk') {
                        $badgeClass = 'badge-masuk';
                    } elseif ($item->jenis_laporan === 'Barang Keluar') {
                        $badgeClass = 'badge-keluar';
                    } elseif ($item->jenis_laporan === 'Permintaan Stok') {
                        $badgeClass = 'badge-permintaan';
                    } elseif ($item->jenis_laporan === 'Barang Rusak') {
                        $badgeClass = 'badge-rusak';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td class="text-center">{{ $dateStrIndo }}</td>
                    <td><strong>{{ $item->no_referensi }}</strong></td>
                    <td>
                        @if(isset($is_excel) && $is_excel)
                            {{ $item->jenis_laporan }}
                        @else
                            <span class="badge {{ $badgeClass }}">{{ $item->jenis_laporan }}</span>
                        @endif
                    </td>
                    <td>{{ $item->produk }}</td>
                    <td class="text-right">{{ $item->qty ?? 0 }}</td>
                    <td class="text-right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if(isset($is_excel) && $is_excel)
                            {{ $item->status }}
                        @else
                            @if(strtolower($item->status) === 'selesai')
                                <span class="badge badge-status-selesai">Selesai</span>
                            @elseif(strtolower($item->status) === 'menunggu')
                                <span class="badge badge-status-menunggu">Menunggu</span>
                            @else
                                <span class="badge" style="background-color: #cbd5e1; color: #475569;">{{ $item->status }}</span>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Manajemen ASA Tirta.
    </div>

</body>
</html>
