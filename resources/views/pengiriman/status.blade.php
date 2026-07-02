@extends('layouts.app')

@section('content')

<div class="container-status">

    <h2>Status Pengiriman</h2>

    <table class="status-table">

        <tr>
            <th>No</th>
            <th>Produk</th>
            <th>Driver</th>
            <th>Status</th>
        </tr>

        <tr>
            <td>1</td>
            <td>Aqua 600ml</td>
            <td>Budi</td>
            <td>
                <span class="status proses">
                    Diproses
                </span>
            </td>
        </tr>

        <tr>
            <td>2</td>
            <td>Le Minerale</td>
            <td>Andi</td>
            <td>
                <span class="status kirim">
                    Dikirim
                </span>
            </td>
        </tr>

        <tr>
            <td>3</td>
            <td>Cleo</td>
            <td>Rizal</td>
            <td>
                <span class="status selesai">
                    Selesai
                </span>
            </td>
        </tr>

    </table>

</div>

<style>

.container-status{
    padding:20px;
}

.status-table{
    width:100%;
    background:white;
    border-collapse:collapse;
    margin-top:20px;
    border-radius:15px;
    overflow:hidden;
}

.status-table th,
.status-table td{
    padding:15px;
    border-bottom:1px solid #eee;
}

.status{
    padding:8px 15px;
    border-radius:20px;
    color:white;
    font-size:13px;
}

.proses{
    background:orange;
}

.kirim{
    background:#2563eb;
}

.selesai{
    background:green;
}

</style>

@endsection