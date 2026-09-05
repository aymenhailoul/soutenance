<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPagePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $currentRoute = $request->route()->getName();

        // Skip permission check for certain routes (login, logout, etc.)
        $excludedRoutes = ['login', 'logout', 'password.request', 'password.email', 'password.reset', 'password.update'];

        if (in_array($currentRoute, $excludedRoutes)) {
            return $next($request);
        }

        if ($currentRoute === 'pages.test') {
            $targetPage = $request->route('page');
            if ($targetPage && !$user->hasPageAccess($targetPage)) {
                abort(403, "Vous n'avez pas la permission d'accéder à cette page ({$targetPage}).");
            }
            return $next($request);
        }

        // Check if user has permission to access this page
        // The hasPageAccess method on the User model handles all inheritance logic
        if (!$user->hasPageAccess($currentRoute)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
