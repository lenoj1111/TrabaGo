<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE dbo.job_applications DROP CONSTRAINT CK_job_applications_status");
        DB::statement("
            ALTER TABLE dbo.job_applications WITH CHECK ADD CONSTRAINT CK_job_applications_status
            CHECK ([status] IN (
                'jpo_pending',
                'pending',
                'reviewed',
                'interview',
                'offered',
                'hired',
                'rejected',
                'declined',
                'withdrawn',
                'training_recommended'
            ))
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE dbo.job_applications DROP CONSTRAINT CK_job_applications_status");
        DB::statement("
            ALTER TABLE dbo.job_applications WITH CHECK ADD CONSTRAINT CK_job_applications_status
            CHECK ([status] IN (
                'jpo_pending','pending','reviewed','interview','offered','hired','rejected','declined','withdrawn'
            ))
        ");
    }
};
