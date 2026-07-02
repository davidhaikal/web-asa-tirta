@extends('layouts.app', [
    'title' => 'Produksi',
    'subtitle' => 'Ringkasan produksi harian',
])

@section('content')
    <div class="grid">
        <div class="card">
            <span style="color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em;">Total Produksi</span>
            <h3 style="font-size: 26px; margin: 6px 0;">{{ $total }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Total data produksi tersimpan.</p>
        </div>
        <div class="card">
            <span style="color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em;">Produksi Hari Ini</span>
            <h3 style="font-size: 26px; margin: 6px 0;">{{ $today }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Jumlah batch dibuat hari ini.</p>
        </div>
    </div>

    <div class="card">
        <h2 style="font-size: 18px; margin-bottom: 10px;">Produksi Terbaru</h2>
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="text-align: left; color: var(--muted);">
                    <th style="padding: 8px 0;">ID</th>
                    <th style="padding: 8px 0;">Tanggal</th>
                    <th style="padding: 8px 0;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr style="border-top: 1px solid #e2e8f0;">
                        <td style="padding: 10px 0;">#{{ $item->id }}</td>
                        <td style="padding: 10px 0;">{{ $item->created_at->format('d M Y H:i') }}</td>
                        <td style="padding: 10px 0;">Tercatat</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 10px 0; color: var(--muted);">Belum ada data produksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
