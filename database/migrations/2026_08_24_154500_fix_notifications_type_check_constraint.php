<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            // Drop any check constraint on notifications.type so any notification type string is permitted
            $notifConstraints = DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('notifications') AND (definition LIKE '%type%' OR name LIKE '%notificati%type%')");
            foreach ($notifConstraints as $c) {
                DB::statement("ALTER TABLE [notifications] DROP CONSTRAINT [{$c->name}]");
            }
        }
    }

    public function down(): void
    {
        // No need to restore restrictive constraint
    }
};
