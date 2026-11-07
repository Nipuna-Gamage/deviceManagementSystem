<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Employee::with('department')->withCount('assignments')->withCount(['assignments as active_assignments_count' => fn ($q) => $q->whereNull('returned_at')]);
        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(fn ($q) => $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('employee_code', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }
        if ($request->filled('department_id')) $query->where('department_id', $request->integer('department_id'));
        $employees = $query->orderBy('first_name')->paginate(12)->withQueryString();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('employees.index', compact('employees', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        Employee::create($request->validate([
            'employee_code' => ['required', 'string', 'max:40', 'unique:employees,employee_code'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]));
        return back()->with('success', 'Employee added successfully.');
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validate([
            'employee_code' => ['required', 'string', 'max:40', Rule::unique('employees')->ignore($employee->id)],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255', Rule::unique('employees')->ignore($employee->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]));
        return back()->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->assignments()->exists()) return back()->withErrors(['employee' => 'This employee has assignment history and cannot be deleted. Deactivate the employee instead.']);
        $employee->delete();
        return back()->with('success', 'Employee deleted successfully.');
    }
}
