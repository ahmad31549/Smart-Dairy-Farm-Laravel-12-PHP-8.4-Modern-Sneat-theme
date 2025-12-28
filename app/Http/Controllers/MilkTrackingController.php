<?php

namespace App\Http\Controllers;

use App\Models\MilkProduction;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MilkTrackingController extends Controller
{
    public function index()
    {
        $todayProduction = MilkProduction::whereDate('production_date', today())
            ->sum(DB::raw('morning_quantity + evening_quantity'));

        $weekProduction = MilkProduction::whereBetween('production_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum(DB::raw('morning_quantity + evening_quantity'));

        $monthProduction = MilkProduction::whereMonth('production_date', now()->month)
            ->whereYear('production_date', now()->year)
            ->sum(DB::raw('morning_quantity + evening_quantity'));

        $avgDailyProduction = MilkProduction::whereMonth('production_date', now()->month)
            ->whereYear('production_date', now()->year)
            ->selectRaw('AVG(morning_quantity + evening_quantity) as avg_production')
            ->first()
            ->avg_production ?? 0;

        $animals = Animal::where('status', 'active')
            ->orderBy('tag_number')
            ->get();

        return view('milk-tracking.index', compact(
            'todayProduction',
            'weekProduction',
            'monthProduction',
            'avgDailyProduction',
            'animals'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'production_date' => 'required|date',
            'morning_quantity' => 'required|numeric|min:0',
            'evening_quantity' => 'required|numeric|min:0',
            'fat_content' => 'nullable|numeric|min:0|max:100',
            'protein_content' => 'nullable|numeric|min:0|max:100',
            'quality_grade' => 'required|in:A,B,C',
            'notes' => 'nullable|string'
        ]);

        $production = MilkProduction::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Milk production record saved successfully',
            'data' => $production->load('animal')
        ]);
    }

    public function getAll(Request $request)
    {
        $records = $this->filteredQuery($request)->paginate(20);

        $data = collect($records->items())->map(function ($record) {
            return [
                'id' => $record->id,
                'production_date' => $record->production_date->format('Y-m-d'),
                'animal_id' => $record->animal->tag_number ?? 'N/A',
                'animal_name' => $record->animal->name ?? 'Unknown',
                'morning_quantity' => number_format($record->morning_quantity, 2),
                'evening_quantity' => number_format($record->evening_quantity, 2),
                'total_quantity' => number_format($record->morning_quantity + $record->evening_quantity, 2),
                'fat_content' => $record->fat_content ? number_format($record->fat_content, 2) . '%' : 'N/A',
                'protein_content' => $record->protein_content ? number_format($record->protein_content, 2) . '%' : 'N/A',
                'quality_grade' => $record->quality_grade,
                'notes' => $record->notes ?? ''
            ];
        });

        return response()->json([
            'data' => $data,
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
        $record = MilkProduction::with('animal')->findOrFail($id);

        return response()->json([
            'id' => $record->id,
            'animal_id' => $record->animal_id,
            'animal_tag' => $record->animal->tag_number ?? 'N/A',
            'animal_name' => $record->animal->name ?? 'Unknown',
            'production_date' => $record->production_date->format('Y-m-d'),
            'morning_quantity' => $record->morning_quantity,
            'evening_quantity' => $record->evening_quantity,
            'total_quantity' => $record->morning_quantity + $record->evening_quantity,
            'fat_content' => $record->fat_content,
            'protein_content' => $record->protein_content,
            'quality_grade' => $record->quality_grade,
            'notes' => $record->notes ?? ''
        ]);
    }

    public function update(Request $request, $id)
    {
        $record = MilkProduction::findOrFail($id);

        $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'production_date' => 'required|date',
            'morning_quantity' => 'required|numeric|min:0',
            'evening_quantity' => 'required|numeric|min:0',
            'fat_content' => 'nullable|numeric|min:0|max:100',
            'protein_content' => 'nullable|numeric|min:0|max:100',
            'quality_grade' => 'required|in:A,B,C',
            'notes' => 'nullable|string'
        ]);

        $record->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Milk production record updated successfully',
            'data' => $record->load('animal')
        ]);
    }

    public function destroy($id)
    {
        $record = MilkProduction::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Milk production record deleted successfully'
        ]);
    }

    protected function filteredQuery(Request $request)
    {
        $query = MilkProduction::with('animal');

        if ($request->filled('quality_grade')) {
            $query->where('quality_grade', $request->string('quality_grade'));
        }

        if ($request->filled('animal')) {
            $query->whereHas('animal', function($q) use ($request) {
                $q->where('tag_number', $request->string('animal'));
            });
        }

        if ($request->filled('date_range')) {
            $range = $request->string('date_range');
            $today = now();
            if ($range === 'today') {
                $query->whereDate('production_date', $today);
            } elseif ($range === 'last-7-days') {
                $query->where('production_date', '>=', $today->copy()->subDays(7));
            } elseif ($range === 'last-30-days') {
                $query->where('production_date', '>=', $today->copy()->subDays(30));
            } elseif ($range === 'this-month') {
                $query->whereMonth('production_date', $today->month)->whereYear('production_date', $today->year);
            }
        }

        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->where(function($q) use ($term) {
                $q->whereRaw('LOWER(notes) LIKE ?', ['%' . $term . '%'])
                  ->orWhereHas('animal', function($sq) use ($term) {
                      $sq->whereRaw('LOWER(tag_number) LIKE ?', ['%' . $term . '%'])
                         ->orWhereRaw('LOWER(name) LIKE ?', ['%' . $term . '%']);
                  });
            });
        }

        return $query->latest('production_date');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        $records = $this->filteredQuery($request)->get();
        // Also fetch daily aggregated records
        $dailyRecords = \App\Models\DailyMilkRecord::latest('date')->take(50)->get(); 
        
        $html = view('milk-tracking.print', compact('records', 'dailyRecords'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('milk_production_report.pdf');
    }

    public function print(Request $request)
    {
        $records = $this->filteredQuery($request)->get();
        // Also fetch daily aggregated records
        $dailyRecords = \App\Models\DailyMilkRecord::latest('date')->take(50)->get();
        
        return view('milk-tracking.print', compact('records', 'dailyRecords'));
    }
}
