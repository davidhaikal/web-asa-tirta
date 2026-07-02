<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\BarangRusak;

class DashboardController extends Controller
{
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