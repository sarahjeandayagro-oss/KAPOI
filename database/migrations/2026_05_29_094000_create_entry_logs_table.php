<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entry_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->dateTime('entry_time');
            $table->date('date');
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'entry_time']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entry_logs');
    }
};
