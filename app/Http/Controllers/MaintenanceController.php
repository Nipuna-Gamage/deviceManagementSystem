<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MaintenanceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = MaintenanceRecord::with(['device', 'reporter']);
        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('priority')) $query->where('priority', $request->string('priority'));
        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhereHas('device', fn ($d) => $d->where('name', 'like', "%{$search}%")->orWhere('asset_tag', 'like', "%{$search}%")));
        }
        $records = $query->latest()->paginate(12)->withQueryString();
        $devices = Device::orderBy('name')->get();
        $openCount = MaintenanceRecord::whereIn('status', ['open', 'in_progress'])->count();
        $completedCount = MaintenanceRecord::where('status', 'completed')->count();
        return view('maintenance.index', compact('records', 'devices', 'openCount', 'completedCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', 'in:repair,preventive,inspection'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'scheduled_date' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);
        DB::transaction(function () use ($data, $request) {
            $record = MaintenanceRecord::create([...$data, 'reported_by' => $request->user()->id]);
            $device = Device::whereKey($data['device_id'])->lockForUpdate()->firstOrFail();
            if ($device->status !== 'assigned' && $device->status !== 'retired') {
                $device->update(['status' => 'maintenance']);
            }
        });
        return back()->with('success', 'Maintenance record created.');
    }

    public function update(Request $request, MaintenanceRecord $maintenance): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,completed,cancelled'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'completed_date' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'resolution' => ['nullable', 'string', 'max:5000'],
        ]);
        DB::transaction(function () use ($maintenance, $data) {
            $maintenance->update($data);
            if ($data['status'] === 'completed') {
                $device = Device::whereKey($maintenance->device_id)->lockForUpdate()->firstOrFail();
                $hasOpenWork = MaintenanceRecord::where('device_id', $device->id)
                    ->where('id', '!=', $maintenance->id)->whereIn('status', ['open', 'in_progress'])->exists();
                if (!$hasOpenWork && $device->status === 'maintenance') {
                    $device->update(['status' => 'available']);
                }
                $maintenance->update(['completed_date' => $data['completed_date'] ?? now()->toDateString()]);
            }
        });
        return back()->with('success', 'Maintenance record updated.');
    }
}
