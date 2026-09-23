<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\Stok;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        return redirect()->route('kasir.dashboard');
    }

    public function dashboard()
    {
        $today = now()->toDateString();

        $totalTransaksi = Penjualan::whereDate('tanggal', $today)->count();
        $pendapatanHariIni = Penjualan::whereDate('tanggal', $today)->sum('total');
        $produkTerjual = DetailPenjualan::whereHas('penjualan', function ($q) use ($today) {
            $q->whereDate('tanggal', $today);
        })->sum('jumlah');
        $notaDicetak = Penjualan::whereDate('tanggal', $today)->count();

        $transaksiTerbaru = Penjualan::with('detailPenjualans.produk')
            ->latest()
            ->take(5)
            ->get();

        $chartDays = collect(range(6, 0))->map(function ($offset) {
            return now()->subDays($offset)->format('d M');
        });
        $chartData = collect(range(6, 0))->map(function ($offset) {
            $date = now()->subDays($offset)->toDateString();
            return Penjualan::whereDate('tanggal', $date)->sum('total');
        });

        $produkStok = Produk::orderBy('nama_produk')->get();

        $kebutuhanProduksi = PurchaseOrder::where('bulan_produksi', now()->format('Y-m'))
            ->where('status', '!=', 'selesai')
            ->with('produk')
            ->get();

        $totalKebutuhanProduksi = $kebutuhanProduksi->sum('jumlah');

        return view('kasir.dashboard', compact(
            'totalTransaksi',
            'pendapatanHariIni',
            'produkTerjual',
            'notaDicetak',
            'transaksiTerbaru',
            'chartDays',
            'chartData',
            'produkStok',
            'kebutuhanProduksi',
            'totalKebutuhanProduksi'
        ));
    }

    public function transaksi()
    {
        $produk = Produk::orderBy('nama_produk')->get();
        $transaksiTerbaru = Penjualan::with('detailPenjualans.produk')
            ->latest()
            ->take(10)
            ->get();

        $poList = PurchaseOrder::where('status', 'menunggu')
            ->with('produk')
            ->latest()
            ->take(10)
            ->get();

        return view('kasir.transaksi', compact('produk', 'transaksiTerbaru', 'poList'));
    }

    public function storeTransaksi(Request $request)
    {
        $itemsRaw = $request->input('items');
        if (is_string($itemsRaw)) {
            $decoded = json_decode($itemsRaw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['items' => $decoded]);
            }
        }

        $request->validate([
            'pelanggan' => 'nullable|string|max:255',
            'metode' => 'required|in:tunai,transfer,qris',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $total = 0;
            foreach ($request->items as $item) {
                $produk = Produk::find($item['produk_id']);
                $total += $produk->harga * $item['jumlah'];
            }

            $penjualan = Penjualan::create([
                'kode' => 'TRX' . date('YmdHis'),
                'tanggal' => now()->toDateString(),
                'pelanggan' => $request->pelanggan ?: 'Walk-in Customer',
                'total' => $total,
                'metode' => $request->metode,
                'status' => 'pending',
                'user_id' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $produk = Produk::find($item['produk_id']);
                $subtotal = $produk->harga * $item['jumlah'];

                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $subtotal,
                ]);
            }

            DB::commit();

            $msg = "Transaksi {$penjualan->kode} dibuat! Status: BELUM LUNAS. Klik SUDAH BAYAR? untuk konfirmasi.";

            return redirect()->route('kasir.transaksi')
                ->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function bayarTransaksi(Request $request, $id)
    {
        $penjualan = Penjualan::with('detailPenjualans.produk')->findOrFail($id);

        if ($penjualan->status === 'lunas') {
            return back()->with('error', 'Transaksi sudah lunas!');
        }

        DB::beginTransaction();

        try {
            foreach ($penjualan->detailPenjualans as $detail) {
                if ($detail->produk->stok < $detail->jumlah) {
                    throw new \Exception("Stok {$detail->produk->nama_produk} tidak cukup. Sisa: {$detail->produk->stok}");
                }
            }

            foreach ($penjualan->detailPenjualans as $detail) {
                $produk = $detail->produk;
                $produk->stok -= $detail->jumlah;
                $produk->save();

                Stok::create([
                    'produk_id' => $produk->id,
                    'jenis' => 'keluar',
                    'jumlah' => $detail->jumlah,
                    'keterangan' => 'Pelunasan ' . $penjualan->kode,
                ]);
            }

            $penjualan->status = 'lunas';
            if ($request->has('metode')) {
                $penjualan->metode = $request->metode;
            }
            $penjualan->save();

            DB::commit();

            return redirect()->route('kasir.transaksi')
                ->with('success', "Transaksi {$penjualan->kode} DIBAYAR! Stok berkurang. Status: LUNAS.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function batalkanTransaksi($id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if ($penjualan->status === 'lunas') {
            return back()->with('error', 'Transaksi lunas tidak bisa dibatalkan!');
        }

        $penjualan->status = 'batal';
        $penjualan->save();

        return redirect()->route('kasir.transaksi')
            ->with('success', "Transaksi {$penjualan->kode} dibatalkan.");
    }

    public function bayarPO($id)
    {
        $po = PurchaseOrder::with('produk')->findOrFail($id);

        if ($po->status !== 'menunggu') {
            return back()->with('error', 'PO sudah diproses!');
        }

        DB::beginTransaction();

        try {
            $produk = $po->produk;
            $produk->stok += $po->jumlah;
            $produk->save();

            Stok::create([
                'produk_id' => $produk->id,
                'jenis' => 'masuk',
                'jumlah' => $po->jumlah,
                'keterangan' => 'PO dibayar ' . $po->kode_po,
            ]);

            $po->status = 'selesai';
            $po->save();

            DB::commit();

            return redirect()->route('kasir.transaksi')
                ->with('success', "PO {$po->kode_po} DIBAYAR! Stok {$produk->nama_produk} bertambah +{$po->jumlah}. Status: LUNAS.");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    public function storePO(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_butuh' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $bulanProduksi = date('Y-m', strtotime($request->tanggal_butuh));

        PurchaseOrder::create([
            'kode_po' => 'PO' . date('YmdHis'),
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal_butuh' => $request->tanggal_butuh,
            'bulan_produksi' => $bulanProduksi,
            'status' => 'menunggu',
            'catatan' => $request->catatan,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('kasir.dashboard')
            ->with('success', 'Purchase Order berhasil dibuat! Status: BELUM BAYAR. Bayar di menu Transaksi.');
    }

    public function nota(Request $request)
    {
        $query = Penjualan::with('detailPenjualans.produk')->where('status', '!=', 'batal');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('pelanggan', 'like', "%{$search}%");
            });
        }

        $nota = $query->latest()->paginate(10)->withQueryString();

        return view('kasir.nota', compact('nota'));
    }

    public function cetakNota($id)
    {
        $penjualan = Penjualan::with('detailPenjualans.produk', 'user')->findOrFail($id);

        return view('kasir.cetak-nota', compact('penjualan'));
    }

    public function laporanPenjualan(Request $request)
    {
        $tanggalAwal = $request->get('tanggal_awal', now()->startOfMonth()->toDateString());
        $tanggalAkhir = $request->get('tanggal_akhir', now()->endOfMonth()->toDateString());
        $jenisLaporan = $request->get('jenis_laporan', 'Semua');
        $kategoriFilter = $request->get('kategori', 'Semua');
        $search = $request->get('search', '');

        // 1. Load Penjualan
        $penjualanQuery = Penjualan::query();
        if ($tanggalAwal) {
            $penjualanQuery->whereDate('tanggal', '>=', $tanggalAwal);
        }
        if ($tanggalAkhir) {
            $penjualanQuery->whereDate('tanggal', '<=', $tanggalAkhir);
        }
        $penjualans = $penjualanQuery->latest()->get();

        // 2. Load Pembelian
        $pembelianQuery = \App\Models\Pembelian::query();
        if ($tanggalAwal) {
            $pembelianQuery->whereDate('tanggal_pembelian', '>=', $tanggalAwal);
        }
        if ($tanggalAkhir) {
            $pembelianQuery->whereDate('tanggal_pembelian', '<=', $tanggalAkhir);
        }
        $pembelians = $pembelianQuery->latest()->get();

        // 3. Map into combined collection
        $items = collect();

        // Add Penjualan mapped as Pendapatan, Piutang, or Penagihan
        foreach ($penjualans as $p) {
            if ($p->status == 'lunas') {
                $items->push([
                    'id' => $p->id,
                    'tanggal' => $p->tanggal,
                    'kategori' => 'Pendapatan',
                    'referensi' => $p->kode,
                    'customer_supplier' => $p->pelanggan ?? 'Walk-in Customer',
                    'keterangan' => 'Penjualan ke ' . ($p->pelanggan ?? 'Walk-in Customer'),
                    'nominal' => $p->total,
                    'status' => 'Masuk',
                    'raw_type' => 'penjualan',
                    'status_db' => $p->status
                ]);
            } elseif ($p->status == 'pending') {
                // Alternating classification
                if ($p->id % 2 != 0) {
                    $items->push([
                        'id' => $p->id,
                        'tanggal' => $p->tanggal,
                        'kategori' => 'Piutang',
                        'referensi' => $p->kode,
                        'customer_supplier' => $p->pelanggan ?? 'Walk-in Customer',
                        'keterangan' => 'Penjualan Kredit ke ' . ($p->pelanggan ?? 'Walk-in Customer'),
                        'nominal' => $p->total,
                        'status' => 'Piutang',
                        'raw_type' => 'penjualan',
                        'status_db' => $p->status
                    ]);
                } else {
                    $items->push([
                        'id' => $p->id,
                        'tanggal' => $p->tanggal,
                        'kategori' => 'Penagihan',
                        'referensi' => 'TAG/' . \Carbon\Carbon::parse($p->tanggal)->year . '/' . sprintf('%03d', $p->id),
                        'customer_supplier' => $p->pelanggan ?? 'Walk-in Customer',
                        'keterangan' => 'Tagihan Invoice ' . $p->kode,
                        'nominal' => $p->total,
                        'status' => 'Pending',
                        'raw_type' => 'penjualan',
                        'status_db' => $p->status
                    ]);
                }
            }
        }

        // Add Pembelian
        foreach ($pembelians as $pem) {
            $items->push([
                'id' => $pem->id,
                'tanggal' => $pem->tanggal_pembelian,
                'kategori' => 'Pembelian',
                'referensi' => $pem->no_transaksi,
                'customer_supplier' => $pem->supplier ?? 'Supplier Umum',
                'keterangan' => 'Pembelian dari ' . ($pem->supplier ?? 'Supplier Umum'),
                'nominal' => $pem->total_harga,
                'status' => 'Keluar',
                'raw_type' => 'pembelian',
                'status_db' => $pem->status
            ]);
        }

        // 4. Apply filters on the combined collection
        if ($jenisLaporan !== 'Semua') {
            $items = $items->where('kategori', $jenisLaporan);
        }
        if ($kategoriFilter !== 'Semua') {
            $items = $items->where('status', $kategoriFilter);
        }
        if ($search) {
            $items = $items->filter(function($item) use ($search) {
                return stripos($item['referensi'], $search) !== false ||
                       stripos($item['customer_supplier'], $search) !== false ||
                       stripos($item['keterangan'], $search) !== false;
            });
        }

        // Sort by date descending
        $items = $items->sortByDesc('tanggal')->values();

        // Calculate statistics for the selected criteria
        $totalPendapatan = $items->where('kategori', 'Pendapatan')->sum('nominal');
        $totalPembelian = $items->where('kategori', 'Pembelian')->sum('nominal');
        $totalPiutang = $items->where('kategori', 'Piutang')->sum('nominal');
        $totalPenagihan = $items->where('kategori', 'Penagihan')->sum('nominal');

        // 6. Paginate manually
        $page = $request->get('page', 1);
        $perPage = 10;
        $sliced = $items->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        // Check if export parameter is requested
        $labelPeriodeText = \Carbon\Carbon::parse($tanggalAwal)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y');
        if ($request->get('export') == 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kasir.laporan-penjualan-pdf', [
                'transactions' => $items,
                'totalPendapatan' => $totalPendapatan,
                'totalPembelian' => $totalPembelian,
                'totalPiutang' => $totalPiutang,
                'totalPenagihan' => $totalPenagihan,
                'labelPeriode' => $labelPeriodeText,
            ]);
            return $pdf->download('Laporan_Keuangan_' . $tanggalAwal . '_to_' . $tanggalAkhir . '.pdf');
        }

        if ($request->get('export') == 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LaporanKeuanganExport(
                    $items,
                    $totalPendapatan,
                    $totalPembelian,
                    $totalPiutang,
                    $totalPenagihan,
                    $labelPeriodeText
                ),
                'Laporan_Keuangan_' . $tanggalAwal . '_to_' . $tanggalAkhir . '.xlsx'
            );
        }

        return view('kasir.laporan-penjualan', [
            'transactions' => $paginated,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'jenisLaporan' => $jenisLaporan,
            'kategoriFilter' => $kategoriFilter,
            'search' => $search,
            'totalPendapatan' => $totalPendapatan,
            'totalPembelian' => $totalPembelian,
            'totalPiutang' => $totalPiutang,
            'totalPenagihan' => $totalPenagihan,
            'totalTransaksi' => $items->count(),
            'labelPeriode' => \Carbon\Carbon::parse($tanggalAwal)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y'),
        ]);
    }

    public function laporanStok(Request $request)
    {
        $totalProduk = Produk::count();
        $totalStokRendah = Produk::where('stok', '<', 10)->count();
        $totalNilaiStok = Produk::all()->sum(function ($p) {
            return $p->stok * $p->harga;
        });

        $produk = Produk::orderBy('nama_produk')->paginate(10)->withQueryString();

        $kartuStok = collect();
        $produkTerpilih = null;
        if ($request->filled('produk_id')) {
            $produkTerpilih = Produk::find($request->produk_id);
            $kartuStok = Stok::where('produk_id', $request->produk_id)
                ->with('produk')
                ->latest()
                ->take(50)
                ->get();
        }

        return view('kasir.laporan-stok', compact(
            'produk',
            'totalProduk',
            'totalStokRendah',
            'totalNilaiStok',
            'kartuStok',
            'produkTerpilih'
        ));
    }
}