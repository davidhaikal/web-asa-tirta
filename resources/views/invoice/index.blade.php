@extends('layouts.kasir', [
    'title' => 'Invoice',
    'subtitle' => 'Kasir > Invoice',
])

@section('content')

<div class="k-page-head">
    <div>
        <h1>Invoice</h1>
        <p>Invoice dibuat otomatis oleh sistem saat transaksi dibayar</p>
    </div>
</div>

<div class="k-card">
    <div class="k-card-body">
        <form method="GET" action="{{ route('invoice.index') }}" class="row g-2 align-items-center">
            <div class="col-md-8">
                <div class="k-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari nomor invoice / pelanggan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="k-btn k-btn-primary flex-grow-1"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('invoice.index') }}" class="k-btn k-btn-ghost">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="k-card" style="margin-top: 20px;">
    <div class="k-card-body">
        <div class="table-responsive">
            <table class="k-table">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>No. Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th class="t-right">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="fw-semibold">{{ $inv->invoice_no ?? '-' }}</td>
                            <td>{{ $inv->penjualan->kode ?? '-' }}</td>
                            <td>{{ $inv->pelanggan ?? '-' }}</td>
                            <td>{{ $inv->tanggal ? \Carbon\Carbon::parse($inv->tanggal)->format('d M Y') : '-' }}</td>
                            <td class="t-right">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                            <td><span class="k-badge b-lunas">Terbit</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada invoice. Invoice dibuat otomatis saat transaksi dibayar kasir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $invoices->withQueryString()->links() }}
    </div>
</div>

@endsection
