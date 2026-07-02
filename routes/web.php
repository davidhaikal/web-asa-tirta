<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangRusakController;
use App\Http\Controllers\PermintaanStokController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\QcController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\LaporanController;

//
use App\Models\Invoice;
use App\Models\Pengiriman;
use App\Models\Produksi;
use App\Models\Qc;

Route::get('/', function () {
    return view('welcome');
});

// authentication routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

//dashboard
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/admin', [DashboardController::class, 'index']);
Route::get('/admin', [AdminController::class, 'index']);

// Tampilkan produk
Route::get('/produk', [ProdukController::class, 'index']);

// Simpan produk
Route::post('/produk/store', [ProdukController::class, 'store']);
Route::get('/produk/edit/{id}', [ProdukController::class, 'edit']);
Route::put('/produk/update/{id}', [ProdukController::class, 'update']);
Route::delete('/produk/delete/{id}', [ProdukController::class, 'destroy']);

//Penjualan
Route::resource('penjualan', PenjualanController::class);

// Halaman marketing
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/po', [PoController::class, 'index'])->name('po.index');
Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice.index');
Route::get('/permintaan-uang', [PermintaanUangController::class, 'index'])->name('permintaan-uang.index');

//dashboard gudang
Route::get('/gudang', function () {
    return view('gudang.dashboard');
});
// Halaman gudang
Route::get('/gudang', [StokController::class, 'index']);
Route::get('/gudang/dashboard', [DashboardController::class, 'gudang']);
Route::get('/gudang/produk', [ProdukController::class, 'index']);
Route::post('/gudang/produk/store', [ProdukController::class, 'store']);
Route::get('/gudang/produk/edit/{id}', [ProdukController::class, 'edit']);
Route::put('/gudang/produk/update/{id}', [ProdukController::class, 'update']);
Route::get('/gudang/produk/detail/{id}',[ProdukController::class, 'show']);
Route::get('/gudang/barang-masuk', [BarangMasukController::class, 'index']);
Route::post('/gudang/barang-masuk/store', [BarangMasukController::class, 'store']);
Route::get('/gudang/barang-keluar', [BarangKeluarController::class, 'index']);
Route::post('/gudang/barang-keluar/store', [BarangKeluarController::class, 'store']);
//edit - update -hapus
Route::get('/gudang/barang-masuk/edit/{id}', [BarangMasukController::class, 'edit']);
Route::put('/gudang/barang-masuk/update/{id}', [BarangMasukController::class, 'update']);
Route::delete('/gudang/barang-masuk/delete/{id}', [BarangMasukController::class, 'destroy']);
Route::get('/gudang/barang-keluar/edit/{id}', [BarangKeluarController::class, 'edit']);
Route::put('/gudang/barang-keluar/update/{id}', [BarangKeluarController::class, 'update']);
Route::delete('/gudang/barang-keluar/delete/{id}', [BarangKeluarController::class, 'destroy']);
//pengiriman
Route::get('/pengiriman/status',function () { return view('pengiriman.status');});
//permintaan stok
Route::get('/gudang/permintaan-stok', [PermintaanStokController::class, 'index']);
Route::post( '/gudang/permintaan-stok/store',[PermintaanStokController::class, 'store']);
Route::get( '/gudang/permintaan-stok/edit/{id}',[PermintaanStokController::class, 'edit']);
Route::put( '/gudang/permintaan-stok/update/{id}',[PermintaanStokController::class, 'update']);
Route::delete( '/gudang/permintaan-stok/delete/{id}',[PermintaanStokController::class, 'destroy']);
//barang rusak
Route::get('/gudang/barang-rusak',[BarangRusakController::class, 'index']);
Route::post('/gudang/barang-rusak/store',[BarangRusakController::class, 'store']);
Route::get('/gudang/barang-rusak/edit/{id}',[BarangRusakController::class, 'edit']);
Route::put('/gudang/barang-rusak/update/{id}',[BarangRusakController::class, 'update']);
Route::delete('/gudang/barang-rusak/delete/{id}',[BarangRusakController::class, 'destroy']);

// Stok masuk
Route::post('/stok/masuk', [StokController::class, 'masuk']);
Route::post('/stok/keluar', [StokController::class, 'keluar']);
Route::get('/stok', [StokController::class, 'riwayat']);
Route::get('/stok/{jenis}', [StokController::class, 'filter']);

// Halaman kasir
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


