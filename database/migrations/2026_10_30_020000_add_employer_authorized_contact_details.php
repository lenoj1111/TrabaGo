<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNS = [
        'contact_landline',
        'secondary_contact_person',
        'secondary_contact_position',
        'secondary_contact_phone',
        'secondary_contact_landline',
    ];

    public function up(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            if (!Schema::hasColumn('employers', 'contact_landline')) {
                $table->string('contact_landline', 50)->nullable()->after('contact_phone');
            }
            if (!Schema::hasColumn('employers', 'secondary_contact_person')) {
                $table->string('secondary_contact_person', 150)->nullable()->after('contact_landline');
            }
            if (!Schema::hasColumn('employers', 'secondary_contact_position')) {
                $table->string('secondary_contact_position', 100)->nullable()->after('secondary_contact_person');
            }
            if (!Schema::hasColumn('employers', 'secondary_contact_phone')) {
                $table->string('secondary_contact_phone', 50)->nullable()->after('secondary_contact_position');
            }
            if (!Schema::hasColumn('employers', 'secondary_contact_landline')) {
                $table->string('secondary_contact_landline', 50)->nullable()->after('secondary_contact_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $columns = array_values(array_filter(
                self::COLUMNS,
                fn (string $column): bool => Schema::hasColumn('employers', $column)
            ));

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
