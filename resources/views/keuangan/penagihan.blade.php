@extends('layouts.app')

@section('title', 'Penagihan Customer')

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
        <h1 class="fw-bold">Penagihan Customer</h1>
        <p class="text-muted">Monitoring penagihan, pengiriman tagihan, dan follow-up pembayaran tempo customer</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('penagihan.kirim.semua') }}" 
           class="btn btn-outline-danger rounded-pill px-3" 
           title="Tandai semua tagihan belum lunas sebagai terkirim">
            <i class="bi bi-send-check me-1"></i> Kirim Semua
        </a>
        <a href="{{ route('penagihan.form.kirim') }}" 
           class="btn btn-danger rounded-pill px-4">
            <i class="bi bi-send me-1"></i> Kirim Tagihan
        </a>
    </div>
</div>

<!-- ========================== STATISTIK ========================== -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-red"><i class="bi bi-wallet2"></i></span>
            <div>
                <div class="k-stat-label">Total Tagihan Aktif</div>
                <div class="k-stat-value v-red">Rp {{ number_format($totalTagihan ?? ($tagihans ? $tagihans->sum('total') : 0), 0, ',', '.') }}</div>
                <div class="k-stat-sub">Tagihan belum lunas</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-accent"><i class="bi bi-receipt"></i></span>
            <div>
                <div class="k-stat-label">Jumlah Tagihan</div>
                <div class="k-stat-value v-accent">{{ $jumlahTagihan ?? ($tagihans ? $tagihans->count() : 0) }} Faktur</div>
                <div class="k-stat-sub">Perlu diproses</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-green"><i class="bi bi-send-check"></i></span>
            <div>
                <div class="k-stat-label">Sudah Dikirim</div>
                <div class="k-stat-value v-green">{{ $sudahDikirim ?? ($tagihans ? $tagihans->whereNotNull('tagihan_dikirim_at')->count() : 0) }} Tagihan</div>
                <div class="k-stat-sub">Terkirim ke customer</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-amber"><i class="bi bi-hourglass-split"></i></span>
            <div>
                <div class="k-stat-label">Belum Dikirim</div>
                <div class="k-stat-value v-amber">{{ $belumDikirim ?? ($tagihans ? $tagihans->whereNull('tagihan_dikirim_at')->count() : 0) }} Tagihan</div>
                <div class="k-stat-sub">Menunggu pengiriman</div>
            </div>
        </div>
    </div>
</div>

