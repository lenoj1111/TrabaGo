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
        Schema::table('job_postings', function (Blueprint $table) {
            if (!Schema::hasColumn('job_postings', 'job_type')) {
                $table->string('job_type', 100)->nullable()->after('description');
            }
            if (!Schema::hasColumn('job_postings', 'location')) {
                $table->string('location', 255)->nullable()->after('job_type');
            }
            if (!Schema::hasColumn('job_postings', 'salary_range')) {
                $table->string('salary_range', 150)->nullable()->after('location');
            }
            if (!Schema::hasColumn('job_postings', 'benefits')) {
                $table->text('benefits')->nullable()->after('salary_range');
            }
            if (!Schema::hasColumn('job_postings', 'other_instructions')) {
                $table->text('other_instructions')->nullable()->after('benefits');
            }
            if (!Schema::hasColumn('job_postings', 'skills')) {
                $table->text('skills')->nullable()->after('qualifications');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $columns = ['job_type', 'location', 'salary_range', 'benefits', 'other_instructions', 'skills'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('job_postings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
