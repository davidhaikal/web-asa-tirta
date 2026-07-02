@extends('layouts.app', [
    'title' => 'Keuangan',
    'subtitle' => 'Ringkasan invoice dan pemasukan',
])

@section('content')
    <div class="grid">
        <div class="card">
            <span style="color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em;">Total Invoice</span>
            <h3 style="font-size: 26px; margin: 6px 0;">{{ $total }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Semua invoice yang tercatat.</p>
        </div>
        <div class="card">
            <span style="color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em;">Invoice Hari Ini</span>
            <h3 style="font-size: 26px; margin: 6px 0;">{{ $today }}</h3>
            <p style="color: var(--muted); font-size: 13px;">Invoice baru hari ini.</p>
        </div>
    </div>

    <div class="card">
        <h2 style="font-size: 18px; margin-bottom: 10px;">Invoice Terbaru</h2>
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
                        <td style="padding: 10px 0;">Terbit</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 10px 0; color: var(--muted);">Belum ada data invoice.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
