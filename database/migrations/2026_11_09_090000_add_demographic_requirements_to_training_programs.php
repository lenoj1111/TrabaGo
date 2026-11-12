<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('training_programs', 'demographic_requirements')) {
            Schema::table('training_programs', function (Blueprint $table) {
                $table->json('demographic_requirements')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('training_programs', 'demographic_requirements')) {
            Schema::table('training_programs', function (Blueprint $table) {
                $table->dropColumn('demographic_requirements');
            });
        }
    }
};
