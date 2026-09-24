<?php

namespace App\Http\Controllers\Staff;

use App\Enums\OperationalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\UpdateOperationalStatusRequest;
use App\Models\Facility;
use App\Services\StaffFacilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function __construct(
        protected StaffFacilityService $facilityService
    ) {}

    /**
     * Display all facilities for staff workspace.
     */
    public function index(Request $request): View
    {
        $facilities = $this->facilityService->getAllFacilities(
            $request->input('search'),
            $request->input('type')
        );

        return view('staff.facilities.index', compact('facilities'));
    }

    /**
     * Display facilities under maintenance.
     */
    public function maintenance(Request $request): View
    {
        $maintenanceFacilities = $this->facilityService->getMaintenanceFacilities(
            $request->input('search'),
            $request->input('type')
        );

        return view('staff.facilities.maintenance', [
            'facilities' => $maintenanceFacilities,
            'maintenanceFacilities' => $maintenanceFacilities,
        ]);
    }

    /**
     * Show single facility detail.
     */
    public function show(Facility $facility): View
    {
        $facility->load([
            'photos',
            'statusLogs' => fn ($q) => $q->latest('changed_at')->with('changedBy'),
        ]);

        return view('staff.facilities.show', compact('facility'));
    }

    /**
     * Update facility operational status.
     */
    public function updateStatus(UpdateOperationalStatusRequest $request, Facility $facility): RedirectResponse
    {
        $targetStatus = OperationalStatus::from($request->validated('operational_status'));

        $this->facilityService->updateOperationalStatus(
            $facility,
            $targetStatus,
            $request->validated('reason'),
            auth()->id(),
            $request->validated('report_id')
        );

        return redirect()->route('staff.facilities.maintenance')
            ->with('success', "Status operasional fasilitas {$facility->name} berhasil diperbarui.");
    }
}
