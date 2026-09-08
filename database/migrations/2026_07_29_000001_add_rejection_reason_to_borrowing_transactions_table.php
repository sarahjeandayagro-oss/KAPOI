<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('borrowing_transactions') && ! Schema::hasColumn('borrowing_transactions', 'rejection_reason')) {
            Schema::table('borrowing_transactions', function (Blueprint $table) {
                $table->string('rejection_reason')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('borrowing_transactions') && Schema::hasColumn('borrowing_transactions', 'rejection_reason')) {
            Schema::table('borrowing_transactions', function (Blueprint $table) {
                $table->dropColumn('rejection_reason');
            });
        }
    }
};
