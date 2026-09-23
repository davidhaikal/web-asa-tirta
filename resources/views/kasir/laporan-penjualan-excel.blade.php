<table>
    <thead>
        <tr>
            <th colspan="7" style="font-weight: bold; font-size: 16px; text-align: center;">LAPORAN KEUANGAN - ASA TIRTA</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">Periode: {{ $labelPeriode }}</th>
        </tr>
        <tr></tr>
        <tr>
            <th style="font-weight: bold; background-color: #0d6efd; color: #ffffff;">No</th>
            <th style="font-weight: bold; background-color: #0d6efd; color: #ffffff;">Tanggal</th>
            <th style="font-weight: bold; background-color: #0d6efd; color: #ffffff;">No Referensi</th>
            <th style="font-weight: bold; background-color: #0d6efd; color: #ffffff;">Customer / Supplier</th>
            <th style="font-weight: bold; background-color: #0d6efd; color: #ffffff;">Keterangan</th>
            <th style="font-weight: bold; background-color: #0d6efd; color: #ffffff;">Nominal</th>
            <th style="font-weight: bold; background-color: #0d6efd; color: #ffffff;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $index => $t)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($t['tanggal'])->format('d M Y') }}</td>
                <td>{{ $t['referensi'] }}</td>
                <td>{{ $t['customer_supplier'] }}</td>
                <td>{{ $t['keterangan'] }}</td>
                <td>{{ $t['nominal'] }}</td>
                <td>{{ $t['status'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr></tr>
        <tr>
            <td colspan="5" style="font-weight: bold; text-align: right;">Total Pendapatan:</td>
            <td style="font-weight: bold;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="5" style="font-weight: bold; text-align: right;">Total Pembelian:</td>
            <td style="font-weight: bold;">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="5" style="font-weight: bold; text-align: right;">Total Piutang:</td>
            <td style="font-weight: bold;">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="5" style="font-weight: bold; text-align: right;">Total Penagihan:</td>
            <td style="font-weight: bold;">Rp {{ number_format($totalPenagihan, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
