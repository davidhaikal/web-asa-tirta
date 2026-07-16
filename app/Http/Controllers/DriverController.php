<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengiriman;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    public const LABEL_STATUS = [
        'baru'      => 'Invoice Baru',
        'siap'      => 'Siap Dikirim',
        'berangkat' => 'Sedang Dikirim',
        'sampai'    => 'Sampai Tujuan',
        'selesai'   => 'Selesai',
    ];

    // ====================== DASHBOARD ======================

    public function dashboard()
    {
        $pengirimans = Pengiriman::with('penjualan.detailPenjualans.produk')->latest()->get();

        $pengirimanHariIni  = $pengirimans->filter(fn($p) => \Carbon\Carbon::parse($p->tanggal_kirim)->isToday())->count();
        $sedangDikirim      = $pengirimans->where('status', 'berangkat')->count();
        $selesaiHariIni     = $pengirimans->where('status', 'selesai')->filter(fn($p) => \Carbon\Carbon::parse($p->tanggal_kirim)->isToday())->count();
        $menungguKonfirmasi = $pengirimans->where('status', 'baru')->count();

        $pengirimanTerbaru = $pengirimans->take(5);

        return view('driver.dashboard', compact(
            'pengirimanHariIni',
            'sedangDikirim',
            'selesaiHariIni',
            'menungguKonfirmasi',
            'pengirimanTerbaru'
        ));
    }

    // ====================== PENGIRIMAN (LIST + DETAIL) ======================

    public function pengiriman(Request $request, $id = null)
    {
        // ====== MODE DETAIL ======
        if ($id !== null) {
            $pengiriman = Pengiriman::with('penjualan.detailPenjualans.produk')->findOrFail($id);

            return view('driver.pengiriman', [
                'mode'       => 'detail',
                'pengiriman' => $pengiriman,
            ]);
        }

        // ====== MODE LIST ======
        $query = Pengiriman::with('penjualan.detailPenjualans');

        $tab = $request->get('tab', 'semua');
        $search = $request->get('search');

        if ($tab === 'hari-ini') {
            $query->whereIn('status', ['baru', 'siap']);
        } elseif ($tab === 'dikirim') {
            $query->whereIn('status', ['berangkat', 'sampai']);
        } elseif ($tab === 'selesai') {
            $query->where('status', 'selesai');
        }

        if ($search) {
            $query->whereHas('penjualan', function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('pelanggan', 'like', "%{$search}%");
            });
        }

        $daftarPengiriman = $query->latest()->get();

        return view('driver.pengiriman', [
            'mode'             => 'list',
            'daftarPengiriman' => $daftarPengiriman,
            'tab'              => $tab,
            'search'           => $search,
        ]);
    }

    // ====================== AKSI STATUS ======================

    public function terima($id)
    {
        return $this->majukanStatus($id, 'baru', 'siap', 'Invoice diterima. Silakan siapkan barang.');
    }

    public function mulai($id)
    {
        return $this->majukanStatus($id, 'siap', 'berangkat', 'Pengiriman dimulai. Selamat jalan!');
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_foto' => 'required|image|max:5120',
        ]);

        $pengiriman = Pengiriman::findOrFail($id);

        if ($pengiriman->status !== 'berangkat') {
            return back()->with('error', 'Upload bukti hanya bisa dilakukan saat status "Sedang Dikirim".');
        }

        $path = $request->file('bukti_foto')->store('bukti-pengiriman', 'public');
        $pengiriman->bukti_foto = $path;
        $pengiriman->status = 'sampai';
        $pengiriman->save();

        return redirect()->route('driver.pengiriman', $id)->with('success', 'Bukti pengiriman berhasil diupload.');
    }

    public function selesai($id)
    {
        return $this->majukanStatus($id, 'sampai', 'selesai', 'Pengiriman selesai. Terima kasih!');
    }

    private function majukanStatus($id, string $statusSaatIni, string $statusBaru, string $pesan)
    {
        $pengiriman = Pengiriman::findOrFail($id);

        if ($pengiriman->status !== $statusSaatIni) {
            return back()->with('error', 'Status pengiriman tidak sesuai urutan.');
        }

        $pengiriman->status = $statusBaru;
        $pengiriman->save();

        return redirect()->route('driver.pengiriman', $id)->with('success', $pesan);
    }
}