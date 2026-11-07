<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeviceAssignmentController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware(['auth', 'active', 'audit'])->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/search', [GlobalSearchController::class, 'index'])->name('search');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    Route::get('/', function () {
        $totalDevices = \App\Models\Device::count();
        $availableDevices = \App\Models\Device::where('status', 'available')->count();
        $assignedDevices = \App\Models\Device::where('status', 'assigned')->count();
        $maintenanceDevices = \App\Models\Device::where('status', 'maintenance')->count();
        $recentDevices = \App\Models\Device::with('activeAssignment.employee')->latest()->limit(5)->get();
        $dashboardStatusStats = \App\Models\Device::query()->select('status')->selectRaw('count(*) as total')->groupBy('status')->pluck('total', 'status');
        $dashboardMonthlyStats = collect(range(5, 0))->map(function ($monthsAgo) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);
            return ['label' => $month->format('M'), 'total' => \App\Models\Device::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count()];
        });
        return view('home', compact('totalDevices', 'availableDevices', 'assignedDevices', 'maintenanceDevices', 'recentDevices', 'dashboardStatusStats', 'dashboardMonthlyStats'));
    })->name('home');

    Route::get('/devices/register', [DeviceController::class, 'create'])->name('register');
    Route::post('/devices/register', [DeviceController::class, 'store'])->middleware('role:admin,manager')->name('register.store');
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
        Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
        Route::patch('/devices/{device}', [DeviceController::class, 'update']);
        Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    });

    Route::get('/assignments', [DeviceAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [DeviceAssignmentController::class, 'store'])->middleware('role:admin,manager')->name('assignments.store');
    Route::patch('/assignments/{assignment}/return', [DeviceAssignmentController::class, 'returnDevice'])->middleware('role:admin,manager')->name('assignments.return');

    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->middleware('role:admin,manager')->name('employees.store');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->middleware('role:admin,manager')->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->middleware('role:admin')->name('employees.destroy');

    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->middleware('role:admin,manager')->name('departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->middleware('role:admin,manager')->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->middleware('role:admin')->name('departments.destroy');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
    });

    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->middleware('role:admin,manager')->name('maintenance.store');
    Route::patch('/maintenance/{maintenance}', [MaintenanceController::class, 'update'])->middleware('role:admin,manager')->name('maintenance.update');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/devices.csv', [ReportController::class, 'devices'])->name('reports.devices');
    Route::get('/reports/assignments.csv', [ReportController::class, 'assignments'])->name('reports.assignments');
    Route::view('/settings', 'placeholder', ['pageTitle' => 'Settings', 'pageDescription' => 'Configure your AssetFlow workspace.'])->name('settings.index');
    Route::get('/update', fn () => redirect()->route('devices.index'))->name('update');
    Route::get('/check', fn () => redirect()->route('devices.index'))->name('check');
    Route::get('/delete', fn () => redirect()->route('devices.index'))->name('delete');
});
