<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfUnAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        // New: Check if user is NOT authenticated on guarded routes that require login
        foreach ($guards as $guard) {
            if (!Auth::guard($guard)->check()) {
                // If user is not authenticated and trying to access protected pages,
                // redirect them to the appropriate login route:
                if ($guard === 'geomapping') {
                    return redirect()->route('geomapping.iplan.login');
                }

                return redirect('/login');
            }
        }

        return $next($request);
    }
}
