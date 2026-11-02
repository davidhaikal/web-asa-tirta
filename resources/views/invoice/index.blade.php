@extends('layouts.kasir', [
    'title' => 'Daftar Invoice',
    'subtitle' => 'Kasir > Invoice',
])

@section('content')

<div class="k-page-head mb-4">
    <div>
        <h1 class="fw-bold">Faktur &amp; Invoice Penjualan</h1>
        <p class="text-muted">Invoice resmi dibuat otomatis oleh sistem saat transaksi penjualan berstatus lunas</p>
    </div>
</div>

{{-- ============ STATISTIK RINGKASAN ============ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-accent"><i class="bi bi-file-earmark-text"></i></span>
            <div>
                <div class="k-stat-label">Total Invoice Terbit</div>
                <div class="k-stat-value v-accent">{{ $totalInvoice }}</div>
                <div class="k-stat-sub">seluruh transaksi</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-green"><i class="bi bi-cash-stack"></i></span>
            <div>
                <div class="k-stat-label">Total Omzet Invoice</div>
                <div class="k-stat-value v-green">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
                <div class="k-stat-sub">akumulasi nilai</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-amber"><i class="bi bi-calendar-check"></i></span>
            <div>
                <div class="k-stat-label">Invoice Hari Ini</div>
                <div class="k-stat-value v-amber">{{ $invoiceHariIni }}</div>
                <div class="k-stat-sub">faktur terbit hari ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="k-stat">
            <span class="k-stat-icon i-info"><i class="bi bi-wallet2"></i></span>
            <div>
                <div class="k-stat-label">Omzet Hari Ini</div>
                <div class="k-stat-value v-info">Rp {{ number_format($nominalHariIni, 0, ',', '.') }}</div>
                <div class="k-stat-sub">pembayaran hari ini</div>
            </div>
        </div>
    </div>
</div>

{{-- ============ FILTER & PENCARIAN ============ --}}
<div class="k-card mb-4">
    <div class="k-card-body">
        <form method="GET" action="{{ route('invoice.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="k-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari No. Invoice / No. TRX / Pelanggan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="metode" class="form-select">
                    <option value="">-- Semua Metode Bayar --</option>
                    <option value="tunai" {{ request('metode') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="transfer" {{ request('metode') === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="qris" {{ request('metode') === 'qris' ? 'selected' : '' }}>QRIS</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status_kirim" class="form-select">
                    <option value="">-- Status Kirim --</option>
                    <option value="baru" {{ request('status_kirim') === 'baru' ? 'selected' : '' }}>Menunggu Driver</option>
                    <option value="siap" {{ request('status_kirim') === 'siap' ? 'selected' : '' }}>Siap Dikirim</option>
                    <option value="berangkat" {{ request('status_kirim') === 'berangkat' ? 'selected' : '' }}>Sedang Dikirim</option>
                    <option value="selesai" {{ request('status_kirim') === 'selesai' ? 'selected' : '' }}>Selesai / Terkirim</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="k-btn k-btn-primary flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('invoice.index') }}" class="k-btn k-btn-ghost" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ============ TABEL INVOICE ============ --}}
<div class="k-card">
    <div class="k-card-head d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2><i class="bi bi-receipt-cutoff me-2" style="color: var(--k-accent);"></i>Daftar Faktur / Invoice</h2>
            <small>Menampilkan faktur penjualan yang telah dibayar lunas</small>
        </div>
        <span class="k-badge b-accent">{{ $invoices->total() }} Faktur Ditemukan</span>
    </div>
    <div class="k-card-body p-0">
        <div class="table-responsive">
            <table class="k-table">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>No. Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Terbit</th>
                        <th>Metode</th>
                        <th class="t-right">Total Tagihan</th>
                        <th>Status Invoice</th>
                        <th>Status Kirim (Driver)</th>
                        <th class="t-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        @php
                            $metode = strtolower($inv->penjualan->metode ?? 'tunai');
                            $pengiriman = $inv->penjualan->pengiriman ?? null;
                            $statusKirim = $pengiriman ? strtolower($pengiriman->status) : null;
                        @endphp
                        <tr>
                            <td class="fw-bold text-primary">
                                <i class="bi bi-file-earmark-text me-1"></i>{{ $inv->invoice_no ?? '-' }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $inv->penjualan->kode ?? '-' }}</span>
                            </td>
                            <td class="fw-semibold">{{ $inv->pelanggan ?? '-' }}</td>
                            <td>{{ $inv->tanggal ? \Carbon\Carbon::parse($inv->tanggal)->format('d M Y') : '-' }}</td>
                            <td>
                                @if ($metode === 'tunai')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-cash me-1"></i>TUNAI</span>
                                @elseif ($metode === 'transfer')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-bank me-1"></i>TRANSFER</span>
                                @elseif ($metode === 'qris')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="bi bi-qr-code me-1"></i>QRIS</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ strtoupper($metode) }}</span>
                                @endif
                            </td>
                            <td class="t-right fw-bold text-dark">
                                Rp {{ number_format($inv->total, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="k-badge b-lunas"><i class="bi bi-check-circle-fill me-1"></i> Terbit</span>
                            </td>
                            <td>
                                @if (!$pengiriman)
                                    <span class="badge bg-light text-muted border">— Langsung —</span>
                                @elseif ($statusKirim === 'baru')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="bi bi-clock-history me-1"></i>Menunggu Driver</span>
                                @elseif ($statusKirim === 'siap')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="bi bi-box-seam me-1"></i>Siap Dikirim</span>
                                @elseif ($statusKirim === 'berangkat')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-truck me-1"></i>Sedang Dikirim</span>
                                @elseif ($statusKirim === 'sampai' || $statusKirim === 'selesai')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-all me-1"></i>Terkirim</span>
                                    @if ($pengiriman->bukti_foto)
                                        <div class="mt-1">
                                            <span class="badge bg-light text-success border border-success-subtle" style="font-size: 0.72rem;">
                                                <i class="bi bi-camera-fill me-1"></i>Ada Bukti Foto
                                            </span>
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ ucfirst($statusKirim) }}</span>
                                @endif
                            </td>
                            <td class="t-right" style="white-space: nowrap;">
                                <button type="button" class="k-btn k-btn-ghost k-btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $inv->id }}" title="Lihat Rincian Item">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                                <a href="{{ route('invoice.cetak', $inv->id) }}" target="_blank" class="k-btn k-btn-primary k-btn-sm" title="Cetak Dokumen Faktur / Invoice">
                                    <i class="bi bi-printer"></i> Cetak
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                Belum ada data invoice yang sesuai pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <small class="text-muted">Menampilkan {{ $invoices->count() }} dari {{ $invoices->total() }} invoice</small>
            {{ $invoices->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- ============ MODALS DETAIL INVOICE (DITEMPATKAN DI LUAR TABEL) ============ --}}
@foreach($invoices as $inv)
<div class="modal fade" id="modalDetail{{ $inv->id }}" tabindex="-1" aria-labelledby="modalDetailLabel{{ $inv->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title fw-bold" id="modalDetailLabel{{ $inv->id }}">
                        <i class="bi bi-receipt text-primary me-2"></i>Rincian Faktur: {{ $inv->invoice_no }}
                    </h5>
                    <small class="text-muted">Tanggal: {{ $inv->tanggal ? \Carbon\Carbon::parse($inv->tanggal)->format('d F Y') : '-' }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                {{-- Metadata Grid --}}
                <div class="row g-3 p-3 bg-light rounded-3 mb-4 border">
                    <div class="col-sm-6">
                        <div class="text-muted small">No. Transaksi Asal</div>
                        <div class="fw-bold fs-6">{{ $inv->penjualan->kode ?? '-' }}</div>
                        
                        <div class="text-muted small mt-2">Nama Pelanggan</div>
                        <div class="fw-semibold text-dark">{{ $inv->pelanggan }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Metode Pembayaran</div>
                        <div class="fw-semibold text-uppercase text-primary">{{ $inv->penjualan->metode ?? 'Tunai' }}</div>
                        
                        <div class="text-muted small mt-2">Status Pengiriman (Driver)</div>
                        <div>
                            @if ($inv->penjualan && $inv->penjualan->pengiriman)
                                @php
                                    $st = strtolower($inv->penjualan->pengiriman->status);
                                @endphp
                                @if ($st === 'baru')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Menunggu Driver</span>
                                @elseif ($st === 'siap')
                                    <span class="badge bg-info text-dark"><i class="bi bi-box-seam me-1"></i>Siap Dikirim</span>
                                @elseif ($st === 'berangkat')
                                    <span class="badge bg-primary"><i class="bi bi-truck me-1"></i>Sedang Dikirim</span>
                                @elseif ($st === 'sampai' || $st === 'selesai')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Terkirim ({{ ucfirst($st) }})</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($st) }}</span>
                                @endif
                                <small class="text-muted ms-1">({{ \Carbon\Carbon::parse($inv->penjualan->pengiriman->tanggal_kirim)->format('d/m/Y') }})</small>
                            @else
                                <span class="text-muted">Tidak memerlukan pengiriman khusus</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Bukti Foto Serah Terima / Pengiriman Driver --}}
                @if ($inv->penjualan && $inv->penjualan->pengiriman)
                    @php
                        $p = $inv->penjualan->pengiriman;
                        $stKirim = strtolower($p->status);
                    @endphp
                    <div class="card border rounded-3 mb-4 overflow-hidden">
                        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-dark">
                                <i class="bi bi-camera-fill text-primary me-2"></i>Bukti Pengiriman Driver
                            </span>
                            @if ($p->bukti_foto)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-check2-circle me-1"></i>Foto Tersedia
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    <i class="bi bi-camera me-1"></i>Belum Ada Foto
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-3">
                            @if ($p->bukti_foto)
                                <div class="row g-3 align-items-center">
                                    <div class="col-sm-5 text-center">
                                        <a href="{{ asset('storage/' . $p->bukti_foto) }}" target="_blank" title="Klik untuk membuka ukuran penuh">
                                            <img src="{{ asset('storage/' . $p->bukti_foto) }}" 
                                                 alt="Bukti Pengiriman {{ $inv->invoice_no }}" 
                                                 class="img-fluid rounded-3 border shadow-sm"
                                                 style="max-height: 180px; width: 100%; object-fit: cover; cursor: pointer;">
                                        </a>
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $p->bukti_foto) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                                                <i class="bi bi-arrows-fullscreen me-1"></i> Buka Foto Penuh
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless mb-0 small">
                                                <tr>
                                                    <td class="text-muted" style="width: 140px;">Status Pengiriman:</td>
                                                    <td>
                                                        @if ($stKirim === 'selesai' || $stKirim === 'sampai')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                                                                <i class="bi bi-check-circle me-1"></i>{{ strtoupper($stKirim) }}
                                                            </span>
                                                        @elseif ($stKirim === 'berangkat')
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                                                                <i class="bi bi-truck me-1"></i>SEDANG DIKIRIM
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary fw-bold">{{ strtoupper($stKirim) }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Tanggal Jadwal:</td>
                                                    <td class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($p->tanggal_kirim)->translatedFormat('d F Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Waktu Dokumentasi:</td>
                                                    <td class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($p->updated_at)->translatedFormat('d F Y, H:i') }} WIB</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="alert alert-light border small text-muted mt-2 mb-0 py-2 px-3 rounded-2">
                                            <i class="bi bi-shield-check text-success me-1"></i> Foto ini diunggah langsung oleh driver saat tiba di lokasi serah terima sebagai bukti sah penerimaan barang.
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-3 text-muted">
                                    <div class="mb-2">
                                        <i class="bi bi-image fs-1 opacity-25"></i>
                                    </div>
                                    <div class="fw-semibold text-secondary small">Belum ada foto bukti pengiriman</div>
                                    @if ($stKirim === 'berangkat')
                                        <div class="small text-muted mt-1">Armada driver sedang dalam perjalanan mengantar pesanan ke lokasi tujuan.</div>
                                    @elseif ($stKirim === 'siap')
                                        <div class="small text-muted mt-1">Pesanan telah dikemas &amp; menunggu armada driver memulai perjalanan.</div>
                                    @elseif ($stKirim === 'baru')
                                        <div class="small text-muted mt-1">Pesanan baru masuk dan menunggu konfirmasi driver.</div>
                                    @else
                                        <div class="small text-muted mt-1">Pengiriman telah diselesaikan tanpa lampiran dokumentasi digital.</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Tabel Produk Yang Dibeli --}}
                <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-2 text-secondary"></i>Daftar Produk / Barang</h6>
                <div class="table-responsive border rounded-3 mb-3">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">#</th>
                                <th>Nama Produk</th>
                                <th class="text-center" style="width: 100px;">Jumlah</th>
                                <th class="text-end" style="width: 140px;">Harga Satuan</th>
                                <th class="text-end" style="width: 150px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($inv->penjualan && $inv->penjualan->detailPenjualans->isNotEmpty())
                                @foreach($inv->penjualan->detailPenjualans as $idx => $item)
                                    <tr>
                                        <td class="text-center">{{ $idx + 1 }}</td>
                                        <td class="fw-semibold">{{ $item->produk->nama_produk ?? 'Produk' }}</td>
                                        <td class="text-center">{{ $item->jumlah }} unit</td>
                                        <td class="text-end">Rp {{ number_format($item->produk->harga ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Rincian item tidak tersedia</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="table-group-divider bg-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold py-2">TOTAL AKHIR:</td>
                                <td class="text-end fw-bold text-success fs-6 py-2">Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                <a href="{{ route('invoice.cetak', $inv->id) }}" target="_blank" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-printer me-1"></i> Cetak Dokumen Invoice
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
