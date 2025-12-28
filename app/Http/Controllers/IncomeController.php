<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $dailyIncome = Income::whereDate('income_date', today())->sum('amount');
        $monthlyIncome = Income::whereMonth('income_date', now()->month)->sum('amount');
        $yearlyIncome = Income::whereYear('income_date', now()->year)->sum('amount');
        $totalIncome = Income::count();

        return view('income.index', compact(
            'dailyIncome',
            'monthlyIncome',
            'yearlyIncome',
            'totalIncome'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'source' => 'required|in:milk_sales,animal_sales,other',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
            'customer' => 'nullable|string',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $income = Income::create($request->all());

        return response()->json([
            'success' => true, 
            'message' => 'Income record saved successfully',
            'data' => $income
        ]);
    }

    public function getIncome(Request $request)
    {
        $income = $this->filteredQuery($request)->paginate(20);
        
        return response()->json([
            'data' => $income->items(),
            'pagination' => [
                'total' => $income->total(),
                'per_page' => $income->perPage(),
                'current_page' => $income->currentPage(),
                'last_page' => $income->lastPage(),
                'from' => $income->firstItem(),
                'to' => $income->lastItem()
            ]
        ]);
    }

    public function getAll(Request $request)
    {
        return $this->getIncome($request);
    }

    public function show($id)
    {
        $income = Income::findOrFail($id);
        return response()->json($income);
    }

    public function update(Request $request, $id)
    {
        $income = Income::findOrFail($id);

        $request->validate([
            'source' => 'required|in:milk_sales,animal_sales,other',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
            'customer' => 'nullable|string',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $income->update($request->all());

        return response()->json([
            'success' => true, 
            'message' => 'Income record updated successfully',
            'data' => $income
        ]);
    }

    public function destroy($id)
    {
        $income = Income::findOrFail($id);
        $income->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Income record deleted successfully'
        ]);
    }

    protected function filteredQuery(Request $request)
    {
        $query = Income::query();

        if ($request->filled('source')) {
            $query->where('source', $request->string('source'));
        }

        if ($request->filled('date_range')) {
            $range = $request->string('date_range');
            $today = now();
            if ($range === 'today') {
                $query->whereDate('income_date', $today);
            } elseif ($range === 'this-week') {
                $query->whereBetween('income_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($range === 'this-month') {
                $query->whereMonth('income_date', $today->month)->whereYear('income_date', $today->year);
            } elseif ($range === 'this-year') {
                $query->whereYear('income_date', $today->year);
            }
        }

        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->where(function($q) use ($term) {
                $q->whereRaw('LOWER(description) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(customer) LIKE ?', ['%' . $term . '%']);
            });
        }

        return $query->latest('income_date');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        $incomes = $this->filteredQuery($request)->get();
        $html = view('income.print', compact('incomes'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('income_report.pdf');
    }

    public function print(Request $request)
    {
        $incomes = $this->filteredQuery($request)->get();
        return view('income.print', compact('incomes'));
    }
}