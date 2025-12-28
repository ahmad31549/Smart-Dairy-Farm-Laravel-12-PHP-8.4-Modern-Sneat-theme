<?php

namespace App\Http\Controllers;

use App\Models\EmergencyAlert;
use App\Models\Animal;
use Illuminate\Http\Request;

class EmergencyAlertController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = EmergencyAlert::with(['user', 'animal'])->latest();
        
        if ($user->role === 'farm_worker') {
            $query->where('user_id', $user->id);
        }
        
        // Get counts for header stats before pagination
        $totalCount = $query->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $resolvedCount = (clone $query)->where('status', 'resolved')->count();

        $alerts = $query->paginate(10);
        return view('alerts.index', compact('alerts', 'totalCount', 'pendingCount', 'resolvedCount'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string',
                'animal_id' => 'nullable|integer|exists:animals,id',
                'temperature' => 'nullable|numeric',
                'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
            ]);

            $user = auth()->user();
            $isAdmin = in_array($user->role, ['super_admin', 'admin', 'manager']);

            $imagePath = null;
            if ($request->hasFile('attachment')) {
                $imagePath = $request->file('attachment')->store('uploads/alerts', 'public');
            }

            $alert = EmergencyAlert::create([
                'user_id' => $user->id,
                'animal_id' => $request->animal_id ?: null,
                'message' => $request->message,
                'temperature' => $request->temperature,
                'status' => $isAdmin ? 'forwarded_to_doctor' : 'pending',
                'is_forwarded' => $isAdmin ? true : false,
                'image_path' => $imagePath
            ]);

            $animal = $request->animal_id ? Animal::find($request->animal_id) : null;

            // 1. Send notification to all admins
            $admins = \App\Models\User::whereIn('role', ['super_admin', 'admin', 'manager'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\EmergencyAlertNotification($alert, $animal, $user));
            }

            // 2. If auto-forwarded to doctor (created by Admin), notify Doctors
            if ($isAdmin) {
                $doctors = \App\Models\User::where('role', 'veterinary_doctor')->get();
                foreach ($doctors as $doctor) {
                    $doctor->notify(new \App\Notifications\DoctorForwardNotification($alert, $user));
                }
            }

            return response()->json([
                'success' => true, 
                'message' => $isAdmin 
                    ? 'Alert created and sent to Doctor directly.' 
                    : 'Emergency alert sent successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forward($id)
    {
        $alert = EmergencyAlert::findOrFail($id);
        $alert->update([
            'status' => 'forwarded_to_doctor',
            'is_forwarded' => true
        ]);

        // Notify Doctors
        $doctors = \App\Models\User::where('role', 'veterinary_doctor')->get();
        foreach ($doctors as $doctor) {
            $doctor->notify(new \App\Notifications\DoctorForwardNotification($alert, auth()->user()));
        }

        return response()->json(['success' => true, 'message' => 'Alert forwarded to Veterinary Doctor.']);
    }

    public function advise(Request $request, $id)
    {
        $request->validate([
            'advice' => 'required|string',
        ]);

        $alert = EmergencyAlert::findOrFail($id);
        $alert->update([
            'doctor_advice' => $request->advice,
            'status' => 'advised'
        ]);

        // Notify Admins
        $admins = \App\Models\User::whereIn('role', ['super_admin', 'admin', 'manager'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\DoctorAdviceNotification($alert, auth()->user()));
        }

        // Notify the Worker who created the alert
        $worker = $alert->user;
        if ($worker) {
            $worker->notify(new \App\Notifications\DoctorAdviceNotification($alert, auth()->user()));
        }

        return response()->json(['success' => true, 'message' => 'Advice sent. Admin and Worker notified.']);
    }
    
    public function resolve($id)
    {
        $alert = EmergencyAlert::findOrFail($id);
        $alert->update([
            'status' => 'resolved'
        ]);

        return response()->json(['success' => true, 'message' => 'Alert marked as resolved.']);
    }

    public function requestUrgentVisit($id)
    {
        $alert = EmergencyAlert::findOrFail($id);
        $alert->update([
            'status' => 'forwarded_to_doctor', // Keep active for Doctor
            'message' => $alert->message . " | [URGENT REQUEST: Doctor Visit Required & Admin Alerted]",
            'is_forwarded' => true // Ensure Doctor sees it
        ]);
        
        $user = auth()->user();
        $animal = $alert->animal;

        // 1. Notify Admins about the URGENT request
        $admins = \App\Models\User::whereIn('role', ['super_admin', 'admin', 'manager'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\EmergencyAlertNotification($alert, $animal, $user));
        }

        // 2. Notify Doctors about the URGENT visit required
        $doctors = \App\Models\User::where('role', 'veterinary_doctor')->get();
        foreach ($doctors as $doctor) {
            $doctor->notify(new \App\Notifications\DoctorForwardNotification($alert, $user));
        }

        return response()->json(['success' => true, 'message' => 'Urgent request sent. Doctor and Admin notified.']);
    }

    public function confirmVisit($id)
    {
        $alert = EmergencyAlert::findOrFail($id);
        $alert->update([
            'status' => 'visit_confirmed'
        ]);

        return response()->json(['success' => true, 'message' => 'Visit confirmed.']);
    }

    public function checkIn($id)
    {
        $alert = EmergencyAlert::findOrFail($id);
        $alert->update([
            'status' => 'on_site'
        ]);

        return response()->json(['success' => true, 'message' => 'Checked in at farm.']);
    }

    public function provideTreatment(Request $request, $id)
    {
        $request->validate([
            'treatment_notes' => 'required|string',
            'treatment_date' => 'nullable|string'
        ]);

        $alert = EmergencyAlert::findOrFail($id);
        
        if ($request->treatment_date === 'multiple') {
            $newEntry = $request->treatment_notes;
        } else {
            $dateStr = $request->treatment_date ? date('d M Y, h:i A', strtotime($request->treatment_date)) : date('d M Y, h:i A');
            $newEntry = "📅 Date: {$dateStr}\n💊 Treatment: " . $request->treatment_notes;
        }

        // Append to existing notes if any
        $updatedNotes = $alert->treatment_notes ? $alert->treatment_notes . "\n\n--------------------------------\n\n" . $newEntry : $newEntry;

        $alert->update([
            'status' => 'under_treatment',
            'treatment_notes' => $updatedNotes
        ]);

        return response()->json(['success' => true, 'message' => 'Treatment details added.']);
    }

    public function export()
    {
        ini_set('memory_limit', '512M');
        $user = auth()->user();
        $query = EmergencyAlert::with(['user', 'animal'])->latest();
        
        if ($user->role === 'farm_worker') {
            $query->where('user_id', $user->id);
        }
        
        $alerts = $query->get();
        $html = view('alerts.print', compact('alerts'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('emergency_alerts_report.pdf');
    }

    public function print()
    {
        $user = auth()->user();
        $query = EmergencyAlert::with(['user', 'animal'])->latest();
        
        if ($user->role === 'farm_worker') {
            $query->where('user_id', $user->id);
        }
        
        $alerts = $query->get();
        return view('alerts.print', compact('alerts'));
    }
}
