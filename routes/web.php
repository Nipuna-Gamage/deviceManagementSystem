<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DeviceController;

Route::get('/', [DeviceController::class, 'index'])->name('home');
Route::get('/register', [DeviceController::class, 'register'])->name('register');
Route::get('/update', [DeviceController::class, 'update'])->name('update');
Route::get('/check', [DeviceController::class, 'check'])->name('check');
Route::get('/delete', [DeviceController::class, 'delete'])->name('delete');