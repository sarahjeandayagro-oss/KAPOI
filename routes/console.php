<?php

use App\Console\Commands\ExpireWifiVouchers;
use App\Console\Commands\SendOverdueReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send overdue book reminders daily at 9:00 AM
Schedule::command(SendOverdueReminders::class)->dailyAt('9:00');

// Expire past-due WiFi vouchers every 30 minutes
Schedule::command(ExpireWifiVouchers::class)->everyThirtyMinutes();
