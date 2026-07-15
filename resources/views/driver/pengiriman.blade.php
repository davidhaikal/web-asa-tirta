@extends('layouts.app')

@section('title', $mode === 'detail' ? 'Detail Pengiriman' : 'Pengiriman')

@section('content')
<div class="container-fluid py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ====================== MODE: LIST ====================== --}}
    @if ($mode === 'list')

        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div class="d-flex gap-3">
                <div class="header-icon">🚚</div>
                <div>
                    <h2 class="mb-0">Data Pengiriman</h2>
                    <p class="mb-0">Kelola seluruh proses pengiriman barang kepada pelanggan.</p>
                </div>
            </div>
        </div>

        <div class="content-card">

            <form method="GET" class="d-flex flex-wrap gap-2 align-items-center mb-4">
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control search-input" placeholder="🔍 Cari Invoice, Customer, atau Alamat...">

                <div class="btn-group tab-filter">
                    <button type="submit" name="tab" value="semua" class="btn {{ $tab === 'semua' ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</button>
                    <button type="submit" name="tab" value="hari-ini" class="btn {{ $tab === 'hari-ini' ? 'btn-primary' : 'btn-outline-secondary' }}">Invoice Baru</button>
                    <button type="submit" name="tab" value="dikirim" class="btn {{ $tab === 'dikirim' ? 'btn-primary' : 'btn-outline-secondary' }}">Sedang Dikirim</button>
                    <button type="submit" name="tab" value="selesai" class="btn {{ $tab === 'selesai' ? 'btn-primary' : 'btn-outline-secondary' }}">Selesai</button>
                </div>
            </form>

            <div class="d-flex flex-column gap-3">
                @forelse ($daftarPengiriman as $item)
                    <div class="item-pengiriman border-{{ $item->status }}">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                            <div class="d-flex gap-3 align-items-center">
                                <div class="item-icon icon-{{ $item->status }}">
                                    @if ($item->status === 'baru') 📄
                                    @elseif (in_array($item->status, ['berangkat', 'sampai'])) 🚚
                                    @else ✅
                                    @endif
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $item->no_invoice }}</h6>
                                    <span class="small d-block">{{ $item->customer }}</span>
                                    <small class="text-muted">📍 {{ $item->alamat }}</small><br>
                                    <small class="text-muted">📅 {{ $item->tanggal_kirim }} • {{ $item->jam }}</small>
                                </div>
                            </div>

                            <div class="text-center">
                                <small class="text-muted d-block">Total Barang</small>
                                <span class="fw-bold">{{ collect($item->items ?? [])->sum('qty') ?: '-' }} Dus</span>
                            </div>

                            <div class="text-end">
                                <small class="text-muted d-block mb-1">Status Pengiriman</small>
                                <span class="badge-status status-{{ $item->status }}">
                                    {{ \App\Http\Controllers\DriverController::LABEL_STATUS[$item->status] ?? ucfirst($item->status) }}
                                </span>
                            </div>

                            <a href="{{ route('driver.pengiriman', $item->id) }}"
                               class="btn {{ in_array($item->status, ['baru', 'selesai']) ? 'btn-outline-primary' : 'btn-primary' }} rounded-pill">
                                {{ in_array($item->status, ['baru', 'selesai']) ? 'Detail' : 'Lanjutkan' }}
                            </a>

                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-5 mb-0">Tidak ada data pengiriman yang cocok.</p>
                @endforelse
            </div>

        </div>

    {{-- ====================== MODE: DETAIL ====================== --}}
    @else

        <a href="{{ route('driver.pengiriman') }}" class="text-decoration-none mb-3 d-inline-block">
            &larr; Kembali ke Daftar Pengiriman
        </a>

        <h4 class="fw-bold mb-3">Detail Pengiriman</h4>

        <div class="row g-4">

            <!-- Informasi Pengiriman -->
            <div class="col-lg-4">
                <div class="content-card h-100">
                    <h6 class="fw-bold mb-3">Informasi Pengiriman</h6>

                    <div class="mb-3">
                        <small class="text-muted d-block">Invoice</small>
                        <span class="fw-semibold">{{ $pengiriman->no_invoice }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Customer</small>
                        <span class="fw-semibold">{{ $pengiriman->customer }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">No. Telepon</small>
                        <span>{{ $pengiriman->no_telp }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Alamat</small>
                        <span>{{ $pengiriman->alamat }}</span>
                    </div>

                    <div>
                        <small class="text-muted d-block">Tanggal Kirim</small>
                        <span>{{ $pengiriman->tanggal_kirim }} &bull; {{ $pengiriman->jam }}</span>
                    </div>
                </div>
            </div>

            <!-- Daftar Produk -->
            <div class="col-lg-4">
                <div class="content-card h-100">
                    <h6 class="fw-bold mb-3">Daftar Produk</h6>

                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengiriman->items as $item)
                                <tr>
                                    <td>{{ $item->produk }}</td>
                                    <td class="text-end">{{ $item->qty }} Dus</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold border-top">
                                <td>Total</td>
                                <td class="text-end">{{ $pengiriman->items->sum('qty') }} Dus</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Status Pengiriman (Timeline) -->
            <div class="col-lg-4">
                <div class="content-card h-100">
                    <h6 class="fw-bold mb-3">Status Pengiriman</h6>

                    @php
                        $timeline = [
                            'siap'      => 'Siap Dikirim',
                            'berangkat' => 'Berangkat',
                            'sampai'    => 'Sampai Tujuan',
                            'selesai'   => 'Selesai',
                        ];
                        $urutanTimeline = array_keys($timeline);
                        $posisiSaatIni = array_search($pengiriman->status, $urutanTimeline);
                        $posisiSaatIni = $posisiSaatIni === false ? -1 : $posisiSaatIni;
                    @endphp

                    <div class="timeline">
                        @foreach ($timeline as $key => $label)
                            <div class="timeline-item {{ $loop->index <= $posisiSaatIni ? 'done' : '' }} {{ $loop->index === $posisiSaatIni ? 'current' : '' }}">
                                <div class="timeline-dot"></div>
                                <div>
                                    <span class="fw-semibold">{{ $label }}</span>
                                    @if ($loop->index === $posisiSaatIni)
                                        <br><small class="text-muted">Status saat ini</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    @if ($pengiriman->status === 'baru')
                        <form action="{{ route('driver.pengiriman.terima', $pengiriman->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                ✅ Terima Invoice
                            </button>
                        </form>
                    @endif

                    @if ($pengiriman->status === 'siap')
                        <form action="{{ route('driver.pengiriman.mulai', $pengiriman->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                🚚 Mulai Pengiriman
                            </button>
                        </form>
                    @endif

                    @if ($pengiriman->status === 'berangkat')
                        <form action="{{ route('driver.pengiriman.upload-bukti', $pengiriman->id) }}"
                              method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="form-label small fw-semibold">📷 Upload Bukti Sampai Tujuan</label>
                            <input type="file" name="bukti_foto" accept="image/*" class="form-control form-control-sm mb-2" required>
                            <button type="submit" class="btn btn-outline-primary w-100 rounded-pill">
                                Upload Bukti
                            </button>
                        </form>
                    @endif

                    @if ($pengiriman->status === 'sampai')
                        <form action="{{ route('driver.pengiriman.selesai', $pengiriman->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 rounded-pill">
                                🏁 Konfirmasi Selesai
                            </button>
                        </form>
                    @endif

                    @if ($pengiriman->status === 'selesai')
                        <div class="alert alert-success mb-0 text-center">
                            🎉 Pengiriman selesai
                        </div>
                    @endif

                </div>
            </div>

        </div>

    @endif

</div>

<style>
    .header-icon {
        width: 48px;
        height: 48px;
        background: #2563eb;
        color: #fff;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    h2 {
        font-weight: 700;
        color: #1e293b;
    }

    .content-card {
        background: #fff;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
    }

    .search-input {
        flex: 1;
        min-width: 220px;
        border-radius: 12px;
    }

    .tab-filter .btn {
        border-radius: 10px !important;
        margin-left: 4px;
        font-size: 13px;
        font-weight: 600;
    }

    .item-pengiriman {
        border: 1px solid #eef0f4;
        border-left: 5px solid #94a3b8;
        border-radius: 14px;
        padding: 16px 20px;
    }

    .item-pengiriman.border-baru      { border-left-color: #f59e0b; }
    .item-pengiriman.border-siap      { border-left-color: #3b82f6; }
    .item-pengiriman.border-berangkat { border-left-color: #3b82f6; }
    .item-pengiriman.border-sampai    { border-left-color: #eab308; }
    .item-pengiriman.border-selesai   { border-left-color: #22c55e; }

    .item-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .icon-baru      { background: #fef3c7; }
    .icon-siap      { background: #dbeafe; }
    .icon-berangkat { background: #dbeafe; }
    .icon-sampai    { background: #fef9c3; }
    .icon-selesai   { background: #dcfce7; }

    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-baru      { background: #fef3c7; color: #b45309; }
    .status-siap      { background: #dbeafe; color: #1d4ed8; }
    .status-berangkat { background: #dbeafe; color: #1d4ed8; }
    .status-sampai    { background: #fef9c3; color: #854d0e; }
    .status-selesai   { background: #dcfce7; color: #15803d; }

    .timeline {
        position: relative;
        padding-left: 6px;
    }

    .timeline-item {
        display: flex;
        gap: 14px;
        position: relative;
        padding-bottom: 26px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 5px;
        top: 16px;
        bottom: -10px;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #e2e8f0;
        margin-top: 4px;
        flex-shrink: 0;
        z-index: 1;
    }

    .timeline-item.done .timeline-dot {
        background: #22c55e;
    }

    .timeline-item.done::before {
        background: #22c55e;
    }

    .timeline-item.current .timeline-dot {
        background: #2563eb;
        box-shadow: 0 0 0 4px #dbeafe;
    }

    .timeline-item span {
        color: #94a3b8;
    }

    .timeline-item.done span,
    .timeline-item.current span {
        color: #1e293b;
    }
</style>
@endsection