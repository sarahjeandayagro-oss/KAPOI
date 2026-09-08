<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('borrowing_transactions') && ! Schema::hasColumn('borrowing_transactions', 'returned_by')) {
            Schema::table('borrowing_transactions', function (Blueprint $table) {
                $table->string('returned_by')->nullable()->after('returned_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('borrowing_transactions') && Schema::hasColumn('borrowing_transactions', 'returned_by')) {
            Schema::table('borrowing_transactions', function (Blueprint $table) {
                $table->dropColumn('returned_by');
            });
        }
    }
};
