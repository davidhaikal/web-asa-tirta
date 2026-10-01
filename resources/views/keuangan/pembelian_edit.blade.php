@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-white">
            <h4 class="fw-bold mb-0">
                Edit Pembelian Barang
            </h4>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- Nomor Pembelian --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Nomor Pembelian
                        </label>

                        <input type="text"
                               class="form-control"
                               name="no_transaksi"
                               value="{{ $pembelian->no_transaksi }}"
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
                               value="{{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('Y-m-d') }}">
                    </div>

                    {{-- Supplier --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            Supplier
                        </label>

                        <select class="form-select" name="supplier">

                            <option value="">-- Pilih Supplier --</option>

                            @foreach(['PT Tirta Abadi', 'CV Sumber Air', 'PT Aqua Indonesia'] as $supplier)
                                <option value="{{ $supplier }}"
                                    {{ ($pembelian->supplier === $supplier) ? 'selected' : '' }}>
                                    {{ $supplier }}
                                </option>
                            @endforeach

                            {{-- Pastikan nilai saat ini selalu tersedia --}}
                            @if($pembelian->supplier && !in_array($pembelian->supplier, ['PT Tirta Abadi', 'CV Sumber Air', 'PT Aqua Indonesia']))
                                <option value="{{ $pembelian->supplier }}" selected>
                                    {{ $pembelian->supplier }}
                                </option>
                            @endif

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
                                   {{ (strtolower($pembelian->status) === 'lunas') ? 'checked' : '' }}>

                            <label class="form-check-label">
                                Lunas
                            </label>

                        </div>

                        <div class="form-check form-check-inline">

                            <input class="form-check-input"
                                   type="radio"
                                   name="status"
                                   value="utang"
                                   {{ (strtolower($pembelian->status) !== 'lunas') ? 'checked' : '' }}>

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
                               name="tanggal_jatuh_tempo"
                               value="{{ $pembelian->tanggal_jatuh_tempo
                                   ? \Carbon\Carbon::parse($pembelian->tanggal_jatuh_tempo)->format('Y-m-d')
                                   : '' }}">

                    </div>

                    {{-- Keterangan --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Keterangan
                        </label>

                        <textarea class="form-control"
                                  rows="1"
                                  name="keterangan">{{ $pembelian->keterangan }}</textarea>

                    </div>

                </div>

                <hr>

                <h5 class="fw-bold mb-3">
                    Daftar Barang
                </h5>

                <p class="text-muted">
                    <i class="bi bi-info-circle"></i>
                    Detail barang tidak dapat diubah dari halaman ini.
                </p>

                <table class="table table-bordered align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>No</th>
                            <th>Produk</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($pembelian->details as $detail)

                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td class="fw-semibold">
                                    {{ $detail->produk->nama_produk ?? '-' }}
                                </td>

                                <td class="text-end">{{ number_format($detail->qty) }}</td>

                                <td class="text-end">
                                    Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}
                                </td>

                                <td class="text-end fw-bold">
                                    Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    Belum ada detail barang.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

                <div class="d-flex justify-content-end gap-2 mt-4">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-save"></i>

                        Simpan Perubahan

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
