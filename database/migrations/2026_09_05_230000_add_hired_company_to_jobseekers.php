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
        if (Schema::hasTable('jobseekers') && !Schema::hasColumn('jobseekers', 'hired_company')) {
            Schema::table('jobseekers', function (Blueprint $table) {
                $table->string('hired_company', 150)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('jobseekers') && Schema::hasColumn('jobseekers', 'hired_company')) {
            Schema::table('jobseekers', function (Blueprint $table) {
                $table->dropColumn('hired_company');
            });
        }
    }
};
