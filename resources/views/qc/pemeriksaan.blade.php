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
                                    class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalQC{{ $p->id }}">

                                    🔍 Proses QC

                                </button>

                            </td>

                        </tr>

                        <!-- Modal QC -->

                        <div class="modal fade"
                            id="modalQC{{ $p->id }}"
                            tabindex="-1">

                            <div class="modal-dialog">

                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5 class="modal-title">

                                            🧪 Pemeriksaan Produk

                                        </h5>

                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                        </button>

                                    </div>

                                    <form action="/qc/store" method="POST">

                                        @csrf

                                        <div class="modal-body">

                                            <input
                                                type="hidden"
                                                name="produksi_id"
                                                value="{{ $p->id }}">

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Produk
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    value="{{ $p->produk->nama_produk ?? '-' }}"
                                                    readonly>

                                            </div>

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Hasil QC
                                                </label>

                                                <select
                                                    name="hasil"
                                                    class="form-select"
                                                    required>

                                                    <option value="">
                                                        -- Pilih Hasil --
                                                    </option>

                                                    <option value="Layak">
                                                        ✅ Layak
                                                    </option>

                                                    <option value="Tidak Layak">
                                                        ❌ Tidak Layak
                                                    </option>

                                                </select>

                                            </div>

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Keterangan
                                                </label>

                                                <textarea
                                                    name="keterangan"
                                                    class="form-control"
                                                    rows="3"></textarea>

                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button
                                                type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">

                                                Batal

                                            </button>

                                            <button
                                                type="submit"
                                                class="btn btn-success">

                                                💾 Simpan Hasil QC

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                        @empty

                        <tr>

                            <td colspan="5" class="text-center">

                                Tidak ada data produksi

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection