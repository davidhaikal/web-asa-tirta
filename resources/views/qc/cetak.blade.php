<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Quality Control</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            font-family: Arial, Helvetica, sans-serif;
            font-size:14px;
            color:#000;
            margin:40px;
        }

        .header{
            text-align:center;
            border-bottom:3px solid #000;
            padding-bottom:15px;
            margin-bottom:25px;
        }

        .header h2{
            margin:0;
            font-weight:bold;
        }

        .header h5{
            margin-top:5px;
            color:#666;
        }

        table{
            width:100%;
        }

        th{
            background:#0d6efd !important;
            color:white !important;
            text-align:center;
        }

        td{
            vertical-align:middle;
        }

        .badge{
            padding:6px 12px;
            border-radius:20px;
        }

        .footer{
            margin-top:70px;
        }

        @media print{

            .no-print{
                display:none;
            }

            body{
                margin:20px;
            }

        }

    </style>

</head>

<body>

<div class="container-fluid">

    <!-- Header -->

    <div class="header">

        <h2>ASA TIRTA</h2>

        <h5>Laporan Pemeriksaan Quality Control</h5>

    </div>

    <div class="row mb-4">

        <div class="col-6">

            <strong>Tanggal Cetak :</strong>

            {{ date('d F Y') }}

        </div>

        <div class="col-6 text-end">

            <strong>Total Data :</strong>

            {{ $dataQc->count() }}

        </div>

    </div>

    <!-- Tabel -->

    <table class="table table-bordered">

        <thead>

        <tr>

            <th>No</th>

            <th>Tanggal</th>

            <th>Produk</th>

            <th>Keterangan</th>

            <th>Pemeriksa</th>

            <th>Status</th>

        </tr>

        </thead>

        <tbody>

        @forelse($dataQc as $q)

        <tr>

            <td class="text-center">

                {{ $loop->iteration }}

            </td>

            <td>

                {{ $q->created_at->format('d M Y H:i') }}

            </td>

            <td>

                {{ $q->produksi->produk->nama_produk ?? '-' }}

            </td>

            <td>

                {{ $q->keterangan ?? '-' }}

            </td>

            <td>

                QC User

            </td>

            <td class="text-center">

                @if($q->hasil == 'Layak')

                    <span class="badge bg-success">

                        Lolos

                    </span>

                @else

                    <span class="badge bg-danger">

                        Reject

                    </span>

                @endif

            </td>

        </tr>

        @empty

        <tr>

            <td colspan="6" class="text-center">

                Tidak ada data pemeriksaan.

            </td>

        </tr>

        @endforelse

        </tbody>

    </table>

    <!-- Tanda tangan -->

    <div class="footer">

        <div class="row">

            <div class="col-8"></div>

            <div class="col-4 text-center">

                <p>Malang, {{ date('d F Y') }}</p>

                <br><br><br>

                <strong>QC Supervisor</strong>

            </div>

        </div>

    </div>

    <!-- Tombol -->

    <div class="text-center mt-4 no-print">

        <button onclick="window.print()" class="btn btn-primary">

            🖨 Cetak Laporan

        </button>

        <a href="/qc" class="btn btn-secondary">

            Kembali

        </a>

    </div>

</div>

<script>

window.onload = function(){

    window.print();

}

</script>

</body>

</html>