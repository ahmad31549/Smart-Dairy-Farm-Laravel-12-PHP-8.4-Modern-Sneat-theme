<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::where('status', 'active')->count();
        $presentToday = Attendance::whereDate('attendance_date', today())
                                 ->where('status', 'present')->count();
        $absentToday = Attendance::whereDate('attendance_date', today())
                                ->where('status', 'absent')->count();
        $lateToday = Attendance::whereDate('attendance_date', today())
                              ->where('status', 'late')->count();
        $onLeaveToday = Attendance::whereDate('attendance_date', today())
                                 ->where('status', 'leave')->count();

        $employees = Employee::where('status', 'active')->get();

        return view('attendance.index', compact(
            'presentToday',
            'absentToday',
            'lateToday',
            'onLeaveToday',
            'totalEmployees',
            'employees'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,leave',
            'notes' => 'nullable|string'
        ]);

        Attendance::create($request->all());

        return response()->json(['success' => true, 'message' => 'Attendance record saved successfully']);
    }

    public function getAttendance(Request $request)
    {
        $attendance = $this->filteredQuery($request)->paginate(20);
        
        return response()->json([
            'data' => $attendance->items(),
            'pagination' => [
                'total' => $attendance->total(),
                'per_page' => $attendance->perPage(),
                'current_page' => $attendance->currentPage(),
                'last_page' => $attendance->lastPage(),
                'from' => $attendance->firstItem(),
                'to' => $attendance->lastItem()
            ]
        ]);
    }

    public function show($id)
    {
        $attendance = Attendance::with('employee')->findOrFail($id);
        return response()->json($attendance);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,leave',
            'notes' => 'nullable|string'
        ]);

        $attendance = Attendance::findOrFail($id);
        $attendance->update($request->all());

        return response()->json(['success' => true, 'message' => 'Attendance updated successfully']);
    }

    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->delete();

            return response()->json(['success' => true, 'message' => 'Attendance deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting attendance: ' . $e->getMessage()], 500);
        }
    }

    protected function filteredQuery(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('department')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department', $request->string('department'));
            });
        }

        if ($request->filled('date_range')) {
            $range = $request->string('date_range');
            $today = now();
            if ($range === 'today') {
                $query->whereDate('attendance_date', $today);
            } elseif ($range === 'yesterday') {
                $query->whereDate('attendance_date', $today->copy()->subDay());
            } elseif ($range === 'this-week') {
                $query->whereBetween('attendance_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($range === 'this-month') {
                $query->whereMonth('attendance_date', $today->month)->whereYear('attendance_date', $today->year);
            }
        }

        if ($request->filled('search')) {
            $term = strtolower($request->string('search'));
            $query->whereHas('employee', function ($q) use ($term) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $term . '%'])
                  ->orWhereRaw('LOWER(employee_id) LIKE ?', ['%' . $term . '%']);
            });
        }

        return $query->latest('attendance_date');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit', '1024M');
        $attendance = $this->filteredQuery($request)->get();
        $html = view('attendance.print', compact('attendance'))->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download('attendance_report.pdf');
    }

    public function print(Request $request)
    {
        $attendance = $this->filteredQuery($request)->get();
        return view('attendance.print', compact('attendance'));
    }
}
