<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalHealthRecord;
use Illuminate\Http\Request;

class AnimalHealthController extends Controller
{
    public function index()
    {
        $healthyCount = AnimalHealthRecord::where('health_status', 'healthy')->count();
        $treatmentCount = AnimalHealthRecord::where('health_status', 'treatment')->count();
        $criticalCount = AnimalHealthRecord::where('health_status', 'critical')->count();
        $vaccinationDue = 15; // This would be calculated based on vaccination schedules
        $animals = Animal::where('status', 'active')->orderBy('animal_id')->get();

        return view('animal-health.index', compact(
            'healthyCount',
            'treatmentCount',
            'criticalCount',
            'vaccinationDue',
            'animals'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'health_status' => 'required|in:healthy,treatment,critical',
            'check_date' => 'required|date',
            'next_check_date' => 'nullable|date',
            'veterinarian' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'symptoms' => 'nullable|string',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        AnimalHealthRecord::create($request->all());

        return response()->json(['success' => true, 'message' => 'Health record saved successfully']);
    }

    public function getHealthRecords(Request $request)
    {
        $records = $this->filteredQuery($request)->paginate(20);
        
        return response()->json([
            'data' => $records->items(),
            'pagination' => [
                'total' => $records->total(),
                'per_page' => $records->perPage(),
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'from' => $records->firstItem(),
                'to' => $records->lastItem()
            ]
        ]);
    }

    public function getHealthRecord($id)
    {
        $record = AnimalHealthRecord::with('animal')->findOrFail($id);
        return response()->json($record);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'health_status' => 'required|in:healthy,treatment,critical',
            'check_date' => 'required|date',
            'next_check_date' => 'nullable|date',
            'veterinarian' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'symptoms' => 'nullable|string',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $record = AnimalHealthRecord::findOrFail($id);
        $record->update($request->all());

        return response()->json(['success' => true, 'message' => 'Health record updated successfully']);
    }

    public function destroy($id)
    {
        try {
            $record = AnimalHealthRecord::findOrFail($id);
            $record->delete();

            return response()->json(['success' => true, 'message' => 'Health record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting health record: ' . $e->getMessage()], 500);
        }
    }

    protected function filteredQuery(Request $request)
    {
        $query = AnimalHealthRecord::with('animal');

        // Health status filter
        if ($request->filled('health_status')) {
            $query->where('health_status', $request->string('health_status'));
        }

        // Breed filter
        if ($request->filled('breed')) {
            $query->whereHas('animal', function ($q) use ($request) {
                $q->where('breed', $request->string('breed'));
            });
        }

        // Age group filter based on birth_date
        if ($request->filled('age_group')) {
            $ageGroup = $request->string('age_group');
            $query->whereHas('animal', function ($q) use ($ageGroup) {
                // Calculate age using birth_date; approximate by year difference
                $q->whereNotNull('birth_date');
                $now = now();
                if ($ageGroup === 'calf') {
                    // <= 1 year old
                    $q->whereDate('birth_date', '>=', $now->copy()->subYear());
                } elseif ($ageGroup === 'heifer') {
                    // > 1 year and <= 2 years
                    $q->whereDate('birth_date', '<', $now->copy()->subYear())
                      ->whereDate('birth_date', '>=', $now->copy()->subYears(2));
                } elseif ($ageGroup === 'cow') {
                    // > 2 years
                    $q->whereDate('birth_date', '<', $now->copy()->subYears(2));
                }
            });
        }

        // Search filter across animal_id, name, tag_number
        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->whereHas('animal', function ($q) use ($term) {
                $q->whereRaw('LOWER(animal_id) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(tag_number) LIKE ?', ['%' . $term . '%']);
            });
        }

        return $query->latest('check_date');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        // Export as PDF using the same table as the print view
        $records = $this->filteredQuery($request)->get();

        $html = view('animal-health.print', compact('records'))->render();

        // Create PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        return $pdf->download('animal_health_export.pdf');
    }

    public function print(Request $request)
    {
        $records = $this->filteredQuery($request)->get();
        return view('animal-health.print', compact('records'));
    }
}
