@extends('layouts.app')

@section('content')

@php
    $search = request('search');
    $statusFilter = request('status', 'semua');
    $tanggalFilter = request('tanggal');

    // Mengambil query dasar
    $query = App\Models\Produksi::with(['qc', 'produk']);

    // Filter Pencarian Nama Produk
    if ($search) {
        $query->whereHas('produk', function($q) use ($search) {
            $q->where('nama_produk', 'like', "%{$search}%");
        });
    }

    // Filter Tanggal Produksi / QC
    if ($tanggalFilter) {
        $query->where(function($q) use ($tanggalFilter) {
            $q->whereDate('tanggal_produksi', $tanggalFilter)
              ->orWhereHas('qc', function($sub) use ($tanggalFilter) {
                  $sub->whereDate('created_at', $tanggalFilter);
              });
        });
    }

    // Filter Status di level Database Builder
    if ($statusFilter && $statusFilter !== 'semua') {
        if ($statusFilter === 'menunggu') {
            $query->doesntHave('qc');
        } elseif ($statusFilter === 'lolos') {
            $query->whereHas('qc', function($q) {
                $q->where('hasil', 'Layak');
            });
        } elseif ($statusFilter === 'reject') {
            $query->whereHas('qc', function($q) {
                $q->where('hasil', 'Tidak Layak');
            });
        }
    }

    $dataProduksi = $query->latest()->paginate(10)->withQueryString();
@endphp

