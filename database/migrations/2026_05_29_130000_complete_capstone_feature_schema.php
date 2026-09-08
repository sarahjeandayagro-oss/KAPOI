<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'contact_no')) {
                $table->string('contact_no')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('school');
            }
            if (! Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('gender');
            }
            if (! Schema::hasColumn('users', 'year_level')) {
                $table->string('year_level')->nullable()->after('birthdate');
            }
        });

        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'status')) {
                $table->string('status')->default('Available')->after('available');
            }
            if (! Schema::hasColumn('books', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('status');
            }
        });

        Schema::table('entry_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('entry_logs', 'exit_time')) {
                $table->dateTime('exit_time')->nullable()->after('entry_time');
            }
            if (! Schema::hasColumn('entry_logs', 'status')) {
                $table->string('status')->default('Checked In')->after('date');
            }
        });

        Schema::table('proposals', function (Blueprint $table) {
            if (! Schema::hasColumn('proposals', 'user_id')) {
                $table->string('user_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('proposals', 'requirements')) {
                $table->text('requirements')->nullable()->after('status');
            }
            if (! Schema::hasColumn('proposals', 'requirements_file')) {
                $table->string('requirements_file')->nullable()->after('requirements');
            }
            if (! Schema::hasColumn('proposals', 'deadline')) {
                $table->date('deadline')->nullable()->after('budget');
            }
            if (! Schema::hasColumn('proposals', 'feedback')) {
                $table->text('feedback')->nullable()->after('status');
            }
            if (! Schema::hasColumn('proposals', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('feedback')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('proposals', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }
            if (! Schema::hasColumn('proposals', 'approved_grant')) {
                $table->decimal('approved_grant', 15, 2)->default(0)->after('budget');
            }
        });

        if (! Schema::hasTable('proposal_documents')) {
            Schema::create('proposal_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnDelete();
                $table->string('document_type');
                $table->string('original_name');
                $table->string('stored_name');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('research_fund_transactions')) {
            Schema::create('research_fund_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnDelete();
                $table->string('type');
                $table->string('description');
                $table->decimal('amount', 15, 2);
                $table->string('status')->default('Pending');
                $table->date('transaction_date');
                $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('body');
                $table->string('recipients')->default('all');
                $table->timestamp('expires_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('certificate_logs')) {
            Schema::create('certificate_logs', function (Blueprint $table) {
                $table->id();
                $table->string('user_id');
                $table->string('certificate_type')->default('Library Clearance');
                $table->string('status');
                $table->text('remarks')->nullable();
                $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('backup_logs')) {
            Schema::create('backup_logs', function (Blueprint $table) {
                $table->id();
                $table->string('filename');
                $table->string('disk')->default('local');
                $table->unsignedBigInteger('size')->default(0);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('certificate_logs');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('research_fund_transactions');
        Schema::dropIfExists('proposal_documents');
    }
};
