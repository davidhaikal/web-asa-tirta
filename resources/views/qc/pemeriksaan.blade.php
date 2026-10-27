@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Header -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold text-primary">
                🧪 Pemeriksaan Produk
            </h2>

            <p class="text-muted">
                Pemeriksaan kualitas hasil produksi sebelum masuk gudang
            </p>

        </div>
        <button class="btn btn-primary px-4 py-2 rounded-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahQC">
            + Tambah Pemeriksaan
        </button>
    </div>

    <!-- Filter Pemeriksaan -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ url('/qc/pemeriksaan') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text"
                           name="search"
                           class="form-control"
                           value="{{ request('search') }}"
                           placeholder="🔍 Cari Produk / Keterangan...">
                </div>

                <div class="col-md-3">
                    <select name="hasil" class="form-select">
                        <option value="">Semua Hasil QC</option>
                        <option value="Layak" {{ request('hasil') === 'Layak' ? 'selected' : '' }}>✔ Layak (Lolos)</option>
                        <option value="Tidak Layak" {{ request('hasil') === 'Tidak Layak' ? 'selected' : '' }}>✖ Tidak Layak (Reject)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="date"
                           name="tanggal"
                           class="form-control"
                           value="{{ request('tanggal') }}">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    @if(request('search') || request('hasil') || request('tanggal'))
                        <a href="{{ url('/qc/pemeriksaan') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Produksi -->
    <div class="card border-0 shadow rounded-4 overflow-hidden">

        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0">
                📋 Daftar Riwayat Pemeriksaan
            </h5>
        </div>

        <div class="card-body p-4">

            <div class="table-responsive">

                <table class="table table-hover align-middle qc-table mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Jumlah Produksi</th>
                            <th>Tanggal Produksi</th>
                            <th>Hasil QC</th>
                            <th>Keterangan</th>
                            <th class="text-center" width="160">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($dataQc as $qc)
                        <tr>
                            <td>{{ ($dataQc->currentPage() - 1) * $dataQc->perPage() + $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $qc->produksi->produk->nama_produk ?? '-' }}</td>
                            <td>{{ $qc->produksi->jumlah_produksi ?? '-' }}</td>
                            <td>{{ $qc->produksi && $qc->produksi->tanggal_produksi ? \Carbon\Carbon::parse($qc->produksi->tanggal_produksi)->format('d M Y') : ($qc->produksi && $qc->produksi->created_at ? $qc->produksi->created_at->format('d M Y') : '-') }}</td>
                            <td>
                                @if($qc->hasil == 'Layak')
                                    <span class="badge bg-success">✔ Lolos</span>
                                @else
                                    <span class="badge bg-danger">✖ Reject</span>
                                @endif
                            </td>
                            <td>{{ $qc->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                <div class="aksi-group">
                                    <button type="button"
                                            class="btn btn-primary btn-action"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDetailQC{{ $qc->id }}"
                                            title="Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>

                                    <button type="button"
                                            class="btn btn-warning btn-action"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditQC{{ $qc->id }}"
                                            title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>

                                    <form action="/qc/delete/{{ $qc->id }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus data pemeriksaan QC ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-danger btn-action"
                                                title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <span class="text-muted">
                                    Belum ada data pemeriksaan produk.
                                </span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan {{ $dataQc->firstItem() ?? 0 }}–{{ $dataQc->lastItem() ?? 0 }} dari {{ $dataQc->total() ?? 0 }} data
                </div>
                <div>
                    {{ $dataQc->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.qc-table th,
.qc-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}
.qc-table th {
    background: #f8fafc;
    font-weight: 600;
    color: #334155;
}
.aksi-group {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: nowrap;
}
.aksi-group form {
    margin: 0;
}
.btn-action {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    padding: 0;
    transition: .2s ease;
}
.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
}
.btn-action i {
    font-size: 15px;
    line-height: 1;
}
</style>

<!-- Modal Detail & Edit Pemeriksaan -->
@foreach($dataQc as $qc)
<div class="modal fade" id="modalDetailQC{{ $qc->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">🔍 Detail Pemeriksaan QC</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="40%" class="text-muted">Produk</th>
                        <td class="fw-semibold">{{ $qc->produksi->produk->nama_produk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Jumlah Produksi</th>
                        <td>{{ $qc->produksi->jumlah_produksi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tanggal Produksi</th>
                        <td>{{ $qc->produksi && $qc->produksi->tanggal_produksi ? \Carbon\Carbon::parse($qc->produksi->tanggal_produksi)->format('d M Y') : ($qc->produksi && $qc->produksi->created_at ? $qc->produksi->created_at->format('d M Y') : '-') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Hasil QC</th>
                        <td>
                            @if($qc->hasil == 'Layak')
                                <span class="badge bg-success">✔ Lolos</span>
                            @else
                                <span class="badge bg-danger">✖ Reject</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Keterangan</th>
                        <td>{{ $qc->keterangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Waktu Pemeriksaan</th>
                        <td>{{ $qc->created_at ? $qc->created_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditQC{{ $qc->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/qc/update/{{ $qc->id }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">✏️ Edit Pemeriksaan QC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select name="produk_id" class="form-select" required>
                            @foreach($semuaProduk as $prod)
                                <option value="{{ $prod->id }}" {{ ($qc->produksi->produk_id ?? '') == $prod->id ? 'selected' : '' }}>
                                    {{ $prod->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Produksi</label>
                        <input type="number" name="jumlah_produksi" class="form-control" min="1" value="{{ $qc->produksi->jumlah_produksi ?? 1 }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hasil Pemeriksaan</label>
                        <select name="hasil" class="form-select" required>
                            <option value="Layak" {{ $qc->hasil == 'Layak' ? 'selected' : '' }}>Layak (Lolos QC)</option>
                            <option value="Tidak Layak" {{ $qc->hasil == 'Tidak Layak' ? 'selected' : '' }}>Tidak Layak (Reject)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Alasan</label>
                        <textarea name="keterangan" class="form-control" rows="3" required>{{ $qc->keterangan }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">✔ Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Tambah Pemeriksaan -->
<div class="modal fade" id="modalTambahQC" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/qc/store" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">📝 Tambah Pemeriksaan Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select name="produk_id" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($semuaProduk as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Produksi</label>
                        <input type="number" name="jumlah_produksi" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hasil Pemeriksaan</label>
                        <select name="hasil" class="form-select" required>
                            <option value="">-- Pilih Hasil --</option>
                            <option value="Layak">Layak (Lolos QC)</option>
                            <option value="Tidak Layak">Tidak Layak (Reject)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Alasan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Masukkan keterangan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">✔ Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection