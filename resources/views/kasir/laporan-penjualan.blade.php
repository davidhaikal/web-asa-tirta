@extends('layouts.app', [
    'title' => 'Laporan Keuangan',
    'subtitle' => 'Kasir > Laporan > Keuangan',
])

@section('content')
<style>
    /* Card Stats Modern */
    .card-modern-light {
        background-color: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        height: 100%;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-modern-light:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
    }
    .circle-icon {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .circle-green {
        background-color: #e6f7ed;
        color: #198754;
    }
    .circle-blue {
        background-color: #e7f1ff;
        color: #0d6efd;
    }
    .circle-orange {
        background-color: #fff3cd;
        color: #fd7e14;
    }
    .circle-purple {
        background-color: #f1e6ff;
        color: #7c3aed;
    }

    /* Table Styles */
    .table thead tr th {
        background-color: #0d6efd !important;
        color: #ffffff !important;
        border: none !important;
        font-weight: 600 !important;
        padding-top: 15px !important;
        padding-bottom: 15px !important;
        vertical-align: middle;
    }
    .table tbody tr:last-child {
        border-bottom: none !important;
    }
    .btn-action-size {
        width: 32px;
        height: 32px;
    }
</style>

<div class="container-fluid px-0">
    <!-- Export Buttons Row -->
    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-danger rounded-3 px-3 py-2 d-flex align-items-center gap-2 fw-semibold text-white">
            <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
        </a>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success rounded-3 px-3 py-2 d-flex align-items-center gap-2 fw-semibold text-white">
            <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn btn-primary rounded-3 px-3 py-2 d-flex align-items-center gap-2 fw-semibold text-white">
            <i class="bi bi-printer-fill"></i> Print
        </button>
    </div>

    <!-- Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Card Total Pendapatan -->
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card-modern-light">
                <div class="circle-icon circle-green">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <span class="text-muted small d-block mb-1">Total Pendapatan</span>
                    <h3 class="fw-bold mb-0" style="color: #198754; font-size: 20px;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    <small class="text-muted text-xs">Periode terpilih</small>
                </div>
            </div>
        </div>

        <!-- Card Total Pembelian -->
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card-modern-light">
                <div class="circle-icon circle-blue">
                    <i class="bi bi-cart-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block mb-1">Total Pembelian</span>
                    <h3 class="fw-bold mb-0" style="color: #0d6efd; font-size: 20px;">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h3>
                    <small class="text-muted text-xs">Periode terpilih</small>
                </div>
            </div>
        </div>

        <!-- Card Total Piutang -->
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card-modern-light">
                <div class="circle-icon circle-orange">
                    <i class="bi bi-credit-card-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block mb-1">Total Piutang</span>
                    <h3 class="fw-bold mb-0" style="color: #fd7e14; font-size: 20px;">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h3>
                    <small class="text-muted text-xs">Periode terpilih</small>
                </div>
            </div>
        </div>

        <!-- Card Total Penagihan -->
        <div class="col-xl-3 col-md-6 col-sm-12">
            <div class="card-modern-light">
                <div class="circle-icon circle-purple">
                    <i class="bi bi-send-fill"></i>
                </div>
                <div>
                    <span class="text-muted small d-block mb-1">Total Penagihan</span>
                    <h3 class="fw-bold mb-0" style="color: #7c3aed; font-size: 20px;">Rp {{ number_format($totalPenagihan, 0, ',', '.') }}</h3>
                    <small class="text-muted text-xs">Periode terpilih</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Data Box -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3 text-dark">Filter Data</h5>
            <form method="GET" action="{{ route('kasir.laporan-penjualan') }}">
                <div class="row g-3 align-items-end">
                    <!-- Tanggal Awal -->
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label class="form-label fw-semibold text-dark small mb-2">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" class="form-control rounded-3" value="{{ $tanggalAwal }}">
                    </div>
                    <!-- Tanggal Akhir -->
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label class="form-label fw-semibold text-dark small mb-2">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control rounded-3" value="{{ $tanggalAkhir }}">
                    </div>
                    <!-- Jenis Laporan -->
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label class="form-label fw-semibold text-dark small mb-2">Jenis Laporan</label>
                        <select name="jenis_laporan" class="form-select rounded-3" onchange="this.form.submit()">
                            <option value="Semua" {{ $jenisLaporan == 'Semua' ? 'selected' : '' }}>Semua</option>
                            <option value="Pendapatan" {{ $jenisLaporan == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                            <option value="Pembelian" {{ $jenisLaporan == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                            <option value="Piutang" {{ $jenisLaporan == 'Piutang' ? 'selected' : '' }}>Piutang</option>
                            <option value="Penagihan" {{ $jenisLaporan == 'Penagihan' ? 'selected' : '' }}>Penagihan</option>
                        </select>
                    </div>
                    <!-- Kategori -->
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <label class="form-label fw-semibold text-dark small mb-2">Kategori</label>
                        <select name="kategori" class="form-select rounded-3" onchange="this.form.submit()">
                            <option value="Semua" {{ $kategoriFilter == 'Semua' ? 'selected' : '' }}>Semua</option>
                            <option value="Masuk" {{ $kategoriFilter == 'Masuk' ? 'selected' : '' }}>Masuk</option>
                            <option value="Keluar" {{ $kategoriFilter == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                            <option value="Piutang" {{ $kategoriFilter == 'Piutang' ? 'selected' : '' }}>Piutang</option>
                            <option value="Pending" {{ $kategoriFilter == 'Pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <!-- Cari Data -->
                    <div class="col-lg-8 col-md-7 col-sm-12">
                        <label class="form-label fw-semibold text-dark small mb-2">Cari Data</label>
                        <div class="position-relative">
                            <input type="text" name="search" class="form-control rounded-3" placeholder="Cari no referensi, customer..." value="{{ $search }}" style="padding-right: 38px;">
                            <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3 text-muted"></i>
                        </div>
                    </div>
                    <!-- Buttons -->
                    <div class="col-lg-4 col-md-5 col-sm-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('kasir.laporan-penjualan') }}" class="btn btn-outline-secondary w-100 py-2 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-1.5" style="border-color: #cbd5e1; color: #475569;">
                                <i class="bi bi-arrow-clockwise"></i> Reset Filter
                            </a>
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-1.5">
                                <i class="bi bi-search"></i> Cari Data
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            @if ($transactions->isEmpty())
                <p class="text-muted text-center py-5 mb-0">Tidak ada data transaksi pada periode ini.</p>
            @else
                <div class="table-responsive">
                    <table class="asa-table table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 text-center" style="width: 5%;">No</th>
                                <th class="py-3 px-3 text-center" style="width: 12%;">Tanggal</th>
                                <th class="py-3 px-3" style="width: 15%;">No Referensi</th>
                                <th class="py-3 px-3" style="width: 20%;">Customer / Supplier</th>
                                <th class="py-3 px-3" style="width: 25%;">Keterangan</th>
                                <th class="py-3 px-3 text-center" style="width: 13%;">Nominal</th>
                                <th class="py-3 px-3 text-center" style="width: 10%;">Status</th>
                                <th class="py-3 px-4 text-center" style="width: 12%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $index => $t)
                                <tr class="border-bottom border-light-subtle">
                                    <td class="py-3 px-4 text-center text-secondary">{{ $transactions->firstItem() + $index }}</td>
                                    <td class="py-3 px-3 text-center text-secondary">{{ \Carbon\Carbon::parse($t['tanggal'])->format('d M Y') }}</td>
                                    <td class="py-3 px-3 fw-bold text-dark">{{ $t['referensi'] }}</td>
                                    <td class="py-3 px-3 text-dark fw-semibold">{{ $t['customer_supplier'] }}</td>
                                    <td class="py-3 px-3 text-secondary" style="font-size: 14px;">{{ $t['keterangan'] }}</td>
                                    <td class="py-3 px-3 text-center fw-bold">
                                        @if($t['status'] == 'Masuk')
                                            <span style="color: #198754;">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</span>
                                        @elseif($t['status'] == 'Keluar')
                                            <span style="color: #dc3545;">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</span>
                                        @elseif($t['status'] == 'Piutang')
                                            <span style="color: #0d6efd;">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</span>
                                        @else
                                            <span style="color: #fd7e14;">Rp {{ number_format($t['nominal'], 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        @if($t['status'] == 'Masuk')
                                            <span class="badge px-3 py-1.5 rounded-pill fw-semibold text-success" style="background-color: #e6f7ed; border: 1px solid #a3cfbb; font-size: 12px;">
                                                Masuk
                                            </span>
                                        @elseif($t['status'] == 'Keluar')
                                            <span class="badge px-3 py-1.5 rounded-pill fw-semibold text-danger" style="background-color: #f8d7da; border: 1px solid #f5c2c7; font-size: 12px;">
                                                Keluar
                                            </span>
                                        @elseif($t['status'] == 'Piutang')
                                            <span class="badge px-3 py-1.5 rounded-pill fw-semibold text-primary" style="background-color: #e7f1ff; border: 1px solid #9ec5fe; font-size: 12px;">
                                                Piutang
                                            </span>
                                        @else
                                            <span class="badge px-3 py-1.5 rounded-pill fw-semibold" style="background-color: #fff3cd; color: #d97706; border: 1px solid #ffe69c; font-size: 12px;">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="d-flex justify-content-center gap-1.5">
                                            <!-- View Button -->
                                            <button type="button" class="btn btn-primary p-0 d-flex align-items-center justify-content-center rounded-3 btn-action-size" data-bs-toggle="modal" data-bs-target="#modalView{{ $t['raw_type'] }}{{ $t['id'] }}">
                                                <i class="bi bi-eye-fill fs-6"></i>
                                            </button>
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-warning p-0 d-flex align-items-center justify-content-center rounded-3 text-white btn-action-size" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $t['raw_type'] }}{{ $t['id'] }}" style="background-color: #ffc107; border-color: #ffc107;">
                                                <i class="bi bi-pencil-fill fs-6"></i>
                                            </button>
                                            <!-- Delete Button -->
                                            <button type="button" class="btn btn-danger p-0 d-flex align-items-center justify-content-center rounded-3 btn-action-size" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $t['raw_type'] }}{{ $t['id'] }}">
                                                <i class="bi bi-trash-fill fs-6"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Detail/View -->
                                <div class="modal fade" id="modalView{{ $t['raw_type'] }}{{ $t['id'] }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-bottom-0 pb-0">
                                                <h5 class="modal-title fw-bold text-dark">Detail Transaksi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">Tanggal</label>
                                                    <span class="fw-semibold text-dark fs-6">{{ \Carbon\Carbon::parse($t['tanggal'])->format('d M Y') }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">No Referensi</label>
                                                    <span class="fw-bold text-dark fs-6">{{ $t['referensi'] }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">Kategori</label>
                                                    <span class="fw-semibold text-dark fs-6">{{ $t['kategori'] }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">Customer / Supplier</label>
                                                    <span class="fw-semibold text-dark fs-6">{{ $t['customer_supplier'] }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">Keterangan</label>
                                                    <span class="fw-semibold text-dark fs-6">{{ $t['keterangan'] }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">Nominal</label>
                                                    <span class="fw-bold fs-5 @if($t['status']=='Masuk') text-success @elseif($t['status']=='Keluar') text-danger @elseif($t['status']=='Piutang') text-primary @else text-warning @endif">
                                                        Rp {{ number_format($t['nominal'], 0, ',', '.') }}
                                                    </span>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="small text-muted d-block">Status</label>
                                                    <span class="fw-semibold text-dark fs-6">{{ $t['status'] }}</span>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0">
                                                <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="modalEdit{{ $t['raw_type'] }}{{ $t['id'] }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            @if($t['raw_type'] == 'penjualan')
                                                <form action="{{ route('keuangan.penagihan.update', $t['id']) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header border-bottom-0 pb-0">
                                                        <h5 class="modal-title fw-bold text-dark">Edit Transaksi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold text-dark small">Customer</label>
                                                            <input type="text" class="form-control rounded-3" name="pelanggan" value="{{ $t['customer_supplier'] }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold text-dark small">Nominal (Rp)</label>
                                                            <input type="number" class="form-control rounded-3" name="total" value="{{ $t['nominal'] }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold text-dark small">Tanggal</label>
                                                            <input type="date" class="form-control rounded-3" name="tanggal" value="{{ \Carbon\Carbon::parse($t['tanggal'])->format('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="mb-0">
                                                            <label class="form-label fw-semibold text-dark small">Status</label>
                                                            <select class="form-select rounded-3" name="status">
                                                                <option value="pending" {{ $t['status_db'] == 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="lunas" {{ $t['status_db'] == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                                                <option value="batal" {{ $t['status_db'] == 'batal' ? 'selected' : '' }}>Batal</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pt-0">
                                                        <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan</button>
                                                    </div>
                                                </form>
                                            @else
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-dark">Edit Pembelian</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    Pengubahan data pembelian dengan referensi <strong>{{ $t['referensi'] }}</strong> tidak diperbolehkan secara langsung demi integritas audit stok gudang.
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Delete -->
                                <div class="modal fade" id="modalDelete{{ $t['raw_type'] }}{{ $t['id'] }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            @if($t['raw_type'] == 'penjualan')
                                                <form action="{{ route('keuangan.penagihan.destroy', $t['id']) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-header border-bottom-0 pb-0">
                                                        <h5 class="modal-title fw-bold text-dark">Hapus Penjualan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        Apakah Anda yakin ingin menghapus data penjualan dengan referensi <strong>{{ $t['referensi'] }}</strong>? Tindakan ini tidak dapat dibatalkan.
                                                    </div>
                                                    <div class="modal-footer border-top-0 pt-0">
                                                        <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger rounded-3 px-4">Hapus</button>
                                                    </div>
                                                </form>
                                            @else
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-dark">Hapus Pembelian</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    Penghapusan data pembelian dengan referensi <strong>{{ $t['referensi'] }}</strong> tidak diperbolehkan secara langsung demi integritas audit stok gudang.
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination inside card -->
                <div class="d-flex justify-content-between align-items-center p-4 border-top bg-white">
                    <div class="text-muted small">
                        Menampilkan {{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() ?? 0 }} data
                    </div>
                    <div>
                        {{ $transactions->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection