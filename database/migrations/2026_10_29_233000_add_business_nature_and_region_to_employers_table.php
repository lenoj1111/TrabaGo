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
        Schema::table('employers', function (Blueprint $table) {
            if (!Schema::hasColumn('employers', 'business_nature')) {
                $table->string('business_nature', 150)->nullable()->after('company_address');
            }
            if (!Schema::hasColumn('employers', 'region')) {
                $table->string('region', 100)->default('Region VII - Central Visayas')->nullable()->after('business_nature');
            }
            if (!Schema::hasColumn('employers', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('region');
            }
            if (!Schema::hasColumn('employers', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn(['business_nature', 'region', 'created_at', 'updated_at']);
        });
    }
};
