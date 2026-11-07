<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\Employee;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $deviceStats = Device::query()->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');
        $categoryStats = Device::query()->select('category', DB::raw('count(*) as total'))->groupBy('category')->orderBy('category')->get();
        $maintenanceCost = MaintenanceRecord::where('status', 'completed')->sum('cost');
        $activeEmployees = Employee::where('is_active', true)->count();
        $activeAssignments = DeviceAssignment::whereNull('returned_at')->count();
        $recentMaintenance = MaintenanceRecord::with('device')->latest()->limit(5)->get();
        $monthlyDevices = collect(range(5, 0))->map(function ($monthsAgo) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);
            return [
                'label' => $month->format('M Y'),
                'total' => Device::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count(),
            ];
        });
        $statusChartData = collect(['available', 'assigned', 'maintenance', 'retired'])
            ->map(fn (string $status): int => (int) ($deviceStats[$status] ?? 0))
            ->values()
            ->all();
        $monthlyChartLabels = $monthlyDevices->pluck('label')->values()->all();
        $monthlyChartData = $monthlyDevices->pluck('total')->values()->all();

        return view('reports.index', compact(
            'deviceStats',
            'categoryStats',
            'maintenanceCost',
            'activeEmployees',
            'activeAssignments',
            'recentMaintenance',
            'statusChartData',
            'monthlyChartLabels',
            'monthlyChartData',
        ));
    }

    public function devices(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Asset Tag', 'Name', 'Category', 'Manufacturer', 'Model', 'Serial Number', 'Status', 'Condition', 'Purchase Date', 'Warranty Expiry']);
            Device::orderBy('asset_tag')->chunk(500, function ($devices) use ($out) {
                foreach ($devices as $device) {
                    fputcsv($out, array_map(fn ($value) => $this->safeCsvCell($value), [$device->asset_tag, $device->name, $device->category, $device->manufacturer, $device->model, $device->serial_number, $device->status, $device->condition, $device->purchase_date?->format('Y-m-d'), $device->warranty_expiry?->format('Y-m-d')]));
                }
            });
            fclose($out);
        }, 'assetflow-devices-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function assignments(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Asset Tag', 'Device', 'Employee', 'Employee Code', 'Assigned At', 'Returned At', 'Assignment Notes', 'Return Notes']);
            DeviceAssignment::with(['device', 'employee'])->orderBy('assigned_at')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    fputcsv($out, array_map(fn ($value) => $this->safeCsvCell($value), [$row->device?->asset_tag, $row->device?->name, trim(($row->employee?->first_name ?? '') . ' ' . ($row->employee?->last_name ?? '')), $row->employee?->employee_code, $row->assigned_at?->format('Y-m-d H:i:s'), $row->returned_at?->format('Y-m-d H:i:s'), $row->assignment_notes, $row->return_notes]));
                }
            });
            fclose($out);
        }, 'assetflow-assignments-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function safeCsvCell(mixed $value): string
    {
        $cell = (string) ($value ?? '');
        return preg_match('/^[\\s]*[=+@\\-]/u', $cell) ? "'".$cell : $cell;
    }
}
