<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\MaintenanceRecord;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalDevices = Device::count();
        $availableDevices = Device::where('status', 'available')->count();
        $assignedDevices = Device::where('status', 'assigned')->count();
        $maintenanceDevices = Device::where('status', 'maintenance')->count();

        $statusStats = Device::query()
            ->select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $monthlyStats = collect(range(5, 0))->map(function (int $monthsAgo): array {
            $month = now()->startOfMonth()->subMonths($monthsAgo);

            return [
                'label' => $month->format('M Y'),
                'total' => Device::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        });

        $recentDevices = Device::with('activeAssignment.employee')
            ->latest()
            ->limit(5)
            ->get();

        $categoryStats = Device::query()
            ->select('category')
            ->selectRaw('count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        $recentActivity = $this->recentActivity();

        return view('home', [
            'totalDevices' => $totalDevices,
            'availableDevices' => $availableDevices,
            'assignedDevices' => $assignedDevices,
            'maintenanceDevices' => $maintenanceDevices,
            'recentDevices' => $recentDevices,
            'categoryStats' => $categoryStats,
            'recentActivity' => $recentActivity,
            'dashboardStatusChartData' => collect(['available', 'assigned', 'maintenance', 'retired'])
                ->map(fn (string $status): int => (int) ($statusStats[$status] ?? 0))
                ->values()
                ->all(),
            'dashboardMonthlyLabels' => $monthlyStats->pluck('label')->values()->all(),
            'dashboardMonthlyChartData' => $monthlyStats->pluck('total')->values()->all(),
        ]);
    }

    private function recentActivity(): Collection
    {
        $registered = Device::latest()
            ->limit(5)
            ->get(['id', 'name', 'asset_tag', 'created_at'])
            ->map(fn (Device $device): array => [
                'type' => 'Device registered',
                'title' => $device->name,
                'detail' => $device->asset_tag ?: 'Asset tag not assigned',
                'occurred_at' => $device->created_at,
                'url' => route('devices.show', $device),
                'icon' => '+',
                'tone' => 'blue',
            ]);

        $assignments = DeviceAssignment::with(['device', 'employee'])
            ->latest('assigned_at')
            ->limit(5)
            ->get()
            ->map(fn (DeviceAssignment $assignment): array => [
                'type' => 'Device assigned',
                'title' => $assignment->device?->name ?? 'Unknown device',
                'detail' => 'To ' . ($assignment->employee?->full_name ?? 'Unknown employee'),
                'occurred_at' => $assignment->assigned_at,
                'url' => route('assignments.index'),
                'icon' => '⇄',
                'tone' => 'violet',
            ]);

        $returns = DeviceAssignment::with(['device', 'employee'])
            ->whereNotNull('returned_at')
            ->latest('returned_at')
            ->limit(5)
            ->get()
            ->map(fn (DeviceAssignment $assignment): array => [
                'type' => 'Device returned',
                'title' => $assignment->device?->name ?? 'Unknown device',
                'detail' => 'Returned by ' . ($assignment->employee?->full_name ?? 'Unknown employee'),
                'occurred_at' => $assignment->returned_at,
                'url' => route('assignments.index'),
                'icon' => '↩',
                'tone' => 'green',
            ]);

        $maintenance = MaintenanceRecord::with('device')
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->map(fn (MaintenanceRecord $record): array => [
                'type' => 'Maintenance updated',
                'title' => $record->title,
                'detail' => ($record->device?->name ?? 'Unknown device') . ' · ' . str_replace('_', ' ', $record->status),
                'occurred_at' => $record->updated_at,
                'url' => route('maintenance.index'),
                'icon' => '⚒',
                'tone' => 'orange',
            ]);

        return $registered
            ->concat($assignments)
            ->concat($returns)
            ->concat($maintenance)
            ->sortByDesc('occurred_at')
            ->take(8)
            ->values();
    }
}
