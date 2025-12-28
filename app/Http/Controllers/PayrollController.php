<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $totalPayroll = Payroll::whereMonth('payroll_month', now()->month)->sum('net_salary');
        $pendingCount = Payroll::where('status', 'pending')->count();
        $processedCount = Payroll::where('status', 'processed')->count();
        $paidCount = Payroll::where('status', 'paid')->count();

        $employees = Employee::where('status', 'active')->get();

        return view('payroll.index', compact(
            'employees',
            'totalPayroll',
            'pendingCount',
            'processedCount',
            'paidCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_month' => 'required|string',
            'basic_salary' => 'required|numeric',
            'overtime_hours' => 'nullable|numeric',
            'overtime_amount' => 'nullable|numeric',
            'bonus' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'net_salary' => 'required|numeric',
            'status' => 'nullable|in:pending,processed,paid',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        Payroll::create($request->all());

        return response()->json(['success' => true, 'message' => 'Payroll saved successfully']);
    }

    public function getPayroll()
    {
        $payroll = Payroll::with('employee')->latest()->get();
        return response()->json($payroll);
    }

    public function show($id)
    {
        $payroll = Payroll::with('employee')->findOrFail($id);
        return response()->json($payroll);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_month' => 'required|string',
            'basic_salary' => 'required|numeric',
            'overtime_hours' => 'nullable|numeric',
            'overtime_amount' => 'nullable|numeric',
            'bonus' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'net_salary' => 'required|numeric',
            'status' => 'required|in:pending,processed,paid',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $payroll = Payroll::findOrFail($id);
        $payroll->update($request->all());

        return response()->json(['success' => true, 'message' => 'Payroll updated successfully']);
    }

    public function destroy($id)
    {
        try {
            $payroll = Payroll::findOrFail($id);
            $payroll->delete();

            return response()->json(['success' => true, 'message' => 'Payroll deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting payroll: ' . $e->getMessage()], 500);
        }
    }

    protected function filteredQuery(Request $request)
    {
        $query = Payroll::with('employee');

        if ($request->filled('month')) {
            $query->where('payroll_month', $request->string('month'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->integer('employee_id'));
        }

        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->whereHas('employee', function($q) use ($term) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(employee_id) LIKE ?', ['%' . $term . '%']);
            });
        }

        return $query->latest('payroll_month');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '512M');
        $payroll = $this->filteredQuery($request)->get();
        $html = view('payroll.print', compact('payroll'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('payroll_report.pdf');
    }

    public function print(Request $request)
    {
        $payroll = $this->filteredQuery($request)->get();
        return view('payroll.print', compact('payroll'));
    }
}

