<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('training_enrollments', 'assessment_attempts')) {
                $table->unsignedInteger('assessment_attempts')->default(0)->after('assessment_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('training_enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('training_enrollments', 'assessment_attempts')) {
                $table->dropColumn('assessment_attempts');
            }
        });
    }
};