// Halaman QC
Route::get('/qc', [QcController::class, 'index']);
Route::get('/qc/dashboard', [QcController::class, 'index']);
Route::get('/qc/pemeriksaan', [QcController::class, 'pemeriksaan']);
Route::get('/qc/lolos', [QcController::class, 'lolos']);
Route::get('/qc/reject', [QcController::class, 'reject']);
Route::get('/qc/laporan', [QcController::class, 'laporan']);
Route::post('/qc/store', [QcController::class, 'store']);
Route::get('/qc/data', [QcController::class, 'data']);

// Keuangan
Route::middleware(['auth'])->group(function () {
Route::get('/keuangan', [KeuanganController::class, 'index']) ->name('keuangan.dashboard');
Route::get('/keuangan/pelanggan', [KeuanganController::class, 'pelanggan']) ->name('keuangan.pelanggan');
Route::get('/keuangan/laporan', [KeuanganController::class, 'laporan']) ->name('keuangan.laporan');
Route::get('/keuangan/piutang', [KeuanganController::class, 'piutang'])->name('keuangan.piutang');
Route::get('/keuangan/penagihan', [KeuanganController::class, 'penagihan']) ->name('keuangan.penagihan');});

// Driver
Route::prefix('driver')->middleware(['auth'])->group(function () {
Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('driver.dashboard');
Route::get('/invoice', [DriverController::class, 'invoice'])->name('driver.invoice.index');
Route::get('/invoice/{id}', [DriverController::class, 'showInvoice'])->name('driver.invoice.show');
Route::post('/invoice/{id}/terima', [DriverController::class, 'terimaInvoice'])->name('driver.invoice.terima');
Route::get('/pengiriman', [DriverController::class, 'pengiriman'])->name('driver.pengiriman.index');
Route::get('/pengiriman/upload/{id}', [DriverController::class, 'uploadForm'])->name('driver.pengiriman.upload');
Route::post('/pengiriman/upload/{id}', [DriverController::class, 'uploadStore'])->name('driver.pengiriman.store');});

// Halaman laporan
Route::get('/laporan', [LaporanController::class, 'index']);

Route::get('/test', function () {
    return "Sistem berjalan";
});

//
Route::post('/produksi', [ProduksiController::class, 'store']);
Route::post('/qc', [QcController::class, 'store']);

// (removed /home route — each role redirects to its own dashboard)

Route::post('/stok/masuk', [StokController::class, 'masuk']);
Route::post('/penjualan', [PenjualanController::class, 'store']);

// role-specific groups (URL prefixes optional)
Route::middleware(['auth','role:admin'])->prefix('admin')->group(function () {
    Route::get('/', function(){ return 'Dashboard Admin'; })->name('admin.dashboard');
    // add other admin routes here
});

Route::middleware(['auth','role:admin,qc'])->prefix('qc')->group(function () {
    Route::get('/', function () {
        return view('modules.qc', [
            'total' => Qc::count(),
            'today' => Qc::whereDate('created_at', now()->toDateString())->count(),
            'items' => Qc::latest()->take(8)->get(),
        ]);
    });
});

Route::middleware(['auth','role:admin,produksi'])->prefix('produksi')->group(function () {
    Route::get('/', function () {
        return view('modules.produksi', [
            'total' => Produksi::count(),
            'today' => Produksi::whereDate('created_at', now()->toDateString())->count(),
            'items' => Produksi::latest()->take(8)->get(),
        ]);
    });
});

Route::middleware(['auth','role:admin,gudang,driver'])->prefix('pengiriman')->group(function () {
    Route::get('/', function () {
        return view('modules.pengiriman', [
            'total' => Pengiriman::count(),
            'today' => Pengiriman::whereDate('created_at', now()->toDateString())->count(),
            'items' => Pengiriman::latest()->take(8)->get(),
        ]);
    });
});

Route::middleware(['auth','role:admin,gudang'])->prefix('gudang')->group(function () {
    Route::get('/', function () {
        return view('modules.gudang', [
            'pengirimanTotal' => Pengiriman::count(),
            'produksiTotal' => Produksi::count(),
        ]);
    });
});

Route::middleware(['auth','role:admin,keuangan'])->prefix('keuangan')->group(function () {
    Route::get('/', function () {
        return view('modules.keuangan', [
            'total' => Invoice::count(),
            'today' => Invoice::whereDate('created_at', now()->toDateString())->count(),
            'items' => Invoice::latest()->take(8)->get(),
        ]);
    });
});

Route::middleware(['auth','role:admin,driver'])->prefix('driver')->group(function () {
    Route::get('/', function () {
        return view('modules.driver', [
            'total' => Pengiriman::count(),
            'today' => Pengiriman::whereDate('created_at', now()->toDateString())->count(),
            'items' => Pengiriman::latest()->take(8)->get(),
        ]);
    });
});
