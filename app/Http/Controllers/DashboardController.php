<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Employee;
use App\Models\MilkProduction;
use App\Models\Income;
use App\Models\Expense;
use App\Models\AnimalHealthRecord;
use App\Models\EmergencyAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'veterinary_doctor') {
            return redirect()->route('doctor.dashboard');
        } elseif ($user->role === 'farm_worker') {
            return redirect()->route('worker.dashboard');
        } else {
            return redirect()->route('admin.dashboard');
        }
    }

    public function doctorIndex()
    {
        $alerts = EmergencyAlert::with(['user', 'animal'])->where('status', '!=', 'resolved')->latest()->get();
        return view('dashboard.doctor', compact('alerts'));
    }

    public function workerIndex()
    {
        $totalAnimals = Animal::where('status', 'active')->count();
        $todayMilk = \App\Models\DailyMilkRecord::whereDate('date', today())->sum('total_milk_quantity') ?? 0;
        $pendingAlerts = EmergencyAlert::where('user_id', auth()->id())->where('status', '!=', 'resolved')->count();

        return view('dashboard.worker', compact('totalAnimals', 'todayMilk', 'pendingAlerts'));
    }

    public function adminIndex()
    {
        $role = auth()->user()->role;
        $user = auth()->user();

        // Common data
        $totalAnimals = Animal::where('status', 'active')->count();
        $dailyMilk = \App\Models\DailyMilkRecord::whereDate('date', today())->sum('total_milk_quantity') ?? 0;

        // Role-specific data initialization
        $activeEmployees = 0;
        $monthlyRevenue = 0;
        $recentDailyRecords = [];
        $recentAnimals = [];

        if ($role === 'super_admin' || $role === 'admin' || $role === 'manager') {
            $activeEmployees = Employee::where('status', 'active')->count();
            $recentDailyRecords = \App\Models\DailyMilkRecord::with('recorder')->latest('date')->take(5)->get();
            $recentAnimals = Animal::latest()->take(5)->get();
        }

        if ($role === 'super_admin' || $role === 'admin') {
            $monthlyRevenue = Income::whereMonth('income_date', now()->month)
                                    ->whereYear('income_date', now()->year)
                                    ->sum('amount') ?? 0;
        }

        // New Feature: Comprehensive Overview (Daily, Weekly, Monthly, Quarterly, Annual)
        $overview = [];
        if ($role === 'super_admin' || $role === 'admin' || $role === 'manager') {
            $periods = ['daily', 'weekly', 'monthly', 'quarterly', 'annual'];
            foreach ($periods as $period) {
                switch ($period) {
                    case 'daily': 
                        $start = Carbon::today(); 
                        $end = Carbon::today(); 
                        break;
                    case 'weekly': 
                        $start = Carbon::now()->startOfWeek(); 
                        $end = Carbon::now()->endOfWeek(); 
                        break;
                    case 'monthly': 
                        $start = Carbon::now()->startOfMonth(); 
                        $end = Carbon::now()->endOfMonth(); 
                        break;
                    case 'quarterly': 
                        $start = Carbon::now()->startOfQuarter(); 
                        $end = Carbon::now()->endOfQuarter(); 
                        break;
                    case 'annual': 
                        $start = Carbon::now()->startOfYear(); 
                        $end = Carbon::now()->endOfYear(); 
                        break;
                }

                $inc = Income::whereBetween('income_date', [$start, $end])->sum('amount') ?? 0;
                $exp = Expense::whereBetween('expense_date', [$start, $end])->sum('amount') ?? 0;
                $milk = \App\Models\DailyMilkRecord::whereBetween('date', [$start, $end])->sum('total_milk_quantity') ?? 0;

                $overview[$period] = [
                    'income' => $inc,
                    'expense' => $exp,
                    'profit' => $inc - $exp,
                    'milk' => $milk
                ];
            }
        }

        try {
            // Alerts for Admin
            $pendingAlerts = [];
            if ($role === 'super_admin' || $role === 'admin' || $role === 'manager') {
                $pendingAlerts = EmergencyAlert::with('user', 'animal')
                                    ->where(function($query) {
                                        $query->where('status', 'pending')
                                              ->orWhere(function($q) {
                                                  $q->where('status', 'forwarded_to_doctor')
                                                    ->where('message', 'LIKE', '%[URGENT%');
                                              });
                                    })
                                    ->latest()
                                    ->take(5)
                                    ->get();
            }

            // Milk Production Chart
            $milkData = [];
            $milkLabels = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $milkLabels[] = $date->format('M d');
                $milkData[] = \App\Models\DailyMilkRecord::whereDate('date', $date)->sum('total_milk_quantity') ?? 0;
            }

            // Health Status
            $healthyAnimals = Animal::where('status', 'active')->count();
            $sickAnimals = 0;
            $underTreatment = 0;

            try {
                if (AnimalHealthRecord::exists()) {
                     $sickAnimals = AnimalHealthRecord::where('health_status', 'sick')->distinct('animal_id')->count();
                     $underTreatment = AnimalHealthRecord::where('health_status', 'under_treatment')->distinct('animal_id')->count();
                     // Simplified healthy count
                     $healthyAnimals = max(0, $totalAnimals - $sickAnimals - $underTreatment);
                }
            } catch (\Exception $e) {}

            // Recent Activities
            $recentActivities = AnimalHealthRecord::with('animal')->latest('check_date')->take(5)->get();

            return view('dashboard.index', compact(
                'totalAnimals',
                'activeEmployees',
                'dailyMilk',
                'monthlyRevenue',
                'milkData',
                'milkLabels',
                'healthyAnimals',
                'sickAnimals',
                'underTreatment',
                'recentActivities',
                'pendingAlerts',
                'recentDailyRecords',
                'recentAnimals',
                'overview'
            ));

        } catch (\Exception $e) {
            Log::error('Admin Dashboard Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            // Re-throw in debug mode or DD to see on screen
            // dd($e->getMessage());

            // Return safe fallback
            return view('dashboard.index', [
                'totalAnimals' => 0,
                'activeEmployees' => 0,
                'dailyMilk' => 0,
                'monthlyRevenue' => 0,
                'milkData' => [0,0,0,0,0,0,0],
                'milkLabels' => [],
                'healthyAnimals' => 0,
                'sickAnimals' => 0,
                'underTreatment' => 0,
                'recentActivities' => [],
                'pendingAlerts' => [],
                'recentDailyRecords' => [],
                'recentAnimals' => [],
                'overview' => []
            ]);
        }
    }
    public function markNotificationsAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
