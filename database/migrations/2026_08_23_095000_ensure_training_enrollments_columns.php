<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
        if (Schema::hasTable('training_enrollments')) {
            Schema::table('training_enrollments', function (Blueprint $table) {
                $columns = ['score', 'passed', 'certificate_no', 'certificate_issued', 'certificate_issued_at', 'trainer_feedback'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('training_enrollments', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
