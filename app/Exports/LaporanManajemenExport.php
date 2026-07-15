<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanManajemenExport implements FromView, ShouldAutoSize
{
    protected $data;
    protected $labelPihak;

    public function __construct($data, $labelPihak)
    {
        $this->data = $data;
        $this->labelPihak = $labelPihak;
    }

    public function view(): View
    {
        return view('manajemen.laporan_pdf', [
            'data' => $this->data,
            'labelPihak' => $this->labelPihak
        ]);
    }
}
