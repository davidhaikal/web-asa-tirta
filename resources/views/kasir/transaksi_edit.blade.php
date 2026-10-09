@extends('layouts.kasir', [
    'title' => 'Edit Transaksi Penjualan',
    'subtitle' => 'Kasir > Transaksi > Edit',
])

@section('content')
<div class="k-page-head">
    <div>
        <h1>Edit Transaksi</h1>
        <p>Edit data pelanggan atau metode pembayaran transaksi: <strong>{{ $transaksi->kode }}</strong>.</p>
    </div>
    <a href="{{ route('kasir.transaksi') }}" class="k-btn k-btn-ghost"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row g-3">
    <div class="col-xl-7">
        <div class="k-card">
            <div class="k-card-head">
                <div>
                    <h2>Informasi Transaksi</h2>
                </div>
            </div>
            <div class="k-card-body">
                <form action="{{ route('kasir.transaksi.update', $transaksi->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="pelanggan">Nama Pelanggan</label>
                            <input type="text" id="pelanggan" name="pelanggan" class="form-control" value="{{ old('pelanggan', $transaksi->pelanggan) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="metode">Metode Pembayaran</label>
                            <select id="metode" name="metode" class="form-select" required>
                                <option value="tunai" {{ $transaksi->metode == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                <option value="transfer" {{ $transaksi->metode == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="qris" {{ $transaksi->metode == 'qris' ? 'selected' : '' }}>QRIS</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="k-table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="t-right">Harga</th>
                                    <th class="t-right">Jumlah</th>
                                    <th class="t-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi->detailPenjualans as $detail)
                                <tr>
                                    <td>{{ $detail->produk->nama_produk ?? 'Produk Dihapus' }}</td>
                                    <td class="t-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                    <td class="t-right">{{ $detail->jumlah }}</td>
                                    <td class="t-right">Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="t-right fw-semibold">Total</td>
                                    <td class="t-right fw-bold k-grand">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-2 mb-3">*Catatan: Untuk mengubah item produk, silakan hapus transaksi ini dan buat baru (jika belum lunas).</small>

                    <button type="submit" class="k-btn k-btn-primary k-btn-block k-btn-lg">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
