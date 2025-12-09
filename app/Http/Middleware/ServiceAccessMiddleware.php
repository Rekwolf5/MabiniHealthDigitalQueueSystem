<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceAccessMiddleware
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

        // Get service ID from route parameter
        $serviceId = $request->route('serviceId');
        
        // Check if user can access this service
        if ($serviceId && !$user->canAccessService($serviceId)) {
            abort(403, 'You do not have permission to access this service.');
        }

        return $next($request);
    }
}
