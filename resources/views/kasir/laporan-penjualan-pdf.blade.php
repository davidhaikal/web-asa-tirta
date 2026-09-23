<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #334155; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 16px; font-weight: bold; margin-bottom: 5px; color: #0f172a; }
        .periode { color: #64748b; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        th { background-color: #0d6efd; color: white; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .nominal-masuk { color: #198754; font-weight: bold; }
        .nominal-keluar { color: #dc3545; font-weight: bold; }
        .nominal-piutang { color: #0d6efd; font-weight: bold; }
        .nominal-pending { color: #fd7e14; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN KEUANGAN - ASA TIRTA</div>
        <div class="periode">Periode: {{ $labelPeriode }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th class="text-center" style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">No Referensi</th>
                <th style="width: 20%;">Customer / Supplier</th>
                <th>Keterangan</th>
                <th class="text-center" style="width: 15%;">Nominal</th>
                <th class="text-center" style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $index => $t)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($t['tanggal'])->format('d M Y') }}</td>
                    <td class="text-bold">{{ $t['referensi'] }}</td>
                    <td>{{ $t['customer_supplier'] }}</td>
                    <td>{{ $t['keterangan'] }}</td>
                    <td class="text-center">
                        @if($t['status'] == 'Masuk')
                            <span class="nominal-masuk">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</span>
                        @elseif($t['status'] == 'Keluar')
                            <span class="nominal-keluar">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</span>
                        @elseif($t['status'] == 'Piutang')
                            <span class="nominal-piutang">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</span>
                        @else
                            <span class="nominal-pending">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $t['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 50%; margin-left: auto; margin-top: 30px; border: 1px solid #e2e8f0; background-color: #f8fafc;">
        <tr>
            <td class="text-bold" style="border: none; padding: 6px 12px;">Total Pendapatan:</td>
            <td class="text-bold text-right" style="border: none; padding: 6px 12px; color: #198754;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-bold" style="border: none; padding: 6px 12px;">Total Pembelian:</td>
            <td class="text-bold text-right" style="border: none; padding: 6px 12px; color: #dc3545;">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-bold" style="border: none; padding: 6px 12px;">Total Piutang:</td>
            <td class="text-bold text-right" style="border: none; padding: 6px 12px; color: #0d6efd;">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-bold" style="border: none; padding: 6px 12px;">Total Penagihan:</td>
            <td class="text-bold text-right" style="border: none; padding: 6px 12px; color: #7c3aed;">Rp {{ number_format($totalPenagihan, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
