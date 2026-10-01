<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KeuanganExport implements FromView, ShouldAutoSize
{
    protected $data;

    protected $labelPeriode;

    public function __construct($data, $labelPeriode = null)
    {
        $this->data = $data;
        $this->labelPeriode = $labelPeriode;
    }

    public function view(): View
    {
        return view('keuangan.laporan_pdf', [
            'data' => $this->data,
            'labelPeriode' => $this->labelPeriode,
        ]);
    }
}
