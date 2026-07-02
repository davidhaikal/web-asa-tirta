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

    </div>

    <!-- Tabel Produksi -->

    <div class="card border-0 shadow rounded-4">

        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">
                📋 Daftar Produksi Menunggu QC
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
                            <th>Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($produksi as $p)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $p->produk->nama_produk ?? '-' }}
                            </td>

                            <td>
                                {{ $p->jumlah_produksi }}
                            </td>

                            <td>
                                {{ $p->created_at->format('d M Y') }}
                            </td>

                            <td>

                                <button
                                    class="btn btn-success btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalLolos{{ $p->id }}">

                                    ✔ Lolos

                                </button>

                                <button
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalReject{{ $p->id }}">

                                    ✖ Reject

                                </button>

                            </td>

                        </tr>

                        <!-- Modal Lolos -->
                        <div class="modal fade" id="modalLolos{{ $p->id }}" tabindex="-1">

                            <div class="modal-dialog">

                                <div class="modal-content">

                                    <form action="/qc/store" method="POST">

                                        @csrf

                                        <input type="hidden"
                                            name="produksi_id"
                                            value="{{ $p->id }}">

                                        <input type="hidden"
                                            name="hasil"
                                            value="Layak">

                                        <div class="modal-header bg-success text-white">

                                            <h5 class="modal-title">
                                                ✔ Produk Lolos
                                            </h5>

                                            <button type="button"
                                                    class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal">
                                            </button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Produk
                                                </label>

                                                <input type="text"
                                                    class="form-control"
                                                    value="{{ $p->produk->nama_produk ?? '-' }}"
                                                    readonly>

                                            </div>

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Keterangan
                                                </label>

                                                <textarea
                                                    name="keterangan"
                                                    class="form-control"
                                                    rows="3"
                                                    placeholder="Masukkan keterangan produk lolos..."
                                                    required></textarea>

                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal">
                                                Batal
                                            </button>

                                            <button type="submit"
                                                    class="btn btn-success">
                                                ✔ Simpan
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                        <!-- Modal Reject -->
                        <div class="modal fade" id="modalReject{{ $p->id }}" tabindex="-1">

                            <div class="modal-dialog">

                                <div class="modal-content">

                                    <form action="/qc/store" method="POST">

                                        @csrf

                                        <input type="hidden"
                                            name="produksi_id"
                                            value="{{ $p->id }}">

                                        <input type="hidden"
                                            name="hasil"
                                            value="Tidak Layak">

                                        <div class="modal-header bg-danger text-white">

                                            <h5 class="modal-title">
                                                ✖ Produk Reject
                                            </h5>

                                            <button type="button"
                                                    class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal">
                                            </button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Produk
                                                </label>

                                                <input type="text"
                                                    class="form-control"
                                                    value="{{ $p->produk->nama_produk ?? '-' }}"
                                                    readonly>

                                            </div>

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Alasan Reject
                                                </label>

                                                <textarea
                                                    name="keterangan"
                                                    class="form-control"
                                                    rows="3"
                                                    placeholder="Masukkan alasan produk ditolak..."
                                                    required></textarea>

                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal">
                                                Batal
                                            </button>

                                            <button type="submit"
                                                    class="btn btn-danger">
                                                ✖ Simpan
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    @empty

                    <tr>

                        <td colspan="5" class="text-center py-4">

                        <span class="text-muted">

                            Belum ada data produksi yang menunggu pemeriksaan.

                        </span>

                    </td>

                </tr>
            <td>
        <td>

    @endforelse

@endsection