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
        $produksi = Produksi::doesntHave('qc')->latest()->get();
        $semuaProduk = Produk::all();

        return view('qc.pemeriksaan', compact('produksi', 'semuaProduk'));
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
    public function laporan(Request $request)
    {
        $query = Qc::query();

        if ($request->has('tanggal_awal') && $request->tanggal_awal) {
            $query->whereDate('created_at', '>=', $request->tanggal_awal);
        }
        if ($request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }

        $dataQc = $query->latest()->get();
        $totalQc = $query->count();
        $totalLolos = (clone $query)->where('hasil', 'Layak')->count();
        $totalReject = (clone $query)->where('hasil', 'Tidak Layak')->count();

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
        if ($request->has('produk_id') && $request->produk_id != '') {
            $produksi = Produksi::create([
                'produk_id' => $request->produk_id,
                'jumlah_produksi' => $request->jumlah_produksi,
                'tanggal_produksi' => now()->toDateString(),
                'status' => 'selesai'
            ]);
        } else {
            $produksi = Produksi::find($request->produksi_id);
        }

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