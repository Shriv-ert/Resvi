<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreReportRequest;
use App\Models\Facility;
use App\Models\Report;
use App\Services\UserReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected UserReportService $reportService
    ) {}

    /**
     * Menampilkan riwayat laporan kerusakan fasilitas milik Pengguna.
     */
    public function index(Request $request): View
    {
        $reports = Report::with(['facility'])
            ->where('reporter_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.reports.index', [
            'reports' => $reports,
        ]);
    }

    /**
     * Menampilkan formulir pelaporan kendala fasilitas.
     */
    public function create(Request $request): View
    {
        $facilities = Facility::active()->orderBy('name')->get();

        return view('user.reports.create', [
            'facilities' => $facilities,
        ]);
    }

    /**
     * Menyimpan laporan kendala fasilitas baru ke database.
     */
    public function store(StoreReportRequest $request): RedirectResponse
    {
        $report = $this->reportService->createReport(
            user: $request->user(),
            data: $request->validated(),
            photo: $request->file('photo')
        );

        return redirect()
            ->route('user.reports.index', ['as_user' => 1])
            ->with('success', "Laporan kendala #{$report->report_number} berhasil dikirim dan akan segera ditinjau oleh petugas sarpras.");
    }

    /**
     * Menampilkan detail laporan kerusakan milik Pengguna.
     */
    public function show(Request $request, Report $report): View
    {
        if ($report->reporter_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan ini.');
        }

        $report->load(['facility', 'processedBy', 'resolvedBy', 'rejectedBy']);

        return view('user.reports.show', [
            'report' => $report,
        ]);
    }
}
