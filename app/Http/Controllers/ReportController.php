<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalHealthRecord;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Get counts for different report types
        $totalAnimals = Animal::count();
        $totalEmployees = Employee::count();
        $totalExpenses = Expense::count();
        $totalIncome = Income::count();
        $totalHealthRecords = AnimalHealthRecord::count();

        return view('reports.index', compact(
            'totalAnimals',
            'totalEmployees',
            'totalExpenses',
            'totalIncome',
            'totalHealthRecords'
        ));
    }

    public function generateReport(Request $request)
    {
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $format = $request->input('format', 'summary'); // summary or detailed

        $reportData = [];

        switch ($reportType) {
            case 'animal_health':
                $reportData = $this->generateAnimalHealthReport($startDate, $endDate, $format);
                break;
            case 'milk_production':
                $reportData = $this->generateMilkProductionReport($startDate, $endDate, $format);
                break;
            case 'employee':
                $reportData = $this->generateEmployeeReport($startDate, $endDate, $format);
                break;
            case 'financial':
                $reportData = $this->generateFinancialReport($startDate, $endDate, $format);
                break;
            case 'inventory':
                $reportData = $this->generateInventoryReport($format);
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid report type'], 400);
        }

        return response()->json([
            'success' => true,
            'report_type' => $reportType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'format' => $format,
            'data' => $reportData
        ]);
    }

    private function generateAnimalHealthReport($startDate, $endDate, $format)
    {
        $query = AnimalHealthRecord::with('animal');

        if ($startDate && $endDate) {
            $query->whereBetween('check_date', [$startDate, $endDate]);
        }

        $records = $query->get();

        $summary = [
            'total_checkups' => $records->count(),
            'healthy' => $records->where('health_status', 'healthy')->count(),
            'treatment' => $records->where('health_status', 'treatment')->count(),
            'critical' => $records->where('health_status', 'critical')->count(),
        ];

        if ($format === 'detailed') {
            $summary['records'] = $records->map(function($record) {
                return [
                    'animal_id' => $record->animal->animal_id,
                    'animal_name' => $record->animal->name,
                    'breed' => $record->animal->breed,
                    'health_status' => $record->health_status,
                    'check_date' => $record->check_date->format('Y-m-d'),
                    'symptoms' => $record->symptoms,
                    'treatment' => $record->treatment,
                    'veterinarian' => $record->veterinarian,
                ];
            });
        }

        return $summary;
    }

    private function generateMilkProductionReport($startDate, $endDate, $format)
    {
        $query = DB::table('milk_production')->join('animals', 'milk_production.animal_id', '=', 'animals.id');

        if ($startDate && $endDate) {
            $query->whereBetween('production_date', [$startDate, $endDate]);
        }

        $records = $query->select('milk_production.*', 'animals.animal_id', 'animals.name')->get();

        $summary = [
            'total_production' => $records->sum('quantity'),
            'average_daily' => $records->avg('quantity'),
            'total_sessions' => $records->count(),
            'morning_sessions' => $records->where('session', 'morning')->count(),
            'evening_sessions' => $records->where('session', 'evening')->count(),
        ];

        if ($format === 'detailed') {
            $summary['records'] = $records;
        }

        return $summary;
    }

    private function generateEmployeeReport($startDate, $endDate, $format)
    {
        $employees = Employee::withCount(['attendance', 'payrolls'])->get();

        $payrollQuery = Payroll::query();

        if ($startDate && $endDate) {
            $payrollQuery->whereBetween('payroll_month', [$startDate, $endDate]);
        }

        $totalPayroll = $payrollQuery->sum('net_salary');

        $summary = [
            'total_employees' => $employees->count(),
            'active_employees' => $employees->where('status', 'active')->count(),
            'total_payroll' => $totalPayroll,
            'by_department' => $employees->groupBy('department')->map->count(),
        ];

        if ($format === 'detailed') {
            $summary['employees'] = $employees->map(function($emp) {
                return [
                    'employee_id' => $emp->employee_id,
                    'name' => $emp->name,
                    'position' => $emp->position,
                    'department' => $emp->department,
                    'salary' => $emp->salary,
                    'status' => $emp->status,
                ];
            });
        }

        return $summary;
    }

    private function generateFinancialReport($startDate, $endDate, $format)
    {
        $incomeQuery = Income::query();
        $expenseQuery = Expense::query();

        if ($startDate && $endDate) {
            $incomeQuery->whereBetween('income_date', [$startDate, $endDate]);
            $expenseQuery->whereBetween('expense_date', [$startDate, $endDate]);
        }

        $totalIncome = $incomeQuery->sum('amount');
        $totalExpenses = $expenseQuery->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        $summary = [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'profit_margin' => $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0,
            'income_by_source' => $incomeQuery->get()->groupBy('source')->map(function($items) {
                return $items->sum('amount');
            }),
            'expenses_by_category' => $expenseQuery->get()->groupBy('category')->map(function($items) {
                return $items->sum('amount');
            }),
        ];

        if ($format === 'detailed') {
            $summary['income_records'] = $incomeQuery->get();
            $summary['expense_records'] = $expenseQuery->get();
        }

        return $summary;
    }

    private function generateInventoryReport($format)
    {
        $inventory = Inventory::all();

        $summary = [
            'total_items' => $inventory->count(),
            'low_stock_items' => $inventory->filter(function($item) {
                return $item->quantity <= $item->reorder_level;
            })->count(),
            'total_value' => $inventory->sum(function($item) {
                return $item->quantity * $item->unit_price;
            }),
            'by_category' => $inventory->groupBy('category')->map->count(),
        ];

        if ($format === 'detailed') {
            $summary['items'] = $inventory->map(function($item) {
                return [
                    'item_name' => $item->item_name,
                    'category' => $item->category,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'total_value' => $item->quantity * $item->unit_price,
                    'reorder_level' => $item->reorder_level,
                    'status' => $item->quantity <= $item->reorder_level ? 'Low Stock' : 'In Stock',
                ];
            });
        }

        return $summary;
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $format = $request->input('format', 'detailed');

        $reportData = $this->getReportData($reportType, $startDate, $endDate, $format);
        
        $html = view('reports.print', [
            'reportType' => $reportType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'format' => $format,
            'data' => $reportData
        ])->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        
        $filename = str_replace('_', '-', $reportType) . '-report-' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function print(Request $request)
    {
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $format = $request->input('format', 'detailed');

        $reportData = $this->getReportData($reportType, $startDate, $endDate, $format);
        
        return view('reports.print', [
            'reportType' => $reportType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'format' => $format,
            'data' => $reportData
        ]);
    }

    private function getReportData($reportType, $startDate, $endDate, $format)
    {
        switch ($reportType) {
            case 'animal_health':
                return $this->generateAnimalHealthReport($startDate, $endDate, $format);
            case 'milk_production':
                return $this->generateMilkProductionReport($startDate, $endDate, $format);
            case 'employee':
                return $this->generateEmployeeReport($startDate, $endDate, $format);
            case 'financial':
                return $this->generateFinancialReport($startDate, $endDate, $format);
            case 'inventory':
                return $this->generateInventoryReport($format);
            default:
                return [];
        }
    }

    public function downloadReport(Request $request)
    {
        // This would handle PDF/Excel generation
        // For now, return JSON
        return response()->json([
            'success' => true,
            'message' => 'Report download functionality'
        ]);
    }
}

