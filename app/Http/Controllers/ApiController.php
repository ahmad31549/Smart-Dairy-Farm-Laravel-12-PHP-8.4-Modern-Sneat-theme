<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Employee;
use App\Models\MilkProduction;
use App\Models\AnimalHealthRecord;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getDashboardStats()
    {
        return response()->json([
            'total_animals' => Animal::where('status', 'active')->count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'daily_milk' => MilkProduction::whereDate('production_date', today())->sum('total_quantity'),
            'healthy_animals' => AnimalHealthRecord::where('health_status', 'healthy')->count(),
            'treatment_animals' => AnimalHealthRecord::where('health_status', 'treatment')->count(),
            'critical_animals' => AnimalHealthRecord::where('health_status', 'critical')->count(),
        ]);
    }

    public function getAnimals()
    {
        $animals = Animal::with('healthRecords')->get();

        return response()->json($animals);
    }

    public function getAnimal($id)
    {
        $animal = Animal::with('healthRecords')->findOrFail($id);

        return response()->json($animal);
    }

    public function storeAnimal(Request $request)
    {
        $request->validate([
            'animal_id' => 'required|string|unique:animals,animal_id',
            'tag_number' => 'required|string|unique:animals,tag_number',
            'name' => 'required|string',
            'breed' => 'required|in:holstein,jersey,guernsey,ayrshire',
            'gender' => 'required|in:female,male',
            'birth_date' => 'required|date',
            'weight' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,sold,deceased'
        ]);

        $animal = Animal::create($request->all());

        // Notify Admins
        // Notify Admins
        try {
            $admins = \App\Models\User::whereIn('role', ['super_admin', 'admin', 'manager'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\NewAnimalAddedNotification($animal));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification Error: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Animal saved successfully', 'data' => $animal]);
    }

    public function updateAnimal(Request $request, $id)
    {
        $animal = Animal::findOrFail($id);

        $request->validate([
            'animal_id' => 'required|string|unique:animals,animal_id,' . $id,
            'tag_number' => 'required|string|unique:animals,tag_number,' . $id,
            'name' => 'required|string',
            'breed' => 'required|in:holstein,jersey,guernsey,ayrshire',
            'gender' => 'required|in:female,male',
            'birth_date' => 'required|date',
            'weight' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,sold,deceased'
        ]);

        $animal->update($request->all());

        return response()->json(['success' => true, 'message' => 'Animal updated successfully']);
    }

    public function destroyAnimal($id)
    {
        try {
            $animal = Animal::findOrFail($id);
            $animal->delete();

            return response()->json(['success' => true, 'message' => 'Animal deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting animal: ' . $e->getMessage()], 500);
        }
    }

    public function getMilkProductionChart()
    {
        $data = MilkProduction::selectRaw('DATE(production_date) as date, SUM(total_quantity) as total')
                             ->whereBetween('production_date', [now()->subDays(30), now()])
                             ->groupBy('date')
                             ->orderBy('date')
                             ->get();

        return response()->json($data);
    }
}
