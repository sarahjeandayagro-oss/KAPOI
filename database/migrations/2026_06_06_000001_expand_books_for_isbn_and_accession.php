<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'isbn')) {
                $table->string('isbn')->nullable()->index()->after('author');
            }

            if (! Schema::hasColumn('books', 'publisher')) {
                $table->string('publisher')->nullable()->after('call_number');
            }

            if (! Schema::hasColumn('books', 'place_of_publication')) {
                $table->string('place_of_publication')->nullable()->after('publisher');
            }

            if (! Schema::hasColumn('books', 'pages')) {
                $table->integer('pages')->nullable()->after('place_of_publication');
            }

            if (! Schema::hasColumn('books', 'status')) {
                $table->string('status')->default('Available')->after('available');
            }

            if (! Schema::hasColumn('books', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            foreach (['isbn', 'publisher', 'place_of_publication', 'pages', 'status', 'archived_at'] as $column) {
                if (Schema::hasColumn('books', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
