<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckAvailabilityRequest;
use App\Services\FacilityAvailabilityService;
use App\Services\FacilityDetailService;
use App\Services\GuestReservationDraftService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationFlowController extends Controller
{
    /**
     * Menampilkan form reservasi bertahap (tahap 1: pengecekan jadwal).
     */
    public function create(
        Request $request,
        FacilityDetailService $detailService,
        GuestReservationDraftService $draftService
    ): View|RedirectResponse {
        $draft = $draftService->getDraft();
        $facilityId = $request->query('facility_id') ?? $draft['facility_id'] ?? null;

        if (! $facilityId) {
            return redirect()->route('home')
                ->with('warning', 'Silakan pilih fasilitas terlebih dahulu dari katalog.');
        }

        $facility = $detailService->getFacility($facilityId);

        $now = CarbonImmutable::now('Asia/Jakarta');
        $defaultDate = $now->toDateString();
        if ($now->hour >= 19) {
            $defaultDate = $now->addDay()->toDateString();
            $defaultStart = '08:00';
            $defaultEnd = '10:00';
        } else {
            $nextMinute = $now->minute < 30 ? 30 : 0;
            $nextHour = $now->minute < 30 ? $now->hour : $now->hour + 1;
            if ($nextHour < 7) {
                $nextHour = 7;
                $nextMinute = 0;
            }
            $startCarbon = $now->setTime($nextHour, $nextMinute);
            $defaultStart = $startCarbon->format('H:i');
            $endCarbon = $startCarbon->addHours(2);
            if ($endCarbon->hour > 20 || ($endCarbon->hour == 20 && $endCarbon->minute > 0)) {
                $defaultEnd = '20:00';
            } else {
                $defaultEnd = $endCarbon->format('H:i');
            }
        }

        $date = $request->query('date') ?? $draft['date'] ?? $defaultDate;
        $startTime = $request->query('start_time') ?? $draft['start_time'] ?? $defaultStart;
        $endTime = $request->query('end_time') ?? $draft['end_time'] ?? $defaultEnd;

        return view('reservations.create', [
            'facility' => $facility,
            'date' => $date,
            'startTime' => $startTime,
            'endTime' => $endTime,
            'availability' => null,
            'hasDraft' => $draft !== null,
        ]);
    }

    /**
     * Memeriksa ketersediaan slot waktu dan mengembalikan hasil evaluasi biner tanpa membocorkan PII.
     */
    public function checkAvailability(
        CheckAvailabilityRequest $request,
        FacilityDetailService $detailService,
        FacilityAvailabilityService $availabilityService
    ): View {
        $data = $request->validated();
        $facility = $detailService->getFacility($data['facility_id']);

        $availabilityResult = $availabilityService->check(
            $facility,
            $data['date'],
            $data['start_time'],
            $data['end_time']
        );

        return view('reservations.create', [
            'facility' => $facility,
            'date' => $data['date'],
            'startTime' => $data['start_time'],
            'endTime' => $data['end_time'],
            'availability' => $availabilityResult,
            'hasDraft' => false,
        ]);
    }

    /**
     * Menyimpan draft jadwal sementara pengunjung ke session dan mengarahkan ke halaman login.
     */
    public function storeDraft(
        CheckAvailabilityRequest $request,
        GuestReservationDraftService $draftService
    ): RedirectResponse {
        $data = $request->validated();

        $draftService->storeDraft(
            $data['facility_id'],
            $data['date'],
            $data['start_time'],
            $data['end_time']
        );

        return redirect()->route('login')
            ->with('info', 'Pilihan jadwal Anda telah disimpan sementara. Silakan masuk untuk melanjutkan reservasi.');
    }
}
