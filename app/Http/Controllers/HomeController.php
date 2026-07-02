<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Pengiriman;
use App\Models\Produksi;
use App\Models\Qc;

class HomeController extends Controller
{
    public function index()
    {
        $produksiToday = Produksi::whereDate('created_at', now()->toDateString())->count();
        $pengirimanTotal = Pengiriman::count();
        $invoiceTotal = Invoice::count();
        $qcTotal = Qc::count();

        $chartDays = collect(range(6, 0))->map(function ($offset) {
            return now()->subDays($offset)->format('d M');
        });

        $chartCounts = collect(range(6, 0))->map(function ($offset) {
            $date = now()->subDays($offset)->toDateString();
            return Produksi::whereDate('created_at', $date)->count();
        });

        $chartMax = max(1, $chartCounts->max());
        $chartHeights = $chartCounts->map(function ($count) use ($chartMax) {
            return (int) round(20 + ($count / $chartMax) * 100);
        });

        $activities = [];
        $latestQc = Qc::latest()->first();
        $latestInvoice = Invoice::latest()->first();
        $latestPengiriman = Pengiriman::latest()->first();

        if ($latestQc) {
            $activities[] = [
                'title' => 'QC',
                'detail' => 'Pemeriksaan baru #'.$latestQc->id,
                'time' => $latestQc->created_at,
            ];
        }

        if ($latestInvoice) {
            $activities[] = [
                'title' => 'Invoice',
                'detail' => 'Invoice dibuat #'.$latestInvoice->id,
                'time' => $latestInvoice->created_at,
            ];
        }

        if ($latestPengiriman) {
            $activities[] = [
                'title' => 'Pengiriman',
                'detail' => 'Pengiriman terdaftar #'.$latestPengiriman->id,
                'time' => $latestPengiriman->created_at,
            ];
        }

        usort($activities, function ($left, $right) {
            if (!$left['time'] && !$right['time']) {
                return 0;
            }
            if (!$left['time']) {
                return 1;
            }
            if (!$right['time']) {
                return -1;
            }
            return $right['time']->timestamp <=> $left['time']->timestamp;
        });

        if (empty($activities)) {
            $activities[] = [
                'title' => 'Belum ada aktivitas',
                'detail' => 'Data akan muncul setelah transaksi dibuat.',
                'time' => null,
            ];
        }

        return view('home', [
            'produksiToday' => $produksiToday,
            'pengirimanTotal' => $pengirimanTotal,
            'invoiceTotal' => $invoiceTotal,
            'qcTotal' => $qcTotal,
            'activities' => $activities,
            'chartDays' => $chartDays,
            'chartHeights' => $chartHeights,
        ]);
    }
}
