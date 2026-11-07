<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Device::query();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_tag', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('manufacturer', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($category = $request->string('category')->toString()) {
            $query->where('category', $category);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $devices = $query->latest()->paginate(10)->withQueryString();
        $categories = Device::query()->select('category')->distinct()->orderBy('category')->pluck('category');

        return view('devices.index', compact('devices', 'categories'));
    }

    public function create(): View
    {
        return view('register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $device = Device::create($validated);
        $device->update(['asset_tag' => 'AST-' . str_pad((string) $device->id, 5, '0', STR_PAD_LEFT)]);

        return redirect()->route('devices.show', $device)->with('success', 'Device registered successfully.');
    }

    public function show(Device $device): View
    {
        return view('devices.show', compact('device'));
    }

    public function edit(Device $device): View
    {
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        $device->update($request->validate($this->rules($device)));

        return redirect()->route('devices.show', $device)->with('success', 'Device details updated successfully.');
    }

    public function destroy(Device $device): RedirectResponse
    {
        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }

    private function rules(?Device $device = null): array
    {
        $serialRule = 'unique:devices,serial_number';
        if ($device) {
            $serialRule .= ',' . $device->id;
        }

        return [
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', 'max:60'],
            'manufacturer' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'serial_number' => ['required', 'string', 'max:120', $serialRule],
            'status' => ['required', 'in:available,assigned,maintenance,retired'],
            'condition' => ['required', 'in:excellent,good,fair,poor'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expiry' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
