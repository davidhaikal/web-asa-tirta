@extends('layouts.app', [
    'title' => 'Gudang',
    'subtitle' => 'Ringkasan stok dan distribusi',
])

@section('content')
    <div class="grid">
        <div class="card">
            <span style="color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em;">Pengiriman Terdaftar</span>
            <h3 style="font-size: 26px; margin: 6px 0;">{{ $pengirimanTotal }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Total pengiriman yang tercatat.</p>
        </div>
        <div class="card">
            <span style="color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em;">Produksi Tercatat</span>
            <h3 style="font-size: 26px; margin: 6px 0;">{{ $produksiTotal }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Jumlah batch produksi.</p>
        </div>
    </div>

    <div class="card">
        <h2 style="font-size: 18px; margin-bottom: 8px;">Catatan Gudang</h2>
        <div class="list">
            <div><strong>Stok siap kirim</strong> diperiksa setiap pagi.</div>
            <div><strong>Barang masuk</strong> wajib dicatat di modul Produksi.</div>
            <div><strong>Barang keluar</strong> ikuti jadwal Pengiriman.</div>
        </div>
    </div>
@endsection
