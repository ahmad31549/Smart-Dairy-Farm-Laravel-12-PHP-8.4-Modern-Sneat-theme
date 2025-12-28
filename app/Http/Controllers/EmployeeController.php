<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $employees = Employee::latest()->paginate(10);
        $totalEmployees = Employee::where('status', 'active')->count();
        $activeToday = Attendance::whereDate('attendance_date', today())
                                ->where('status', 'present')->count();
        $onLeave = Attendance::whereDate('attendance_date', today())
                            ->where('status', 'leave')->count();
        $newThisMonth = Employee::whereMonth('hire_date', now()->month)->count();

        return view('employee.index', compact(
            'employees',
            'totalEmployees',
            'activeToday',
            'onLeave',
            'newThisMonth'
        ));
    }

    public function create()
    {
        return view('employee.create');
    }

    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        $data['employee_id'] = $this->employeeService->generateEmployeeId();
        $data['status'] = $data['status'] ?? 'active';

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        return view('employee.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('employee.edit', compact('employee'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $employee->update($request->validated());

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    protected function filteredQuery(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('position')) {
            $query->where('position', $request->string('position'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->string('department'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->where(function($q) use ($term) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(employee_id) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $term . '%']);
            });
        }

        return $query->latest();
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '512M');
        $employees = $this->filteredQuery($request)->get();
        $html = view('employee.print', compact('employees'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('employees_report.pdf');
    }

    public function print(Request $request)
    {
        $employees = $this->filteredQuery($request)->get();
        return view('employee.print', compact('employees'));
    }

    public function getEmployees(Request $request)
    {
        return response()->json(Employee::latest()->get());
    }

    public function getEmployee($id)
    {
        return response()->json(Employee::findOrFail($id));
    }

    public function storeApi(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string',
            'position' => 'required|string',
            'department' => 'required|string',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'address' => 'nullable|string',
        ]);

        $data['employee_id'] = $this->employeeService->generateEmployeeId();
        $data['status'] = 'active';

        $employee = Employee::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'data' => $employee
        ]);
    }

    public function updateApi(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'nullable|string',
            'position' => 'required|string',
            'department' => 'required|string',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'status' => 'required|in:active,inactive,terminated',
            'address' => 'nullable|string',
        ]);

        $employee->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully'
        ]);
    }

    public function destroyApi($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }
}