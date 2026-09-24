<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\CancelUrgentReservationRequest;
use App\Http\Requests\Staff\RejectReservationRequest;
use App\Models\Reservation;
use App\Services\StaffReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        protected StaffReservationService $reservationService
    ) {}

    /**
     * Display active reservations queue (Menunggu, Disetujui, Berlangsung).
     */
    public function index(Request $request): View
    {
        $reservations = $this->reservationService->getActiveQueue(
            $request->input('search'),
            $request->input('type'),
            $request->input('status')
        );

        return view('staff.reservations.index', compact('reservations'));
    }

    /**
     * Display final reservations history.
     */
    public function history(Request $request): View
    {
        $reservations = $this->reservationService->getHistory(
            $request->input('search'),
            $request->input('type'),
            $request->input('status')
        );

        return view('staff.reservations.history', compact('reservations'));
    }

    /**
     * Show single reservation detail.
     */
    public function show(Reservation $reservation): View
    {
        $reservation->evaluateStatus();
        $reservation->load(['facility', 'user', 'approvedBy', 'rejectedBy', 'cancelledBy']);

        return view('staff.reservations.show', compact('reservation'));
    }

    /**
     * Approve reservation.
     */
    public function approve(Reservation $reservation): RedirectResponse
    {
        $this->reservationService->approve($reservation, auth()->id());

        return redirect()->route('staff.reservations.index')
            ->with('success', "Permohonan reservasi #{$reservation->reservation_number} berhasil disetujui.");
    }

    /**
     * Reject reservation.
     */
    public function reject(RejectReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->reservationService->reject(
            $reservation,
            $request->validated('rejection_reason'),
            auth()->id()
        );

        return redirect()->route('staff.reservations.index')
            ->with('success', "Permohonan reservasi #{$reservation->reservation_number} berhasil ditolak.");
    }

    /**
     * Urgent cancellation by staff.
     */
    public function cancel(CancelUrgentReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->reservationService->cancelUrgent(
            $reservation,
            $request->validated('cancellation_reason'),
            auth()->id()
        );

        return redirect()->route('staff.reservations.index')
            ->with('success', "Reservasi #{$reservation->reservation_number} telah dibatalkan secara mendesak.");
    }
}
