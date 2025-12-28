<?php

namespace App\Http\Controllers;

use App\Models\MilkQualityTest;
use App\Models\Animal;
use Illuminate\Http\Request;

class QualityAnalysisController extends Controller
{
    public function index()
    {
        $totalTests = MilkQualityTest::count();
        $passedTests = MilkQualityTest::where('test_result', 'Passed')->count();
        $failedTests = MilkQualityTest::where('test_result', 'Failed')->count();
        $pendingTests = MilkQualityTest::where('test_result', 'Pending')->count();

        $gradeA = MilkQualityTest::where('quality_grade', 'A')->count();
        $gradeB = MilkQualityTest::where('quality_grade', 'B')->count();

        $animals = Animal::where('status', 'active')
            ->orderBy('tag_number')
            ->get();

        return view('quality-analysis.index', compact(
            'totalTests',
            'passedTests',
            'failedTests',
            'pendingTests',
            'gradeA',
            'gradeB',
            'animals'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'animal_id' => 'nullable|exists:animals,id',
            'test_date' => 'required|date',
            'batch_number' => 'required|string|unique:milk_quality_tests,batch_number',
            'fat_content' => 'required|numeric|min:0|max:100',
            'protein_content' => 'required|numeric|min:0|max:100',
            'lactose_content' => 'nullable|numeric|min:0|max:100',
            'ph_level' => 'required|numeric|min:0|max:14',
            'temperature' => 'required|numeric',
            'somatic_cell_count' => 'nullable|integer|min:0',
            'quality_grade' => 'required|in:A,B,C,D',
            'test_result' => 'required|in:Passed,Failed,Pending',
            'tested_by' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $test = MilkQualityTest::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Quality test record saved successfully',
            'data' => $test->load('animal')
        ]);
    }

    public function getAll(Request $request)
    {
        $tests = $this->filteredQuery($request)->paginate(20);

        $data = collect($tests->items())->map(function ($test) {
            return [
                'id' => $test->id,
                'test_date' => $test->test_date->format('Y-m-d'),
                'animal_id' => $test->animal ? $test->animal->tag_number : 'Batch',
                'animal_name' => $test->animal ? $test->animal->name : 'Mixed',
                'batch_number' => $test->batch_number,
                'fat_content' => number_format($test->fat_content, 2),
                'protein_content' => number_format($test->protein_content, 2),
                'lactose_content' => $test->lactose_content ? number_format($test->lactose_content, 2) : 'N/A',
                'ph_level' => number_format($test->ph_level, 2),
                'temperature' => number_format($test->temperature, 1),
                'somatic_cell_count' => $test->somatic_cell_count ? number_format($test->somatic_cell_count) : 'N/A',
                'quality_grade' => $test->quality_grade,
                'test_result' => $test->test_result,
                'tested_by' => $test->tested_by ?? 'N/A',
                'notes' => $test->notes ?? ''
            ];
        });

        return response()->json([
            'data' => $data,
            'pagination' => [
                'total' => $tests->total(),
                'per_page' => $tests->perPage(),
                'current_page' => $tests->currentPage(),
                'last_page' => $tests->lastPage(),
                'from' => $tests->firstItem(),
                'to' => $tests->lastItem()
            ]
        ]);
    }

    public function show($id)
    {
        $test = MilkQualityTest::with('animal')->findOrFail($id);

        return response()->json([
            'id' => $test->id,
            'animal_id' => $test->animal_id,
            'animal_tag' => $test->animal ? $test->animal->tag_number : 'Batch',
            'animal_name' => $test->animal ? $test->animal->name : 'Mixed',
            'test_date' => $test->test_date->format('Y-m-d'),
            'batch_number' => $test->batch_number,
            'fat_content' => $test->fat_content,
            'protein_content' => $test->protein_content,
            'lactose_content' => $test->lactose_content,
            'ph_level' => $test->ph_level,
            'temperature' => $test->temperature,
            'somatic_cell_count' => $test->somatic_cell_count,
            'quality_grade' => $test->quality_grade,
            'test_result' => $test->test_result,
            'tested_by' => $test->tested_by ?? '',
            'notes' => $test->notes ?? ''
        ]);
    }

    public function update(Request $request, $id)
    {
        $test = MilkQualityTest::findOrFail($id);

        $request->validate([
            'animal_id' => 'nullable|exists:animals,id',
            'test_date' => 'required|date',
            'batch_number' => 'required|string|unique:milk_quality_tests,batch_number,' . $id,
            'fat_content' => 'required|numeric|min:0|max:100',
            'protein_content' => 'required|numeric|min:0|max:100',
            'lactose_content' => 'nullable|numeric|min:0|max:100',
            'ph_level' => 'required|numeric|min:0|max:14',
            'temperature' => 'required|numeric',
            'somatic_cell_count' => 'nullable|integer|min:0',
            'quality_grade' => 'required|in:A,B,C,D',
            'test_result' => 'required|in:Passed,Failed,Pending',
            'tested_by' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $test->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Quality test record updated successfully',
            'data' => $test->load('animal')
        ]);
    }

    public function destroy($id)
    {
        $test = MilkQualityTest::findOrFail($id);
        $test->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quality test record deleted successfully'
        ]);
    }

    protected function filteredQuery(Request $request)
    {
        $query = MilkQualityTest::with('animal');

        if ($request->filled('test_result')) {
            $query->where('test_result', $request->string('test_result'));
        }

        if ($request->filled('quality_grade')) {
            $query->where('quality_grade', $request->string('quality_grade'));
        }

        if ($request->filled('date_range')) {
            $range = $request->string('date_range');
            $today = now();
            if ($range === 'today') {
                $query->whereDate('test_date', $today);
            } elseif ($range === 'this-week') {
                $query->whereBetween('test_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($range === 'this-month') {
                $query->whereMonth('test_date', $today->month)->whereYear('test_date', $today->year);
            }
        }

        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->where(function($q) use ($term) {
                $q->whereRaw('LOWER(batch_number) LIKE ?', ['%' . $term . '%'])
                  ->orWhereHas('animal', function($sq) use ($term) {
                      $sq->whereRaw('LOWER(tag_number) LIKE ?', ['%' . $term . '%'])
                         ->orWhereRaw('LOWER(name) LIKE ?', ['%' . $term . '%']);
                  });
            });
        }

        return $query->latest('test_date');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        $tests = $this->filteredQuery($request)->get();
        $html = view('quality-analysis.print', compact('tests'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('milk_quality_report.pdf');
    }

    public function print(Request $request)
    {
        $tests = $this->filteredQuery($request)->get();
        return view('quality-analysis.print', compact('tests'));
    }
}

