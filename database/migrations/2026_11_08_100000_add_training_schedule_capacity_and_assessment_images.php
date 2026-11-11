<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_programs', function (Blueprint $table) {
            if (!Schema::hasColumn('training_programs', 'training_start_date')) {
                $table->date('training_start_date')->nullable();
            }
            if (!Schema::hasColumn('training_programs', 'training_end_date')) {
                $table->date('training_end_date')->nullable();
            }
            if (!Schema::hasColumn('training_programs', 'trainee_capacity')) {
                $table->unsignedInteger('trainee_capacity')->nullable();
            }
        });

        Schema::table('training_assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('training_assessments', 'image_path')) {
                $table->string('image_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('training_programs', function (Blueprint $table) {
            $columns = array_filter(['training_start_date', 'training_end_date', 'trainee_capacity'], fn($column) => Schema::hasColumn('training_programs', $column));
            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('training_assessments', function (Blueprint $table) {
            if (Schema::hasColumn('training_assessments', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
