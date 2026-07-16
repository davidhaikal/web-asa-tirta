@extends('layouts.app')

@section('title', 'Dashboard Manajemen')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Dashboard Manajemen</h4>
    <form action="{{ url('/manajemen/dashboard') }}" method="GET" class="d-flex gap-2">
        <select name="period" class="form-select border-0 shadow-sm rounded-3 fw-bold text-primary" onchange="this.form.submit()" style="cursor: pointer;">
            <option value="semua" {{ $period == 'semua' ? 'selected' : '' }}>Semua Waktu</option>
            <option value="hari_ini" {{ $period == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
            <option value="bulan_ini" {{ $period == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="tahun_ini" {{ $period == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
        </select>
    </form>
</div>

<div class="row g-3 mb-4">

    @foreach($ringkasan as $jenis => $r)

        @php
            $warna = [
                'masuk'     => ['bg' => '#dcfce7', 'icon' => '#16a34a', 'i' => 'bi-box-arrow-in-down'],
                'keluar'    => ['bg' => '#ffedd5', 'icon' => '#ea580c', 'i' => 'bi-box-arrow-up'],
                'rusak'     => ['bg' => '#fee2e2', 'icon' => '#dc2626', 'i' => 'bi-exclamation-triangle-fill'],
                'penjualan' => ['bg' => '#dbeafe', 'icon' => '#2563eb', 'i' => 'bi-cash-coin'],
            ][$jenis];
        @endphp

        <div class="col-md-3">
            <div class="dash-card">

                <div class="dash-icon" style="background:{{ $warna['bg'] }}; color:{{ $warna['icon'] }};">
                    <i class="bi {{ $warna['i'] }}"></i>
                </div>

                <div class="dash-label">{{ $r['label'] }}</div>

                <div class="dash-value">{{ number_format($r['total_transaksi']) }}</div>
                <div class="dash-sub">Transaksi {{ strtolower($subLabel) }}</div>

                <div class="dash-footer">
                    <span>{{ number_format($r['total_jumlah']) }} item</span>
                    <span>Rp {{ number_format($r['total_nilai'], 0, ',', '.') }}</span>
                </div>

            </div>
        </div>

    @endforeach

</div>

<div class="produk-box">

    <h5 class="fw-bold mb-3">
        <i class="bi bi-clock-history text-primary"></i>
        Aktivitas Terkini
    </h5>

    <table class="table-aktivitas">

        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>No. Transaksi</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Nilai (Rp)</th>
            </tr>
        </thead>

        <tbody>

            @forelse($aktivitasTerbaru as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d M Y') }}</td>
                <td><span class="badge-jenis">{{ $row['jenis'] }}</span></td>
                <td>{{ $row['no_transaksi'] }}</td>
                <td>{{ $row['produk'] }}</td>
                <td>{{ number_format($row['jumlah']) }}</td>
                <td>Rp {{ number_format($row['nilai'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Belum ada aktivitas.</td>
            </tr>
            @endforelse

        </tbody>

    </table>

    <div class="text-end mt-3">
        <a href="{{ route('manajemen.laporan') }}" class="btn btn-outline-primary btn-sm">
            Lihat Semua Laporan <i class="bi bi-arrow-right"></i>
        </a>
    </div>

</div>

<style>

.dash-card{
    background:#fff;
    border-radius:16px;
    padding:20px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
    height:100%;
}

.dash-icon{
    width:44px;
    height:44px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    margin-bottom:12px;
}

.dash-label{
    font-size:13px;
    color:#64748b;
    font-weight:600;
    margin-bottom:4px;
}

.dash-value{
    font-size:26px;
    font-weight:800;
    color:#0f172a;
    line-height:1.1;
}

.dash-sub{
    font-size:12px;
    color:#94a3b8;
    margin-bottom:12px;
}

.dash-footer{
    display:flex;
    justify-content:space-between;
    font-size:12px;
    color:#475569;
    border-top:1px solid #f1f5f9;
    padding-top:10px;
    font-weight:600;
}

.produk-box{
    background:#fff;
    padding:25px;
    border-radius:20px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

.table-aktivitas{
    width:100%;
    border-collapse:collapse;
}

.table-aktivitas th{
    text-align:left;
    padding:12px;
    background:#f8fafc;
    color:#334155;
    font-size:13px;
    font-weight:600;
    border-bottom:1px solid #e5e7eb;
}

.table-aktivitas td{
    padding:12px;
    border-bottom:1px solid #f1f5f9;
    font-size:13.5px;
    color:#1f2937;
}

.badge-jenis{
    background:#eef2ff;
    color:#4338ca;
    padding:4px 10px;
    border-radius:20px;
    font-size:11.5px;
    font-weight:600;
}

</style>

@endsection