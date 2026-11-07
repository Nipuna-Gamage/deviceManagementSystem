<?php

namespace App\Console\Commands;

use Database\Seeders\AssetFlowDemoSeeder;
use Illuminate\Console\Command;

class AssetFlowDemo extends Command
{
    protected $signature = 'assetflow:demo
                            {--seed : Insert fictional demo records}
                            {--remove : Remove only marked fictional demo records}';

    protected $description = 'Seed or remove safe, marked AssetFlow demo data';

    public function handle(): int
    {
        if ($this->option('seed') === $this->option('remove')) {
            $this->error('Specify exactly one of --seed or --remove.');
            return self::INVALID;
        }

        $seeder = new AssetFlowDemoSeeder();

        if ($this->option('remove')) {
            $counts = $seeder->remove();
            $this->info('Marked demo records removed.');
            $this->table(['Users', 'Departments', 'Employees', 'Devices'], [[
                $counts['users'], $counts['departments'], $counts['employees'], $counts['devices'],
            ]]);
            return self::SUCCESS;
        }

        $result = $seeder->run();
        if ($result['status'] === 'skipped') {
            $this->warn('Marked AssetFlow demo data already exists. No records were inserted.');
            return self::SUCCESS;
        }

        $this->info('AssetFlow demo data inserted into the configured database.');
        $this->table(['Users', 'Departments', 'Employees', 'Devices', 'Assignments', 'Maintenance', 'Audit logs', 'Notifications'], [[
            $result['users'], $result['departments'], $result['employees'], $result['devices'],
            $result['assignments'], $result['maintenance'], $result['audits'], $result['notifications'],
        ]]);
        $this->line('Demo login password: AssetFlowDemo!2026');
        return self::SUCCESS;
    }
}
