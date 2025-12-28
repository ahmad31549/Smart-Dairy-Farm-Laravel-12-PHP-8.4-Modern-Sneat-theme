<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the profile page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Fetch real stats
        $alertCount = \App\Models\EmergencyAlert::where('user_id', $user->id)->count();
        $milkRecordCount = \App\Models\DailyMilkRecord::where('recorded_by', $user->id)->count();
        
        // Fetch recent activities (combined)
        $activities = collect();
        
        // Alerts
        \App\Models\EmergencyAlert::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get()
            ->each(function($alert) use ($activities) {
                $activities->push([
                    'type' => 'Emergency Alert',
                    'title' => 'Emergency Alert Created',
                    'description' => \Illuminate\Support\Str::limit($alert->message, 50),
                    'date' => $alert->created_at,
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'text-danger'
                ]);
            });
            
        // Milk records
        \App\Models\DailyMilkRecord::where('recorded_by', $user->id)
            ->latest()
            ->limit(5)
            ->get()
            ->each(function($record) use ($activities) {
                $activities->push([
                    'type' => 'Milk Record',
                    'title' => 'Milk Record Added',
                    'description' => "Recorded {$record->total_milk_quantity} liters of milk.",
                    'date' => $record->created_at,
                    'icon' => 'fa-vial',
                    'color' => 'text-primary'
                ]);
            });
            
        // Sort by date and take recent ones
        $recentActivities = $activities->sortByDesc('date')->take(10);
        
        return view('profile.index', compact('user', 'alertCount', 'milkRecordCount', 'recentActivities'));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'user' => $user
        ]);
    }

    /**
     * Update profile image
     */
    public function updateImage(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Delete old image if exists
        if ($user->profile_image && file_exists(public_path($user->profile_image))) {
            unlink(public_path($user->profile_image));
        }

        // Store new image
        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = 'profile_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/profiles'), $imageName);
            
            $user->profile_image = 'uploads/profiles/' . $imageName;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile image updated successfully!',
            'image_url' => asset($user->profile_image)
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect!'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }
}
