<?php

namespace App\Http\Controllers;

class MarketingController extends Controller
{
    public function index()
    {
        // Sementara pakai data dummy dulu.
        // Nanti ganti dengan query dari model sesuai kebutuhan, misal:
        // $totalPO = Po::whereDate('created_at', today())->count();

        $totalPO         = 12;
        $totalProduk     = 245;
        $totalInvoice    = 8;
        $permintaanUang  = 5;

        return view('dashboard', compact(
            'totalPO',
            'totalProduk',
            'totalInvoice',
            'permintaanUang'
        ));
    }
}