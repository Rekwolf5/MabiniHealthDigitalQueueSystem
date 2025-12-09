<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class FrontDeskAccess
{
    /**
     * Handle an incoming request - Allow Front Desk and Admin users only
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Allow admin (full access) and front desk staff
        if ($user->isAdmin() || $user->isFrontDesk()) {
            return $next($request);
        }
        
        abort(403, 'Access denied. This area is restricted to front desk staff and administrators.');
    }
}
