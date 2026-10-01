<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangRusakController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenagihanController;
use App\Http\Controllers\PermintaanStokController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\QcController;
use App\Http\Controllers\StokController;
use App\Models\Invoice;
//
use App\Models\Pengiriman;
use App\Models\Produksi;
use App\Models\Qc;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.index');
})->name('landing');

// authentication routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Tampilkan & kelola produk (master produk, juga dipakai modul gudang)
Route::middleware(['auth', 'role:gudang'])->group(function () {
    Route::get('/produk', [ProdukController::class, 'index']);

    // Simpan produk
    Route::post('/produk/store', [ProdukController::class, 'store']);
    Route::get('/produk/edit/{id}', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/update/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/delete/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
});

// Halaman stok (riwayat stok + mutasi)
Route::middleware(['auth', 'role:gudang'])->group(function () {
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::post('/stok/masuk', [StokController::class, 'masuk']);
    Route::post('/stok/keluar', [StokController::class, 'keluar']);
    Route::get('/stok/{jenis}', [StokController::class, 'filter'])->name('stok.filter');
});

// Halaman gudang
Route::middleware(['auth', 'role:gudang'])->group(function () {
    Route::get('/gudang/dashboard', [DashboardController::class, 'gudang']);
    Route::get('/gudang/produk', [ProdukController::class, 'index']);
    Route::get('/gudang/laporan', [GudangController::class, 'laporan'])->name('gudang.laporan');
    Route::get('/gudang/export/pdf', [GudangController::class, 'exportPdf'])->name('gudang.export.pdf');
    Route::get('/gudang/export/excel', [GudangController::class, 'exportExcel'])->name('gudang.export.excel');
    Route::post('/gudang/produk/store', [ProdukController::class, 'store']);
    Route::get('/gudang/produk/edit/{id}', [ProdukController::class, 'edit']);
    Route::put('/gudang/produk/update/{id}', [ProdukController::class, 'update']);
    Route::delete('/gudang/produk/delete/{id}', [ProdukController::class, 'destroy']);
    Route::get('/gudang/produk/detail/{id}', [ProdukController::class, 'show'])->name('gudang.produk.detail');
    Route::get('/gudang/barang-masuk', [BarangMasukController::class, 'index']);
    Route::post('/gudang/barang-masuk/store', [BarangMasukController::class, 'store']);
    Route::get('/gudang/barang-masuk/detail/{id}', [BarangMasukController::class, 'show']);
    Route::get('/gudang/barang-keluar', [BarangKeluarController::class, 'index']);
    Route::post('/gudang/barang-keluar/store', [BarangKeluarController::class, 'store']);
    // edit - update -hapus
    Route::get('/gudang/barang-masuk/edit/{id}', [BarangMasukController::class, 'edit'])->name('gudang.barangMasuk.edit');
    Route::put('/gudang/barang-masuk/update/{id}', [BarangMasukController::class, 'update'])->name('gudang.barangMasuk.update');
    Route::delete('/gudang/barang-masuk/delete/{id}', [BarangMasukController::class, 'destroy'])->name('gudang.barangMasuk.destroy');
    Route::get('/gudang/barang-keluar/detail/{id}', [BarangKeluarController::class, 'show']);
    Route::get('/gudang/barang-keluar/edit/{id}', [BarangKeluarController::class, 'edit'])->name('gudang.barangKeluar.edit');
    Route::put('/gudang/barang-keluar/update/{id}', [BarangKeluarController::class, 'update'])->name('gudang.barangKeluar.update');
    Route::delete('/gudang/barang-keluar/delete/{id}', [BarangKeluarController::class, 'destroy'])->name('gudang.barangKeluar.destroy');
    // permintaan stok
    Route::get('/gudang/permintaan-stok', [PermintaanStokController::class, 'index']);
    Route::post('/gudang/permintaan-stok/store', [PermintaanStokController::class, 'store']);
    Route::get('/gudang/permintaan-stok/edit/{id}', [PermintaanStokController::class, 'edit'])->name('gudang.permintaanStok.edit');
    Route::put('/gudang/permintaan-stok/update/{id}', [PermintaanStokController::class, 'update'])->name('gudang.permintaanStok.update');
    Route::delete('/gudang/permintaan-stok/delete/{id}', [PermintaanStokController::class, 'destroy'])->name('gudang.permintaanStok.destroy');
    // barang rusak
    Route::get('/gudang/barang-rusak', [BarangRusakController::class, 'index']);
    Route::post('/gudang/barang-rusak/store', [BarangRusakController::class, 'store']);
    Route::get('/gudang/barang-rusak/detail/{id}', [BarangRusakController::class, 'show']);
    Route::get('/gudang/barang-rusak/edit/{id}', [BarangRusakController::class, 'edit'])->name('gudang.barangRusak.edit');
    Route::put('/gudang/barang-rusak/update/{id}', [BarangRusakController::class, 'update'])->name('gudang.barangRusak.update');
    Route::delete('/gudang/barang-rusak/delete/{id}', [BarangRusakController::class, 'destroy'])->name('gudang.barangRusak.destroy');
});

// Status pengiriman (dilihat gudang & driver)
Route::middleware(['auth', 'role:gudang,driver'])->group(function () {
    Route::get('/pengiriman/status', function () {
        return view('pengiriman.status');
    });
});

// Halaman kasir
Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir', [KasirController::class, 'index']);
    Route::get('/kasir/dashboard', [KasirController::class, 'dashboard'])->name('kasir.dashboard');
    Route::get('/kasir/transaksi', [KasirController::class, 'transaksi'])->name('kasir.transaksi');
    Route::post('/kasir/transaksi/store', [KasirController::class, 'storeTransaksi'])->name('kasir.transaksi.store');
    Route::post('/kasir/transaksi/{id}/bayar', [KasirController::class, 'bayarTransaksi'])->name('kasir.transaksi.bayar');
    Route::post('/kasir/transaksi/{id}/batal', [KasirController::class, 'batalkanTransaksi'])->name('kasir.transaksi.batal');
    Route::post('/kasir/po/store', [KasirController::class, 'storePO'])->name('kasir.po.store');
    Route::post('/kasir/po/{id}/bayar', [KasirController::class, 'bayarPO'])->name('kasir.po.bayar');
    Route::get('/kasir/nota', [KasirController::class, 'nota'])->name('kasir.nota');
    Route::get('/kasir/nota/{id}/cetak', [KasirController::class, 'cetakNota'])->name('kasir.nota.cetak');
    Route::get('/kasir/laporan-penjualan', [KasirController::class, 'laporanPenjualan'])->name('kasir.laporan-penjualan');
    Route::get('/kasir/laporan-stok', [KasirController::class, 'laporanStok'])->name('kasir.laporan-stok');
    // Laporan SPJ bulanan (use case skripsi: kasir melihat laporan penjualan & stok)
    Route::get('/kasir/spj', [KasirController::class, 'spj'])->name('kasir.spj');
    // Daftar invoice (dibuat otomatis sistem saat transaksi lunas — use case: "sistem membuat invoice")
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
});

