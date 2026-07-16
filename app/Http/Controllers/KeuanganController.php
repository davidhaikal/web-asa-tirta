<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $totalPendapatan = \App\Models\Penjualan::whereDate('tanggal', $today)->where('status', '!=', 'batal')->sum('total');
        $totalLunas = \App\Models\Penjualan::whereDate('tanggal', $today)->where('status', 'lunas')->count();
        $totalPiutang = \App\Models\Penjualan::where('status', 'pending')->sum('total');
        $tagihanPending = \App\Models\Pembelian::where('status', '!=', 'Lunas')->count();

        $pembelianJatuhTempo = \App\Models\Pembelian::where('status', '!=', 'Lunas')
            ->orderBy('tanggal_pembelian', 'asc')
            ->take(5)
            ->get();

        $chartDays = collect(range(6, 0))->map(function ($offset) {
            return now()->subDays($offset)->format('d M');
        });
        
        $pendapatanData = collect(range(6, 0))->map(function ($offset) {
            $date = now()->subDays($offset)->toDateString();
            return \App\Models\Penjualan::whereDate('tanggal', $date)->where('status', '!=', 'batal')->sum('total');
        });

        $pengeluaranData = collect(range(6, 0))->map(function ($offset) {
            $date = now()->subDays($offset)->toDateString();
            return \App\Models\Pembelian::whereDate('tanggal_pembelian', $date)->sum('total_harga');
        });

        return view('keuangan.dashboard', compact(
            'totalPendapatan', 'totalLunas', 'totalPiutang', 'tagihanPending', 'pembelianJatuhTempo',
            'chartDays', 'pendapatanData', 'pengeluaranData'
        ));
    }

    public function pelanggan()
    {
        $totalPiutang = \App\Models\Penjualan::where('status', 'pending')->sum('total');
        $belumDibayar = \App\Models\Penjualan::where('status', 'pending')->distinct('pelanggan')->count('pelanggan');
        $sudahLunas = \App\Models\Penjualan::where('status', 'lunas')->distinct('pelanggan')->count('pelanggan');

        $pelanggans = \App\Models\Pelanggan::latest()->get();
        return view('keuangan.pelanggan', compact('pelanggans', 'totalPiutang', 'belumDibayar', 'sudahLunas'));
    }

    public function storePelanggan(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        \App\Models\Pelanggan::create($request->all());

        return redirect()->back()->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function updatePelanggan(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $pelanggan = \App\Models\Pelanggan::findOrFail($id);
        $pelanggan->update($request->all());

        return redirect()->back()->with('success', 'Data Pelanggan berhasil diupdate');
    }

    public function destroyPelanggan($id)
    {
        $pelanggan = \App\Models\Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return redirect()->back()->with('success', 'Pelanggan berhasil dihapus');
    }

    public function laporan()
    {
        return view('keuangan.laporan');
    }

    public function piutang()
    {
        $totalPiutang = \App\Models\Penjualan::where('status', 'pending')->sum('total');
        $belumDibayar = \App\Models\Penjualan::where('status', 'pending')->distinct('pelanggan')->count('pelanggan');
        $sudahLunas = \App\Models\Penjualan::where('status', 'lunas')->distinct('pelanggan')->count('pelanggan');

        $piutangList = \App\Models\Penjualan::where('status', 'pending')->latest()->get();

        $chartData = collect(range(5, 0))->map(function ($offset) {
            $month = now()->subMonths($offset)->month;
            $year = now()->subMonths($offset)->year;
            return \App\Models\Penjualan::where('status', 'pending')
                ->whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->sum('total');
        });
        $chartLabels = collect(range(5, 0))->map(function ($offset) {
            return now()->subMonths($offset)->format('M');
        });

        return view('keuangan.piutang', compact(
            'totalPiutang', 'belumDibayar', 'sudahLunas', 'piutangList', 'chartData', 'chartLabels'
        ));
    }

    public function updatePiutang(Request $request, $id)
    {
        $penjualan = \App\Models\Penjualan::findOrFail($id);
        $penjualan->update([
            'pelanggan' => $request->pelanggan,
            'total' => $request->total,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
        ]);
        return redirect()->back()->with('success', 'Data Piutang berhasil diupdate.');
    }

    public function destroyPiutang($id)
    {
        $penjualan = \App\Models\Penjualan::findOrFail($id);
        $penjualan->delete();
        return redirect()->back()->with('success', 'Data Piutang berhasil dihapus.');
    }

    public function penagihan()
    {
        $tagihans = \App\Models\Penjualan::where('status', '!=', 'lunas')->latest()->get();
        return view('keuangan.penagihan', compact('tagihans'));
    }

    public function updatePenagihan(Request $request, $id)
    {
        $penjualan = \App\Models\Penjualan::findOrFail($id);
        $penjualan->update([
            'pelanggan' => $request->pelanggan,
            'total' => $request->total,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
        ]);
        return redirect()->back()->with('success', 'Data Penagihan berhasil diupdate.');
    }

    public function destroyPenagihan($id)
    {
        $penjualan = \App\Models\Penjualan::findOrFail($id);
        $penjualan->delete();
        return redirect()->back()->with('success', 'Data Penagihan berhasil dihapus.');
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