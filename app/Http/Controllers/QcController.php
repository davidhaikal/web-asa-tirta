<?php

namespace App\Http\Controllers;

use App\Exports\QcExport;
use App\Models\Produk;
use App\Models\Produksi;
use App\Models\Qc;
use App\Models\Stok;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class QcController extends Controller
{
    // DASHBOARD QC
    public function index(Request $request)
    {
        $query = Qc::with('produksi.produk');

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        if ($request->filled('bulan') && str_contains($request->bulan, '-')) {
            [$year, $month] = explode('-', $request->bulan);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        }

        $totalQc = (clone $query)->count();
        $totalLolos = (clone $query)->where('hasil', 'Layak')->count();
        $totalReject = (clone $query)->where('hasil', 'Tidak Layak')->count();
        $hariIni = Qc::whereDate('created_at', now()->toDateString())->count();
        $dataQc = (clone $query)->latest()->take(10)->get();

        return view('qc.dashboard', compact(
            'totalQc',
            'totalLolos',
            'totalReject',
            'hariIni',
            'dataQc'
        ));
    }

    // HALAMAN PEMERIKSAAN
    public function pemeriksaan(Request $request)
    {
        $query = Qc::with('produksi.produk');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('produksi.produk', function ($p) use ($search) {
                    $p->where('nama_produk', 'like', "%{$search}%");
                })->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('hasil')) {
            $query->where('hasil', $request->hasil);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $dataQc = $query->latest()->paginate(10)->withQueryString();
        $semuaProduk = Produk::orderBy('nama_produk')->get();

        return view('qc.pemeriksaan', compact('dataQc', 'semuaProduk'));
    }

    // PRODUK LOLOS
    public function lolos()
    {
        $totalQc = Qc::count();
        $totalLolos = Qc::where('hasil', 'Layak')->count();
        $totalReject = Qc::where('hasil', 'Tidak Layak')->count();
        $dataLolos = Qc::with('produksi.produk')->where('hasil', 'Layak')->latest()->get();

        return view('qc.lolos', compact('dataLolos', 'totalQc', 'totalLolos', 'totalReject'));
    }

    // PRODUK REJECT
    public function reject()
    {
        $totalQc = Qc::count();
        $totalLolos = Qc::where('hasil', 'Layak')->count();
        $totalReject = Qc::where('hasil', 'Tidak Layak')->count();
        $dataReject = Qc::with('produksi.produk')->where('hasil', 'Tidak Layak')->latest()->get();

        return view('qc.reject', compact('dataReject', 'totalQc', 'totalLolos', 'totalReject'));
    }

    // LAPORAN QC
    public function laporan(Request $request)
    {
        $query = Qc::with('produksi.produk');

        if ($request->filled('tanggal_awal')) {
            $query->whereDate('created_at', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('hasil')) {
            $query->where('hasil', $request->hasil);
        }

        $totalQc = (clone $query)->count();
        $totalLolos = (clone $query)->where('hasil', 'Layak')->count();
        $totalReject = (clone $query)->where('hasil', 'Tidak Layak')->count();
        $dataQc = $query->latest()->get();

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
                'status' => 'selesai',
            ]);
        } else {
            $produksi = Produksi::find($request->produksi_id);
        }

        Qc::create([
            'produksi_id' => $produksi->id,
            'hasil' => $request->hasil,
            'keterangan' => $request->keterangan,
        ]);

        // Siklus produksi selesai setelah diperiksa QC
        $produksi->status = 'selesai';
        $produksi->save();

        // JIKA LOLOS QC
        if ($request->hasil == 'Layak') {

            $produk = Produk::find($produksi->produk_id);

            // tambah stok produk

            $produk->stok += $produksi->jumlah_produksi;
            $produk->save();

            // simpan kartu stok

            Stok::create([
                'produk_id' => $produk->id,
                'jenis' => 'masuk',
                'jumlah' => $produksi->jumlah_produksi,
                'keterangan' => 'Hasil produksi',
            ]);
        }

        return redirect('/qc/pemeriksaan')
            ->with('success', 'QC berhasil diproses');
    }

    // UPDATE QC
    public function update(Request $request, $id)
    {
        $qc = Qc::with('produksi')->findOrFail($id);
        $produksi = $qc->produksi;
        $hasilLama = $qc->hasil;
        $produkIdLama = $produksi->produk_id;
        $jumlahLama = $produksi->jumlah_produksi;

        if ($request->filled('produk_id')) {
            $produksi->produk_id = $request->produk_id;
        }
        if ($request->filled('jumlah_produksi')) {
            $produksi->jumlah_produksi = $request->jumlah_produksi;
        }
        $produksi->save();

        $qc->hasil = $request->hasil;
        $qc->keterangan = $request->keterangan;
        $qc->save();

        $produkIdBaru = $produksi->produk_id;
        $jumlahBaru = $produksi->jumlah_produksi;

        if ($hasilLama == 'Layak' && ($request->hasil != 'Layak' || $produkIdLama != $produkIdBaru || $jumlahLama != $jumlahBaru)) {
            $produkLama = Produk::find($produkIdLama);
            if ($produkLama) {
                $produkLama->stok -= $jumlahLama;
                $produkLama->save();
                Stok::create([
                    'produk_id' => $produkLama->id,
                    'jenis' => 'keluar',
                    'jumlah' => $jumlahLama,
                    'keterangan' => 'Revisi Hasil QC (Tidak Layak)',
                ]);
            }
        }

        if ($request->hasil == 'Layak' && ($hasilLama != 'Layak' || $produkIdLama != $produkIdBaru || $jumlahLama != $jumlahBaru)) {
            $produkBaru = Produk::find($produkIdBaru);
            if ($produkBaru) {
                $produkBaru->stok += $jumlahBaru;
                $produkBaru->save();
                Stok::create([
                    'produk_id' => $produkBaru->id,
                    'jenis' => 'masuk',
                    'jumlah' => $jumlahBaru,
                    'keterangan' => 'Revisi Hasil QC (Layak)',
                ]);
            }
        }

        return redirect('/qc/pemeriksaan')->with('success', 'Data QC berhasil diperbarui');
    }

    // HAPUS QC
    public function destroy($id)
    {
        $qc = Qc::findOrFail($id);
        $produksi = $qc->produksi;
        
        if ($qc->hasil == 'Layak') {
            $produk = Produk::find($produksi->produk_id);
            $produk->stok -= $produksi->jumlah_produksi;
            $produk->save();
            Stok::create([
                'produk_id' => $produk->id,
                'jenis' => 'keluar',
                'jumlah' => $produksi->jumlah_produksi,
                'keterangan' => 'Pembatalan Data QC',
            ]);
        }

        $qc->delete();
        $produksi->delete();

        return redirect('/qc/pemeriksaan')->with('success', 'Data QC berhasil dihapus');
    }


    // Query builder bantuan untuk export & cetak
    private function buildExportQuery(Request $request)
    {
        $query = Qc::with('produksi.produk')->latest();

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }
        if ($request->filled('bulan') && str_contains($request->bulan, '-')) {
            [$year, $month] = explode('-', $request->bulan);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
        }
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('created_at', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('created_at', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('hasil')) {
            $query->where('hasil', $request->hasil);
        }

        return $query;
    }

    // EXPORT EXCEL
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new QcExport($request->all()),
            'Laporan_QC.xlsx'
        );
    }

    // EXPORT PDF
    public function exportPdf(Request $request)
    {
        $dataQc = $this->buildExportQuery($request)->get();
        $pdf = Pdf::loadView('qc.cetak', compact('dataQc'))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_QC.pdf');
    }

    // CETAK LAPORAN
    public function cetak(Request $request)
    {
        $dataQc = $this->buildExportQuery($request)->get();

        return view('qc.cetak', compact('dataQc'));
    }
}
