<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wifi_vouchers', function (Blueprint $table) {
            if (! Schema::hasColumn('wifi_vouchers', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wifi_vouchers', function (Blueprint $table) {
            if (Schema::hasColumn('wifi_vouchers', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
