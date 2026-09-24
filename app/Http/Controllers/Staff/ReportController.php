<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\RejectReportRequest;
use App\Http\Requests\Staff\ResolveReportRequest;
use App\Models\Report;
use App\Services\StaffReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected StaffReportService $reportService
    ) {}

    /**
     * Display active reports queue (Baru, Diproses).
     */
    public function index(Request $request): View
    {
        $reports = $this->reportService->getActiveQueue(
            $request->input('search'),
            $request->input('type'),
            $request->input('status')
        );

        return view('staff.reports.index', compact('reports'));
    }

    /**
     * Display final reports history (Selesai, Ditolak).
     */
    public function history(Request $request): View
    {
        $reports = $this->reportService->getHistory(
            $request->input('search'),
            $request->input('type'),
            $request->input('status')
        );

        return view('staff.reports.history', compact('reports'));
    }

    /**
     * Show report detail.
     */
    public function show(Report $report): View
    {
        $report->load(['facility', 'reporter', 'processedBy', 'resolvedBy', 'rejectedBy']);

        return view('staff.reports.show', compact('report'));
    }

    /**
     * Start processing a new report.
     */
    public function startProcess(Report $report): RedirectResponse
    {
        $this->reportService->startProcess($report, auth()->id());

        return redirect()->route('staff.reports.show', $report)
            ->with('success', "Laporan #{$report->report_number} mulai diproses.");
    }

    /**
     * Resolve a processing report.
     */
    public function resolve(ResolveReportRequest $request, Report $report): RedirectResponse
    {
        $this->reportService->resolve(
            $report,
            $request->validated('resolution_notes'),
            auth()->id()
        );

        return redirect()->route('staff.reports.index')
            ->with('success', "Laporan #{$report->report_number} berhasil diselesaikan.");
    }

    /**
     * Reject a processing report.
     */
    public function reject(RejectReportRequest $request, Report $report): RedirectResponse
    {
        $this->reportService->reject(
            $report,
            $request->validated('rejection_reason'),
            auth()->id()
        );

        return redirect()->route('staff.reports.index')
            ->with('success', "Laporan #{$report->report_number} telah ditolak.");
    }
}
