<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AdminMiddleware
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (! $user) {
            // Try to get user data from cookie (set by JavaScript)
            $userDataCookie = $request->cookie('user_data');
            $userIDHeader = $request->header('X-User-ID');
            
            if ($userDataCookie || $userIDHeader) {
                try {
                    // Parse user data from cookie
                    if ($userDataCookie) {
                        $userData = json_decode(urldecode($userDataCookie), true);
                        $userID = $userData['UserID'] ?? null;
                    } else {
                        $userID = $userIDHeader;
                    }
                    
                    if ($userID) {
                        // Find user by UserID
                        $user = User::where('UserID', $userID)->first();
                        
                        if ($user) {
                            // Log in the user for this request using Laravel's auth guard
                            auth()->guard('web')->setUser($user);
                        }
                    }
                } catch (\Exception $e) {
                    // Invalid data, continue to redirect
                }
            }
        }
        
        if (! $user) {
            // return redirect('https://dental.stgserver.co.in/auth/login');
            return redirect()->route('login');
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
