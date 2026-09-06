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
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('job_applications', 'resignation_status')) {
                    $table->string('resignation_status', 50)->nullable();
                }
                if (!Schema::hasColumn('job_applications', 'resignation_reason')) {
                    $table->text('resignation_reason')->nullable();
                }
                if (!Schema::hasColumn('job_applications', 'resignation_requested_at')) {
                    $table->dateTime('resignation_requested_at')->nullable();
                }
                if (!Schema::hasColumn('job_applications', 'resignation_approved_at')) {
                    $table->dateTime('resignation_approved_at')->nullable();
                }
                if (!Schema::hasColumn('job_applications', 'resignation_remarks')) {
                    $table->text('resignation_remarks')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $cols = ['resignation_status', 'resignation_reason', 'resignation_requested_at', 'resignation_approved_at', 'resignation_remarks'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('job_applications', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
