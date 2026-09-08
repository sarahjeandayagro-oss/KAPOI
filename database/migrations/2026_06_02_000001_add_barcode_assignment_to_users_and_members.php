<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'barcode_id')) {
                $table->string('barcode_id')->nullable()->unique()->after('user_id');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'assigned_user_id')) {
                $table->string('assigned_user_id')->nullable()->unique()->after('barcode_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'barcode_id')) {
                $table->dropUnique(['barcode_id']);
                $table->dropColumn('barcode_id');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'assigned_user_id')) {
                $table->dropUnique(['assigned_user_id']);
                $table->dropColumn('assigned_user_id');
            }
        });
    }
};
