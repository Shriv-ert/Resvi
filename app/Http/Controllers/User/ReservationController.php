<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CancelReservationRequest;
use App\Http\Requests\User\StoreReservationRequest;
use App\Models\Reservation;
use App\Services\UserReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(
        protected UserReservationService $reservationService
    ) {}

    /**
     * Menampilkan riwayat reservasi milik Pengguna yang sedang login.
     */
    public function index(Request $request): View
    {
        $reservations = Reservation::with(['facility'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->evaluateStatus();
        }

        return view('user.reservations.index', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Menampilkan detail reservasi milik Pengguna.
     */
    public function show(Request $request, Reservation $reservation): View
    {
        if ($reservation->user_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat reservasi ini.');
        }

        $reservation->load(['facility', 'cancelledBy', 'approvedBy', 'rejectedBy']);
        $reservation->evaluateStatus();

        return view('user.reservations.show', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Menyimpan pengajuan reservasi baru ke database.
     */
    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $reservation = $this->reservationService->createReservation(
            user: $request->user(),
            data: $request->validated()
        );

        return redirect()
            ->route('user.reservations.index', ['as_user' => 1])
            ->with('success', "Reservasi #{$reservation->reservation_number} berhasil diajukan dan sedang menunggu persetujuan petugas.");
    }

    /**
     * Membatalkan reservasi mandiri oleh Pengguna sebelum waktu mulai.
     */
    public function cancel(CancelReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $this->reservationService->cancelReservation(
            user: $request->user(),
            reservation: $reservation,
            reason: $request->validated('cancellation_reason')
        );

        return redirect()
            ->route('user.reservations.show', ['reservation' => $reservation->id, 'as_user' => 1])
            ->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
