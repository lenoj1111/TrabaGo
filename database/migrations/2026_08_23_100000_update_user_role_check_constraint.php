<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            $constraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('users') AND definition LIKE '%role%'");
            foreach ($constraints as $c) {
                DB::statement("ALTER TABLE [users] DROP CONSTRAINT [{$c->name}]");
            }
            DB::statement("ALTER TABLE [users] ADD CONSTRAINT [CK_users_role] CHECK ([role] IN ('admin', 'supervisor', 'pesd_supervisor', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            DB::statement("ALTER TABLE [users] DROP CONSTRAINT IF EXISTS [CK_users_role]");
            DB::statement("ALTER TABLE [users] ADD CONSTRAINT [CK_users_role] CHECK ([role] IN ('admin', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker'))");
        }
    }
};
