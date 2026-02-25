<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    /**
     * Show the profile editing form.
     */
    public function edit()
    {
       $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:Users,Email,' . $user->UserID . ',UserID',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Update basic information
            $data = [
                "Name"=>$request->name,
                "Email"=>$request->Email,
                "Mobile"=>$request->Mobile,
                ];
           

            // Handle password change
            if ($request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->Password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect.'
                    ], 422);
                }
                 $data["Password"]= Hash::make($request->new_password);
            }

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                
                // Delete old photo if exists
                if ($user->ProfilePhoto) {
                    Storage::disk('public')->delete($user->ProfilePhoto);
                }
                
                // Store new photo
                $path = $photo->store('profile-photos', 'public');
                 $data["ProfilePhoto"]= $path;
            }

            User::where('UserID',$user->UserID)->update($data);
            $user = User::where('UserID',$user->UserID)->first();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'user' => [
                    'name' => $user->Name,
                    'email' => $user->Email,
                    'phone' => $user->Mobile,
                    'profile_photo_url' => $user->ProfilePhoto ? asset('admin/storage/' . $user->ProfilePhoto) : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user profile data for AJAX requests.
     */
    public function getProfile(): JsonResponse
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->UserID,
                'name' => $user->Name,
                'email' => $user->Email,
                'phone' => $user->Mobile,
                'profile_photo_url' => $user->ProfilePhoto ? asset('admin/storage/' . $user->ProfilePhoto) : null,
                'created_at' => $user->created_at->format('M d, Y'),
                'updated_at' => $user->updated_at->format('M d, Y')
            ]
        ]);
    }
}
