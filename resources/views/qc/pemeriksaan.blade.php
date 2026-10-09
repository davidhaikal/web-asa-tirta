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

    <!-- Tabel Produksi -->

    <div class="card border-0 shadow rounded-4">

            <h5 class="fw-bold mb-0">
                📋 Daftar Riwayat Pemeriksaan
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th>Jumlah Produksi</th>
                            <th>Tanggal Produksi</th>
                            <th>Hasil QC</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($dataQc as $qc)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $qc->produksi->produk->nama_produk ?? '-' }}</td>
                            <td>{{ $qc->produksi->jumlah_produksi ?? '-' }}</td>
                            <td>{{ $qc->produksi->tanggal_produksi ? \Carbon\Carbon::parse($qc->produksi->tanggal_produksi)->format('d M Y') : ($qc->produksi->created_at ? $qc->produksi->created_at->format('d M Y') : '-') }}</td>
                            <td>
                                @if($qc->hasil == 'Layak')
                                    <span class="badge bg-success">✔ Lolos</span>
                                @else
                                    <span class="badge bg-danger">✖ Reject</span>
                                @endif
                            </td>
                            <td>{{ $qc->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <span class="text-muted">
                                    Belum ada data pemeriksaan produk.
                                </span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

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