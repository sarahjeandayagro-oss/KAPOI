<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('wifi_vouchers')) {
            Schema::create('wifi_vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('voucher_code')->unique();
                $table->string('name')->nullable();
                $table->string('user_id')->nullable();
                $table->string('role')->nullable();
                $table->string('status')->default('Available');
                $table->unsignedInteger('duration_minutes')->default(120);
                $table->decimal('bandwidth_gb', 8, 2)->default(0);
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wifi_vouchers');
    }
};
