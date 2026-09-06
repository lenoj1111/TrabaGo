<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dateTime('offered_at')->nullable()->after('hired_date');
            $table->decimal('offer_salary', 10, 2)->nullable()->after('offered_at');
            $table->date('offer_start_date')->nullable()->after('offer_salary');
            $table->text('offer_notes')->nullable()->after('offer_start_date');
            $table->dateTime('declined_at')->nullable()->after('offer_notes');
            $table->text('decline_reason')->nullable()->after('declined_at');
        });

        if (DB::getDriverName() === 'sqlsrv') {
            $jobAppConstraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('job_applications') AND (definition LIKE '%status%' OR name LIKE '%job_applications%statu%')");
            foreach ($jobAppConstraints as $c) {
                DB::statement("ALTER TABLE [job_applications] DROP CONSTRAINT [{$c->name}]");
            }
            DB::statement("ALTER TABLE [job_applications] ADD CONSTRAINT [CK_job_applications_status] CHECK ([status] IN ('pending', 'reviewed', 'interview', 'offered', 'hired', 'declined', 'rejected', 'withdrawn', 'cancelled'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            DB::statement("ALTER TABLE [job_applications] DROP CONSTRAINT IF EXISTS [CK_job_applications_status]");
            DB::statement("ALTER TABLE [job_applications] ADD CONSTRAINT [CK_job_applications_status] CHECK ([status] IN ('pending', 'reviewed', 'interview', 'hired', 'rejected', 'withdrawn', 'cancelled'))");
        }

        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'offered_at',
                'offer_salary',
                'offer_start_date',
                'offer_notes',
                'declined_at',
                'decline_reason',
            ]);
        });
    }
};
