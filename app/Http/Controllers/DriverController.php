<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DriverController extends Controller
{
    /**
     * Alur status:
     * baru -> siap -> berangkat -> sampai -> selesai
     */
    public const LABEL_STATUS = [
        'baru'      => 'Invoice Baru',
        'siap'      => 'Siap Dikirim',
        'berangkat' => 'Sedang Dikirim',
        'sampai'    => 'Sampai Tujuan',
        'selesai'   => 'Selesai',
    ];

    /**
     * Data dummy pengiriman.
     * NOTE: nanti ganti dengan query asli:
     *   \App\Models\Pengiriman::where('driver_id', auth()->id())->get();
     */
    private function getDataDummy(): array
    {
        return [
            1 => [
                'id' => 1, 'no_invoice' => 'INV-2026001', 'customer' => 'PT Tirta Malang',
                'no_telp' => '0812-3456-7890',
                'alamat' => 'Jl. Soekarno Hatta No.45, Malang',
                'items' => [
                    ['produk' => 'Aqua Botol 600ml', 'qty' => 25],
                    ['produk' => 'Aqua Botol 1500ml', 'qty' => 10],
                ],
                'tanggal_kirim' => '18 Juli 2026', 'jam' => '09:30 WIB', 'status' => 'baru',
                'bukti_foto' => null,
            ],
            2 => [
                'id' => 2, 'no_invoice' => 'INV-2026002', 'customer' => 'CV Maju Jaya',
                'no_telp' => '0813-1234-5678',
                'alamat' => 'Jl. Ahmad Yani No.12, Surabaya',
                'items' => [
                    ['produk' => 'Aqua Gelas 240ml', 'qty' => 20],
                ],
                'tanggal_kirim' => '18 Juli 2026', 'jam' => '10:15 WIB', 'status' => 'berangkat',
                'bukti_foto' => null,
            ],
            3 => [
                'id' => 3, 'no_invoice' => 'INV-2026003', 'customer' => 'PT Sumber Air',
                'no_telp' => '0822-9876-5432',
                'alamat' => 'Jl. Raya Kediri No.88, Kediri',
                'items' => [
                    ['produk' => 'Aqua Gelas 240ml', 'qty' => 50],
                ],
                'tanggal_kirim' => '17 Juli 2026', 'jam' => '15:20 WIB', 'status' => 'selesai',
                'bukti_foto' => null,
            ],
        ];
    }

    /** Ambil data + perubahan status yang tersimpan di session */
    private function getData(): array
    {
        $data = $this->getDataDummy();
        $sessionStatus = Session::get('pengiriman_status', []);
        $sessionBukti = Session::get('pengiriman_bukti', []);

        foreach ($data as $id => &$item) {
            if (isset($sessionStatus[$id])) {
                $item['status'] = $sessionStatus[$id];
            }
            if (isset($sessionBukti[$id])) {
                $item['bukti_foto'] = $sessionBukti[$id];
            }
        }

        return $data;
    }

    // ====================== DASHBOARD ======================

    public function dashboard()
    {
        $data = collect($this->getData());

        $pengirimanHariIni  = $data->count();
        $sedangDikirim      = $data->where('status', 'berangkat')->count();
        $selesaiHariIni     = $data->where('status', 'selesai')->count();
        $menungguKonfirmasi = $data->where('status', 'baru')->count();

        $pengirimanTerbaru = $data->sortByDesc('id')->take(5)->map(fn ($item) => (object) $item);

        return view('driver.dashboard', compact(
            'pengirimanHariIni',
            'sedangDikirim',
            'selesaiHariIni',
            'menungguKonfirmasi',
            'pengirimanTerbaru'
        ));
    }

    // ====================== PENGIRIMAN (LIST + DETAIL) ======================

    /**
     * Kalau $id kosong -> tampilkan list.
     * Kalau $id diisi   -> tampilkan detail.
     */
    public function pengiriman(Request $request, $id = null)
    {
        // ====== MODE DETAIL ======
        if ($id !== null) {
            $data = $this->getData();
            abort_unless(isset($data[$id]), 404);

            $item = $data[$id];
            $item['items'] = collect($item['items'])->map(fn ($i) => (object) $i);
            $pengiriman = (object) $item;

            return view('driver.pengiriman', [
                'mode'       => 'detail',
                'pengiriman' => $pengiriman,
            ]);
        }

        // ====== MODE LIST ======
        $data = collect($this->getData());

        $tab = $request->get('tab', 'semua');
        $search = $request->get('search');

        if ($tab === 'hari-ini') {
            $data = $data->whereIn('status', ['baru', 'siap']);
        } elseif ($tab === 'dikirim') {
            $data = $data->whereIn('status', ['berangkat', 'sampai']);
        } elseif ($tab === 'selesai') {
            $data = $data->where('status', 'selesai');
        }

        if ($search) {
            $search = strtolower($search);
            $data = $data->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['no_invoice']), $search)
                    || str_contains(strtolower($item['customer']), $search)
                    || str_contains(strtolower($item['alamat']), $search);
            });
        }

        $daftarPengiriman = $data->map(fn ($item) => (object) $item)->values();

        return view('driver.pengiriman', [
            'mode'             => 'list',
            'daftarPengiriman' => $daftarPengiriman,
            'tab'              => $tab,
            'search'           => $search,
        ]);
    }

    // ====================== AKSI STATUS ======================

    /** Terima invoice: baru -> siap */
    public function terima($id)
    {
        return $this->majukanStatus($id, 'baru', 'siap', 'Invoice diterima. Silakan siapkan barang.');
    }

    /** Mulai pengiriman: siap -> berangkat */
    public function mulai($id)
    {
        return $this->majukanStatus($id, 'siap', 'berangkat', 'Pengiriman dimulai. Selamat jalan!');
    }

    /** Upload bukti sampai tujuan: berangkat -> sampai */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_foto' => 'required|image|max:5120',
        ]);

        $data = $this->getData();
        abort_unless(isset($data[$id]), 404);

        if ($data[$id]['status'] !== 'berangkat') {
            return back()->with('error', 'Upload bukti hanya bisa dilakukan saat status "Sedang Dikirim".');
        }

        $path = $request->file('bukti_foto')->store('bukti-pengiriman', 'public');

        $sessionBukti = Session::get('pengiriman_bukti', []);
        $sessionBukti[$id] = $path;
        Session::put('pengiriman_bukti', $sessionBukti);

        return $this->majukanStatus($id, 'berangkat', 'sampai', 'Bukti pengiriman berhasil diupload.');
    }

    /** Konfirmasi selesai: sampai -> selesai */
    public function selesai($id)
    {
        return $this->majukanStatus($id, 'sampai', 'selesai', 'Pengiriman selesai. Terima kasih!');
    }

    /** Helper: majukan status kalau sesuai urutan */
    private function majukanStatus($id, string $statusSaatIni, string $statusBaru, string $pesan)
    {
        $data = $this->getData();
        abort_unless(isset($data[$id]), 404);

        if ($data[$id]['status'] !== $statusSaatIni) {
            return back()->with('error', 'Status pengiriman tidak sesuai urutan.');
        }

        $sessionStatus = Session::get('pengiriman_status', []);
        $sessionStatus[$id] = $statusBaru;
        Session::put('pengiriman_status', $sessionStatus);

        return redirect()
            ->route('driver.pengiriman', $id)
            ->with('success', $pesan);
    }
}