<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaporanRequest;
use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function __construct(private readonly LaporanService $laporanService) {}

    public function index(LaporanRequest $request): View
    {
        $filters = $request->validated();
        $year = (int) ($filters['tahun'] ?? now()->year);
        $mode = $filters['mode'] ?? 'bulan';
        $kebunId = isset($filters['kebun_id']) ? (int) $filters['kebun_id'] : null;

        return view('laporan.index', [
            'report' => $this->laporanService->calculate($request->user(), $year, $mode, $kebunId),
            'years' => $this->laporanService->availableYears($request->user()),
        ]);
    }

    public function pdf(LaporanRequest $request): Response
    {
        $filters = $request->validated();
        $year = (int) ($filters['tahun'] ?? now()->year);
        $mode = $filters['mode'] ?? 'bulan';
        $kebunId = isset($filters['kebun_id']) ? (int) $filters['kebun_id'] : null;
        $report = $this->laporanService->calculate($request->user(), $year, $mode, $kebunId);

        return Pdf::loadView('laporan.pdf', [
            'report' => $report,
            'user' => $request->user(),
            'printedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->download("laporan-sawitku-{$year}.pdf");
    }
}
