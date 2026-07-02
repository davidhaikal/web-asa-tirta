<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        return view('keuangan.dashboard');
    }

    public function pelanggan()
    {
        return view('keuangan.pelanggan');
    }

    public function laporan()
    {
        return view('keuangan.laporan');
    }

    public function piutang()
    {
        return view('keuangan.piutang');
    }

    public function penagihan()
    {
        return view('keuangan.penagihan');
    }
}