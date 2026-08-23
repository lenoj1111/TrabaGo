<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            // 1. placement_reports status check constraint
            $placementConstraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('placement_reports') AND (definition LIKE '%status%' OR name LIKE '%placement%statu%')");
            foreach ($placementConstraints as $c) {
                DB::statement("ALTER TABLE [placement_reports] DROP CONSTRAINT [{$c->name}]");
            }
            DB::statement("ALTER TABLE [placement_reports] ADD CONSTRAINT [CK_placement_reports_status] CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_evaluated', 'approved', 'rejected'))");

            // 2. employer_accreditation status check constraint
            $accConstraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('employer_accreditation') AND (definition LIKE '%status%' OR name LIKE '%employer%statu%')");
            foreach ($accConstraints as $c) {
                DB::statement("ALTER TABLE [employer_accreditation] DROP CONSTRAINT [{$c->name}]");
            }
            DB::statement("ALTER TABLE [employer_accreditation] ADD CONSTRAINT [CK_employer_accreditation_status] CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_approved', 'supervisor_approved', 'admin_approved', 'rejected', 'auto_approved', 'manual_review'))");

            // 3. users role check constraint
            $roleConstraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('users') AND (definition LIKE '%role%' OR name LIKE '%users%role%')");
            foreach ($roleConstraints as $c) {
                DB::statement("ALTER TABLE [users] DROP CONSTRAINT [{$c->name}]");
            }
            DB::statement("ALTER TABLE [users] ADD CONSTRAINT [CK_users_role] CHECK ([role] IN ('admin', 'supervisor', 'pesd_supervisor', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'))");

            // 4. job_applications status check constraint
            $jobAppConstraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('job_applications') AND (definition LIKE '%status%' OR name LIKE '%job_applications%statu%')");
            foreach ($jobAppConstraints as $c) {
                DB::statement("ALTER TABLE [job_applications] DROP CONSTRAINT [{$c->name}]");
            }
            DB::statement("ALTER TABLE [job_applications] ADD CONSTRAINT [CK_job_applications_status] CHECK ([status] IN ('pending', 'reviewed', 'interview', 'hired', 'rejected', 'withdrawn', 'cancelled'))");

            // 5. training_enrollments status check constraint
            $trainingConstraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('training_enrollments') AND (definition LIKE '%status%' OR name LIKE '%training_enrollments%statu%')");
            foreach ($trainingConstraints as $c) {
                DB::statement("ALTER TABLE [training_enrollments] DROP CONSTRAINT [{$c->name}]");
            }
            DB::statement("ALTER TABLE [training_enrollments] ADD CONSTRAINT [CK_training_enrollments_status] CHECK ([status] IN ('enrolled', 'in_progress', 'completed', 'failed', 'cancelled'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            DB::statement("ALTER TABLE [placement_reports] DROP CONSTRAINT IF EXISTS [CK_placement_reports_status]");
            DB::statement("ALTER TABLE [employer_accreditation] DROP CONSTRAINT IF EXISTS [CK_employer_accreditation_status]");
            DB::statement("ALTER TABLE [job_applications] DROP CONSTRAINT IF EXISTS [CK_job_applications_status]");
            DB::statement("ALTER TABLE [training_enrollments] DROP CONSTRAINT IF EXISTS [CK_training_enrollments_status]");
        }
    }
};
