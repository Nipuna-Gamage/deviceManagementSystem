<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount('employees')->orderBy('name')->paginate(12);
        return view('departments.index', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        Department::create($request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:departments,name'],
            'code' => ['required', 'string', 'max:30', 'alpha_dash', 'unique:departments,code'],
            'description' => ['nullable', 'string', 'max:500'],
        ]));
        return back()->with('success', 'Department created successfully.');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $department->update($request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('departments')->ignore($department->id)],
            'code' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique('departments')->ignore($department->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]));
        return back()->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->employees()->exists()) {
            return back()->withErrors(['department' => 'Move or remove this department’s employees before deleting it.']);
        }
        $department->delete();
        return back()->with('success', 'Department deleted successfully.');
    }
}
