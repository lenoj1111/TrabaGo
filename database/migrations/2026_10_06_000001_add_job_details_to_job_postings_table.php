<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            if (!Schema::hasColumn('job_postings', 'job_type')) {
                $table->string('job_type', 100)->nullable();
            }
            if (!Schema::hasColumn('job_postings', 'location')) {
                $table->string('location', 255)->nullable();
            }
            if (!Schema::hasColumn('job_postings', 'salary_compensation')) {
                $table->text('salary_compensation')->nullable();
            }
            if (!Schema::hasColumn('job_postings', 'benefits_perks')) {
                $table->text('benefits_perks')->nullable();
            }
            if (!Schema::hasColumn('job_postings', 'other_instructions')) {
                $table->text('other_instructions')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $drop = [];
            foreach (['job_type', 'location', 'salary_compensation', 'benefits_perks', 'other_instructions'] as $col) {
                if (Schema::hasColumn('job_postings', $col)) {
                    $drop[] = $col;
                }
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};