<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    //
     public function index()
    {
        $penjualan = Penjualan::latest()->get();
        $stok = Stok::with('produk')->latest()->get();

        return view('laporan.index', compact('penjualan', 'stok'));
    }
}
