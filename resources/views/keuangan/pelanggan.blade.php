@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')

{{-- Flash Messages --}}
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

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-2"></i> Terjadi kesalahan input:</div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="k-page-head mb-4">
    <div>
        <h1 class="fw-bold">Data Pelanggan</h1>
        <p class="text-muted">Kelola data master pelanggan, kontak, wilayah, dan status customer</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Pelanggan
        </button>
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-3 no-print">
            <i class="bi bi-printer me-1"></i> Cetak Daftar
        </button>
    </div>
</div>

<!-- ========================== STATISTIK ========================== -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-accent"><i class="bi bi-people-fill"></i></span>
            <div>
                <div class="k-stat-label">Total Pelanggan</div>
                <div class="k-stat-value v-accent">{{ $totalPelanggan ?? 0 }}</div>
                <div class="k-stat-sub">Terdaftar di sistem</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-green"><i class="bi bi-person-check-fill"></i></span>
            <div>
                <div class="k-stat-label">Pelanggan Aktif</div>
                <div class="k-stat-value v-green">{{ $pelangganAktif ?? 0 }}</div>
                <div class="k-stat-sub">Status aktif</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-amber"><i class="bi bi-person-x-fill"></i></span>
            <div>
                <div class="k-stat-label">Pelanggan Nonaktif</div>
                <div class="k-stat-value v-amber">{{ $pelangganNonaktif ?? 0 }}</div>
                <div class="k-stat-sub">Tidak aktif</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="k-stat">
            <span class="k-stat-icon i-red"><i class="bi bi-wallet2"></i></span>
            <div>
                <div class="k-stat-label">Customer Piutang</div>
                <div class="k-stat-value v-red">{{ $belumDibayar ?? 0 }} Customer</div>
                <div class="k-stat-sub">Rp {{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- ========================== TABEL PELANGGAN ========================== -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-1">Daftar Master Pelanggan</h5>
                <small class="text-muted">Data kontak dan alamat customer ASA Tirta</small>
            </div>

            <!-- Search & Filter Form -->
            <form method="GET" action="{{ route('keuangan.pelanggan') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="text" name="search" class="form-control rounded-pill px-3" 
                       placeholder="Cari nama, kota, no telp..." value="{{ request('search') }}">

                <select name="status" class="form-select rounded-pill" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                <button type="submit" class="btn btn-primary rounded-pill px-3">
                    <i class="bi bi-search"></i>
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('keuangan.pelanggan') }}" class="btn btn-outline-secondary rounded-pill">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Nama Pelanggan</th>
                        <th>Kota / Wilayah</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 190px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pelanggans as $index => $p)
                    <tr>
                        <td class="text-muted small">
                            {{ method_exists($pelanggans, 'firstItem') ? ($pelanggans->firstItem() + $index) : ($index + 1) }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->nama_pelanggan }}</div>
                        </td>
                        <td>
                            <span class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $p->kota ?: '-' }}
                            </span>
                        </td>
                        <td>
                            @if($p->no_telp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->no_telp) }}" target="_blank" class="text-decoration-none fw-semibold">
                                    <i class="bi bi-whatsapp text-success me-1"></i>{{ $p->no_telp }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="small text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                {{ $p->alamat ?: '-' }}
                            </span>
                        </td>
                        <td>
                            @if(strtolower($p->status) === 'aktif')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                                    <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">
                                    <i class="bi bi-dash-circle me-1"></i> {{ $p->status ?: 'Nonaktif' }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <!-- Tombol View -->
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalView{{ $p->id }}" 
                                        title="Detail Pelanggan">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Tombol Edit -->
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEdit{{ $p->id }}" 
                                        title="Edit Pelanggan">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Tombol Hapus -->
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDelete{{ $p->id }}" 
                                        title="Hapus Pelanggan">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2 text-muted"></i>
                            Tidak ada data pelanggan yang sesuai dengan pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($pelanggans, 'hasPages') && $pelanggans->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                <small class="text-muted">
                    Menampilkan {{ $pelanggans->firstItem() }} - {{ $pelanggans->lastItem() }} dari total {{ $pelanggans->total() }} pelanggan
                </small>
                <div>
                    {{ $pelanggans->links() }}
                </div>
            </div>
        @endif

    </div>
</div>

<!-- ======================================================= -->
<!-- MODALS CONTAINER (Diletakkan di luar tabel agar aktif lancar) -->
<!-- ======================================================= -->

<!-- Modal Tambah Pelanggan -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('keuangan.pelanggan.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary" id="modalTambahLabel">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Pelanggan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_pelanggan" placeholder="Contoh: Toko Berkah / Bpk. Rudi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kota / Wilayah</label>
                        <input type="text" class="form-control" name="kota" placeholder="Contoh: Surabaya">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
                        <input type="text" class="form-control" name="no_telp" placeholder="Contoh: 08123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Lengkap</label>
                        <textarea class="form-control" name="alamat" rows="3" placeholder="Alamat pengiriman / domisili customer..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Pelanggan</label>
                        <select class="form-select" name="status">
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-save me-1"></i> Simpan Pelanggan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($pelanggans as $p)

    <!-- Modal View Detail Pelanggan -->
    <div class="modal fade" id="modalView{{ $p->id }}" tabindex="-1" aria-labelledby="modalView{{ $p->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalView{{ $p->id }}Label">
                        <i class="bi bi-person-badge me-1 text-primary"></i> Detail Pelanggan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Nama Pelanggan:</span>
                            <span class="fw-bold fs-6">{{ $p->nama_pelanggan }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Kota / Wilayah:</span>
                            <span class="fw-semibold">{{ $p->kota ?: '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Nomor Telepon:</span>
                            <span>{{ $p->no_telp ?: '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Status:</span>
                            @if(strtolower($p->status) === 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">{{ $p->status ?: 'Nonaktif' }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small text-muted text-uppercase d-block mb-1">Alamat Lengkap:</label>
                        <div class="p-3 rounded-3 border bg-white">
                            {{ $p->alamat ?: 'Tidak ada keterangan alamat.' }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    @if($p->no_telp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->no_telp) }}" target="_blank" class="btn btn-success rounded-pill px-3">
                            <i class="bi bi-whatsapp me-1"></i> Chat WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pelanggan -->
    <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1" aria-labelledby="modalEdit{{ $p->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('keuangan.pelanggan.update', $p->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="modalEdit{{ $p->id }}Label">
                            <i class="bi bi-pencil me-1 text-primary"></i> Edit Pelanggan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_pelanggan" value="{{ $p->nama_pelanggan }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kota / Wilayah</label>
                            <input type="text" class="form-control" name="kota" value="{{ $p->kota }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon</label>
                            <input type="text" class="form-control" name="no_telp" value="{{ $p->no_telp }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3">{{ $p->alamat }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status">
                                <option value="Aktif" {{ $p->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ $p->status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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

    <!-- Modal Delete Pelanggan -->
    <div class="modal fade" id="modalDelete{{ $p->id }}" tabindex="-1" aria-labelledby="modalDelete{{ $p->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('keuangan.pelanggan.destroy', $p->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger" id="modalDelete{{ $p->id }}Label">
                            <i class="bi bi-trash me-1"></i> Hapus Pelanggan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-3">
                        Apakah Anda yakin ingin menghapus data pelanggan <strong>{{ $p->nama_pelanggan }}</strong>?
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
