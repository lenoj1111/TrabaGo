<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Demographics & Sector Inclusions for Region 7 (Central Visayas)
        Schema::table('social_status', function (Blueprint $table) {
            if (!Schema::hasColumn('social_status', 'is_lgbtq')) {
                $table->boolean('is_lgbtq')->default(0)->nullable()->after('pwd_type');
            }
            if (!Schema::hasColumn('social_status', 'is_senior_citizen')) {
                $table->boolean('is_senior_citizen')->default(0)->nullable()->after('is_lgbtq');
            }
            if (!Schema::hasColumn('social_status', 'is_immigrant')) {
                $table->boolean('is_immigrant')->default(0)->nullable()->after('is_senior_citizen');
            }
            if (!Schema::hasColumn('social_status', 'immigrant_origin')) {
                $table->string('immigrant_origin', 150)->nullable()->after('is_immigrant');
            }
            if (!Schema::hasColumn('social_status', 'region')) {
                $table->string('region', 100)->default('Region VII - Central Visayas')->nullable()->after('immigrant_origin');
            }
        });

        // 2. Employer Contact Management, Dual Email & Update Requests
        Schema::table('employers', function (Blueprint $table) {
            if (!Schema::hasColumn('employers', 'contact_person')) {
                $table->string('contact_person', 150)->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('employers', 'primary_email')) {
                $table->string('primary_email', 150)->nullable()->after('contact_person');
            }
            if (!Schema::hasColumn('employers', 'secondary_email')) {
                $table->string('secondary_email', 150)->nullable()->after('primary_email');
            }
            if (!Schema::hasColumn('employers', 'contact_phone')) {
                $table->string('contact_phone', 50)->nullable()->after('secondary_email');
            }
            if (!Schema::hasColumn('employers', 'company_address')) {
                $table->text('company_address')->nullable()->after('contact_phone');
            }
            if (!Schema::hasColumn('employers', 'pending_updates')) {
                $table->json('pending_updates')->nullable()->after('company_address');
            }
        });

        // 3. Accreditation Certificate & Admin Rejection Loop to JPO
        Schema::table('employer_accreditation', function (Blueprint $table) {
            if (!Schema::hasColumn('employer_accreditation', 'certificate_no')) {
                $table->string('certificate_no', 100)->nullable()->after('status');
            }
            if (!Schema::hasColumn('employer_accreditation', 'certificate_issued_at')) {
                $table->dateTime('certificate_issued_at')->nullable()->after('certificate_no');
            }
            if (!Schema::hasColumn('employer_accreditation', 'certificate_issued_by')) {
                $table->unsignedBigInteger('certificate_issued_by')->nullable()->after('certificate_issued_at');
            }
            if (!Schema::hasColumn('employer_accreditation', 'admin_rejection_remarks')) {
                $table->text('admin_rejection_remarks')->nullable()->after('certificate_issued_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('social_status', function (Blueprint $table) {
            $table->dropColumn(['is_lgbtq', 'is_senior_citizen', 'is_immigrant', 'immigrant_origin', 'region']);
        });

        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'primary_email', 'secondary_email', 'contact_phone', 'company_address', 'pending_updates']);
        });

        Schema::table('employer_accreditation', function (Blueprint $table) {
            $table->dropColumn(['certificate_no', 'certificate_issued_at', 'certificate_issued_by', 'admin_rejection_remarks']);
        });
    }
};
