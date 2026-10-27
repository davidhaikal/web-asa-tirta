<?php

namespace App\Http\Controllers;

use App\Exports\KeuanganExport;
use App\Models\Invoice;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Pengiriman;
use App\Models\Penjualan;
use App\Models\Stok;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function pelanggan(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Pelanggan::latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('kota', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $status);
        }

        $pelanggans = $query->paginate(15)->withQueryString();

        $totalPelanggan = Pelanggan::count();
        $pelangganAktif = Pelanggan::where('status', 'Aktif')->count();
        $pelangganNonaktif = Pelanggan::where('status', 'Nonaktif')->count();
        $totalPiutang = Penjualan::where('status', 'pending')->sum('total');
        $belumDibayar = Penjualan::where('status', 'pending')->distinct('pelanggan')->count('pelanggan');
        $sudahLunas = Penjualan::where('status', 'lunas')->distinct('pelanggan')->count('pelanggan');

        return view('keuangan.pelanggan', compact(
            'pelanggans',
            'totalPelanggan',
            'pelangganAktif',
            'pelangganNonaktif',
            'totalPiutang',
            'belumDibayar',
            'sudahLunas',
            'search',
            'status'
        ));
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

    public function piutang(Request $request)
    {
        $activeTab = $request->get('tab', 'piutang');
        $search = $request->get('search');
        $periode = $request->get('periode');
        $tanggal = $request->get('tanggal');

        $today = now()->toDateString();
        $thisMonth = now()->month;
        $thisYear = now()->year;

        // Metrik Ringkasan
        $totalPiutang = Penjualan::where('status', 'pending')->sum('total');
        $belumDibayar = Penjualan::where('status', 'pending')->distinct('pelanggan')->count('pelanggan');
        $sudahLunas = Penjualan::where('status', 'lunas')->distinct('pelanggan')->count('pelanggan');

        $pendapatanHariIni = Penjualan::where('status', 'lunas')->whereDate('tanggal', $today)->sum('total');
        $pendapatanBulanIni = Penjualan::where('status', 'lunas')->whereMonth('tanggal', $thisMonth)->whereYear('tanggal', $thisYear)->sum('total');
        $totalPendapatanSemua = Penjualan::where('status', 'lunas')->sum('total');
        $transaksiLunasCount = Penjualan::where('status', 'lunas')->count();
        $transaksiPendingCount = Penjualan::where('status', 'pending')->count();

        // Query Piutang (Status: Pending)
        $piutangQuery = Penjualan::with('detailPenjualans.produk')->where('status', 'pending');
        if ($search) {
            $piutangQuery->where(function ($q) use ($search) {
                $q->where('pelanggan', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }
        $piutangList = $piutangQuery->latest('tanggal')->get();

        // Query Pendapatan (Status: Lunas)
        $pendapatanQuery = Penjualan::with('detailPenjualans.produk')->where('status', 'lunas');
        if ($search) {
            $pendapatanQuery->where(function ($q) use ($search) {
                $q->where('pelanggan', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }
        if ($periode === 'hari') {
            $refDate = $tanggal ? Carbon::parse($tanggal)->toDateString() : $today;
            $pendapatanQuery->whereDate('tanggal', $refDate);
        } elseif ($periode === 'bulan') {
            $refDate = $tanggal ? Carbon::parse($tanggal) : now();
            $pendapatanQuery->whereMonth('tanggal', $refDate->month)->whereYear('tanggal', $refDate->year);
        }
        $pendapatanList = $pendapatanQuery->latest('tanggal')->paginate(15, ['*'], 'pendapatan_page')->withQueryString();

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
            'activeTab',
            'search',
            'periode',
            'tanggal',
            'totalPiutang',
            'belumDibayar',
            'sudahLunas',
            'pendapatanHariIni',
            'pendapatanBulanIni',
            'totalPendapatanSemua',
            'transaksiLunasCount',
            'transaksiPendingCount',
            'piutangList',
            'pendapatanList',
            'chartData',
            'chartLabels'
        ));
    }

    public function bayarPiutang(Request $request, $id)
    {
        $penjualan = Penjualan::with('detailPenjualans.produk')->findOrFail($id);

        if ($penjualan->status === 'lunas') {
            return back()->with('error', 'Transaksi ini sudah berstatus lunas.');
        }

        $request->validate([
            'metode' => 'required|string|in:tunai,transfer,qris',
        ]);

        DB::beginTransaction();

        try {
            foreach ($penjualan->detailPenjualans as $detail) {
                if ($detail->produk && $detail->produk->stok < $detail->jumlah) {
                    throw new \Exception("Stok {$detail->produk->nama_produk} tidak mencukupi untuk pelunasan. Sisa stok: {$detail->produk->stok}");
                }
            }

            foreach ($penjualan->detailPenjualans as $detail) {
                if ($detail->produk) {
                    $produk = $detail->produk;
                    $produk->stok -= $detail->jumlah;
                    $produk->save();

                    Stok::create([
                        'produk_id' => $produk->id,
                        'jenis' => 'keluar',
                        'jumlah' => $detail->jumlah,
                        'keterangan' => 'Pelunasan piutang '.$penjualan->kode,
                    ]);
                }
            }

            $penjualan->status = 'lunas';
            $penjualan->metode = $request->metode;
            $penjualan->save();

            // Terbitkan invoice otomatis
            Invoice::firstOrCreate(
                ['penjualan_id' => $penjualan->id],
                [
                    'invoice_no' => 'INV'.date('YmdHis'),
                    'pelanggan' => $penjualan->pelanggan,
                    'total' => $penjualan->total,
                    'tanggal' => now()->toDateString(),
                    'status' => 'terbit',
                ]
            );

            // Tugas pengiriman
            Pengiriman::firstOrCreate(
                ['penjualan_id' => $penjualan->id],
                [
                    'tujuan' => $penjualan->pelanggan,
                    'status' => 'menunggu_driver',
                ]
            );

            DB::commit();

            return redirect()->route('keuangan.piutang', ['tab' => 'pendapatan'])
                ->with('success', "Piutang {$penjualan->kode} berhasil dilunasi dan kini tercatat sebagai Pendapatan.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memproses pelunasan: '.$e->getMessage());
        }
    }

    public function updatePiutang(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $request->validate([
            'pelanggan' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'status' => 'required|string|in:pending,lunas,batal',
            'metode' => 'nullable|string',
        ]);

        $data = [
            'pelanggan' => $request->pelanggan,
            'total' => $request->total,
            'tanggal' => $request->tanggal,
            'status' => $request->status,
        ];
        if ($request->filled('metode')) {
            $data['metode'] = $request->metode;
        }

        $penjualan->update($data);

        return redirect()->back()->with('success', 'Data Piutang & Pendapatan berhasil diupdate.');
    }

    public function destroyPiutang($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    public function penagihan(Request $request)
    {
        $query = Penjualan::with('detailPenjualans.produk')->where('status', '!=', 'lunas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pelanggan', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $tagihans = $query->latest('tanggal')->get();

        $totalTagihan = Penjualan::where('status', '!=', 'lunas')->sum('total');
        $jumlahTagihan = Penjualan::where('status', '!=', 'lunas')->count();
        $sudahDikirim = Penjualan::where('status', '!=', 'lunas')->whereNotNull('tagihan_dikirim_at')->count();
        $belumDikirim = Penjualan::where('status', '!=', 'lunas')->whereNull('tagihan_dikirim_at')->count();

        return view('keuangan.penagihan', compact(
            'tagihans', 'totalTagihan', 'jumlahTagihan', 'sudahDikirim', 'belumDikirim'
        ));
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
