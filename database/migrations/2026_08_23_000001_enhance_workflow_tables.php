<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance employer_accreditation
        if (Schema::hasTable('employer_accreditation')) {
            Schema::table('employer_accreditation', function (Blueprint $table) {
                if (!Schema::hasColumn('employer_accreditation', 'status')) {
                    $table->string('status', 50)->default('submitted_to_jpo')->nullable();
                }
                if (!Schema::hasColumn('employer_accreditation', 'supervisor_approved')) {
                    $table->boolean('supervisor_approved')->default(0)->nullable();
                }
                if (!Schema::hasColumn('employer_accreditation', 'supervisor_approved_at')) {
                    $table->dateTime('supervisor_approved_at')->nullable();
                }
                if (!Schema::hasColumn('employer_accreditation', 'supervisor_remarks')) {
                    $table->text('supervisor_remarks')->nullable();
                }
                if (!Schema::hasColumn('employer_accreditation', 'jpo_id')) {
                    $table->unsignedBigInteger('jpo_id')->nullable();
                }
                if (!Schema::hasColumn('employer_accreditation', 'supervisor_id')) {
                    $table->unsignedBigInteger('supervisor_id')->nullable();
                }
            });
        }

        // 2. Enhance placement_reports
        if (Schema::hasTable('placement_reports')) {
            Schema::table('placement_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('placement_reports', 'jpo_evaluated')) {
                    $table->boolean('jpo_evaluated')->default(0)->nullable();
                }
                if (!Schema::hasColumn('placement_reports', 'jpo_evaluated_at')) {
                    $table->dateTime('jpo_evaluated_at')->nullable();
                }
                if (!Schema::hasColumn('placement_reports', 'jpo_remarks')) {
                    $table->text('jpo_remarks')->nullable();
                }
            });
        }

        // 3. Enhance job_applications
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('job_applications', 'jpo_notes')) {
                    $table->text('jpo_notes')->nullable();
                }
                if (!Schema::hasColumn('job_applications', 'jpo_evaluated_at')) {
                    $table->dateTime('jpo_evaluated_at')->nullable();
                }
                if (!Schema::hasColumn('job_applications', 'jpo_id')) {
                    $table->unsignedBigInteger('jpo_id')->nullable();
                }
            });
        }

        // 4. Enhance training_enrollments (Figure 12)
        if (Schema::hasTable('training_enrollments')) {
            Schema::table('training_enrollments', function (Blueprint $table) {
                if (!Schema::hasColumn('training_enrollments', 'score')) {
                    $table->decimal('score', 5, 2)->nullable();
                }
                if (!Schema::hasColumn('training_enrollments', 'passed')) {
                    $table->boolean('passed')->default(0)->nullable();
                }
                if (!Schema::hasColumn('training_enrollments', 'certificate_no')) {
                    $table->string('certificate_no', 100)->nullable();
                }
                if (!Schema::hasColumn('training_enrollments', 'certificate_issued')) {
                    $table->boolean('certificate_issued')->default(0)->nullable();
                }
                if (!Schema::hasColumn('training_enrollments', 'certificate_issued_at')) {
                    $table->dateTime('certificate_issued_at')->nullable();
                }
                if (!Schema::hasColumn('training_enrollments', 'trainer_feedback')) {
                    $table->text('trainer_feedback')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employer_accreditation')) {
            Schema::table('employer_accreditation', function (Blueprint $table) {
                $columns = ['status', 'supervisor_approved', 'supervisor_approved_at', 'supervisor_remarks', 'jpo_id', 'supervisor_id'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('employer_accreditation', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('placement_reports')) {
            Schema::table('placement_reports', function (Blueprint $table) {
                $columns = ['jpo_evaluated', 'jpo_evaluated_at', 'jpo_remarks'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('placement_reports', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $columns = ['jpo_notes', 'jpo_evaluated_at', 'jpo_id'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('job_applications', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
