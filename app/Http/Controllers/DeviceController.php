<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function register()
    {
        // Implement register logic
        return view('register');
    }

    public function update()
    {
        // Implement update logic
        return view('update');
    }

    public function check()
    {
        // Implement check logic
        return view('check');
    }

    public function delete()
    {
        // Implement delete logic
        return view('delete');
    }
}