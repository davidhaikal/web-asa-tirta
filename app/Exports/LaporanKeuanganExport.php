<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanKeuanganExport implements FromView, ShouldAutoSize
{
    protected $transactions;
    protected $totalPendapatan;
    protected $totalPembelian;
    protected $totalPiutang;
    protected $totalPenagihan;
    protected $labelPeriode;

    public function __construct($transactions, $totalPendapatan, $totalPembelian, $totalPiutang, $totalPenagihan, $labelPeriode)
    {
        $this->transactions = $transactions;
        $this->totalPendapatan = $totalPendapatan;
        $this->totalPembelian = $totalPembelian;
        $this->totalPiutang = $totalPiutang;
        $this->totalPenagihan = $totalPenagihan;
        $this->labelPeriode = $labelPeriode;
    }

    public function view(): View
    {
        return view('kasir.laporan-penjualan-excel', [
            'transactions' => $this->transactions,
            'totalPendapatan' => $this->totalPendapatan,
            'totalPembelian' => $this->totalPembelian,
            'totalPiutang' => $this->totalPiutang,
            'totalPenagihan' => $this->totalPenagihan,
            'labelPeriode' => $this->labelPeriode,
        ]);
    }
}
