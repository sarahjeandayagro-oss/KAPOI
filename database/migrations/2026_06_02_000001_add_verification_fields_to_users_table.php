<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'school_id')) {
                $table->string('school_id')->nullable()->after('school');
            }

            if (! Schema::hasColumn('users', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('birthdate');
            }

            if (! Schema::hasColumn('users', 'valid_id_path')) {
                $table->string('valid_id_path')->nullable()->after('profile_picture');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'valid_id_path')) {
                $table->dropColumn('valid_id_path');
            }

            if (Schema::hasColumn('users', 'school_id')) {
                $table->dropColumn('school_id');
            }

            if (Schema::hasColumn('users', 'age')) {
                $table->dropColumn('age');
            }
        });
    }
};
