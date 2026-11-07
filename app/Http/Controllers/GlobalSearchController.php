<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Department;
use App\Models\Employee;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    public function index(Request $request): View
    {
        $term = trim((string) $request->query('q', ''));
        abort_if(mb_strlen($term) > 100, 422, 'Search query is too long.');

        $devices = collect();
        $employees = collect();
        $departments = collect();
        $maintenance = collect();

        if (mb_strlen($term) >= 2) {
            $like = '%'.$term.'%';
            $devices = Device::where(fn ($q) => $q->where('name', 'like', $like)
                ->orWhere('asset_tag', 'like', $like)->orWhere('serial_number', 'like', $like)
                ->orWhere('manufacturer', 'like', $like))->limit(8)->get();

            $employees = Employee::with('department')->where(fn ($q) => $q
                ->where('first_name', 'like', $like)->orWhere('last_name', 'like', $like)
                ->orWhere('employee_code', 'like', $like)->orWhere('email', 'like', $like))
                ->limit(8)->get();

            $departments = Department::where('name', 'like', $like)->orWhere('code', 'like', $like)->limit(8)->get();

            $maintenance = MaintenanceRecord::with('device')->where('title', 'like', $like)
                ->orWhereHas('device', fn ($q) => $q->where('asset_tag', 'like', $like)->orWhere('name', 'like', $like))
                ->limit(8)->latest()->get();
        }

        return view('search.index', compact('term', 'devices', 'employees', 'departments', 'maintenance'));
    }
}
