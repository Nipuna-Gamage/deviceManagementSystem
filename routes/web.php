<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeviceAssignmentController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $totalDevices = \App\Models\Device::count();
    $availableDevices = \App\Models\Device::where('status', 'available')->count();
    $assignedDevices = \App\Models\Device::where('status', 'assigned')->count();
    $maintenanceDevices = \App\Models\Device::where('status', 'maintenance')->count();
    $recentDevices = \App\Models\Device::with('activeAssignment.employee')->latest()->limit(5)->get();
    return view('home', compact('totalDevices', 'availableDevices', 'assignedDevices', 'maintenanceDevices', 'recentDevices'));
})->name('home');

Route::get('/devices/register', [DeviceController::class, 'create'])->name('register');
Route::post('/devices/register', [DeviceController::class, 'store'])->name('register.store');
Route::resource('devices', DeviceController::class)->except(['create', 'store']);

Route::get('/assignments', [DeviceAssignmentController::class, 'index'])->name('assignments.index');
Route::post('/assignments', [DeviceAssignmentController::class, 'store'])->name('assignments.store');
Route::patch('/assignments/{assignment}/return', [DeviceAssignmentController::class, 'returnDevice'])->name('assignments.return');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

Route::view('/maintenance', 'placeholder', ['pageTitle' => 'Maintenance', 'pageDescription' => 'Track repairs, service schedules and maintenance history.'])->name('maintenance.index');
Route::view('/reports', 'placeholder', ['pageTitle' => 'Reports', 'pageDescription' => 'Reports and exports will be available in a later phase.'])->name('reports.index');
Route::view('/settings', 'placeholder', ['pageTitle' => 'Settings', 'pageDescription' => 'Configure your AssetFlow workspace.'])->name('settings.index');
Route::get('/update', fn () => redirect()->route('devices.index'))->name('update');
Route::get('/check', fn () => redirect()->route('devices.index'))->name('check');
Route::get('/delete', fn () => redirect()->route('devices.index'))->name('delete');
