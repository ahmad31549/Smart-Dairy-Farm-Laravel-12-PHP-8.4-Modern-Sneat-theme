<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Only allow admin/super_admin
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $users = \App\Models\User::where('id', '!=', auth()->id())->get();
        return view('users.index', compact('users'));
    }

    public function approve($id)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($id);
        $user->status = 'active';
        $user->save();

        return back()->with('success', 'User approved successfully.');
    }

    public function reject($id)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        return back()->with('success', 'User rejected.');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,manager,farm_worker,veterinary_doctor',
            'farm_name' => 'nullable|string|max:255',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'farm_name' => $request->farm_name,
            'status' => $request->has('auto_approve') ? 'active' : 'pending',
        ]);

        return back()->with('success', "User '{$user->name}' has been created successfully.");
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($id);
        
        // Prevent editing super_admin
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Cannot edit super admin account.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:admin,manager,farm_worker,veterinary_doctor',
            'status' => 'required|string|in:active,pending,rejected',
            'farm_name' => 'nullable|string|max:255',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->role = $request->role;
        $user->status = $request->status;
        $user->farm_name = $request->farm_name;
        
        // Only update password if provided
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        
        $user->save();

        return back()->with('success', "User '{$user->name}' has been updated successfully.");
    }

    public function updateRole(Request $request, $id)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'role' => 'required|string|in:admin,manager,farm_worker,veterinary_doctor',
        ]);

        $user = \App\Models\User::findOrFail($id);
        
        // Prevent changing own role or super_admin role
        if ($user->id === auth()->id() || $user->role === 'super_admin') {
            return back()->with('error', 'Cannot change role for this user.');
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'User role updated successfully.');
    }

    public function delete($id)
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($id);
        
        // Prevent deleting own account or super_admin
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Cannot delete super admin account.');
        }

        // Check for related records
        $relatedRecords = [];
        
        // Check daily milk records
        $milkRecords = \App\Models\DailyMilkRecord::where('recorded_by', $user->id)->count();
        if ($milkRecords > 0) {
            $relatedRecords[] = "{$milkRecords} daily milk record(s)";
        }
        
        // Check emergency alerts
        $alerts = \App\Models\EmergencyAlert::where('user_id', $user->id)->count();
        if ($alerts > 0) {
            $relatedRecords[] = "{$alerts} emergency alert(s)";
        }

        // If user has related records, prevent deletion
        if (!empty($relatedRecords)) {
            $recordsList = implode(', ', $relatedRecords);
            return back()->with('error', "Cannot delete user '{$user->name}'. This user has {$recordsList}. Please reassign or delete these records first.");
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User '{$userName}' has been deleted successfully.");
    }

    public function export()
    {
        ini_set('memory_limit', '512M');
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $users = \App\Models\User::all();
        $html = view('users.print', compact('users'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        return $pdf->download('users_report.pdf');
    }

    public function print()
    {
        if (!in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            abort(403);
        }

        $users = \App\Models\User::all();
        return view('users.print', compact('users'));
    }
}
