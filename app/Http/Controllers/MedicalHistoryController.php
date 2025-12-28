<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalHealthRecord;
use Illuminate\Http\Request;

class MedicalHistoryController extends Controller
{
    public function index()
    {
        $records = AnimalHealthRecord::with('animal')
            ->latest('check_date')
            ->paginate(20);

        $totalRecords = AnimalHealthRecord::count();
        $healthyCount = AnimalHealthRecord::where('health_status', 'healthy')->count();
        $treatmentCount = AnimalHealthRecord::where('health_status', 'treatment')->count();
        $criticalCount = AnimalHealthRecord::where('health_status', 'critical')->count();
        $animals = Animal::where('status', 'active')->orderBy('animal_id')->get();

        return view('medical-history.index', compact(
            'records',
            'totalRecords',
            'healthyCount',
            'treatmentCount',
            'criticalCount',
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

        return response()->json(['success' => true, 'message' => 'Medical record saved successfully']);
    }

    public function getAll(Request $request)
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

    public function show($id)
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

        return response()->json(['success' => true, 'message' => 'Medical record updated successfully']);
    }

    public function destroy($id)
    {
        try {
            $record = AnimalHealthRecord::findOrFail($id);
            $record->delete();

            return response()->json(['success' => true, 'message' => 'Medical record deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting medical record: ' . $e->getMessage()], 500);
        }
    }

    protected function filteredQuery(Request $request)
    {
        $query = AnimalHealthRecord::with('animal');

        if ($request->filled('health_status')) {
            $query->where('health_status', $request->string('health_status'));
        }

        if ($request->filled('breed')) {
            $query->whereHas('animal', function ($q) use ($request) {
                $q->where('breed', $request->string('breed'));
            });
        }

        if ($request->filled('date_range')) {
            $range = $request->string('date_range');
            if ($range === 'today') {
                $query->whereDate('check_date', today());
            } elseif ($range === 'week') {
                $query->whereBetween('check_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($range === 'month') {
                $query->whereMonth('check_date', now()->month)->whereYear('check_date', now()->year);
            } elseif ($range === 'year') {
                $query->whereYear('check_date', now()->year);
            }
        }

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
        $records = $this->filteredQuery($request)->get();
        $html = view('medical-history.print', compact('records'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('medical_history_export.pdf');
    }

    public function print(Request $request)
    {
        $records = $this->filteredQuery($request)->get();
        return view('medical-history.print', compact('records'));
    }
}

