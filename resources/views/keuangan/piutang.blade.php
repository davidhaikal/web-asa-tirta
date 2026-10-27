@extends('layouts.app')

@section('title', 'Piutang & Pendapatan')

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="k-page-head mb-4">
    <div>
        <h1 class="fw-bold">Piutang &amp; Pendapatan</h1>
        <p class="text-muted">Kelola data piutang customer, pelunasan pembayaran, dan monitoring pendapatan penjualan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('keuangan.export.pdf') }}" class="btn btn-outline-danger rounded-pill px-3">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
        <a href="{{ route('keuangan.export.excel') }}" class="btn btn-outline-success rounded-pill px-3">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
    </div>
</div>

<!-- ========================== STATISTIK ========================== -->
<div class="row g-3 mb-4">
    <!-- Card Total Piutang -->
    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-red"><i class="bi bi-wallet2"></i></span>
            <div>
                <div class="k-stat-label">Total Piutang Berjalan</div>
                <div class="k-stat-value v-red">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
                <div class="k-stat-sub">{{ $transaksiPendingCount ?? 0 }} transaksi belum lunas</div>
            </div>
        </div>
    </div>

    <!-- Card Belum Bayar -->
    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-amber"><i class="bi bi-exclamation-circle"></i></span>
            <div>
                <div class="k-stat-label">Customer Menunggak</div>
                <div class="k-stat-value v-amber">{{ $belumDibayar ?? 0 }} Customer</div>
                <div class="k-stat-sub">Memiliki tagihan aktif</div>
            </div>
        </div>
    </div>

    <!-- Card Pendapatan Hari Ini -->
    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-accent"><i class="bi bi-cash-coin"></i></span>
            <div>
                <div class="k-stat-label">Pendapatan Hari Ini</div>
                <div class="k-stat-value v-accent">Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</div>
                <div class="k-stat-sub">Kas masuk {{ now()->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Card Pendapatan Bulan Ini -->
    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-green"><i class="bi bi-check-circle"></i></span>
            <div>
                <div class="k-stat-label">Pendapatan Bulan Ini</div>
                <div class="k-stat-value v-green">Rp {{ number_format($pendapatanBulanIni ?? 0, 0, ',', '.') }}</div>
                <div class="k-stat-sub">{{ $transaksiLunasCount ?? 0 }} transaksi lunas</div>
            </div>
        </div>
    </div>
</div>

<!-- ========================== TABS NAVIGASI ========================== -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <ul class="nav nav-pills gap-2" id="piutangPendapatanTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 {{ $activeTab !== 'pendapatan' ? 'active' : '' }}" 
                        id="tab-piutang-btn" 
                        data-bs-toggle="pill" 
                        data-bs-target="#tab-piutang" 
                        type="button" 
                        role="tab" 
                        aria-controls="tab-piutang" 
                        aria-selected="{{ $activeTab !== 'pendapatan' ? 'true' : 'false' }}">
                    <i class="bi bi-hourglass-split me-1"></i> Piutang Customer (Belum Lunas)
                    <span class="badge bg-danger ms-2">{{ count($piutangList) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 {{ $activeTab === 'pendapatan' ? 'active' : '' }}" 
                        id="tab-pendapatan-btn" 
                        data-bs-toggle="pill" 
                        data-bs-target="#tab-pendapatan" 
                        type="button" 
                        role="tab" 
                        aria-controls="tab-pendapatan" 
                        aria-selected="{{ $activeTab === 'pendapatan' ? 'true' : 'false' }}">
                    <i class="bi bi-cash-stack me-1"></i> Pendapatan Penjualan (Sudah Lunas)
                    <span class="badge bg-success ms-2">{{ $transaksiLunasCount }}</span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="piutangPendapatanTabContent">
            
            <!-- ======================================================= -->
            <!-- TAB 1: DATA PIUTANG (BELUM LUNAS)                      -->
            <!-- ======================================================= -->
            <div class="tab-pane fade {{ $activeTab !== 'pendapatan' ? 'show active' : '' }}" 
                 id="tab-piutang" 
                 role="tabpanel" 
                 aria-labelledby="tab-piutang-btn">
                
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Daftar Piutang Customer</h5>
                        <small class="text-muted">Transaksi penjualan tempo/kredit yang belum dibayar</small>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('keuangan.piutang') }}" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="piutang">
                        <input type="text" name="search" class="form-control rounded-pill px-3" 
                               placeholder="Cari kode / customer..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary rounded-pill px-3">
                            <i class="bi bi-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('keuangan.piutang', ['tab' => 'piutang']) }}" class="btn btn-outline-secondary rounded-pill">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No Transaksi</th>
                                <th>Customer</th>
                                <th>Rincian Produk</th>
                                <th>Tanggal Transaksi</th>
                                <th>Total Piutang</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($piutangList as $piutang)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $piutang->kode }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $piutang->pelanggan ?? 'Walk-in Customer' }}</div>
                                </td>
                                <td>
                                    @if($piutang->detailPenjualans && $piutang->detailPenjualans->count())
                                        @foreach($piutang->detailPenjualans as $d)
                                            <span class="badge bg-light text-dark border me-1 mb-1">
                                                {{ $d->produk->nama_produk ?? 'Produk' }} x{{ $d->jumlah }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($piutang->tanggal)->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="fw-bold text-danger fs-6">
                                        Rp {{ number_format($piutang->total, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                        <i class="bi bi-hourglass-split me-1"></i> Belum Lunas
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Tombol Pelunasan -->
                                        <button type="button" class="btn btn-success rounded-pill px-3 me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalBayar{{ $piutang->id }}"
                                                title="Proses Pelunasan Piutang">
                                            <i class="bi bi-check2-circle me-1"></i> Lunasi
                                        </button>
                                        
                                        <!-- Tombol Edit -->
                                        <button type="button" class="btn btn-outline-secondary rounded-pill me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEdit{{ $piutang->id }}"
                                                title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <button type="button" class="btn btn-outline-danger rounded-pill" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalDelete{{ $piutang->id }}"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- MODAL LUNASI PIUTANG -->
                            <div class="modal fade" id="modalBayar{{ $piutang->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('keuangan.piutang.bayar', $piutang->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-success">
                                                    <i class="bi bi-check2-circle me-1"></i> Konfirmasi Pelunasan Piutang
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <div class="p-3 bg-light rounded-3 mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted">Kode Transaksi:</span>
                                                        <span class="fw-bold">{{ $piutang->kode }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span class="text-muted">Customer:</span>
                                                        <span class="fw-semibold">{{ $piutang->pelanggan ?? 'Walk-in Customer' }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <span class="text-muted">Total Pembayaran:</span>
                                                        <span class="fw-bold text-danger fs-5">Rp {{ number_format($piutang->total, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Metode Pembayaran</label>
                                                    <select class="form-select" name="metode" required>
                                                        <option value="tunai" {{ $piutang->metode === 'tunai' ? 'selected' : '' }}>Tunai / Cash</option>
                                                        <option value="transfer" {{ $piutang->metode === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                                        <option value="qris" {{ $piutang->metode === 'qris' ? 'selected' : '' }}>QRIS</option>
                                                    </select>
                                                    <small class="text-muted">Pilih metode yang digunakan customer untuk melunasi tagihan.</small>
                                                </div>

                                                <p class="small text-muted mb-0">
                                                    <i class="bi bi-info-circle me-1"></i> Setelah dilunasi, status transaksi akan berubah menjadi <strong>Lunas</strong> dan otomatis tercatat sebagai <strong>Pendapatan Masuk</strong>.
                                                </p>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success rounded-pill px-4">
                                                    <i class="bi bi-check-lg me-1"></i> Simpan Pelunasan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL EDIT PIUTANG -->
                            <div class="modal fade" id="modalEdit{{ $piutang->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('keuangan.piutang.update', $piutang->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">Edit Piutang ({{ $piutang->kode }})</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Customer</label>
                                                    <input type="text" class="form-control" name="pelanggan" value="{{ $piutang->pelanggan }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Total Tagihan (Rp)</label>
                                                    <input type="number" class="form-control" name="total" value="{{ $piutang->total }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Tanggal Transaksi</label>
                                                    <input type="date" class="form-control" name="tanggal" value="{{ \Carbon\Carbon::parse($piutang->tanggal)->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Metode Pembayaran</label>
                                                    <select class="form-select" name="metode">
                                                        <option value="tunai" {{ $piutang->metode === 'tunai' ? 'selected' : '' }}>Tunai</option>
                                                        <option value="transfer" {{ $piutang->metode === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                                        <option value="qris" {{ $piutang->metode === 'qris' ? 'selected' : '' }}>QRIS</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Status Transaksi</label>
                                                    <select class="form-select" name="status">
                                                        <option value="pending" {{ $piutang->status === 'pending' ? 'selected' : '' }}>Belum Lunas (Pending)</option>
                                                        <option value="lunas" {{ $piutang->status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                                                        <option value="batal" {{ $piutang->status === 'batal' ? 'selected' : '' }}>Batal</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL DELETE PIUTANG -->
                            <div class="modal fade" id="modalDelete{{ $piutang->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('keuangan.piutang.destroy', $piutang->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-danger">Hapus Transaksi Piutang</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                Apakah Anda yakin ingin menghapus transaksi piutang <strong>{{ $piutang->kode }}</strong> milik <strong>{{ $piutang->pelanggan }}</strong> sebesar <strong>Rp {{ number_format($piutang->total, 0, ',', '.') }}</strong>?
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger rounded-pill px-4">Ya, Hapus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
                                    Tidak ada data piutang pending. Semua transaksi customer telah lunas!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ======================================================= -->
            <!-- TAB 2: DATA PENDAPATAN (SUDAH LUNAS)                   -->
            <!-- ======================================================= -->
            <div class="tab-pane fade {{ $activeTab === 'pendapatan' ? 'show active' : '' }}" 
                 id="tab-pendapatan" 
                 role="tabpanel" 
                 aria-labelledby="tab-pendapatan-btn">
                
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Daftar Pendapatan Kas Masuk</h5>
                        <small class="text-muted">Seluruh transaksi penjualan yang telah terverifikasi lunas</small>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('keuangan.piutang') }}" class="d-flex flex-wrap gap-2 align-items-center">
                        <input type="hidden" name="tab" value="pendapatan">
                        
                        <select name="periode" class="form-select rounded-pill" onchange="this.form.submit()">
                            <option value="">Semua Periode</option>
                            <option value="hari" {{ request('periode') === 'hari' ? 'selected' : '' }}>Harian</option>
                            <option value="bulan" {{ request('periode') === 'bulan' ? 'selected' : '' }}>Bulanan</option>
                        </select>

                        <input type="date" name="tanggal" class="form-control rounded-pill" 
                               value="{{ request('tanggal', now()->toDateString()) }}" onchange="this.form.submit()">

                        <input type="text" name="search" class="form-control rounded-pill" 
                               placeholder="Cari kode/customer..." value="{{ request('search') }}">

                        <button type="submit" class="btn btn-primary rounded-pill px-3">
                            <i class="bi bi-filter"></i>
                        </button>

                        @if(request('periode') || request('search'))
                            <a href="{{ route('keuangan.piutang', ['tab' => 'pendapatan']) }}" class="btn btn-outline-secondary rounded-pill">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No Transaksi</th>
                                <th>Customer</th>
                                <th>Rincian Produk</th>
                                <th>Tanggal Lunas</th>
                                <th>Total Pendapatan</th>
                                <th>Metode Pembayaran</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendapatanList as $item)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $item->kode }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $item->pelanggan ?? 'Walk-in Customer' }}</div>
                                </td>
                                <td>
                                    @if($item->detailPenjualans && $item->detailPenjualans->count())
                                        @foreach($item->detailPenjualans as $d)
                                            <span class="badge bg-light text-dark border me-1 mb-1">
                                                {{ $d->produk->nama_produk ?? 'Produk' }} x{{ $d->jumlah }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <i class="bi bi-calendar-check me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="fw-bold text-success fs-6">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if(strtolower($item->metode) === 'tunai')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-cash me-1"></i> TUNAI
                                        </span>
                                    @elseif(strtolower($item->metode) === 'transfer')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-bank me-1"></i> TRANSFER
                                        </span>
                                    @elseif(strtolower($item->metode) === 'qris')
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill">
                                            <i class="bi bi-qr-code me-1"></i> QRIS
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">
                                            {{ strtoupper($item->metode ?? '-') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success px-3 py-2 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> LUNAS
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('kasir.nota.cetak', $item->id) }}" target="_blank" 
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-receipt me-1"></i> Nota
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                                    Tidak ada data transaksi pendapatan pada filter yang dipilih.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pendapatanList->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <small class="text-muted">
                            Menampilkan {{ $pendapatanList->firstItem() }} - {{ $pendapatanList->lastItem() }} dari total {{ $pendapatanList->total() }} data
                        </small>
                        <div>
                            {{ $pendapatanList->links() }}
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </div>
</div>

@endsection
