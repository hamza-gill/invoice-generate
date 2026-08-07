<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  string|null  $guard  Optional guard name, e.g. "platform".
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $guard = $guard ?: config('auth.defaults.guard', 'web');

        // ✅ If not logged in, redirect to the relevant login page
        if (!Auth::guard($guard)->check()) {
            $loginRoute = $guard === 'platform' ? 'platform.login' : 'login';

            return redirect()->route($loginRoute)->withErrors([
                'error' => 'You must be logged in to access this page.',
            ]);
        }

        return $next($request);
    }
}