<div class="dashboard-content px-4">

    <!-- Header Halaman -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">🧪 Pemeriksaan Produk</h2>
            <p class="text-muted mb-0">Pemeriksaan kualitas hasil produksi sebelum masuk gudang</p>
        </div>
        <button class="tambah-btn btn shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahQC">
            <i class="bi bi-plus-lg"></i> Tambah Pemeriksaan
        </button>
    </div>

    <!-- Filter Data (Konsisten dengan Modul Gudang) -->
    <div class="content-card mb-4">
        <div class="mb-3">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-funnel-fill text-primary"></i> Filter Data Pemeriksaan
            </h5>
        </div>
        <form action="/qc/pemeriksaan" method="GET">
            <div class="row g-3">
                <!-- Pencarian Produk -->
                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           class="form-control py-2 rounded-3 border-secondary-subtle"
                           value="{{ $search }}"
                           placeholder="🔍 Cari nama produk...">
                </div>

                <!-- Filter Status -->
                <div class="col-md-3">
                    <select name="status" class="form-select py-2 rounded-3 border-secondary-subtle">
                        <option value="semua" {{ $statusFilter == 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="menunggu" {{ $statusFilter == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="lolos" {{ $statusFilter == 'lolos' ? 'selected' : '' }}>Lolos</option>
                        <option value="reject" {{ $statusFilter == 'reject' ? 'selected' : '' }}>Reject</option>
                    </select>
                </div>

                <!-- Filter Tanggal -->
                <div class="col-md-3">
                    <input type="date"
                           name="tanggal"
                           class="form-control py-2 rounded-3 border-secondary-subtle"
                           value="{{ $tanggalFilter }}">
                </div>

                <!-- Tombol Aksi Filter -->
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 shadow-sm">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <a href="/qc/pemeriksaan" class="btn btn-outline-secondary py-2 rounded-3" title="Reset Filter">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabel Pemeriksaan Produk Utama -->
    <div class="content-card p-0 overflow-hidden shadow-sm">
        <div class="table-responsive">
            <table class="asa-table qc-table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="60px" class="text-center">No</th>
                        <th>Produk</th>
                        <th>Jumlah (Pcs)</th>
                        <th>Tanggal</th>
                        <th>Status Pemeriksaan</th>
                        <th>Catatan / Alasan</th>
                        <th width="150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataProduksi as $p)
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ ($dataProduksi->currentPage() - 1) * $dataProduksi->perPage() + $loop->iteration }}</td>
                            <td class="fw-semibold text-slate-800">{{ $p->produk->nama_produk ?? '-' }}</td>
                            <td>{{ $p->jumlah_produksi ?? 0 }}</td>
                            <td>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($p->tanggal_produksi)->format('Y-m-d') }}</span>
                            </td>
                            <td>
                                @if(!$p->qc)
                                    <span class="badge-status menunggu">
                                        <i class="bi bi-hourglass-split"></i> Menunggu
                                    </span>
                                @elseif($p->qc->hasil == 'Layak')
                                    <span class="badge-status lolos">
                                        <i class="bi bi-check-circle-fill"></i> Lolos
                                    </span>
                                @else
                                    <span class="badge-status reject">
                                        <i class="bi bi-x-circle-fill"></i> Reject
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted fs-7">{{ $p->qc ? $p->qc->keterangan : '-' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="aksi-group">
                                    <!-- Tombol Detail (Biru) -->
                                    <button class="btn btn-primary btn-action-icon shadow-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDetail{{ $p->id }}"
                                            title="Detail Pemeriksaan">
                                        <i class="bi bi-eye-fill text-white"></i>
                                    </button>

                                    <!-- Tombol Edit / Pemeriksaan (Kuning) -->
                                    @if(!$p->qc)
                                        <button class="btn btn-warning btn-action-icon shadow-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalPeriksa{{ $p->id }}"
                                                title="Pemeriksaan QC">
                                            <i class="bi bi-pencil-fill text-dark"></i>
                                        </button>
                                    @else
                                        <!-- Tombol Edit aktif membuka Modal Edit/Hapus QC Ulang -->
                                        <button class="btn btn-warning btn-action-icon shadow-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEdit{{ $p->id }}"
                                                title="Edit Pemeriksaan">
                                            <i class="bi bi-pencil-fill text-dark"></i>
                                        </button>
                                    @endif

                                    <!-- Tombol Hapus (Merah) -->
                                    @if($p->qc)
                                        <form action="/qc/delete/{{ $p->qc->id }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemeriksaan QC ini? Data akan kembali ke antrean Menunggu.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-action-icon shadow-sm" title="Hapus Pemeriksaan">
                                                <i class="bi bi-trash-fill text-white"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-danger btn-action-icon shadow-sm disabled opacity-50" title="Hapus (Belum Diperiksa)">
                                            <i class="bi bi-trash-fill text-white"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    Tidak ada data pemeriksaan ditemukan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-4 border-top bg-white">
            <div class="text-muted small">
                Menampilkan {{ $dataProduksi->firstItem() ?? 0 }}–{{ $dataProduksi->lastItem() ?? 0 }} dari {{ $dataProduksi->total() ?? 0 }} data
            </div>
            <div>
                {{ $dataProduksi->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Pemeriksaan & Detail Dinamis (Diletakkan di luar tabel agar struktur HTML valid) -->
@foreach($dataProduksi as $p)
    @if(!$p->qc)
        <!-- Modal Pemeriksaan -->
        <div class="modal fade" id="modalPeriksa{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <form action="/qc/store" method="POST">
                        @csrf
                        <input type="hidden" name="produksi_id" value="{{ $p->id }}">
                        <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                            <h5 class="modal-title fw-bold">🔬 Form Pemeriksaan QC</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label text-muted fw-semibold mb-1">Nama Produk</label>
                                <input type="text" class="form-control bg-light border-0 py-2 rounded-3 fw-bold" value="{{ $p->produk->nama_produk ?? '-' }}" readonly>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fw-semibold mb-1">Jumlah Produksi</label>
                                    <input type="text" class="form-control bg-light border-0 py-2 rounded-3" value="{{ $p->jumlah_produksi }} Pcs" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted fw-semibold mb-1">Tanggal Produksi</label>
                                    <input type="text" class="form-control bg-light border-0 py-2 rounded-3" value="{{ \Carbon\Carbon::parse($p->tanggal_produksi)->format('d M Y') }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold mb-1">Hasil Pemeriksaan</label>
                                <select name="hasil" class="form-select py-2 rounded-3 border-secondary-subtle" required>
                                    <option value="">-- Pilih Hasil --</option>
                                    <option value="Layak">Lolos</option>
                                    <option value="Tidak Layak">Reject</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold mb-1">Keterangan / Alasan</label>
                                <textarea name="keterangan" class="form-control rounded-3 border-secondary-subtle" rows="3" placeholder="Masukkan catatan kelayakan atau alasan reject..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail{{ $p->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header {{ !$p->qc ? 'bg-secondary' : ($p->qc->hasil == 'Layak' ? 'bg-success' : 'bg-danger') }} text-white border-0 py-3 rounded-top-4">
                    <h5 class="modal-title fw-bold">📋 Detail Pemeriksaan QC</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <table class="table table-borderless align-middle mb-0">
                        <tr>
                            <td class="fw-semibold text-muted py-2" width="40%">Nama Produk</td>
                            <td class="fw-bold py-2">: {{ $p->produk->nama_produk ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted py-2">Jumlah Produksi</td>
                            <td class="fw-bold py-2">: {{ $p->jumlah_produksi }} Pcs</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted py-2">Tanggal Produksi</td>
                            <td class="py-2">: {{ \Carbon\Carbon::parse($p->tanggal_produksi)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold text-muted py-2">Status QC</td>
                            <td class="py-2">: 
                                @if(!$p->qc)
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">⏳ Menunggu Pemeriksaan</span>
                                @elseif($p->qc->hasil == 'Layak')
                                    <span class="badge bg-success px-3 py-2 rounded-pill fw-bold">✅ Lolos (Layak)</span>
                                @else
                                    <span class="badge bg-danger px-3 py-2 rounded-pill fw-bold">❌ Reject (Tidak Layak)</span>
                                @endif
                            </td>
                        </tr>
                        @if($p->qc)
                            <tr>
                                <td class="fw-semibold text-muted py-2">Tanggal Diperiksa</td>
                                <td class="py-2">: {{ $p->qc->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="fw-semibold text-muted py-2">Keterangan / Alasan</td>
                            <td class="py-2">: {{ $p->qc ? $p->qc->keterangan : '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Edit Dinamis -->
    @if($p->qc)
        <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header bg-warning text-dark border-0 py-3 rounded-top-4">
                        <h5 class="modal-title fw-bold">✏️ Edit Pemeriksaan QC</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 rounded-3 shadow-sm mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Pemberitahuan:</strong> Untuk mengubah hasil pemeriksaan QC ini, silakan hapus data pemeriksaan saat ini terlebih dahulu dengan menekan tombol <strong>Hapus & QC Ulang</strong> di bawah. Hal ini diperlukan agar data stok gudang Anda terkoreksi kembali secara otomatis dengan aman.
                        </div>
                        <table class="table table-borderless align-middle mb-0">
                            <tr>
                                <td class="fw-semibold text-muted py-2" width="40%">Nama Produk</td>
                                <td class="fw-bold py-2">: {{ $p->produk->nama_produk ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted py-2">Jumlah Produksi</td>
                                <td class="fw-bold py-2">: {{ $p->jumlah_produksi }} Pcs</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted py-2">Hasil Pemeriksaan</td>
                                <td class="py-2">: 
                                    @if($p->qc->hasil == 'Layak')
                                        <span class="badge bg-success px-3 py-2 rounded-pill fw-bold">✅ Lolos (Layak)</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill fw-bold">❌ Reject (Tidak Layak)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted py-2">Catatan / Alasan</td>
                                <td class="py-2">: {{ $p->qc->keterangan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <form action="/qc/delete/{{ $p->qc->id }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemeriksaan QC ini untuk diinput ulang? Stok akan otomatis terkoreksi kembali.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger px-4 py-2 rounded-3 shadow-sm">
                                <i class="bi bi-trash-fill"></i> Hapus & QC Ulang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- Modal Tambah Pemeriksaan Baru (Di pojok kanan atas) -->
<div class="modal fade" id="modalTambahQC" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="/qc/store" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-0 py-3 rounded-top-4">
                    <h5 class="modal-title fw-bold">📝 Tambah Pemeriksaan Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Pilih Produk -->
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1">Produk</label>
                        <select name="produk_id" class="form-select py-2 rounded-3 border-secondary-subtle" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($semuaProduk as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Jumlah Produksi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1">Jumlah Produksi</label>
                        <input type="number" name="jumlah_produksi" class="form-control py-2 rounded-3 border-secondary-subtle" min="1" required placeholder="Masukkan jumlah...">
                    </div>

                    <!-- Hasil Pemeriksaan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1">Hasil Pemeriksaan</label>
                        <select name="hasil" class="form-select py-2 rounded-3 border-secondary-subtle" required>
                            <option value="">-- Pilih Hasil --</option>
                            <option value="Layak">Lolos</option>
                            <option value="Tidak Layak">Reject</option>
                        </select>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold mb-1">Keterangan / Alasan</label>
                        <textarea name="keterangan" class="form-control rounded-3 border-secondary-subtle" rows="3" placeholder="Masukkan catatan kelayakan atau alasan reject..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">✔ Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* CSS Kustom Untuk Tampilan Premium ASA-TIRTA */
.dashboard-content {
    padding-top: 20px;
    padding-bottom: 40px;
}

.content-card {
    background: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    border: none;
}

.tambah-btn {
    background: #1f6feb;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    padding: 10px 18px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.tambah-btn:hover {
    background: #0d6efd;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
}

.qc-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.qc-table th {
    background: #1f6feb !important;
    color: white !important;
    font-weight: 600;
    padding: 16px;
    text-transform: capitalize;
    font-size: 13px;
    letter-spacing: 0.2px;
}

.qc-table th:first-child {
    border-top-left-radius: 12px;
}

.qc-table th:last-child {
    border-top-right-radius: 12px;
}

.qc-table td {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
    font-size: 14px;
}

.qc-table tbody tr {
    transition: all 0.2s ease;
}

.qc-table tbody tr:hover {
    background-color: #f8fafc;
}

.qc-table tbody tr:last-child td {
    border-bottom: none;
}

/* Badge Status Styling */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 13px;
    border: 1px solid transparent;
}

.badge-status.menunggu {
    background-color: #fff8e1 !important;
    color: #b7791f !important;
    border-color: #fde68a !important;
}

.badge-status.lolos {
    background-color: #e8f5e9 !important;
    color: #2e7d32 !important;
    border-color: #c8e6c9 !important;
}

.badge-status.reject {
    background-color: #ffebee !important;
    color: #c62828 !important;
    border-color: #ffcdd2 !important;
}

/* Tombol Aksi */
.btn-action-icon {
    width: 34px;
    height: 34px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    border-radius: 8px;
    padding: 0;
    border: none;
    transition: all 0.2s;
}

.btn-action-icon:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.btn-action-icon i {
    font-size: 14px;
    pointer-events: none;
}

.btn-primary.btn-action-icon {
    background-color: #0d6efd !important;
}

.btn-warning.btn-action-icon {
    background-color: #ffc107 !important;
}

.btn-danger.btn-action-icon {
    background-color: #dc3545 !important;
}

.aksi-group {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
}

.fs-7 {
    font-size: 0.85rem;
}
</style>

@endsection