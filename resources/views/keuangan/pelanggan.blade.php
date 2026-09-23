@extends('layouts.app')

@section('content')
<style>
    .card-modern {
        border-radius: 18px;
        padding: 24px;
        color: #fff !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
        height: 100%;
        transition: transform 0.2s ease;
    }
    .card-modern:hover {
        transform: translateY(-5px);
    }
    .card-modern p {
        margin-bottom: 4px;
        opacity: .9;
        color: #fff !important;
    }
    .card-modern h2 {
        margin-bottom: 0;
        color: #fff !important;
    }
    .card-modern i {
        color: #fff !important;
    }
    .bg-blue {
        background: linear-gradient(135deg, #0d6efd, #3b8bff) !important;
    }
    .bg-green {
        background: linear-gradient(135deg, #198754, #2fb673) !important;
    }
    .bg-red {
        background: linear-gradient(135deg, #dc3545, #ef5b6b) !important;
    }
    .bg-orange {
        background: linear-gradient(135deg, #fd7e14, #ffa347) !important;
    }

    .table thead tr th {
        background-color: #0d6efd !important;
        color: #ffffff !important;
        border: none !important;
        font-weight: 600 !important;
        padding-top: 15px !important;
        padding-bottom: 15px !important;
    }
    .table thead tr th.border-divider {
        border-right: 1px solid rgba(255, 255, 255, 0.25) !important;
    }
    .table td.border-divider {
        border-right: 1px solid #e2e8f0 !important;
    }
    .table tbody tr:last-child {
        border-bottom: none !important;
    }
</style>

<div class="container-fluid py-2">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Card Total Piutang -->
        <div class="col-md-4">
            <div class="card-modern bg-red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-white">Total Piutang</p>
                        <h2 class="fw-bold text-white">
                            Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div>
                        <i class="bi bi-wallet2 fs-1 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Belum Bayar -->
        <div class="col-md-4">
            <div class="card-modern bg-orange">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-white">Belum Dibayar</p>
                        <h2 class="fw-bold text-white">
                            {{ $belumDibayar }} Customer
                        </h2>
                    </div>
                    <div>
                        <i class="bi bi-exclamation-circle fs-1 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Lunas -->
        <div class="col-md-4">
            <div class="card-modern bg-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 text-white">Sudah Lunas</p>
                        <h2 class="fw-bold text-white">
                            {{ $sudahLunas }} Customer
                        </h2>
                    </div>
                    <div>
                        <i class="bi bi-check-circle fs-1 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="font-size: 28px;">Data Pelanggan</h2>
            <p class="text-muted mb-0">Kelola data pelanggan dan monitoring status keaktifan</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary rounded-3 px-4 py-2 d-flex align-items-center gap-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Pelanggan
            </button>
            <button class="btn btn-danger rounded-3 px-4 py-2 d-flex align-items-center gap-2 fw-semibold">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
            </button>
        </div>
    </div>

    <!-- Filter Data Box -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3 text-dark">Filter Data</h5>
            <form action="{{ route('keuangan.pelanggan') }}" method="GET">
                <div class="row align-items-end g-3">
                    <!-- Cari Customer -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark small mb-2">Cari Customer</label>
                        <div class="position-relative">
                            <input type="text" name="search" class="form-control rounded-3" style="padding-right: 38px;" placeholder="Cari nama, kota, telepon..." value="{{ request('search') }}">
                            <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3 text-muted"></i>
                        </div>
                    </div>
                    <!-- Status -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark small mb-2">Status</label>
                        <select name="status" class="form-select rounded-3">
                            <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua Status</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <!-- Buttons -->
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <a href="{{ route('keuangan.pelanggan') }}" class="btn btn-outline-primary w-100 py-2 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-semibold">
                                <i class="bi bi-arrow-clockwise"></i> Reset Filter
                            </a>
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-semibold">
                                <i class="bi bi-search"></i> Cari
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
            <div class="table-responsive">
                <table class="asa-table table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 text-center text-white border-0" style="width: 80px;">No</th>
                            <th class="py-3 px-3 text-white border-0">Customer</th>
                            <th class="py-3 px-3 text-center text-white border-0">Kota</th>
                            <th class="py-3 px-3 text-center text-white border-0">No Telp</th>
                            <th class="py-3 px-3 text-center text-white border-0">Status</th>
                            <th class="py-3 px-4 text-center text-white border-0" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelanggans as $index => $p)
                        <tr class="border-bottom border-light-subtle">
                            <td class="py-3 px-4 text-secondary text-center">{{ $pelanggans->firstItem() + $index }}</td>
                            <td class="py-3 px-3">
                                <div class="fw-bold text-dark">{{ $p->nama_pelanggan }}</div>
                                <small class="text-muted">{{ $p->alamat ?? '-' }}</small>
                            </td>
                            <td class="py-3 px-3 text-center text-secondary">
                                {{ $p->kota ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-center text-secondary border-divider">
                                {{ $p->no_telp ?? '-' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if(strtolower($p->status) === 'aktif')
                                    <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; font-size: 13px;">
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 13px;">
                                        {{ $p->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- View Button -->
                                    <button type="button" class="btn btn-primary p-0 d-flex align-items-center justify-content-center rounded-3" data-bs-toggle="modal" data-bs-target="#modalView{{ $p->id }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-eye-fill fs-6"></i>
                                    </button>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-warning p-0 d-flex align-items-center justify-content-center rounded-3 text-white" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id }}" style="width: 32px; height: 32px; background-color: #ffc107; border-color: #ffc107;">
                                        <i class="bi bi-pencil-fill fs-6"></i>
                                    </button>
                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-danger p-0 d-flex align-items-center justify-content-center rounded-3" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $p->id }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-trash-fill fs-6"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Detail/View -->
                        <div class="modal fade" id="modalView{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold text-dark">Detail Pelanggan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="small text-muted d-block">Nama Pelanggan</label>
                                            <span class="fw-semibold text-dark fs-5">{{ $p->nama_pelanggan }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-muted d-block">Kota</label>
                                            <span class="fw-semibold text-dark">{{ $p->kota ?? '-' }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-muted d-block">No Telp</label>
                                            <span class="fw-semibold text-dark">{{ $p->no_telp ?? '-' }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-muted d-block">Alamat</label>
                                            <span class="fw-semibold text-dark">{{ $p->alamat ?? '-' }}</span>
                                        </div>
                                        <div class="mb-0">
                                            <label class="small text-muted d-block">Status</label>
                                            @if(strtolower($p->status) === 'aktif')
                                                <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">Aktif</span>
                                            @else
                                                <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">{{ $p->status }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('keuangan.pelanggan.update', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold text-dark">Edit Pelanggan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark small">Nama Pelanggan</label>
                                                <input type="text" class="form-control rounded-3" name="nama_pelanggan" value="{{ $p->nama_pelanggan }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark small">Kota</label>
                                                <input type="text" class="form-control rounded-3" name="kota" value="{{ $p->kota }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark small">No Telp</label>
                                                <input type="text" class="form-control rounded-3" name="no_telp" value="{{ $p->no_telp }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark small">Alamat</label>
                                                <textarea class="form-control rounded-3" rows="3" name="alamat">{{ $p->alamat }}</textarea>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-semibold text-dark small">Status</label>
                                                <select class="form-select rounded-3" name="status">
                                                    <option value="Aktif" {{ $p->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="Nonaktif" {{ $p->status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Delete -->
                        <div class="modal fade" id="modalDelete{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('keuangan.pelanggan.destroy', $p->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold text-dark">Hapus Pelanggan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            Apakah Anda yakin ingin menghapus pelanggan <strong>{{ $p->nama_pelanggan }}</strong>?
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger rounded-3 px-4">Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data pelanggan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination inside card -->
            <div class="d-flex justify-content-between align-items-center p-4 border-top bg-white">
                <div class="text-muted small">
                    Menampilkan {{ $pelanggans->firstItem() ?? 0 }}–{{ $pelanggans->lastItem() ?? 0 }} dari {{ $pelanggans->total() ?? 0 }} data
                </div>
                <div>
                    {{ $pelanggans->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('keuangan.pelanggan.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Tambah Pelanggan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Nama Pelanggan</label>
                        <input type="text" class="form-control rounded-3" name="nama_pelanggan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Kota</label>
                        <input type="text" class="form-control rounded-3" name="kota">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">No Telp</label>
                        <input type="text" class="form-control rounded-3" name="no_telp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Alamat</label>
                        <textarea class="form-control rounded-3" rows="3" name="alamat"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-dark small">Status</label>
                        <select class="form-select rounded-3" name="status">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection