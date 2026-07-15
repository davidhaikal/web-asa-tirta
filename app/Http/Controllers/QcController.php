<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\Qc;
use App\Models\Produk;
use App\Exports\QcExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Stok;


class QcController extends Controller
{

    // DASHBOARD QC
    public function index()
    {
        $totalQc = Qc::count();
        $totalLolos = Qc::where('hasil', 'Layak')->count();
        $totalReject = Qc::where('hasil', 'Tidak Layak')->count();
        $hariIni = Qc::whereDate('created_at', now()->toDateString())->count();
        $dataQc = Qc::latest()->take(6)->get();

        return view('qc.dashboard', compact(
            'totalQc',
            'totalLolos',
            'totalReject',
            'hariIni',
            'dataQc'
        ));
    }


    // HALAMAN PEMERIKSAAN
    public function pemeriksaan()
    {
        $produksi = Produksi::all();

        return view('qc.pemeriksaan', compact('produksi'));
    }


    // PRODUK LOLOS
    public function lolos()
    {
        $dataLolos = Qc::where('hasil', 'Layak')->get();

        return view('qc.lolos', compact('dataLolos'));
    }


    // PRODUK REJECT
    public function reject()
    {
        $dataReject = Qc::where('hasil', 'Tidak Layak')->get();

        return view('qc.reject', compact('dataReject'));
    }


    // LAPORAN QC
    public function laporan()
    {
        $dataQc = Qc::latest()->get();

        $totalQc = Qc::count();

        $totalLolos = Qc::where('hasil', 'Layak')->count();

        $totalReject = Qc::where('hasil', 'Tidak Layak')->count();

        return view('qc.laporan', compact(
            'dataQc',
            'totalQc',
            'totalLolos',
            'totalReject'
        ));
    }


    // DATA QC
    public function data()
    {
        return Qc::all();
    }


    // SIMPAN QC

    public function store(Request $request)
    {
        $produksi = Produksi::find($request->produksi_id);

        Qc::create([
            'produksi_id' => $produksi->id,
            'hasil' => $request->hasil,
            'keterangan' => $request->keterangan
        ]);


        // JIKA LOLOS QC
        if($request->hasil == 'Layak'){

            $produk = Produk::find($produksi->produk_id);

            // tambah stok produk

            $produk->stok += $produksi->jumlah_produksi;
            $produk->save();

            // simpan kartu stok

            Stok::create([
                'produk_id' => $produk->id,
                'jenis' => 'masuk',
                'jumlah' => $produksi->jumlah_produksi,
                'keterangan' => 'Hasil produksi'
            ]);
        }

        return redirect('/qc/pemeriksaan')
                ->with('success', 'QC berhasil diproses');
    }


    // EXPORT EXCEL
    public function exportExcel()
    {
        return Excel::download(

            new QcExport,

            'Laporan_QC.xlsx'

        );
    }

    // EXPORT PDF
    public function exportPdf()
    {
        $dataQc = Qc::with('produksi.produk')->latest()->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('qc.cetak', compact('dataQc'))->setPaper('a4', 'landscape');
        return $pdf->download('Laporan_QC.pdf');
    }

    // CETAK LAPORAN
    public function cetak()
    {
        $dataQc = Qc::with('produksi.produk')
                    ->latest()
                    ->get();

        return view('qc.cetak', compact('dataQc'));
    }

}