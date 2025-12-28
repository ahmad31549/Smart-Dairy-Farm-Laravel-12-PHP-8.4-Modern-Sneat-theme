<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnimalHealthController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\VaccinationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\MilkTrackingController;
use App\Http\Controllers\QualityAnalysisController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfitAnalysisController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');
Route::post('/password/reset-request', [AuthController::class, 'passwordResetRequest'])->name('password.request');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard Routes
    // Dashboard Routes
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Role-specific Dashboards
    Route::get('/admin/dashboard', [DashboardController::class, 'adminIndex'])->name('admin.dashboard');
    Route::get('/doctor/dashboard', [DashboardController::class, 'doctorIndex'])->name('doctor.dashboard');
    Route::get('/worker/dashboard', [DashboardController::class, 'workerIndex'])->name('worker.dashboard');

    // Animal Health Module
    Route::get('/animal-health', [AnimalHealthController::class, 'index']);
    Route::post('/animal-health', [AnimalHealthController::class, 'store']);
    Route::get('/animal-health/export', [AnimalHealthController::class, 'export']);
    Route::get('/animal-health/print', [AnimalHealthController::class, 'print']);
    Route::get('/api/health-records', [AnimalHealthController::class, 'getHealthRecords']);
    Route::get('/api/animal-health/records', [AnimalHealthController::class, 'getHealthRecords']);
    Route::get('/api/animal-health/records/{id}', [AnimalHealthController::class, 'getHealthRecord']);
    Route::post('/api/animal-health', [AnimalHealthController::class, 'store']);
    Route::put('/api/animal-health/records/{id}', [AnimalHealthController::class, 'update']);
    Route::delete('/api/animal-health/records/{id}', [AnimalHealthController::class, 'destroy']);

    // Medical History Module
    Route::get('/medical-history', [MedicalHistoryController::class, 'index']);
    Route::get('/medical-history/export', [MedicalHistoryController::class, 'export']);
    Route::get('/medical-history/print', [MedicalHistoryController::class, 'print']);
    Route::get('/api/medical-history/all', [MedicalHistoryController::class, 'getAll']);
    Route::post('/api/medical-history', [MedicalHistoryController::class, 'store']);
    Route::get('/api/medical-history/{id}', [MedicalHistoryController::class, 'show']);
    Route::put('/api/medical-history/{id}', [MedicalHistoryController::class, 'update']);
    Route::delete('/api/medical-history/{id}', [MedicalHistoryController::class, 'destroy']);

    // Vaccination Module
    Route::get('/vaccination', [VaccinationController::class, 'index']);
    Route::get('/vaccination/export', [VaccinationController::class, 'export']);
    Route::get('/vaccination/print', [VaccinationController::class, 'print']);
    Route::post('/vaccination', [VaccinationController::class, 'store']);
    Route::get('/api/vaccinations', [VaccinationController::class, 'getVaccinations']);
    Route::get('/api/vaccinations/{id}', [VaccinationController::class, 'getVaccination']);
    Route::put('/api/vaccinations/{id}', [VaccinationController::class, 'update']);
    Route::delete('/api/vaccinations/{id}', [VaccinationController::class, 'destroy']);

    // Milk Production Module - Milk Tracking
    Route::get('/milk-tracking', [MilkTrackingController::class, 'index']);
    Route::get('/milk-tracking/export', [MilkTrackingController::class, 'export']);
    Route::get('/milk-tracking/print', [MilkTrackingController::class, 'print']);
    Route::post('/milk-tracking', [MilkTrackingController::class, 'store']);
    Route::get('/api/milk-tracking/all', [MilkTrackingController::class, 'getAll']);
    Route::get('/api/milk-tracking/{id}', [MilkTrackingController::class, 'show']);
    Route::put('/api/milk-tracking/{id}', [MilkTrackingController::class, 'update']);
    Route::delete('/api/milk-tracking/{id}', [MilkTrackingController::class, 'destroy']);

    // Milk Production Module - Quality Analysis
    Route::get('/quality-analysis', [QualityAnalysisController::class, 'index']);
    Route::get('/quality-analysis/export', [QualityAnalysisController::class, 'export']);
    Route::get('/quality-analysis/print', [QualityAnalysisController::class, 'print']);
    Route::post('/quality-analysis', [QualityAnalysisController::class, 'store']);
    Route::get('/api/quality-tests/all', [QualityAnalysisController::class, 'getAll']);
    Route::get('/api/quality-tests/{id}', [QualityAnalysisController::class, 'show']);
    Route::put('/api/quality-tests/{id}', [QualityAnalysisController::class, 'update']);
    Route::delete('/api/quality-tests/{id}', [QualityAnalysisController::class, 'destroy']);

    Route::get('/employees/export', [EmployeeController::class, 'export']);
    Route::get('/employees/print', [EmployeeController::class, 'print']);
    Route::resource('employees', EmployeeController::class);

    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::get('/attendance/export', [AttendanceController::class, 'export']);
    Route::get('/attendance/print', [AttendanceController::class, 'print']);
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::get('/api/attendance', [AttendanceController::class, 'getAttendance']);
    Route::post('/api/attendance', [AttendanceController::class, 'store']);
    Route::get('/api/attendance/{id}', [AttendanceController::class, 'show']);
    Route::put('/api/attendance/{id}', [AttendanceController::class, 'update']);
    Route::delete('/api/attendance/{id}', [AttendanceController::class, 'destroy']);

    Route::get('/payroll', [PayrollController::class, 'index']);
    Route::get('/payroll/export', [PayrollController::class, 'export']);
    Route::get('/payroll/print', [PayrollController::class, 'print']);
    Route::post('/payroll', [PayrollController::class, 'store']);
    Route::get('/api/payroll', [PayrollController::class, 'getPayroll']);
    Route::post('/api/payroll', [PayrollController::class, 'store']);
    Route::get('/api/payroll/{id}', [PayrollController::class, 'show']);
    Route::put('/api/payroll/{id}', [PayrollController::class, 'update']);
    Route::delete('/api/payroll/{id}', [PayrollController::class, 'destroy']);

    // Financial Module - Expenses
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::get('/expenses/export', [ExpenseController::class, 'export']);
    Route::get('/expenses/print', [ExpenseController::class, 'print']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/api/expenses', [ExpenseController::class, 'getExpenses']);
    Route::get('/api/expenses/all', [ExpenseController::class, 'getAll']);
    Route::post('/api/expenses', [ExpenseController::class, 'store']);
    Route::get('/api/expenses/{id}', [ExpenseController::class, 'show']);
    Route::put('/api/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/api/expenses/{id}', [ExpenseController::class, 'destroy']);

    // Financial Module - Income
    Route::get('/income', [IncomeController::class, 'index']);
    Route::get('/income/export', [IncomeController::class, 'export']);
    Route::get('/income/print', [IncomeController::class, 'print']);
    Route::post('/income', [IncomeController::class, 'store']);
    Route::get('/api/income', [IncomeController::class, 'getIncome']);
    Route::get('/api/income/all', [IncomeController::class, 'getAll']);
    Route::post('/api/income', [IncomeController::class, 'store']);
    Route::get('/api/income/{id}', [IncomeController::class, 'show']);
    Route::put('/api/income/{id}', [IncomeController::class, 'update']);
    Route::delete('/api/income/{id}', [IncomeController::class, 'destroy']);

    // Financial Module - Profit Analysis
    Route::get('/profit-analysis', [ProfitAnalysisController::class, 'index']);
    Route::get('/profit-analysis/export', [ProfitAnalysisController::class, 'export']);
    Route::get('/profit-analysis/print', [ProfitAnalysisController::class, 'print']);
    Route::get('/api/profit-analysis/monthly-data', [ProfitAnalysisController::class, 'getMonthlyData']);
    Route::get('/api/profit-analysis/category-breakdown', [ProfitAnalysisController::class, 'getCategoryBreakdown']);
    Route::get('/api/profit-analysis/transaction-history', [ProfitAnalysisController::class, 'getTransactionHistory']);

    // Inventory Module
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/export', [InventoryController::class, 'export']);
    Route::get('/inventory/print', [InventoryController::class, 'print']);
    Route::get('/inventory/feed-supplies', [InventoryController::class, 'feedSupplies']);
    Route::get('/inventory/medical-supplies', [InventoryController::class, 'medicalSupplies']);
    Route::get('/api/inventory', [InventoryController::class, 'getInventory']);
    Route::get('/api/inventory/all', [InventoryController::class, 'getAll']);
    Route::post('/api/inventory', [InventoryController::class, 'store']);
    Route::get('/api/inventory/{id}', [InventoryController::class, 'show']);
    Route::put('/api/inventory/{id}', [InventoryController::class, 'update']);
    Route::delete('/api/inventory/{id}', [InventoryController::class, 'destroy']);

    Route::get('/lifecycle', function () {
        return view('lifecycle.index');
    });

    // Reports Module
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/export', [ReportController::class, 'export']);
    Route::get('/reports/print', [ReportController::class, 'print']);
    Route::post('/api/reports/generate', [ReportController::class, 'generateReport']);
    Route::post('/api/reports/download', [ReportController::class, 'downloadReport']);

    // Settings & Profile
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/update-image', [ProfileController::class, 'updateImage']);
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword']);

    Route::get('/settings', function () {
        return view('settings.index');
    });

    // API Routes for AJAX calls
    Route::get('/api/dashboard-stats', [ApiController::class, 'getDashboardStats']);
    Route::get('/api/animals', [ApiController::class, 'getAnimals']);
    Route::get('/api/animals/{id}', [ApiController::class, 'getAnimal']);
    Route::post('/api/animals', [ApiController::class, 'storeAnimal']);
    Route::put('/api/animals/{id}', [ApiController::class, 'updateAnimal']);
    Route::delete('/api/animals/{id}', [ApiController::class, 'destroyAnimal']);
    Route::get('/api/milk-production-chart', [ApiController::class, 'getMilkProductionChart']);
    Route::get('/api/employees', [EmployeeController::class, 'getEmployees']);
    Route::get('/api/employees/{id}', [EmployeeController::class, 'getEmployee']);
    Route::post('/api/employees', [EmployeeController::class, 'storeApi']);
    Route::put('/api/employees/{id}', [EmployeeController::class, 'updateApi']);
    Route::delete('/api/employees/{id}', [EmployeeController::class, 'destroyApi']);

    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-image', [App\Http\Controllers\ProfileController::class, 'updateImage'])->name('profile.update-image');
    Route::post('/profile/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');


    // User Management Routes
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}/update', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::get('/users/export', [App\Http\Controllers\UserController::class, 'export']);
    Route::get('/users/print', [App\Http\Controllers\UserController::class, 'print']);
    Route::post('/users/{id}/approve', [App\Http\Controllers\UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [App\Http\Controllers\UserController::class, 'reject'])->name('users.reject');
    Route::delete('/users/{id}/delete', [App\Http\Controllers\UserController::class, 'delete'])->name('users.delete');
    Route::post('/users/{id}/role', [App\Http\Controllers\UserController::class, 'updateRole'])->name('users.updateRole');

    // Emergency Alerts
    Route::get('/alerts', [App\Http\Controllers\EmergencyAlertController::class, 'index']);
    Route::get('/alerts/export', [App\Http\Controllers\EmergencyAlertController::class, 'export']);
    Route::get('/alerts/print', [App\Http\Controllers\EmergencyAlertController::class, 'print']);
    Route::post('/api/alerts', [App\Http\Controllers\EmergencyAlertController::class, 'store']);
    Route::post('/api/alerts/{id}/forward', [App\Http\Controllers\EmergencyAlertController::class, 'forward']);
    Route::post('/api/alerts/{id}/advise', [App\Http\Controllers\EmergencyAlertController::class, 'advise']);
    Route::post('/api/alerts/{id}/resolve', [App\Http\Controllers\EmergencyAlertController::class, 'resolve']);
    Route::post('/api/alerts/{id}/request-urgent', [App\Http\Controllers\EmergencyAlertController::class, 'requestUrgentVisit']);
    Route::post('/api/alerts/{id}/confirm-visit', [App\Http\Controllers\EmergencyAlertController::class, 'confirmVisit']);
    Route::post('/api/alerts/{id}/check-in', [App\Http\Controllers\EmergencyAlertController::class, 'checkIn']);
    Route::post('/api/alerts/{id}/treatment', [App\Http\Controllers\EmergencyAlertController::class, 'provideTreatment']);

    // Daily Milk Records (Farm Worker)
    Route::post('/api/daily-milk-records', [App\Http\Controllers\DailyMilkRecordController::class, 'store']);
    Route::get('/api/daily-milk-records', [App\Http\Controllers\DailyMilkRecordController::class, 'index']);

    // Notifications
    Route::post('/notifications/mark-all-read', [DashboardController::class, 'markNotificationsAsRead'])->name('notifications.markAllRead');
});
