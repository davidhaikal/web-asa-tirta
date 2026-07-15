@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-white">
            <h4 class="fw-bold mb-0">
                Tambah Pembelian Barang
            </h4>
        </div>

        <div class="card-body">

            <form action="{{ route('pembelian.store') }}" method="POST">
                @csrf

                <div class="row">

                    {{-- Nomor Pembelian --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Nomor Pembelian
                        </label>

                        <input type="text"
                               class="form-control"
                               name="no_transaksi"
                               value="{{ $kode }}"
                               readonly>
                    </div>

                    {{-- Tanggal --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Tanggal Pembelian
                        </label>

                        <input type="date"
                               class="form-control"
                               name="tanggal_pembelian"
                               value="{{ date('Y-m-d') }}">
                    </div>

                    {{-- Supplier --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Supplier
                        </label>

                        <select class="form-select" name="supplier">

                            <option value="">-- Pilih Supplier --</option>

                            <option>PT Tirta Abadi</option>
                            <option>CV Sumber Air</option>
                            <option>PT Aqua Indonesia</option>

                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold d-block">
                            Status Pembayaran
                        </label>

                        <div class="form-check form-check-inline">

                            <input class="form-check-input"
                                   type="radio"
                                   name="status"
                                   value="lunas"
                                   checked>

                            <label class="form-check-label">
                                Lunas
                            </label>

                        </div>

                        <div class="form-check form-check-inline">

                            <input class="form-check-input"
                                   type="radio"
                                   name="status"
                                   value="utang">

                            <label class="form-check-label">
                                Utang
                            </label>

                        </div>

                    </div>

                    {{-- Jatuh Tempo --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Tanggal Jatuh Tempo
                        </label>

                        <input type="date"
                               class="form-control"
                               name="tanggal_jatuh_tempo">

                    </div>

                    {{-- Keterangan --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Keterangan
                        </label>

                        <textarea class="form-control"
                                  rows="1"
                                  name="keterangan"></textarea>

                    </div>

                </div>

                <hr>

                <h5 class="fw-bold mb-3">
                    Daftar Barang
                </h5>

                <table class="table table-bordered align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Produk</th>
                            <th width="100">Qty</th>
                            <th width="180">Harga</th>
                            <th width="180">Subtotal</th>
                            <th width="70">Aksi</th>

                        </tr>

                    </thead>

                    <tbody id="detailBarang">

                        <tr>

                            <td>

                               <select class="form-select" name="produk[]">

                                    <option value="">Pilih Produk</option>
                                    @foreach($produks as $produk)

                                        <option value="{{ $produk->id }}">
                                            {{ $produk->nama_produk }}
                                        </option>

                                    @endforeach

                                </select>

                            </td>

                            <td>

                                <input type="number"
                                       class="form-control"
                                       name="qty[]"
                                       min="1"
                                       value="1">

                            </td>

                            <td>

                                <input type="number"
                                       class="form-control"
                                       name="harga[]"
                                       min="0">

                            </td>

                            <td class="fw-bold text-primary">

                                Rp 0

                            </td>

                            <td class="text-center">

                                <button type="button"
                                        class="btn btn-danger btn-sm">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

                <button type="button"
                        class="btn btn-success">

                    <i class="bi bi-plus-circle"></i>

                    Tambah Barang

                </button>

                <div class="text-end mt-4">

                    <small class="text-muted">
                        Grand Total
                    </small>

                    <h2 class="fw-bold text-success">

                        Rp 0

                    </h2>

                </div>

                <hr>

                <div class="d-flex justify-content-end gap-2">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-save"></i>

                        Simpan Pembelian

                    </button>

                    <a href="{{ route('pembelian.index') }}"
                       class="btn btn-secondary">

                        <i class="bi bi-arrow-left"></i>

                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection