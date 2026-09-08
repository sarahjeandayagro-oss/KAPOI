<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barcode_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('barcode_requests', 'reason_details')) {
                $table->text('reason_details')->nullable()->after('reason');
            }

            if (!Schema::hasColumn('barcode_requests', 'proof_path')) {
                $table->string('proof_path')->nullable()->after('reason_details');
            }
        });
    }

    public function down(): void
    {
        Schema::table('barcode_requests', function (Blueprint $table) {
            if (Schema::hasColumn('barcode_requests', 'reason_details')) {
                $table->dropColumn('reason_details');
            }

            if (Schema::hasColumn('barcode_requests', 'proof_path')) {
                $table->dropColumn('proof_path');
            }
        });
    }
};
