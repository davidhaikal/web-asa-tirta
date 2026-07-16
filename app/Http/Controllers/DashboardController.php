<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\BarangRusak;
use App\Models\PurchaseOrder;

class DashboardController extends Controller
{
    // Dashboard Marketing (default)
    public function index()
    {
        $totalPO = PurchaseOrder::count();
        $totalProduk = Produk::count();
        $totalInvoice = \App\Models\Invoice::count();
        $permintaanUang = 0; // static placeholder karena model belum ada

        return view('marketing.dashboard', compact(
            'totalPO',
            'totalProduk',
            'totalInvoice',
            'permintaanUang'
        ));
    }

    // Dashboard Gudang
    public function gudang()
    {
        $totalProduk = Produk::count();

        $barangMasuk = BarangMasuk::count();

        $barangKeluar = BarangKeluar::count();

        $barangRusak = BarangRusak::count();

        $totalStok = Produk::sum('stok');
        $sumBarangMasuk = BarangMasuk::sum('jumlah');
        $sumBarangKeluar = BarangKeluar::sum('jumlah');
        $sumBarangRusak = BarangRusak::sum('jumlah');

        $stokMenipis = Produk::where('stok', '<=', 10)->count();

        // Ambil aktivitas dari 3 tabel dan urutkan
        $aktivitasMasuk = BarangMasuk::with('produk')->latest()->take(5)->get()->map(function($item) {
            return (object) [
                'tanggal' => $item->tanggal_masuk ?? $item->created_at->format('Y-m-d'),
                'aktivitas' => 'Barang Masuk',
                'produk' => $item->produk->nama_produk ?? '-',
                'jumlah' => $item->jumlah,
                'status' => 'Berhasil',
                'created_at' => $item->created_at
            ];
        });

        $aktivitasKeluar = BarangKeluar::with('produk')->latest()->take(5)->get()->map(function($item) {
            return (object) [
                'tanggal' => $item->tanggal_keluar ?? $item->created_at->format('Y-m-d'),
                'aktivitas' => 'Barang Keluar',
                'produk' => $item->produk->nama_produk ?? '-',
                'jumlah' => $item->jumlah,
                'status' => 'Berhasil',
                'created_at' => $item->created_at
            ];
        });

        $aktivitasRusak = BarangRusak::with('produk')->latest()->take(5)->get()->map(function($item) {
            return (object) [
                'tanggal' => $item->tanggal_rusak ?? $item->created_at->format('Y-m-d'),
                'aktivitas' => 'Barang Rusak',
                'produk' => $item->produk->nama_produk ?? '-',
                'jumlah' => $item->jumlah,
                'status' => 'Gagal',
                'created_at' => $item->created_at
            ];
        });

        $aktivitas = collect()->merge($aktivitasMasuk)->merge($aktivitasKeluar)->merge($aktivitasRusak)->sortByDesc('created_at')->take(5);

        return view('gudang.dashboard', compact(
            'totalProduk',
            'barangMasuk',
            'barangKeluar',
            'barangRusak',
            'totalStok',
            'sumBarangMasuk',
            'sumBarangKeluar',
            'sumBarangRusak',
            'stokMenipis',
            'aktivitas'
        ));
    }
}