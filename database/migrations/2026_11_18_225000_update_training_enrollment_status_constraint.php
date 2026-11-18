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

        DB::statement("IF OBJECT_ID(N'CK_training_enrollments_status', N'C') IS NOT NULL ALTER TABLE [training_enrollments] DROP CONSTRAINT [CK_training_enrollments_status]");
        DB::statement("ALTER TABLE [training_enrollments] ADD CONSTRAINT [CK_training_enrollments_status] CHECK ([status] IN ('enrolled', 'in_progress', 'completed', 'failed', 'cancelled', 'no_show', 'multiple_absences'))");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlsrv') {
            return;
        }

        DB::statement("IF OBJECT_ID(N'CK_training_enrollments_status', N'C') IS NOT NULL ALTER TABLE [training_enrollments] DROP CONSTRAINT [CK_training_enrollments_status]");
        DB::statement("ALTER TABLE [training_enrollments] ADD CONSTRAINT [CK_training_enrollments_status] CHECK ([status] IN ('enrolled', 'in_progress', 'completed', 'failed', 'cancelled'))");
    }
};
