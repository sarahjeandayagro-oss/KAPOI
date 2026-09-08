<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'This account is not allowed to access that area.');
        }

        if ($user->role === 'researcher' && ($user->status ?? 'approved') !== 'approved') {
            auth()->logout();

            return redirect()->route('login')
                ->withErrors(['login_error' => 'Your researcher account is still pending staff verification.']);
        }

        return $next($request);
    }
}
