<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $dailyExpenses = Expense::whereDate('expense_date', today())->sum('amount');
        $monthlyExpenses = Expense::whereMonth('expense_date', now()->month)->sum('amount');
        $yearlyExpenses = Expense::whereYear('expense_date', now()->year)->sum('amount');
        $totalExpenses = Expense::count();

        return view('expenses.index', compact(
            'dailyExpenses',
            'monthlyExpenses',
            'yearlyExpenses',
            'totalExpenses'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'vendor' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'payment_method' => 'required|in:cash,check,card,bank_transfer',
            'notes' => 'nullable|string'
        ]);

        $expense = Expense::create($request->all());

        return response()->json([
            'success' => true, 
            'message' => 'Expense record saved successfully',
            'data' => $expense
        ]);
    }

    public function getExpenses(Request $request)
    {
        $expenses = $this->filteredQuery($request)->paginate(20);
        
        return response()->json([
            'data' => $expenses->items(),
            'pagination' => [
                'total' => $expenses->total(),
                'per_page' => $expenses->perPage(),
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'from' => $expenses->firstItem(),
                'to' => $expenses->lastItem()
            ]
        ]);
    }

    public function getAll(Request $request)
    {
        return $this->getExpenses($request);
    }

    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'vendor' => 'nullable|string',
            'receipt_number' => 'nullable|string',
            'payment_method' => 'required|in:cash,check,card,bank_transfer',
            'notes' => 'nullable|string'
        ]);

        $expense->update($request->all());

        return response()->json([
            'success' => true, 
            'message' => 'Expense record updated successfully',
            'data' => $expense
        ]);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Expense record deleted successfully'
        ]);
    }

    protected function filteredQuery(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->string('payment_method'));
        }

        if ($request->filled('date_range')) {
            $range = $request->string('date_range');
            $today = now();
            if ($range === 'today') {
                $query->whereDate('expense_date', $today);
            } elseif ($range === 'this-week') {
                $query->whereBetween('expense_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($range === 'this-month') {
                $query->whereMonth('expense_date', $today->month)->whereYear('expense_date', $today->year);
            } elseif ($range === 'this-year') {
                $query->whereYear('expense_date', $today->year);
            }
        }

        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->where(function($q) use ($term) {
                $q->whereRaw('LOWER(description) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(vendor) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(receipt_number) LIKE ?', ['%' . $term . '%']);
            });
        }

        return $query->latest('expense_date');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        $expenses = $this->filteredQuery($request)->get();
        $html = view('expenses.print', compact('expenses'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('expense_report.pdf');
    }

    public function print(Request $request)
    {
        $expenses = $this->filteredQuery($request)->get();
        return view('expenses.print', compact('expenses'));
    }
}