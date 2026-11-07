<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DeviceAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = DeviceAssignment::with(['device', 'employee', 'assignedBy']);
        if ($request->input('status') === 'active') $query->whereNull('returned_at');
        if ($request->input('status') === 'returned') $query->whereNotNull('returned_at');
        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(fn ($q) => $q->whereHas('device', fn ($d) => $d->where('name', 'like', "%{$search}%")->orWhere('asset_tag', 'like', "%{$search}%"))
                ->orWhereHas('employee', fn ($e) => $e->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('employee_code', 'like', "%{$search}%")));
        }
        $assignments = $query->latest('assigned_at')->paginate(12)->withQueryString();
        $availableDevices = Device::where('status', 'available')->orderBy('name')->get();
        $employees = Employee::where('is_active', true)->orderBy('first_name')->get();
        $activeCount = DeviceAssignment::whereNull('returned_at')->count();
        return view('assignments.index', compact('assignments', 'availableDevices', 'employees', 'activeCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'assigned_at' => ['required', 'date'],
            'assignment_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $device = Device::whereKey($data['device_id'])->lockForUpdate()->firstOrFail();
            $employee = Employee::whereKey($data['employee_id'])->lockForUpdate()->firstOrFail();
            if ($device->status !== 'available' || $device->assignments()->whereNull('returned_at')->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['device_id' => 'This device is not available for assignment.']);
            }
            if (! $employee->is_active) {
                throw \Illuminate\Validation\ValidationException::withMessages(['employee_id' => 'This employee is inactive and cannot receive devices.']);
            }
            DeviceAssignment::create([
                ...$data,
                'assigned_by' => $request->user()?->id,
            ]);
            $device->update(['status' => 'assigned']);
        });

        return back()->with('success', 'Device assigned successfully.');
    }

    public function returnDevice(Request $request, DeviceAssignment $assignment): RedirectResponse
    {
        $data = $request->validate([
            'returned_at' => ['required', 'date', 'after_or_equal:' . $assignment->assigned_at->format('Y-m-d')],
            'return_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($assignment, $data) {
            $locked = DeviceAssignment::whereKey($assignment->id)->lockForUpdate()->firstOrFail();
            if ($locked->returned_at !== null) {
                throw \Illuminate\Validation\ValidationException::withMessages(['assignment' => 'This device has already been returned.']);
            }
            $locked->update($data);
            $locked->device()->update(['status' => 'available']);
        });

        return back()->with('success', 'Device returned successfully.');
    }
}
