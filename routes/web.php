<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $totalDevices = \App\Models\Device::count();
    $availableDevices = \App\Models\Device::where('status', 'available')->count();
    $assignedDevices = \App\Models\Device::where('status', 'assigned')->count();
    $maintenanceDevices = \App\Models\Device::where('status', 'maintenance')->count();
    $recentDevices = \App\Models\Device::latest()->limit(5)->get();

    return view('home', compact('totalDevices', 'availableDevices', 'assignedDevices', 'maintenanceDevices', 'recentDevices'));
})->name('home');

Route::get('/devices/register', [DeviceController::class, 'create'])->name('register');
Route::post('/devices/register', [DeviceController::class, 'store'])->name('register.store');
Route::resource('devices', DeviceController::class)->except(['create', 'store']);

Route::view('/assignments', 'placeholder', ['pageTitle' => 'Assignments', 'pageDescription' => 'Assign devices to employees and track returns.'])->name('assignments.index');
Route::view('/employees', 'placeholder', ['pageTitle' => 'Employees', 'pageDescription' => 'Manage employees who use organization devices.'])->name('employees.index');
Route::view('/departments', 'placeholder', ['pageTitle' => 'Departments', 'pageDescription' => 'Organize your assets by department.'])->name('departments.index');
Route::view('/maintenance', 'placeholder', ['pageTitle' => 'Maintenance', 'pageDescription' => 'Track repairs, service schedules and maintenance history.'])->name('maintenance.index');
Route::view('/reports', 'placeholder', ['pageTitle' => 'Reports', 'pageDescription' => 'Reports and exports will be available in a later phase.'])->name('reports.index');
Route::view('/settings', 'placeholder', ['pageTitle' => 'Settings', 'pageDescription' => 'Configure your AssetFlow workspace.'])->name('settings.index');

Route::get('/update', fn () => redirect()->route('devices.index'))->name('update');
Route::get('/check', fn () => redirect()->route('devices.index'))->name('check');
Route::get('/delete', fn () => redirect()->route('devices.index'))->name('delete');
