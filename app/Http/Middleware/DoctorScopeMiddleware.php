<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Provider;

class DoctorScopeMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user && $user->role && strtolower($user->role->RoleName) === 'doctor') {
            // Attach doctor_id (= ProviderID) to request for use in controllers/services
            $provider = Provider::where('UserID', $user->UserID)->first();
            $request->merge(['doctor_id' => $provider?->ProviderID]);
        }
        return $next($request);
    }
}
