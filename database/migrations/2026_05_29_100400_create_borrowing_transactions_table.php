<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowing_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('user_id')->nullable();
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->string('barcode_id');
            $table->string('book_barcode');
            $table->date('borrow_date');
            $table->date('due_date');
            $table->timestamp('returned_at')->nullable();
            $table->string('status')->default('Borrowed');
            $table->timestamps();

            $table->index(['barcode_id', 'status']);
            $table->index(['book_barcode', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_transactions');
    }
};
