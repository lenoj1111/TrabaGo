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
        if (Schema::hasTable('placement_reports')) {
            Schema::table('placement_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('placement_reports', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('placement_reports', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('placement_reports')) {
            Schema::table('placement_reports', function (Blueprint $table) {
                if (Schema::hasColumn('placement_reports', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
                if (Schema::hasColumn('placement_reports', 'created_at')) {
                    $table->dropColumn('created_at');
                }
            });
        }
    }
};
