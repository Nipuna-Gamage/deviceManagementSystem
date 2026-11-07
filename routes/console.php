<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// Warranty and maintenance reminders. Run the Laravel scheduler daily.
\Illuminate\Support\Facades\Schedule::command('assetflow:send-alerts')
    ->dailyAt('08:00')
    ->withoutOverlapping();
