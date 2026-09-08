<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'available')) {
                $table->integer('available')->default(1)->after('copies');
            }

            if (! Schema::hasColumn('books', 'call_number')) {
                $table->string('call_number')->nullable()->after('barcode');
            }

            if (! Schema::hasColumn('books', 'accession_number')) {
                $table->string('accession_number')->nullable()->after('call_number');
            }

            if (! Schema::hasColumn('books', 'section_location')) {
                $table->string('section_location')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            foreach (['available', 'call_number', 'accession_number', 'section_location'] as $column) {
                if (Schema::hasColumn('books', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
