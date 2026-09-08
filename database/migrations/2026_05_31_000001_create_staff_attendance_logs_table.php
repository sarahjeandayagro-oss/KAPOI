<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('full_name');
            $table->string('role')->default('staff');
            $table->date('attendance_date');
            $table->dateTime('check_in_at');
            $table->dateTime('check_out_at')->nullable();
            $table->string('status')->default('Checked In');
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'attendance_date']);
            $table->index(['attendance_date', 'check_in_at']);
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendance_logs');
    }
};