// Halaman QC
Route::middleware(['auth', 'role:qc'])->group(function () {
    Route::get('/qc', [QcController::class, 'index']);
    Route::get('/qc/dashboard', [QcController::class, 'index']);
    Route::get('/qc/pemeriksaan', [QcController::class, 'pemeriksaan']);
    Route::get('/qc/lolos', [QcController::class, 'lolos']);
    Route::get('/qc/reject', [QcController::class, 'reject']);
    Route::get('/qc/laporan', [QcController::class, 'laporan']);
    Route::get('/qc/export/excel', [QcController::class, 'exportExcel']);
    Route::get('/qc/export/pdf', [QcController::class, 'exportPdf']);
    Route::get('/qc/cetak', [QcController::class, 'cetak']);
    Route::post('/qc/store', [QcController::class, 'store']);
    Route::get('/qc/data', [QcController::class, 'data']);
});

// Keuangan
Route::middleware(['auth', 'role:keuangan'])->group(function () {
    Route::get('/keuangan/export/pdf', [KeuanganController::class, 'exportPdf'])->name('keuangan.export.pdf');
    Route::get('/keuangan/export/excel', [KeuanganController::class, 'exportExcel'])->name('keuangan.export.excel');
    Route::get('/keuangan/dashboard', [KeuanganController::class, 'index'])->name('keuangan.dashboard');
    Route::get('/keuangan/pelanggan', [KeuanganController::class, 'pelanggan'])->name('keuangan.pelanggan');
    Route::post('/keuangan/pelanggan', [KeuanganController::class, 'storePelanggan'])->name('keuangan.pelanggan.store');
    Route::put('/keuangan/pelanggan/{id}', [KeuanganController::class, 'updatePelanggan'])->name('keuangan.pelanggan.update');
    Route::delete('/keuangan/pelanggan/{id}', [KeuanganController::class, 'destroyPelanggan'])->name('keuangan.pelanggan.destroy');
    Route::get('/keuangan/laporan', [KeuanganController::class, 'laporan'])->name('keuangan.laporan');
    Route::get('/keuangan/piutang', [KeuanganController::class, 'piutang'])->name('keuangan.piutang');
    Route::put('/keuangan/piutang/{id}', [KeuanganController::class, 'updatePiutang'])->name('keuangan.piutang.update');
    Route::delete('/keuangan/piutang/{id}', [KeuanganController::class, 'destroyPiutang'])->name('keuangan.piutang.destroy');
    // Form tambah pembelian didelegasikan ke PembelianController@create (sumber data tunggal)
    Route::get('/keuangan/pembelian/tambah', [PembelianController::class, 'create'])->name('keuangan.pembelian.tambah');
    // Nama keuangan.penagihan (sidebar). URI terpisah agar tidak di-shadow oleh
    // /keuangan/penagihan (PenagihanController) — keduanya merender list tagihan yang sama
    Route::get('/keuangan/tagihan', [KeuanganController::class, 'penagihan'])->name('keuangan.penagihan');
    Route::put('/keuangan/penagihan/{id}', [KeuanganController::class, 'updatePenagihan'])->name('keuangan.penagihan.update');
    Route::delete('/keuangan/penagihan/{id}', [KeuanganController::class, 'destroyPenagihan'])->name('keuangan.penagihan.destroy');
});

