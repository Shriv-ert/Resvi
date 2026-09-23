<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecapFilterRequest;
use App\Services\RecapService;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RecapController extends Controller
{
    public function __construct(
        private readonly RecapService $recapService,
    ) {}

    /**
     * Display the periodic recap analytics page (Menu 3).
     */
    public function index(RecapFilterRequest $request): View
    {
        $month = $request->getSelectedMonth();
        $year = $request->getSelectedYear();
        $metric = $request->getSelectedMetric();
        $groupBy = $request->getSelectedGroupBy();

        $recapData = $this->recapService->getRecapData(
            month: $month,
            year: $year,
            metric: $metric,
            groupBy: $groupBy
        );

        return view('admin.recaps.index', [
            'recapData' => $recapData,
            'selectedMonth' => $month,
            'selectedYear' => $year,
            'selectedMetric' => $metric,
            'selectedGroupBy' => $groupBy,
        ]);
    }

    /**
     * Stream or download the periodic recap report in CSV, Excel (.xlsx), or PDF format.
     */
    public function export(RecapFilterRequest $request): Response|StreamedResponse
    {
        $month = $request->getSelectedMonth();
        $year = $request->getSelectedYear();
        $metric = $request->getSelectedMetric();
        $groupBy = $request->getSelectedGroupBy();
        $format = $request->getSelectedFormat() ?? 'csv';

        $recapData = $this->recapService->getRecapData(
            month: $month,
            year: $year,
            metric: $metric,
            groupBy: $groupBy
        );

        return match ($format) {
            'xlsx' => $this->recapService->exportExcel($recapData),
            'pdf' => $this->recapService->exportPdf($recapData),
            default => $this->recapService->exportCsv($recapData),
        };
    }
}
