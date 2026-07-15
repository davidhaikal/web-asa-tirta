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

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

                            <td class="fw-bold text-primary subtotal-text">
                                Rp 0
                            </td>

                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-danger btn-sm btn-remove-row">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="button"
                        class="btn btn-success btn-add-row">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailBarang = document.getElementById('detailBarang');
    const grandTotalEl = document.querySelector('h2.text-success');

    // Function to calculate subtotal and grand total
    function calculateTotal() {
        let grandTotal = 0;
        const rows = detailBarang.querySelectorAll('tr');
        
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('input[name="qty[]"]').value) || 0;
            const harga = parseFloat(row.querySelector('input[name="harga[]"]').value) || 0;
            const subtotal = qty * harga;
            
            row.querySelector('.subtotal-text').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
            grandTotal += subtotal;
        });
        
        grandTotalEl.textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    // Add event listeners to existing row
    detailBarang.addEventListener('input', function(e) {
        if (e.target.name === 'qty[]' || e.target.name === 'harga[]') {
            calculateTotal();
        }
    });

    // Remove row
    detailBarang.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            const rowCount = detailBarang.querySelectorAll('tr').length;
            if (rowCount > 1) {
                e.target.closest('tr').remove();
                calculateTotal();
            } else {
                alert('Minimal harus ada 1 barang.');
            }
        }
    });

    // Add new row
    document.querySelector('.btn-add-row').addEventListener('click', function() {
        const firstRow = detailBarang.querySelector('tr');
        const newRow = firstRow.cloneNode(true);
        
        // Reset values
        newRow.querySelector('select').value = '';
        newRow.querySelector('input[name="qty[]"]').value = '1';
        newRow.querySelector('input[name="harga[]"]').value = '';
        newRow.querySelector('.subtotal-text').textContent = 'Rp 0';
        
        detailBarang.appendChild(newRow);
    });
});
</script>

@endsection