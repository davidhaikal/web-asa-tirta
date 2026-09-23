@extends('layouts.app')

@section('content')
<div class="container-fluid py-2">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
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

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark" style="font-size: 28px;">Penagihan Customer</h2>
            <p class="text-muted mb-0">Monitoring penagihan dan pembayaran customer</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('keuangan.export.pdf') }}" class="btn btn-danger rounded-3 px-4 py-2 d-flex align-items-center gap-2 fw-semibold">
                <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
            </a>
            <a href="{{ route('penagihan.form.kirim') }}" class="btn btn-primary rounded-3 px-4 py-2 d-flex align-items-center gap-2 fw-semibold">
                <i class="bi bi-send-fill"></i> Kirim Tagihan
            </a>
        </div>
    </div>

    <!-- Filter Data Box -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3 text-dark">Filter Data</h5>
            <form action="{{ route('keuangan.penagihan') }}" method="GET">
                <div class="row align-items-end g-3">
                    <!-- Cari Customer -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark small mb-2">Cari Customer</label>
                        <div class="position-relative">
                            <input type="text" name="search" class="form-control rounded-3" style="padding-right: 38px;" placeholder="Cari nama customer..." value="{{ request('search') }}">
                            <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3 text-muted"></i>
                        </div>
                    </div>
                    <!-- Status -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark small mb-2">Status</label>
                        <select name="status" class="form-select rounded-3">
                            <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        </select>
                    </div>
                    <!-- Jatuh Tempo -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark small mb-2">Jatuh Tempo</label>
                        <input type="date" name="tanggal" class="form-control rounded-3 text-muted" value="{{ request('tanggal') }}">
                    </div>
                    <!-- Buttons -->
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <a href="{{ route('keuangan.penagihan') }}" class="btn btn-outline-primary w-100 py-2 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-semibold">
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
                            <th class="py-3 px-3 text-center text-white border-0">Total Tagihan</th>
                            <th class="py-3 px-3 text-center text-white border-0">Jatuh Tempo</th>
                            <th class="py-3 px-3 text-center text-white border-0">Status</th>
                            <th class="py-3 px-4 text-center text-white border-0" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tagihans as $index => $tagihan)
                        <tr class="border-bottom border-light-subtle">
                            <td class="py-3 px-4 text-secondary text-center">{{ $tagihans->firstItem() + $index }}</td>
                            <td class="py-3 px-3">
                                <div class="fw-bold text-dark">{{ $tagihan->pelanggan ?? 'Walk-in Customer' }}</div>
                                <small class="text-muted">{{ $tagihan->kode }}</small>
                            </td>
                            <td class="py-3 px-3 text-danger fw-bold text-center">
                                Rp {{ number_format($tagihan->total, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-secondary text-center" style="border-right: 1px solid #e2e8f0 !important;">
                                {{ \Carbon\Carbon::parse($tagihan->tanggal)->format('d M Y') }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($tagihan->status == 'lunas')
                                    <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; font-size: 13px;">
                                        Lunas
                                    </span>
                                @elseif($tagihan->status == 'pending')
                                    <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; font-size: 13px;">
                                        Pending
                                    </span>
                                @else
                                    <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 13px;">
                                        {{ ucfirst($tagihan->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- View Button -->
                                    <button type="button" class="btn btn-primary p-0 d-flex align-items-center justify-content-center rounded-3" data-bs-toggle="modal" data-bs-target="#modalView{{ $tagihan->id }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-eye-fill fs-6"></i>
                                    </button>
                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-warning p-0 d-flex align-items-center justify-content-center rounded-3 text-white" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $tagihan->id }}" style="width: 32px; height: 32px; background-color: #ffc107; border-color: #ffc107;">
                                        <i class="bi bi-pencil-fill fs-6"></i>
                                    </button>
                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-danger p-0 d-flex align-items-center justify-content-center rounded-3" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $tagihan->id }}" style="width: 32px; height: 32px;">
                                        <i class="bi bi-trash-fill fs-6"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Detail/View -->
                        <div class="modal fade" id="modalView{{ $tagihan->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold text-dark">Detail Penagihan</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="small text-muted d-block">Customer</label>
                                            <span class="fw-semibold text-dark fs-5">{{ $tagihan->pelanggan ?? 'Walk-in Customer' }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-muted d-block">No Invoice</label>
                                            <span class="fw-semibold text-dark">{{ $tagihan->kode }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-muted d-block">Total Tagihan</label>
                                            <span class="fw-bold text-danger fs-5">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small text-muted d-block">Jatuh Tempo</label>
                                            <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($tagihan->tanggal)->format('d M Y') }}</span>
                                        </div>
                                        <div class="mb-0">
                                            <label class="small text-muted d-block">Status</label>
                                            @if($tagihan->status == 'lunas')
                                                <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">Lunas</span>
                                            @elseif($tagihan->status == 'pending')
                                                <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a;">Pending</span>
                                            @else
                                                <span class="badge px-3 py-2 rounded-pill fw-semibold" style="background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">{{ ucfirst($tagihan->status) }}</span>
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
                        <div class="modal fade" id="modalEdit{{ $tagihan->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('keuangan.penagihan.update', $tagihan->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold text-dark">Edit Penagihan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark small">Customer</label>
                                                <input type="text" class="form-control rounded-3" name="pelanggan" value="{{ $tagihan->pelanggan }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark small">Total Tagihan (Rp)</label>
                                                <input type="number" class="form-control rounded-3" name="total" value="{{ $tagihan->total }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark small">Jatuh Tempo</label>
                                                <input type="date" class="form-control rounded-3" name="tanggal" value="{{ \Carbon\Carbon::parse($tagihan->tanggal)->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label fw-semibold text-dark small">Status</label>
                                                <select class="form-select rounded-3" name="status">
                                                    <option value="pending" {{ $tagihan->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="lunas" {{ $tagihan->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                                    <option value="batal" {{ $tagihan->status == 'batal' ? 'selected' : '' }}>Batal</option>
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
                        <div class="modal fade" id="modalDelete{{ $tagihan->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('keuangan.penagihan.destroy', $tagihan->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold text-dark">Hapus Penagihan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            Apakah Anda yakin ingin menghapus tagihan penagihan dari <strong>{{ $tagihan->pelanggan }}</strong> sejumlah <strong class="text-danger">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</strong>?
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
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada data penagihan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination inside card -->
            <div class="d-flex justify-content-between align-items-center p-4 border-top bg-white">
                <div class="text-muted small">
                    Menampilkan {{ $tagihans->firstItem() ?? 0 }}–{{ $tagihans->lastItem() ?? 0 }} dari {{ $tagihans->total() ?? 0 }} data
                </div>
                <div>
                    {{ $tagihans->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection