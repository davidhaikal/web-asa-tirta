@extends('layouts.kasir', [
    'title' => 'Transaksi Penjualan',
    'subtitle' => 'Kasir > Transaksi > Penjualan',
])

@section('content')

<div class="k-page-head">
    <div>
        <h1>Transaksi Penjualan</h1>
        <p>Transaksi baru berstatus <strong>BELUM LUNAS</strong> — stok dikurangkan saat pembayaran dikonfirmasi.</p>
    </div>
    <a href="{{ route('kasir.nota') }}" class="k-btn k-btn-ghost"><i class="bi bi-receipt"></i> Nota Penjualan</a>
</div>

<div class="row g-3">
    {{-- ============ FORM TRANSAKSI (KERANJANG POS) ============ --}}
    <div class="col-xl-7">
        <div class="k-card" style="margin-bottom: 0;">
            <div class="k-card-head">
                <div>
                    <h2><i class="bi bi-receipt-cutoff me-2 text-primary" style="color: var(--k-accent);"></i>Transaksi Baru</h2>
                    <small>Pilih produk, atur jumlah, lalu simpan sebagai transaksi.</small>
                </div>
            </div>
            <div class="k-card-body">
                <form id="formTransaksi" action="{{ route('kasir.transaksi.store') }}" method="POST">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="pelanggan">Nama Pelanggan</label>
                            <input type="text" id="pelanggan" name="pelanggan" class="form-control" placeholder="Walk-in Customer" value="{{ old('pelanggan') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="metode">Metode Pembayaran</label>
                            <select id="metode" name="metode" class="form-select" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tambah produk ke keranjang --}}
                    <div class="row g-2 mb-3 p-2 rounded-3" style="background: #fbfbf9; border: 1px solid var(--k-line);">
                        <div class="col-md-5">
                            <select class="form-select" id="produkPicker" aria-label="Pilih produk">
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($produk as $item)
                                    <option value="{{ $item->id }}" data-harga="{{ $item->harga }}" data-stok="{{ $item->stok }}">
                                        {{ $item->nama_produk }} (Rp {{ number_format($item->harga, 0, ',', '.') }} · Stok {{ $item->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4 col-md-2">
                            <input type="number" class="form-control" id="qtyPicker" value="1" min="1" aria-label="Jumlah">
                        </div>
                        <div class="col-8 col-md-5">
                            <button type="button" class="k-btn k-btn-ghost btn-block" id="btnAddItem"><i class="bi bi-plus-lg"></i> Tambah ke Transaksi</button>
                        </div>
                    </div>

                    {{-- Keranjang --}}
                    <div class="table-responsive">
                        <table class="k-table" id="tableItems">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="t-right">Jumlah</th>
                                    <th class="t-right">Subtotal</th>
                                    <th style="width: 44px;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemRows"></tbody>
                            <tfoot>
                                <tr>
                                    <td class="t-right" style="color: var(--k-muted); font-size: 13px;">Total</td>
                                    <td></td>
                                    <td class="t-right"><span class="k-grand" id="grandTotal">Rp 0</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <input type="hidden" name="items" id="itemsInput" value="">
                    <button type="submit" class="k-btn k-btn-primary k-btn-block k-btn-lg" style="margin-top: 14px;">
                        <i class="bi bi-check2-circle"></i> Simpan Transaksi (Belum Lunas)
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ============ STOK GUDANG ============ --}}
    <div class="col-xl-5">
        <div class="k-card k-sticky" style="margin-bottom: 0;">
            <div class="k-card-head">
                <div>
                    <h2><i class="bi bi-box-seam me-2" style="color: var(--k-accent);"></i>Stok Gudang Tersedia</h2>
                    <small>Referensi harga &amp; stok saat input transaksi.</small>
                </div>
            </div>
            <div class="k-card-body" style="padding-bottom: 8px;">
                <div class="k-search-wrap mb-3">
                    <i class="bi bi-search"></i>
                    <input type="text" id="kStokSearch" class="form-control form-control-sm" placeholder="Cari produk...">
                </div>
                <div class="table-responsive" style="max-height: 460px; overflow-y: auto;">
                    <table class="k-table" id="kStokTable">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="t-right">Harga</th>
                                <th class="t-right">Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produk as $item)
                                <tr data-nama="{{ strtolower($item->nama_produk) }}">
                                    <td class="fw-semibold">{{ $item->nama_produk }}</td>
                                    <td class="t-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="t-right">
                                        <span class="k-badge {{ $item->stok > 50 ? 'b-aman' : ($item->stok > 10 ? 'b-menipis' : 'b-kritis') }}">{{ $item->stok }}</span>
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

