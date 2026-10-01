@extends('layouts.kasir', [
    'title' => 'Nota Penjualan',
    'subtitle' => 'Kasir > Nota',
])

@section('content')

<div class="k-page-head">
    <div>
        <h1>Nota Penjualan</h1>
        <p>Daftar transaksi yang sudah selesai (kecuali dibatalkan)</p>
    </div>
</div>

<div class="k-card">
    <div class="k-card-body">
        <form method="GET" action="{{ route('kasir.nota') }}" class="row g-2 align-items-center">
            <div class="col-md-8">
                <div class="k-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Kode transaksi atau nama pelanggan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="k-btn k-btn-primary flex-grow-1"><i class="bi bi-search"></i> Cari</button>
                    <a href="{{ route('kasir.nota') }}" class="k-btn k-btn-ghost">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="k-card">
    <div class="table-responsive">
        @if ($nota->isEmpty())
            <p class="k-empty"><i class="bi bi-receipt"></i>Belum ada nota.</p>
        @else
            <table class="k-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th class="t-right">Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th class="t-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nota as $n)
                        <tr>
                            <td class="fw-semibold">{{ $n->kode }}</td>
                            <td>{{ $n->pelanggan }}</td>
                            <td>{{ \Carbon\Carbon::parse($n->tanggal)->format('d M Y') }}</td>
                            <td class="t-right fw-semibold">Rp {{ number_format($n->total, 0, ',', '.') }}</td>
                            <td><span class="k-badge b-method">{{ strtoupper($n->metode) }}</span></td>
                            <td>
                                @if ($n->status === 'lunas')
                                    <span class="k-badge b-lunas">LUNAS</span>
                                @elseif ($n->status === 'pending')
                                    <span class="k-badge b-pending">BELUM LUNAS</span>
                                @else
                                    <span class="k-badge b-batal">BATAL</span>
                                @endif
                            </td>
                            <td class="t-right" style="white-space: nowrap;">
                                <a href="{{ route('kasir.nota.cetak', $n->id) }}" target="_blank" class="k-btn k-btn-ghost k-btn-sm">
                                    <i class="bi bi-printer"></i> Cetak Nota
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center" style="padding: 16px;">
                {{ $nota->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
