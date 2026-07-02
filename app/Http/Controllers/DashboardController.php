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
        $totalInvoice = 8; // static placeholder
        $permintaanUang = 5; // static placeholder

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

        return view('gudang.dashboard', compact(
            'totalProduk',
            'barangMasuk',
            'barangKeluar',
            'barangRusak',
            'totalStok'
        ));
    }
}