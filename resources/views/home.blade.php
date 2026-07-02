@extends('layouts.app', [
    'title' => 'Dashboard',
    'subtitle' => 'Ringkasan harian | ' . now()->format('d M Y'),
])

@section('content')
    <div class="grid">
        <div class="card metric">
            <span>Produksi Hari Ini</span>
            <h3 style="font-size: 26px; margin-bottom: 6px;">{{ $produksiToday }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Jumlah batch yang dibuat hari ini.</p>
        </div>
        <div class="card metric">
            <span>Pengiriman</span>
            <h3 style="font-size: 26px; margin-bottom: 6px;">{{ $pengirimanTotal }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Total pengiriman terdaftar.</p>
        </div>
        <div class="card metric">
            <span>Invoice</span>
            <h3 style="font-size: 26px; margin-bottom: 6px;">{{ $invoiceTotal }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Total invoice yang dibuat.</p>
        </div>
        <div class="card metric">
            <span>QC</span>
            <h3 style="font-size: 26px; margin-bottom: 6px;">{{ $qcTotal }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Total data QC.</p>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h2 style="font-size: 18px; margin-bottom: 8px;">Grafik Produksi 7 Hari</h2>
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; align-items: end; height: 160px; padding-top: 10px;">
                @foreach ($chartDays as $index => $day)
                    <div style="display: grid; gap: 6px; text-align: center;">
                        <div style="height: {{ $chartHeights[$index] }}px; background: linear-gradient(180deg, #1f6feb 0%, #7aa2f7 100%); border-radius: 10px;"></div>
                        <span style="font-size: 11px; color: var(--muted);">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card">
            <h2 style="font-size: 18px; margin-bottom: 8px;">Aktivitas Terbaru</h2>
            <div class="list">
                @foreach ($activities as $activity)
                    <div>
                        <strong>{{ $activity['title'] }}</strong> - {{ $activity['detail'] }}
                        @if ($activity['time'])
                            <div style="font-size: 12px; color: #94a3b8;">{{ $activity['time']->format('H:i') }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <h2 style="font-size: 18px; margin-bottom: 8px;">Aksi Cepat</h2>
            <div class="actions">
                <button class="btn btn-primary" type="button">Buat Produksi</button>
                <button class="btn btn-ghost" type="button">Tambah Pengiriman</button>
                <button class="btn btn-ghost" type="button">Lihat Laporan</button>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 14px;">
                @csrf
                <button class="btn btn-ghost" type="submit">Logout</button>
            </form>
        </div>
        <div class="card">
            <h2 style="font-size: 18px; margin-bottom: 8px;">Catatan Singkat</h2>
            <p style="color: var(--muted); font-size: 14px;">Gunakan menu di sisi kiri untuk masuk ke modul terkait.</p>
        </div>
    </div>
@endsection