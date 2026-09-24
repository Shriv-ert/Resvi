<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        if (! in_array($userRoleValue, $roles, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}
