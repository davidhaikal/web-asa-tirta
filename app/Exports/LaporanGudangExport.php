<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;
use App\Http\Controllers\GudangController;

class LaporanGudangExport implements FromView, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $controller = new GudangController();
        $res = $controller->getLaporanData($this->request);
        $data = $res['data'];

        return view('gudang.laporan_pdf', [
            'data' => $data,
            'request' => $this->request,
            'is_excel' => true
        ]);
    }
}
