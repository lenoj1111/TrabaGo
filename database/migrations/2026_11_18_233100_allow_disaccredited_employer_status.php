<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlsrv') {
            return;
        }

        DB::statement("IF OBJECT_ID(N'CK_employer_accreditation_status', N'C') IS NOT NULL ALTER TABLE [employer_accreditation] DROP CONSTRAINT [CK_employer_accreditation_status]");
        DB::statement("ALTER TABLE [employer_accreditation] ADD CONSTRAINT [CK_employer_accreditation_status] CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_approved', 'supervisor_approved', 'admin_approved', 'rejected', 'auto_approved', 'manual_review', 'disaccredited'))");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlsrv') {
            return;
        }

        DB::statement("IF OBJECT_ID(N'CK_employer_accreditation_status', N'C') IS NOT NULL ALTER TABLE [employer_accreditation] DROP CONSTRAINT [CK_employer_accreditation_status]");
        DB::statement("ALTER TABLE [employer_accreditation] ADD CONSTRAINT [CK_employer_accreditation_status] CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_approved', 'supervisor_approved', 'admin_approved', 'rejected', 'auto_approved', 'manual_review'))");
    }
};
