<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitAnalysisController extends Controller
{
    public function index()
    {
        // Daily stats
        $dailyIncome = Income::whereDate('income_date', today())->sum('amount');
        $dailyExpenses = Expense::whereDate('expense_date', today())->sum('amount');
        $dailyProfit = $dailyIncome - $dailyExpenses;

        // Monthly stats
        $monthlyIncome = Income::whereMonth('income_date', now()->month)
            ->whereYear('income_date', now()->year)
            ->sum('amount');
        $monthlyExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        $monthlyProfit = $monthlyIncome - $monthlyExpenses;

        // Yearly stats
        $yearlyIncome = Income::whereYear('income_date', now()->year)->sum('amount');
        $yearlyExpenses = Expense::whereYear('expense_date', now()->year)->sum('amount');
        $yearlyProfit = $yearlyIncome - $yearlyExpenses;

        // Total stats
        $totalIncome = Income::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $totalProfit = $totalIncome - $totalExpenses;

        // Profit margin percentage
        $profitMargin = $totalIncome > 0 ? ($totalProfit / $totalIncome) * 100 : 0;

        return view('profit-analysis.index', compact(
            'dailyIncome',
            'dailyExpenses',
            'dailyProfit',
            'monthlyIncome',
            'monthlyExpenses',
            'monthlyProfit',
            'yearlyIncome',
            'yearlyExpenses',
            'yearlyProfit',
            'totalIncome',
            'totalExpenses',
            'totalProfit',
            'profitMargin'
        ));
    }

    public function getMonthlyData(Request $request)
    {
        $year = $request->input('year', now()->year);

        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $income = Income::whereMonth('income_date', $month)
                ->whereYear('income_date', $year)
                ->sum('amount');

            $expense = Expense::whereMonth('expense_date', $month)
                ->whereYear('expense_date', $year)
                ->sum('amount');

            $monthlyData[] = [
                'month' => date('M', mktime(0, 0, 0, $month, 1)),
                'income' => (float) $income,
                'expense' => (float) $expense,
                'profit' => (float) ($income - $expense)
            ];
        }

        return response()->json($monthlyData);
    }

    public function getCategoryBreakdown(Request $request)
    {
        $period = $request->input('period', 'month'); // day, week, month, year, all

        $expenseQuery = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category');

        $incomeQuery = Income::select('source as category', DB::raw('SUM(amount) as total'))
            ->groupBy('source');

        // Apply date filters
        if ($period === 'day') {
            $expenseQuery->whereDate('expense_date', today());
            $incomeQuery->whereDate('income_date', today());
        } elseif ($period === 'week') {
            $expenseQuery->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()]);
            $incomeQuery->whereBetween('income_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $expenseQuery->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year);
            $incomeQuery->whereMonth('income_date', now()->month)
                ->whereYear('income_date', now()->year);
        } elseif ($period === 'year') {
            $expenseQuery->whereYear('expense_date', now()->year);
            $incomeQuery->whereYear('income_date', now()->year);
        }

        $expenseBreakdown = $expenseQuery->get()->toArray();
        $incomeBreakdown = $incomeQuery->get()->toArray();

        return response()->json([
            'expenses' => $expenseBreakdown,
            'income' => $incomeBreakdown
        ]);
    }

    public function getTransactionHistory(Request $request)
    {
        $type = $request->input('type', 'all'); // all, income, expense
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $transactions = [];

        if ($type === 'all' || $type === 'income') {
            $incomes = Income::when($startDate, function($query) use ($startDate) {
                return $query->whereDate('income_date', '>=', $startDate);
            })
            ->when($endDate, function($query) use ($endDate) {
                return $query->whereDate('income_date', '<=', $endDate);
            })
            ->orderBy('income_date', 'desc')
            ->get()
            ->map(function($income) {
                return [
                    'id' => $income->id,
                    'type' => 'income',
                    'date' => $income->income_date,
                    'category' => $income->source,
                    'description' => $income->description,
                    'amount' => $income->amount,
                    'customer' => $income->customer,
                    'notes' => $income->notes
                ];
            });

            $transactions = array_merge($transactions, $incomes->toArray());
        }

        if ($type === 'all' || $type === 'expense') {
            $expenses = Expense::when($startDate, function($query) use ($startDate) {
                return $query->whereDate('expense_date', '>=', $startDate);
            })
            ->when($endDate, function($query) use ($endDate) {
                return $query->whereDate('expense_date', '<=', $endDate);
            })
            ->orderBy('expense_date', 'desc')
            ->get()
            ->map(function($expense) {
                return [
                    'id' => $expense->id,
                    'type' => 'expense',
                    'date' => $expense->expense_date,
                    'category' => $expense->category,
                    'description' => $expense->description,
                    'amount' => $expense->amount,
                    'vendor' => $expense->vendor,
                    'payment_method' => $expense->payment_method,
                    'notes' => $expense->notes
                ];
            });

            $transactions = array_merge($transactions, $expenses->toArray());
        }

        // Sort by date descending
        usort($transactions, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return response()->json($transactions);
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '512M');
        $data = $this->filteredSummary($request);
        $html = view('profit-analysis.print', $data)->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('profit_analysis_report.pdf');
    }

    public function print(Request $request)
    {
        $data = $this->filteredSummary($request);
        return view('profit-analysis.print', $data);
    }

    protected function filteredSummary(Request $request)
    {
        $period = $request->input('period', 'all');
        $year = $request->input('year', now()->year);

        $incomeQuery = Income::query();
        $expenseQuery = Expense::query();

        if ($period === 'month') {
            $incomeQuery->whereMonth('income_date', now()->month)->whereYear('income_date', now()->year);
            $expenseQuery->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year);
        } elseif ($period === 'year') {
            $incomeQuery->whereYear('income_date', $year);
            $expenseQuery->whereYear('expense_date', $year);
        }

        $income = $incomeQuery->sum('amount');
        $expenses = $expenseQuery->sum('amount');
        $profit = $income - $expenses;
        $margin = $income > 0 ? ($profit / $income) * 100 : 0;

        $incomeTransactions = $incomeQuery->orderBy('income_date', 'desc')->get();
        $expenseTransactions = $expenseQuery->orderBy('expense_date', 'desc')->get();

        return [
            'income' => $income,
            'expenses' => $expenses,
            'profit' => $profit,
            'margin' => $margin,
            'incomeTransactions' => $incomeTransactions,
            'expenseTransactions' => $expenseTransactions,
            'period' => $period,
            'year' => $year
        ];
    }
}

