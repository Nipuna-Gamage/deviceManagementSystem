<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Notifications\AssetAlertNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendAssetAlerts extends Command
{
    protected $signature = 'assetflow:send-alerts';
    protected $description = 'Send warranty expiry and maintenance due alerts to active administrators and managers';

    public function handle(): int
    {
        $recipients = User::where('is_active', true)->whereIn('role', ['admin', 'manager'])->get();
        if ($recipients->isEmpty()) {
            $this->info('No active admin or manager recipients found.');
            return self::SUCCESS;
        }

        $sent = 0;
        foreach ([30, 7, 0] as $days) {
            $date = now()->addDays($days)->toDateString();
            $devices = Device::whereDate('warranty_expiry', $date)->whereNotNull('warranty_expiry')->get();
            foreach ($devices as $device) {
                $key = 'warranty:'.$device->id.':'.$days;
                if ($this->alreadySent($key)) continue;
                Notification::send($recipients, new AssetAlertNotification(
                    'Warranty expiry approaching',
                    $device->asset_tag.' · '.$device->name.' warranty expires on '.$date.'.',
                    route('devices.show', $device),
                    $key,
                    'warranty'
                ));
                $sent++;
            }
        }

        $dueRecords = MaintenanceRecord::with('device')
            ->whereIn('status', ['open', 'in_progress'])
            ->whereNotNull('scheduled_date')
            ->whereDate('scheduled_date', '<=', now()->addDay()->toDateString())
            ->get();

        foreach ($dueRecords as $record) {
            $key = 'maintenance:'.$record->id.':'.$record->scheduled_date->toDateString();
            if ($this->alreadySent($key)) continue;
            Notification::send($recipients, new AssetAlertNotification(
                'Maintenance due',
                $record->title.' for '.($record->device?->asset_tag ?? 'a device').' is due on '.$record->scheduled_date->format('Y-m-d').'.',
                route('maintenance.index'),
                $key,
                'maintenance'
            ));
            $sent++;
        }

        $this->info("Sent {$sent} alert group(s).");
        return self::SUCCESS;
    }

    private function alreadySent(string $key): bool
    {
        return \Illuminate\Database\Eloquent\Relations\Relation::noConstraints(function () use ($key) {
            return \Illuminate\Notifications\DatabaseNotification::where('data->alert_key', $key)->exists();
        });
    }
}
