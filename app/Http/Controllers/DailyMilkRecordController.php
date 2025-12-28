<?php

namespace App\Http\Controllers;

use App\Models\DailyMilkRecord;
use App\Models\Animal;
use Illuminate\Http\Request;

class DailyMilkRecordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'total_milk_quantity' => 'required|numeric|min:0',
            'total_buffaloes_milked' => 'required|integer|min:0',
            'sick_animals' => 'nullable|integer|min:0',
            'pregnant_animals' => 'nullable|integer|min:0',
            'male_animals' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        $herdSize = $request->total_buffaloes_milked + (int)$request->sick_animals + (int)$request->pregnant_animals + (int)$request->male_animals;

        $record = DailyMilkRecord::create([
            'date' => $request->date,
            'total_milk_quantity' => $request->total_milk_quantity,
            'total_buffaloes_milked' => $request->total_buffaloes_milked,
            'sick_animals' => $request->filled('sick_animals') ? $request->sick_animals : 0,
            'pregnant_animals' => $request->filled('pregnant_animals') ? $request->pregnant_animals : 0,
            'male_animals' => $request->filled('male_animals') ? $request->male_animals : 0,
            'total_herd_size' => $herdSize,
            'recorded_by' => auth()->id(),
            'notes' => $request->notes
        ]);

        // Notify Admins
        $admins = \App\Models\User::whereIn('role', ['super_admin', 'admin', 'manager'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewDailyMilkRecordNotification($record));
        }

        return response()->json(['success' => true, 'message' => 'Daily milk record saved successfully.']);
    }

    public function index()
    {
        $records = DailyMilkRecord::with('recorder')->latest('date')->get();
        return response()->json($records);
    }
}
