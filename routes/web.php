<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeviceAssignmentController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware(['auth', 'active', 'audit'])->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/', function () {
        $totalDevices = \App\Models\Device::count();
        $availableDevices = \App\Models\Device::where('status', 'available')->count();
        $assignedDevices = \App\Models\Device::where('status', 'assigned')->count();
        $maintenanceDevices = \App\Models\Device::where('status', 'maintenance')->count();
        $recentDevices = \App\Models\Device::with('activeAssignment.employee')->latest()->limit(5)->get();
        return view('home', compact('totalDevices', 'availableDevices', 'assignedDevices', 'maintenanceDevices', 'recentDevices'));
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

    Route::view('/maintenance', 'placeholder', ['pageTitle' => 'Maintenance', 'pageDescription' => 'Track repairs, service schedules and maintenance history.'])->name('maintenance.index');
    Route::view('/reports', 'placeholder', ['pageTitle' => 'Reports', 'pageDescription' => 'Reports and exports will be available in a later phase.'])->name('reports.index');
    Route::view('/settings', 'placeholder', ['pageTitle' => 'Settings', 'pageDescription' => 'Configure your AssetFlow workspace.'])->name('settings.index');
    Route::get('/update', fn () => redirect()->route('devices.index'))->name('update');
    Route::get('/check', fn () => redirect()->route('devices.index'))->name('check');
    Route::get('/delete', fn () => redirect()->route('devices.index'))->name('delete');
});