{{-- ============ TRANSAKSI TERBARU ============ --}}
<div class="k-card" style="margin-top: 20px;">
    <div class="k-card-head">
        <div>
            <h2>Transaksi Terbaru</h2>
            <small>10 transaksi terakhir — konfirmasikan pembayaran transaksi yang masih belum lunas.</small>
        </div>
        <span class="k-badge b-pending" id="pendingCountBadge"></span>
    </div>
    <div class="table-responsive">
        <table class="k-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th class="t-right">Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th class="t-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksiTerbaru as $trx)
                    <tr class="{{ $trx->status === 'pending' ? 'tr-pending' : '' }}">
                        <td class="fw-semibold">{{ $trx->kode }}</td>
                        <td>{{ $trx->pelanggan }}</td>
                        <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}</td>
                        <td class="t-right fw-semibold">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                        <td><span class="k-badge b-method">{{ strtoupper($trx->metode) }}</span></td>
                        <td>
                            @if ($trx->status === 'lunas')
                                <span class="k-badge b-lunas">LUNAS</span>
                            @elseif ($trx->status === 'pending')
                                <span class="k-badge b-pending">BELUM LUNAS</span>
                            @else
                                <span class="k-badge b-batal">BATAL</span>
                            @endif
                        </td>
                        <td class="t-right" style="white-space: nowrap;">
                            @if ($trx->status === 'pending')
                                <form action="{{ route('kasir.transaksi.bayar', $trx->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="metode" value="{{ $trx->metode }}">
                                    <button type="submit" class="k-btn k-btn-success k-btn-sm js-confirm" title="Stok akan berkurang & status menjadi LUNAS">
                                        <i class="bi bi-cash-coin"></i> Konfirmasi Bayar
                                    </button>
                                </form>
                                <form action="{{ route('kasir.transaksi.batal', $trx->id) }}" method="POST" class="d-inline ms-1">
                                    @csrf
                                    <button type="submit" class="k-btn k-btn-danger-soft k-btn-sm js-confirm">
                                        <i class="bi bi-x-lg"></i> Batal
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="k-empty"><i class="bi bi-receipt"></i>Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============ PO BELUM BAYAR ============ --}}
@if (!$poList->isEmpty())
<div class="k-card">
    <div class="k-card-head">
        <div>
            <h2>Purchase Order — Belum Bayar</h2>
            <small>Mengonfirmasi pembayaran PO akan menambah stok produk.</small>
        </div>
    </div>
    <div class="table-responsive">
        <table class="k-table">
            <thead>
                <tr>
                    <th>Kode PO</th>
                    <th>Produk</th>
                    <th class="t-right">Jumlah</th>
                    <th>Tgl Butuh</th>
                    <th>Status</th>
                    <th class="t-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($poList as $po)
                    <tr class="{{ $po->status === 'menunggu' ? 'tr-pending' : '' }}">
                        <td class="fw-semibold">{{ $po->kode_po }}</td>
                        <td>{{ $po->produk->nama_produk }}</td>
                        <td class="t-right">{{ $po->jumlah }}</td>
                        <td>{{ \Carbon\Carbon::parse($po->tanggal_butuh)->format('d M Y') }}</td>
                        <td>
                            @if ($po->status === 'menunggu')
                                <span class="k-badge b-pending">BELUM BAYAR</span>
                            @elseif ($po->status === 'selesai')
                                <span class="k-badge b-lunas">LUNAS</span>
                            @else
                                <span class="k-badge b-batal">{{ strtoupper($po->status) }}</span>
                            @endif
                        </td>
                        <td class="t-right" style="white-space: nowrap;">
                            @if ($po->status === 'menunggu')
                                <form action="{{ route('kasir.po.bayar', $po->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="k-btn k-btn-success k-btn-sm js-confirm" title="Stok akan bertambah">
                                        <i class="bi bi-cash-coin"></i> Konfirmasi Bayar
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@push('scripts')
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
    $pendingCount = collect($transaksiTerbaru)->where('status', 'pending')->count();
