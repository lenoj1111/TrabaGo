<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('job_type', 100)->nullable();
            $table->string('location', 255)->nullable();
            $table->text('salary_compensation')->nullable();
            $table->text('benefits_perks')->nullable();
            $table->text('other_instructions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn([
                'job_type',
                'location',
                'salary_compensation',
                'benefits_perks',
                'other_instructions',
            ]);
        });
    }
};