<!-- ========================== TABEL PENAGIHAN ========================== -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-1">Daftar Tagihan Customer</h5>
                <small class="text-muted">Monitoring status penagihan dan pengingat pembayaran</small>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('keuangan.penagihan') }}" class="d-flex gap-2">
                <input type="text" name="search" class="form-control rounded-pill px-3" 
                       placeholder="Cari kode / customer..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary rounded-pill px-3">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('keuangan.penagihan') }}" class="btn btn-outline-secondary rounded-pill">
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
                        <th>Total Tagihan</th>
                        <th>Jatuh Tempo</th>
                        <th>Status Pembayaran</th>
                        <th>Status Penagihan</th>
                        <th class="text-center" style="width: 240px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihans as $tagihan)
                    <tr>
                        <td>
                            <span class="fw-bold text-dark">{{ $tagihan->kode }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $tagihan->pelanggan ?? 'Walk-in Customer' }}</div>
                        </td>
                        <td>
                            @if($tagihan->detailPenjualans && $tagihan->detailPenjualans->count())
                                @foreach($tagihan->detailPenjualans as $d)
                                    <span class="badge bg-light text-dark border me-1 mb-1">
                                        {{ $d->produk->nama_produk ?? 'Produk' }} x{{ $d->jumlah }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-danger fs-6">
                                Rp {{ number_format($tagihan->total, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            <i class="bi bi-calendar-event me-1 text-muted"></i>
                            {{ \Carbon\Carbon::parse($tagihan->tanggal)->format('d M Y') }}
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                <i class="bi bi-hourglass-split me-1"></i> Belum Lunas
                            </span>
                        </td>
                        <td>
                            @if($tagihan->tagihan_dikirim_at)
                                <div class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill mb-1 d-inline-flex align-items-center">
                                    <i class="bi bi-send-check me-1"></i> Terkirim: {{ \Carbon\Carbon::parse($tagihan->tagihan_dikirim_at)->format('d/m/Y') }}
                                </div>
                            @else
                                <div class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill mb-1">
                                    <i class="bi bi-dash-circle me-1"></i> Belum Dikirim
                                </div>
                            @endif

                            @if($tagihan->tagihan_ditagih_at)
                                <div class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill d-inline-flex align-items-center">
                                    <i class="bi bi-bell-fill me-1"></i> Ditagih: {{ \Carbon\Carbon::parse($tagihan->tagihan_ditagih_at)->format('d/m/Y') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1 align-items-center">
                                <!-- Tombol View -->
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalView{{ $tagihan->id }}"
                                        title="Lihat Detail Tagihan">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Tombol Follow-up Tagih -->
                                <form action="{{ route('penagihan.tagih', $tagihan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-2" 
                                            title="Tandai Sudah Ditagih (Follow-up)">
                                        <i class="bi bi-bell"></i>
                                    </button>
                                </form>

                                <!-- Tombol Kirim Tagihan Satuan -->
                                <form action="{{ route('penagihan.kirim', $tagihan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2" 
                                            title="Tandai Tagihan Terkirim">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </form>

                                <!-- Tombol Edit -->
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit{{ $tagihan->id }}"
                                        title="Edit Tagihan">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Tombol Hapus -->
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDelete{{ $tagihan->id }}"
                                        title="Hapus Tagihan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
                            Tidak ada data penagihan. Semua tagihan sudah lunas!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL CONTAINER (Diletakkan di luar tabel agar aktif lancar) -->
<!-- ======================================================= -->
@foreach($tagihans as $tagihan)

    <!-- MODAL VIEW DETAIL -->
    <div class="modal fade" id="modalView{{ $tagihan->id }}" tabindex="-1" aria-labelledby="modalView{{ $tagihan->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalView{{ $tagihan->id }}Label">
                        <i class="bi bi-receipt me-1 text-primary"></i> Detail Penagihan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Kode Invoice:</span>
                            <span class="fw-bold">{{ $tagihan->kode }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Customer:</span>
                            <span class="fw-semibold">{{ $tagihan->pelanggan ?? 'Walk-in Customer' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tanggal Transaksi:</span>
                            <span>{{ \Carbon\Carbon::parse($tagihan->tanggal)->format('d M Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Tagihan:</span>
                            <span class="fw-bold text-danger fs-5">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Status:</span>
                            <span class="badge bg-warning text-dark">{{ ucfirst($tagihan->status) }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block mb-2">Item Produk:</label>
                        <ul class="list-group list-group-flush rounded-3 border">
                            @forelse($tagihan->detailPenjualans as $d)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <span class="fw-semibold">{{ $d->produk->nama_produk ?? 'Produk' }}</span>
                                        <small class="text-muted d-block">Rp {{ number_format($d->produk->harga ?? 0, 0, ',', '.') }} x {{ $d->jumlah }}</small>
                                    </div>
                                    <span class="fw-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted small py-2">Tidak ada rincian item</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="p-3 rounded-3 border">
                        <label class="fw-bold small text-muted text-uppercase d-block mb-1">Riwayat Pengiriman &amp; Penagihan:</label>
                        <div class="small text-muted">
                            <div><i class="bi bi-send me-1"></i> Pengiriman Tagihan: 
                                <strong>{{ $tagihan->tagihan_dikirim_at ? \Carbon\Carbon::parse($tagihan->tagihan_dikirim_at)->format('d M Y H:i') : 'Belum dikirim' }}</strong>
                            </div>
                            <div class="mt-1"><i class="bi bi-bell me-1"></i> Terakhir Ditagih: 
                                <strong>{{ $tagihan->tagihan_ditagih_at ? \Carbon\Carbon::parse($tagihan->tagihan_ditagih_at)->format('d M Y H:i') : 'Belum pernah difollow-up' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('kasir.nota.cetak', $tagihan->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-printer me-1"></i> Cetak Faktur
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit{{ $tagihan->id }}" tabindex="-1" aria-labelledby="modalEdit{{ $tagihan->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('keuangan.penagihan.update', $tagihan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="modalEdit{{ $tagihan->id }}Label">
                            <i class="bi bi-pencil me-1 text-primary"></i> Edit Data Tagihan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Customer</label>
                            <input type="text" class="form-control" name="pelanggan" value="{{ $tagihan->pelanggan }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Total Tagihan (Rp)</label>
                            <input type="number" class="form-control" name="total" value="{{ $tagihan->total }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Transaksi / Jatuh Tempo</label>
                            <input type="date" class="form-control" name="tanggal" value="{{ \Carbon\Carbon::parse($tagihan->tanggal)->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status Pembayaran</label>
                            <select class="form-select" name="status">
                                <option value="pending" {{ $tagihan->status == 'pending' ? 'selected' : '' }}>Belum Lunas (Pending)</option>
                                <option value="lunas" {{ $tagihan->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
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

    <!-- MODAL DELETE -->
    <div class="modal fade" id="modalDelete{{ $tagihan->id }}" tabindex="-1" aria-labelledby="modalDelete{{ $tagihan->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('keuangan.penagihan.destroy', $tagihan->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger" id="modalDelete{{ $tagihan->id }}Label">
                            <i class="bi bi-trash me-1"></i> Hapus Tagihan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        Apakah Anda yakin ingin menghapus data tagihan <strong>{{ $tagihan->kode }}</strong> milik <strong>{{ $tagihan->pelanggan }}</strong> sebesar <strong>Rp {{ number_format($tagihan->total, 0, ',', '.') }}</strong>?
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endforeach

@endsection
