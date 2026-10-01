<?php

namespace App\Http\Controllers;

use App\Exports\KeuanganExport;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KeuanganController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $totalPendapatan = Penjualan::whereDate('tanggal', $today)->where('status', '!=', 'batal')->sum('total');
        $totalLunas = Penjualan::whereDate('tanggal', $today)->where('status', 'lunas')->count();
        $totalPiutang = Penjualan::where('status', 'pending')->sum('total');
        $tagihanPending = Pembelian::where('status', '!=', 'Lunas')->count();

        $pembelianJatuhTempo = Pembelian::where('status', '!=', 'Lunas')
            ->orderBy('tanggal_pembelian', 'asc')
            ->take(5)
            ->get();

        $chartDays = collect(range(6, 0))->map(function ($offset) {
            return now()->subDays($offset)->format('d M');
        });

        $pendapatanData = collect(range(6, 0))->map(function ($offset) {
            $date = now()->subDays($offset)->toDateString();

            return Penjualan::whereDate('tanggal', $date)->where('status', '!=', 'batal')->sum('total');
        });

        $pengeluaranData = collect(range(6, 0))->map(function ($offset) {
            $date = now()->subDays($offset)->toDateString();

            return Pembelian::whereDate('tanggal_pembelian', $date)->sum('total_harga');
        });

        return view('keuangan.dashboard', compact(
            'totalPendapatan', 'totalLunas', 'totalPiutang', 'tagihanPending', 'pembelianJatuhTempo',
            'chartDays', 'pendapatanData', 'pengeluaranData'
        ));
    }

    public function pelanggan()
    {
        $totalPiutang = Penjualan::where('status', 'pending')->sum('total');
        $belumDibayar = Penjualan::where('status', 'pending')->distinct('pelanggan')->count('pelanggan');
        $sudahLunas = Penjualan::where('status', 'lunas')->distinct('pelanggan')->count('pelanggan');

        $pelanggans = Pelanggan::latest()->get();

        return view('keuangan.pelanggan', compact('pelanggans', 'totalPiutang', 'belumDibayar', 'sudahLunas'));
    }

    public function storePelanggan(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        Pelanggan::create($request->all());

        return redirect()->back()->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function updatePelanggan(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->all());

        return redirect()->back()->with('success', 'Data Pelanggan berhasil diupdate');
    }

    public function destroyPelanggan($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return redirect()->back()->with('success', 'Pelanggan berhasil dihapus');
    }

    public function laporan(Request $request)
    {
        [$scope, $labelPeriode, $periode, $tanggal] = $this->laporanFilter($request);

        // Builder berulang dengan scope periode yang sama (non-batal)
        $make = function () use ($scope) {
            $query = Penjualan::where('status', '!=', 'batal');
            $scope($query);

            return $query;
        };

        $totalPendapatan = $make()->where('status', 'lunas')->sum('total');
        $totalTransaksi = $make()->count();
        $totalNilaiPeriode = $make()->sum('total');
        $lunasCount = $make()->where('status', 'lunas')->count();
        $pendingCount = $make()->where('status', 'pending')->count();
        $rataRata = $totalTransaksi > 0 ? $totalNilaiPeriode / $totalTransaksi : 0;

        // Piutang berjalan = kondisi terkini (tidak dibatasi periode)
        $totalPiutang = Penjualan::where('status', 'pending')->sum('total');
        $piutangCount = Penjualan::where('status', 'pending')->count();

        // Rekap per metode pembayaran pada periode
        $metodeRekap = [
            'tunai' => ['total' => 0, 'jumlah' => 0],
            'transfer' => ['total' => 0, 'jumlah' => 0],
            'qris' => ['total' => 0, 'jumlah' => 0],
        ];
        foreach ($make()->get(['metode', 'total']) as $trx) {
            if (isset($metodeRekap[$trx->metode])) {
                $metodeRekap[$trx->metode]['total'] += (float) $trx->total;
                $metodeRekap[$trx->metode]['jumlah']++;
            }
        }

        $penjualans = $make()->latest('tanggal')->paginate(10);

        // Grafik 6 bulan terakhir (tren keseluruhan, tidak mengikuti filter periode)
        $chartLabels = collect(range(5, 0))->map(function ($offset) {
            return now()->subMonths($offset)->format('M Y');
        });
        $chartData = collect(range(5, 0))->map(function ($offset) {
            $bulan = now()->subMonths($offset);

            return Penjualan::where('status', '!=', 'batal')
                ->whereMonth('tanggal', $bulan->month)
                ->whereYear('tanggal', $bulan->year)
                ->sum('total');
        });

        return view('keuangan.laporan', compact(
            'periode', 'tanggal', 'labelPeriode',
            'totalPendapatan', 'totalNilaiPeriode', 'totalTransaksi', 'lunasCount', 'pendingCount', 'rataRata',
            'totalPiutang', 'piutangCount', 'metodeRekap',
            'penjualans', 'chartLabels', 'chartData'
        ));
    }

    public function piutang()
    {
        $totalPiutang = Penjualan::where('status', 'pending')->sum('total');
        $belumDibayar = Penjualan::where('status', 'pending')->distinct('pelanggan')->count('pelanggan');
        $sudahLunas = Penjualan::where('status', 'lunas')->distinct('pelanggan')->count('pelanggan');

        $piutangList = Penjualan::where('status', 'pending')->latest()->get();

        $chartData = collect(range(5, 0))->map(function ($offset) {
            $month = now()->subMonths($offset)->month;
            $year = now()->subMonths($offset)->year;

            return Penjualan::where('status', 'pending')
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
        $penjualan = Penjualan::findOrFail($id);
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
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();

        return redirect()->back()->with('success', 'Data Piutang berhasil dihapus.');
    }

    public function penagihan()
    {
        $tagihans = Penjualan::where('status', '!=', 'lunas')->latest()->get();

        return view('keuangan.penagihan', compact('tagihans'));
    }

    public function updatePenagihan(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
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
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();

        return redirect()->back()->with('success', 'Data Penagihan berhasil dihapus.');
    }

    /**
     * Filter periode laporan keuangan: [scope closure, label periode, periode, tanggal].
     * Dipakai bersama oleh halaman laporan & export agar datanya konsisten.
     */
    private function laporanFilter(Request $request)
    {
        $periode = $request->input('periode', 'bulan');
        if (! in_array($periode, ['hari', 'bulan', 'semua'], true)) {
            $periode = 'bulan';
        }

        $tanggal = $request->input('tanggal');
        try {
            $ref = $tanggal ? Carbon::parse($tanggal) : now();
        } catch (\Throwable) {
            $ref = now();
        }

        $scope = function ($query) use ($periode, $ref) {
            if ($periode === 'hari') {
                return $query->whereDate('tanggal', $ref->toDateString());
            }

            if ($periode === 'bulan') {
                return $query->whereMonth('tanggal', $ref->month)
                    ->whereYear('tanggal', $ref->year);
            }

            return $query;
        };

        $labelPeriode = match ($periode) {
            'hari' => 'Harian — '.$ref->translatedFormat('d M Y'),
            'bulan' => 'Bulanan — '.$ref->translatedFormat('F Y'),
            default => 'Semua Periode',
        };

        return [$scope, $labelPeriode, $periode, $ref->toDateString()];
    }

    /**
     * Data riil untuk export laporan keuangan
     * (bentuk baris sama dengan yang dipakai view keuangan.laporan_pdf)
     */
    private function getLaporanData(Request $request)
    {
        [$scope] = $this->laporanFilter($request);

        $query = Penjualan::where('status', '!=', 'batal');
        $scope($query);

        return $query
            ->latest('tanggal')
            ->limit(100)
            ->get()
            ->map(function ($penjualan) {
                return [
                    'tanggal' => Carbon::parse($penjualan->tanggal)->format('d M Y'),
                    'customer' => $penjualan->pelanggan ?? 'Umum',
                    'total' => 'Rp '.number_format((float) $penjualan->total, 0, ',', '.'),
                    'status' => ucfirst($penjualan->status),
                ];
            })
            ->values();
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getLaporanData($request);
        [, $labelPeriode] = $this->laporanFilter($request);
        $pdf = Pdf::loadView('keuangan.laporan_pdf', compact('data', 'labelPeriode'));

        return $pdf->download('Laporan_Keuangan.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getLaporanData($request);
        [, $labelPeriode] = $this->laporanFilter($request);

        return Excel::download(new KeuanganExport($data, $labelPeriode), 'Laporan_Keuangan.xlsx');
    }
}
