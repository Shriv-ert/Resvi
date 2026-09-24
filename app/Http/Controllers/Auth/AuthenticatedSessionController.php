<?php

namespace App\Http\Controllers\Auth;

use App\Enums\LoginAttemptResult;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthenticationService;
use App\Services\GuestReservationDraftService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(
        LoginRequest $request,
        AuthenticationService $authentication,
        GuestReservationDraftService $draftService
    ): RedirectResponse {
        $credentials = $request->validated();
        $result = $authentication->authenticate(
            $credentials['email'],
            $credentials['password'],
            $request->ip() ?? '',
        );

        if ($result !== LoginAttemptResult::Authenticated) {
            throw ValidationException::withMessages([
                'login' => [$this->loginErrorMessage($result)],
            ]);
        }

        $user = auth()->user();

        if ($user && $user->role === UserRole::Pengguna && $draftService->hasDraft()) {
            return redirect()->route('reservations.create');
        }

        if ($draftService->hasDraft()) {
            $draftService->clearDraft();
        }

        if ($user?->isAdmin()) {
            return redirect()->intended(route('admin.facilities.index'));
        }

        if ($user?->role === UserRole::Petugas) {
            return redirect()->intended(route('staff.reservations.index'));
        }

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request, AuthenticationService $authentication): RedirectResponse
    {
        $authentication->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function loginErrorMessage(LoginAttemptResult $result): string
    {
        return match ($result) {
            LoginAttemptResult::InvalidCredentials => 'Email atau kata sandi tidak valid.',
            LoginAttemptResult::Throttled => 'Terlalu banyak percobaan login. Silakan coba lagi dalam satu menit.',
            LoginAttemptResult::Authenticated => throw new \LogicException('Status login tidak dapat menjadi authenticated saat menangani kegagalan.'),
        };
    }
}