// Pembelian (resource)
Route::middleware(['auth', 'role:keuangan'])->group(function () {
    Route::resource('pembelian', PembelianController::class);
});

// PENAGIHAN HUTANG
Route::middleware(['auth', 'role:keuangan'])->group(function () {
    Route::get('/keuangan/penagihan', [PenagihanController::class, 'index'])->name('penagihan.index');
    Route::get('/keuangan/penagihan/kirim-tagihan', [PenagihanController::class, 'formKirim'])->name('penagihan.form.kirim');
    Route::post('/keuangan/penagihan/kirim-tagihan', [PenagihanController::class, 'prosesKirim'])->name('penagihan.proses.kirim');
    Route::get('/keuangan/penagihan/kirim-semua', [PenagihanController::class, 'kirimSemua'])->name('penagihan.kirim.semua');
    Route::get('/keuangan/penagihan/{id}', [PenagihanController::class, 'show'])->name('penagihan.show');
    Route::post('/keuangan/penagihan/{id}/kirim', [PenagihanController::class, 'kirim'])->name('penagihan.kirim');
    Route::post('/keuangan/penagihan/{id}/tagih', [PenagihanController::class, 'tagih'])->name('penagihan.tagih');
});

// Driver
Route::middleware(['auth', 'role:driver'])->group(function () {
    Route::get('/driver/dashboard', [DriverController::class, 'dashboard'])->name('driver.dashboard');
    Route::get('/driver/pengiriman/{id?}', [DriverController::class, 'pengiriman'])->name('driver.pengiriman');
    Route::post('/driver/pengiriman/{id}/terima', [DriverController::class, 'terima'])->name('driver.pengiriman.terima');
    Route::post('/driver/pengiriman/{id}/mulai', [DriverController::class, 'mulai'])->name('driver.pengiriman.mulai');
    Route::post('/driver/pengiriman/{id}/upload-bukti', [DriverController::class, 'uploadBukti'])->name('driver.pengiriman.upload-bukti');
    Route::post('/driver/pengiriman/{id}/selesai', [DriverController::class, 'selesai'])->name('driver.pengiriman.selesai');
});

// Halaman laporan (use case "Laporan Sistem": Gudang, Kasir, Keuangan)
Route::middleware(['auth', 'role:gudang,kasir,keuangan'])->get('/laporan', [LaporanController::class, 'index']);

// role-specific groups (URL prefixes optional)
Route::middleware(['auth', 'role:qc'])->prefix('qc')->group(function () {
    Route::get('/', function () {
        return view('modules.qc', [
            'total' => Qc::count(),
            'today' => Qc::whereDate('created_at', now()->toDateString())->count(),
            'items' => Qc::latest()->take(8)->get(),
        ]);
    });
});

Route::middleware(['auth', 'role:gudang,driver'])->prefix('pengiriman')->group(function () {
    Route::get('/', function () {
        return view('modules.pengiriman', [
            'total' => Pengiriman::count(),
            'today' => Pengiriman::whereDate('created_at', now()->toDateString())->count(),
            'items' => Pengiriman::latest()->take(8)->get(),
        ]);
    });
});

Route::middleware(['auth', 'role:gudang'])->prefix('gudang')->group(function () {
    Route::get('/', function () {
        return view('modules.gudang', [
            'pengirimanTotal' => Pengiriman::count(),
            'produksiTotal' => Produksi::count(),
        ]);
    });
});

Route::middleware(['auth', 'role:keuangan'])->prefix('keuangan')->group(function () {
    Route::get('/', function () {
        return view('modules.keuangan', [
            'total' => Invoice::count(),
            'today' => Invoice::whereDate('created_at', now()->toDateString())->count(),
            'items' => Invoice::latest()->take(8)->get(),
        ]);
    });
});

Route::middleware(['auth', 'role:driver'])->prefix('driver')->group(function () {
    Route::get('/', function () {
        return view('modules.driver', [
            'total' => Pengiriman::count(),
            'today' => Pengiriman::whereDate('created_at', now()->toDateString())->count(),
            'items' => Pengiriman::latest()->take(8)->get(),
        ]);
    });
});
