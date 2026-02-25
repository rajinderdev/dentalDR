<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Validate user session from localStorage data
     */
    public function validateSession(Request $request): JsonResponse
    {
        $request->validate([
            'userID' => 'required|string'
        ]);

        $userID = $request->input('userID');
        
        // Find user by UserID
        $user = User::where('UserID', $userID)->first();
        
        if (!$user) {
            return response()->json([
                'valid' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check if user is active and has proper role
        if (!$user->isActive ?? true) {
            return response()->json([
                'valid' => false,
                'message' => 'User account is inactive'
            ], 403);
        }

        // Check if user has admin role (optional, based on your requirements)
        $roleName = $user->RoleName ?? null;
        $isAdmin = false;

        if (method_exists($user, 'hasRole')) {
            $isAdmin = $user->hasRole('Administrator') || $user->hasRole('administrator');
        }

        if (!$isAdmin && is_string($roleName)) {
            $isAdmin = strtolower($roleName) === 'administrator';
        }

        if (!$isAdmin) {
            return response()->json([
                'valid' => false,
                'message' => 'User does not have admin privileges'
            ], 403);
        }

        return response()->json([
            'valid' => true,
            'user' => [
                'UserID' => $user->UserID,
                'UserName' => $user->UserName ?? $user->name,
                'RoleName' => $roleName,
                'Email' => $user->Email ?? $user->email
            ]
        ]);
    }

    /**
     * Login user and store session data
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'userID' => 'required|string',
            'userData' => 'required|array'
        ]);

        $userID = $request->input('userID');
        $userData = $request->input('userData');

        // Find user by UserID
        $user = User::where('UserID', $userID)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // You might want to update user data here if needed
        // For example, update last login time

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'UserID' => $user->UserID,
                'UserName' => $user->UserName ?? $user->name,
                'RoleName' => $user->RoleName,
                'Email' => $user->Email ?? $user->email
            ]
        ]);
    }
}
