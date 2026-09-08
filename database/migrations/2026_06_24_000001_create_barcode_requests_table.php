<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcode_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('old_barcode_id')->nullable()->comment('The barcode they lost or need replacement for');
            $table->string('new_barcode_id')->nullable()->comment('New barcode assigned by staff');
            $table->string('status')->default('pending')->comment('pending, approved, rejected');
            $table->text('reason')->nullable()->comment('Reason for requesting new barcode');
            $table->text('staff_notes')->nullable()->comment('Notes from staff when approving/rejecting');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('Staff who approved the request');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcode_requests');
    }
};
