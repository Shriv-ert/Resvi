<?php

namespace App\Services;

use App\Enums\LoginAttemptResult;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Support\Str;

final readonly class AuthenticationService
{
    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    public function __construct(
        private AuthFactory $auth,
        private RateLimiter $rateLimiter,
    ) {}

    public function authenticate(string $email, string $password, string $ipAddress): LoginAttemptResult
    {
        $throttleKey = $this->throttleKey($email, $ipAddress);

        if ($this->rateLimiter->tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            return LoginAttemptResult::Throttled;
        }

        if (! $this->auth->guard('web')->attempt(['email' => $email, 'password' => $password])) {
            $this->rateLimiter->hit($throttleKey, self::DECAY_SECONDS);

            return LoginAttemptResult::InvalidCredentials;
        }

        $this->rateLimiter->clear($throttleKey);

        return LoginAttemptResult::Authenticated;
    }

    public function logout(): void
    {
        $this->auth->guard('web')->logout();
    }

    private function throttleKey(string $email, string $ipAddress): string
    {
        return hash('sha256', Str::lower($email).'|'.$ipAddress);
    }
}
