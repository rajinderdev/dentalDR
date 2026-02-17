<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }
        $roleName = $user->RoleName ?? null;

        $isAdmin = false;

        if (method_exists($user, 'hasRole')) {
            $isAdmin = $user->hasRole('Administrator') || $user->hasRole('administrator');
        }

        if (! $isAdmin && is_string($roleName)) {
            $isAdmin = strtolower($roleName) === 'administrator';
        }

        if (! $isAdmin) {   
            abort(403);
        }

        return $next($request);
    }
}
