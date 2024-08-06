<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('devicemanagement/home');
});

Route::get('register', function () {
    return view('devicemanagement/register');
});

Route::get('update', function () {
    return view('devicemanagement/update');
});

Route::get('checkdevice', function () {
    return view('devicemanagement/checkdevice');
});