@endphp
<script>
(function () {
    var produkData = <?php echo $produkJson; ?>;

    var tbody = document.getElementById('itemRows');
    var grandEl = document.getElementById('grandTotal');
    var items = []; // {id, nama, harga, stok, qty}
    var uid = 0;

    function formatRp(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

    function renderRow(item) {
        var tr = document.createElement('tr');
        tr.id = 'krow-' + item.id;
        tr.className = item.qty > item.stok ? 'k-warn-row' : '';
        var warn = item.qty > item.stok
            ? ' <span class="k-badge b-kritis" style="margin-left:6px;">Stok: ' + item.stok + '</span>'
            : '';
        tr.innerHTML =
            '<td><span class="fw-semibold">' + item.nama + '</span>' + warn +
            '<div style="font-size:12px;color:var(--k-muted);">Rp ' + item.harga.toLocaleString('id-ID') + ' / unit</div></td>' +
            '<td class="t-right"><input type="number" class="form-control form-control-sm k-qty text-end" min="1" value="' + item.qty + '" aria-label="Jumlah ' + item.nama + '"></td>' +
            '<td class="t-right fw-bold">' + formatRp(item.harga * item.qty) + '</td>' +
            '<td class="t-right"><button type="button" class="k-item-remove" aria-label="Hapus"><i class="bi bi-x-lg"></i></button></td>';

        tr.querySelector('.k-qty').addEventListener('input', function () {
            item.qty = Math.max(1, parseInt(this.value) || 1);
            this.value = item.qty;
            updateRow(item);
        });
        tr.querySelector('.k-item-remove').addEventListener('click', function () {
            items = items.filter(function (it) { return it.id !== item.id; });
            tr.remove();
            recalc();
        });
        tbody.appendChild(tr);
        updateRow(item);
    }

    function updateRow(item) {
        var tr = document.getElementById('krow-' + item.id);
        if (!tr) return;
        tr.className = item.qty > item.stok ? 'k-warn-row' : '';
        var warn = tr.querySelector('td .k-badge');
        if (item.qty > item.stok) {
            if (!warn) {
                warn = document.createElement('span');
                warn.className = 'k-badge b-kritis';
                warn.style.marginLeft = '6px';
                tr.querySelector('td').firstChild.insertAdjacentElement('afterend', warn);
            }
            warn.textContent = 'Stok: ' + item.stok;
        } else if (warn) {
            warn.remove();
        }
        tr.querySelector('.fw-bold').textContent = formatRp(item.harga * item.qty);
        recalc();
    }

    function recalc() {
        var total = 0;
        items.forEach(function (it) { total += it.harga * it.qty; });
        grandEl.textContent = formatRp(total);
    }

    function addItem() {
        var sel = document.getElementById('produkPicker');
        var qtyInput = document.getElementById('qtyPicker');
        if (!sel.value) {
            sel.focus();
            return;
        }
        var opt = sel.selectedOptions[0];
        var qty = Math.max(1, parseInt(qtyInput.value) || 1);
        var pid = parseInt(sel.value);
        var existing = items.filter(function (it) { return it.id === pid; })[0];
        if (existing) {
            existing.qty += qty;
            updateRow(existing);
        } else {
            var src = produkData.filter(function (p) { return p.id === pid; })[0] || {};
            var item = {
                id: pid,
                nama: src.nama || opt.textContent.split(' (')[0],
                harga: src.harga || parseInt(opt.dataset.harga) || 0,
                stok: (src.stok === undefined ? parseInt(opt.dataset.stok) : src.stok) || 0,
                qty: qty
            };
            items.push(item);
            renderRow(item);
        }
        recalc();
        sel.selectedIndex = 0;
        qtyInput.value = 1;
        sel.focus();
    }

    document.getElementById('btnAddItem').addEventListener('click', addItem);

    document.getElementById('formTransaksi').addEventListener('submit', function (e) {
        var payload = items.filter(function (it) { return it.qty > 0; })
            .map(function (it) { return { produk_id: it.id, jumlah: it.qty }; });
        if (payload.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 produk!');
            return;
        }
        document.getElementById('itemsInput').value = JSON.stringify(payload);
    });

    // Pencarian stok (client-side)
    var search = document.getElementById('kStokSearch');
    if (search) {
        search.addEventListener('input', function () {
            var q = search.value.toLowerCase();
            document.querySelectorAll('#kStokTable tbody tr').forEach(function (tr) {
                tr.style.display = tr.dataset.nama.indexOf(q) !== -1 ? '' : 'none';
            });
        });
    }

    // Badge jumlah pending
    var pendingBadge = document.getElementById('pendingCountBadge');
    var pendingCount = {{ $pendingCount }};
    if (pendingCount > 0) {
        pendingBadge.innerHTML = pendingCount + ' belum bayar';
    } else {
        pendingBadge.style.display = 'none';
    }
})();
</script>
@endpush

@endsection
