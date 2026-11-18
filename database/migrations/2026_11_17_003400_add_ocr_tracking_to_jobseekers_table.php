<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobseekers', function (Blueprint $table) {
            // Shorter column names to avoid DB limits
            $table->integer('id_scan_tries')->default(0);
            $table->string('id_scan_error')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jobseekers', function (Blueprint $table) {
            $table->dropColumn(['id_scan_tries', 'id_scan_error']);
        });
    }
};
