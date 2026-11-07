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
        return view('register');
    }

    public function store(Request $request)
    {
        return back()->with('status', 'Device registration will be enabled in Phase 2.');
    }

    public function update()
    {
        // Update logic here
    }

    public function check()
    {
        // Check logic here
    }

    public function delete()
    {
        // Delete logic here
    }
}