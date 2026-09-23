<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacilityFilterRequest;
use App\Services\FacilityCatalogService;
use Illuminate\Contracts\View\View;

class FacilityController extends Controller
{
    public function __construct(
        private readonly FacilityCatalogService $catalogService
    ) {}

    public function index(FacilityFilterRequest $request): View
    {
        $filters = $request->validated();
        $facilities = $this->catalogService->getPaginatedFacilities($filters);
        $locations = $this->catalogService->getAvailableLocations();
        $types = $this->catalogService->getAvailableTypes();

        return view('facilities.index', [
            'facilities' => $facilities,
            'locations' => $locations,
            'types' => $types,
            'filters' => $filters,
        ]);
    }

    public function show(string $facility, \App\Services\FacilityDetailService $detailService): View
    {
        $facilityModel = $detailService->getFacility($facility);

        return view('facilities.show', [
            'facility' => $facilityModel,
        ]);
    }
}
