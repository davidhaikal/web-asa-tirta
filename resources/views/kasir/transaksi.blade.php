@extends('layouts.app', [
    'title' => 'Transaksi Penjualan',
    'subtitle' => 'Kasir > Transaksi > Penjualan',
])

@section('content')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Transaksi Penjualan</h2>
            <p class="text-muted mb-0">Semua transaksi baru berstatus <strong>BELUM LUNAS</strong>. Klik SUDAH BAYAR untuk konfirmasi.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Form Transaksi Baru</h5>
                    <form id="formTransaksi" action="{{ route('kasir.transaksi.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="pelanggan" class="form-control" placeholder="Walk-in Customer" value="{{ old('pelanggan') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Metode Pembayaran</label>
                                <select name="metode" class="form-select" required>
                                    <option value="tunai">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <h6 class="fw-bold mb-3">Pilih Produk</h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="tableItems">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemRows"></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addRow()">+ Tambah Produk</button>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold">Total:</h5>
                            <h4 class="fw-bold text-success" id="grandTotal">Rp 0</h4>
                        </div>
                        <input type="hidden" name="items" id="itemsInput" value="">
                        <button type="submit" class="btn btn-primary w-100 py-2" onclick="return prepareSubmit()">
                            Simpan Transaksi (Belum Lunas)
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Stok Gudang Tersedia</h5>
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produk as $item)
                                    <tr>
                                        <td>{{ $item->nama_produk }}</td>
                                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge {{ $item->stok > 50 ? 'bg-success' : ($item->stok > 10 ? 'bg-warning' : 'bg-danger') }}">{{ $item->stok }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Daftar Transaksi Terbaru</h5>
            @if ($transaksiTerbaru->isEmpty())
                <p class="text-muted">Belum ada transaksi.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksiTerbaru as $trx)
                                <tr>
                                    <td>{{ $trx->kode }}</td>
                                    <td>{{ $trx->pelanggan }}</td>
                                    <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-secondary">{{ strtoupper($trx->metode) }}</span></td>
                                    <td>
                                        @if ($trx->status === 'lunas')
                                            <span class="badge bg-success">LUNAS</span>
                                        @elseif ($trx->status === 'pending')
                                            <span class="badge bg-danger">BELUM LUNAS</span>
                                        @else
                                            <span class="badge bg-dark">BATAL</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($trx->status === 'pending')
                                            <form action="{{ route('kasir.transaksi.bayar', $trx->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="metode" value="{{ $trx->metode }}">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi pembayaran {{ $trx->kode }}? Stok akan berkurang & status jadi LUNAS!')">SUDAH BAYAR?</button>
                                            </form>
                                            <form action="{{ route('kasir.transaksi.batal', $trx->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Batalkan transaksi {{ $trx->kode }}?')">BATAL</button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if (!$poList->isEmpty())
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body">
            <h5 class="fw-bold mb-4">Purchase Order (PO) - Belum Bayar</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode PO</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Tgl Butuh</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($poList as $po)
                            <tr>
                                <td>{{ $po->kode_po }}</td>
                                <td>{{ $po->produk->nama_produk }}</td>
                                <td>{{ $po->jumlah }}</td>
                                <td>{{ \Carbon\Carbon::parse($po->tanggal_butuh)->format('d M Y') }}</td>
                                <td>
                                    @if ($po->status === 'menunggu')
                                        <span class="badge bg-danger">BELUM BAYAR</span>
                                    @elseif ($po->status === 'selesai')
                                        <span class="badge bg-success">LUNAS</span>
                                    @else
                                        <span class="badge bg-dark">{{ strtoupper($po->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($po->status === 'menunggu')
                                        <form action="{{ route('kasir.po.bayar', $po->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi bayar PO {{ $po->kode_po }}? Stok akan bertambah!')">SUDAH BAYAR?</button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

@php
    $produkArray = [];
    foreach ($produk as $p) {
        $produkArray[] = [
            'id' => $p->id,
            'nama' => $p->nama_produk,
            'harga' => $p->harga,
            'stok' => $p->stok,
        ];
    }
    $produkJson = json_encode($produkArray);
@endphp

<script>
var produkData = <?php echo $produkJson; ?>;
var rowCount = 0;

function addRow() {
    rowCount++;
    var tbody = document.getElementById('itemRows');
    var options = '';
    for (var i = 0; i < produkData.length; i++) {
        var p = produkData[i];
        options += '<option value="' + p.id + '" data-harga="' + p.harga + '" data-stok="' + p.stok + '">' + p.nama + ' (Stok: ' + p.stok + ')</option>';
    }
    var tr = document.createElement('tr');
    tr.id = 'row-' + rowCount;
    tr.innerHTML = '<td><select class="form-select form-select-sm produk-select" onchange="hitungSubtotal(this)"><option value="">-- Pilih --</option>' + options + '</select></td>' +
        '<td class="harga-display text-muted">-</td>' +
        '<td class="stok-display text-muted">-</td>' +
        '<td><input type="number" class="form-control form-control-sm jumlah-input" min="1" value="1" onchange="hitungSubtotal(this)" style="width: 80px;"></td>' +
        '<td class="subtotal-display fw-bold">Rp 0</td>' +
        '<td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(\'row-' + rowCount + '\')">X</button></td>';
    tbody.appendChild(tr);
}

function removeRow(rowId) {
    var el = document.getElementById(rowId);
    if (el) el.remove();
    hitungGrandTotal();
}

function hitungSubtotal(el) {
    var row = el.closest('tr');
    var select = row.querySelector('.produk-select');
    var jumlahInput = row.querySelector('.jumlah-input');
    var selected = select.selectedOptions[0];
    if (selected && selected.value) {
        var harga = parseInt(selected.dataset.harga) || 0;
        var stok = parseInt(selected.dataset.stok) || 0;
        var jumlah = parseInt(jumlahInput.value) || 0;
        var subtotal = harga * jumlah;
        row.querySelector('.harga-display').textContent = 'Rp ' + harga.toLocaleString('id-ID');
        row.querySelector('.stok-display').textContent = stok;
        row.querySelector('.subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    } else {
        row.querySelector('.harga-display').textContent = '-';
        row.querySelector('.stok-display').textContent = '-';
        row.querySelector('.subtotal-display').textContent = 'Rp 0';
    }
    hitungGrandTotal();
}

function hitungGrandTotal() {
    var total = 0;
    var rows = document.querySelectorAll('#itemRows tr');
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var select = row.querySelector('.produk-select');
        var jumlahEl = row.querySelector('.jumlah-input');
        var jumlah = parseInt(jumlahEl ? jumlahEl.value : 0) || 0;
        if (select && select.value) {
            var harga = parseInt(select.selectedOptions[0] ? select.selectedOptions[0].dataset.harga : 0) || 0;
            total += harga * jumlah;
        }
    }
    document.getElementById('grandTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function prepareSubmit() {
    var items = [];
    var rows = document.querySelectorAll('#itemRows tr');
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var select = row.querySelector('.produk-select');
        var jumlahEl = row.querySelector('.jumlah-input');
        var jumlah = parseInt(jumlahEl ? jumlahEl.value : 0) || 0;
        if (select && select.value && jumlah > 0) {
            items.push({
                produk_id: parseInt(select.value),
                jumlah: jumlah
            });
        }
    }
    if (items.length === 0) {
        alert('Pilih minimal 1 produk!');
        return false;
    }
    document.getElementById('itemsInput').value = JSON.stringify(items);
    return true;
}

addRow();
</script>

@endsection