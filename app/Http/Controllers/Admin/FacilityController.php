<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeactivateFacilityRequest;
use App\Http\Requests\Admin\StoreFacilityRequest;
use App\Http\Requests\Admin\UpdateFacilityRequest;
use App\Models\Facility;
use App\Services\FacilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function __construct(
        private readonly FacilityService $facilityService,
    ) {}

    /**
     * Display a listing of all facilities (Admin Homepage).
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $type = $request->query('type');
        $masterStatus = $request->query('master_status');
        $operationalStatus = $request->query('operational_status');

        $facilities = $this->facilityService->getPaginatedFacilities(
            search: is_string($search) ? $search : null,
            type: is_string($type) ? $type : null,
            masterStatus: is_string($masterStatus) ? $masterStatus : null,
            operationalStatus: is_string($operationalStatus) ? $operationalStatus : null,
        );

        return view('admin.facilities.index', [
            'facilities' => $facilities,
            'search' => $search,
            'selectedType' => $type,
            'selectedMasterStatus' => $masterStatus,
            'selectedOperationalStatus' => $operationalStatus,
        ]);
    }

    /**
     * Show the form for creating a new facility.
     */
    public function create(): View
    {
        return view('admin.facilities.create');
    }

    /**
     * Store a newly created facility in storage.
     */
    public function store(StoreFacilityRequest $request): RedirectResponse
    {
        $facility = $this->facilityService->createFacility(
            data: $request->validated(),
            photo: $request->file('photo'),
            adminId: (string) $request->user()?->id,
        );

        return redirect()->route('admin.facilities.index')
            ->with('status', sprintf('Fasilitas "%s" berhasil ditambahkan.', $facility->name));
    }

    /**
     * Display the specified facility detail.
     */
    public function show(Facility $facility): View
    {
        $facility->load(['photos', 'statusLogs.changedBy']);

        return view('admin.facilities.show', [
            'facility' => $facility,
        ]);
    }

    /**
     * Show the form for editing the specified facility.
     */
    public function edit(Facility $facility): View
    {
        $facility->load('photos');

        return view('admin.facilities.edit', [
            'facility' => $facility,
        ]);
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility): RedirectResponse
    {
        $updatedFacility = $this->facilityService->updateFacility(
            facility: $facility,
            data: $request->validated(),
            photo: $request->file('photo'),
        );

        return redirect()->route('admin.facilities.show', $updatedFacility)
            ->with('status', sprintf('Data fasilitas "%s" berhasil diperbarui.', $updatedFacility->name));
    }

    /**
     * Deactivate the specified facility with conflict guard.
     */
    public function deactivate(DeactivateFacilityRequest $request, Facility $facility): RedirectResponse
    {
        try {
            $this->facilityService->deactivateFacility(
                facility: $facility,
                reason: $request->validated('reason'),
                adminId: (string) $request->user()?->id,
            );

            return redirect()->route('admin.facilities.show', $facility)
                ->with('status', sprintf('Fasilitas "%s" berhasil dinonaktifkan.', $facility->name));
        } catch (ValidationException $e) {
            $errorMsg = $e->validator->errors()->first('deactivate') ?? 'Gagal menonaktifkan fasilitas.';

            return redirect()->route('admin.facilities.show', $facility)
                ->with('error', $errorMsg);
        }
    }

    /**
     * Reactivate the specified facility.
     */
    public function reactivate(Request $request, Facility $facility): RedirectResponse
    {
        $this->facilityService->reactivateFacility(
            facility: $facility,
            adminId: (string) $request->user()?->id,
        );

        return redirect()->route('admin.facilities.show', $facility)
            ->with('status', sprintf('Fasilitas "%s" berhasil diaktifkan kembali.', $facility->name));
    }
}
