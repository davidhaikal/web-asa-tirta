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

    private function getDummyData()
    {
        return [
            ['tanggal' => '20 Mei 2026', 'customer' => 'PT Maju Jaya', 'total' => 'Rp 5.000.000', 'status' => 'Selesai'],
            ['tanggal' => '22 Mei 2026', 'customer' => 'CV Tirta Abadi', 'total' => 'Rp 8.500.000', 'status' => 'Diproses'],
        ];
    }

    public function exportPdf()
    {
        $data = $this->getDummyData();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('keuangan.laporan_pdf', compact('data'));
        return $pdf->download('Laporan_Keuangan.pdf');
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KeuanganExport($this->getDummyData()), 'Laporan_Keuangan.xlsx');
    }
}