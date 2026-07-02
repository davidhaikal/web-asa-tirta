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

        $nota = $query->latest()->paginate(15);

        return view('kasir.nota', compact('nota'));
    }

    public function cetakNota($id)
    {
        $penjualan = Penjualan::with('detailPenjualans.produk', 'user')->findOrFail($id);

        return view('kasir.cetak-nota', compact('penjualan'));
    }

    public function laporanPenjualan(Request $request)
    {
        $periode = $request->get('periode', 'hari');
        $tanggal = $request->get('tanggal', now()->toDateString());

        $query = Penjualan::with('detailPenjualans.produk')->where('status', '!=', 'batal');

        switch ($periode) {
            case 'hari':
                $query->whereDate('tanggal', $tanggal);
                $labelPeriode = 'Harian - ' . \Carbon\Carbon::parse($tanggal)->format('d M Y');
                break;

            case 'minggu':
                $startOfWeek = \Carbon\Carbon::parse($tanggal)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::parse($tanggal)->endOfWeek();
                $query->whereBetween('tanggal', [$startOfWeek, $endOfWeek]);
                $labelPeriode = 'Mingguan - ' . $startOfWeek->format('d M Y') . ' s/d ' . $endOfWeek->format('d M Y');
                break;

            case 'bulan':
                $month = \Carbon\Carbon::parse($tanggal)->month;
                $year = \Carbon\Carbon::parse($tanggal)->year;
                $query->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
                $labelPeriode = 'Bulanan - ' . \Carbon\Carbon::parse($tanggal)->format('F Y');
                break;

            default:
                $query->whereDate('tanggal', $tanggal);
                $labelPeriode = 'Harian - ' . \Carbon\Carbon::parse($tanggal)->format('d M Y');
        }

        $penjualan = $query->latest()->paginate(20);

        $totalTransaksi = (clone $query)->count();
        $totalPendapatan = (clone $query)->sum('total');
        $totalProdukTerjual = DetailPenjualan::whereHas('penjualan', function ($q) use ($periode, $tanggal) {
            $q->where('status', '!=', 'batal');
            switch ($periode) {
                case 'hari':
                    $q->whereDate('tanggal', $tanggal);
                    break;
                case 'minggu':
                    $start = \Carbon\Carbon::parse($tanggal)->startOfWeek();
                    $end = \Carbon\Carbon::parse($tanggal)->endOfWeek();
                    $q->whereBetween('tanggal', [$start, $end]);
                    break;
                case 'bulan':
                    $q->whereMonth('tanggal', \Carbon\Carbon::parse($tanggal)->month)
                      ->whereYear('tanggal', \Carbon\Carbon::parse($tanggal)->year);
                    break;
            }
        })->sum('jumlah');

        $lunasCount = (clone $query)->where('status', 'lunas')->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();

        return view('kasir.laporan-penjualan', compact(
            'penjualan',
            'periode',
            'tanggal',
            'labelPeriode',
            'totalTransaksi',
            'totalPendapatan',
            'totalProdukTerjual',
            'lunasCount',
            'pendingCount'
        ));
    }

    public function laporanStok(Request $request)
    {
        $produk = Produk::orderBy('nama_produk')->get();

        $totalProduk = $produk->count();
        $totalStokRendah = $produk->where('stok', '<', 10)->count();
        $totalNilaiStok = $produk->sum(function ($p) {
            return $p->stok * $p->harga;
        });

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