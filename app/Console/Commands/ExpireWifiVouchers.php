<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireWifiVouchers extends Command
{
    protected $signature = 'library:expire-wifi-vouchers';
    protected $description = 'Expire WiFi vouchers that have passed their expiration date';

    public function handle(): int
    {
        $count = DB::table('wifi_vouchers')
            ->where('status', 'Available')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update([
                'status' => 'Expired',
                'updated_at' => now(),
            ]);

        $this->info("Expired {$count} WiFi vouchers.");
        return Command::SUCCESS;
    }
}
