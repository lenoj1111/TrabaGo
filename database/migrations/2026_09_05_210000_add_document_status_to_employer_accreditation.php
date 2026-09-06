<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('employer_accreditation')) {
            Schema::table('employer_accreditation', function (Blueprint $table) {
                if (!Schema::hasColumn('employer_accreditation', 'document_status')) {
                    $table->string('document_status', 50)->default('pending')->after('status');
                }
                if (!Schema::hasColumn('employer_accreditation', 'document_incomplete_reason')) {
                    $table->text('document_incomplete_reason')->nullable()->after('document_status');
                }
                if (!Schema::hasColumn('employer_accreditation', 'document_verified_at')) {
                    $table->timestamp('document_verified_at')->nullable()->after('document_incomplete_reason');
                }
                if (!Schema::hasColumn('employer_accreditation', 'document_verified_by')) {
                    $table->unsignedBigInteger('document_verified_by')->nullable()->after('document_verified_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employer_accreditation')) {
            Schema::table('employer_accreditation', function (Blueprint $table) {
                $columns = ['document_status', 'document_incomplete_reason', 'document_verified_at', 'document_verified_by'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('employer_accreditation', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
