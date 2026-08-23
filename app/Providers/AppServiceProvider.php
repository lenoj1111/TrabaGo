<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlsrv') {
            try {
                // Proactively synchronize placement_reports status check constraint
                $placementConstraints = \Illuminate\Support\Facades\DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('placement_reports') AND (definition LIKE '%status%' OR name LIKE '%placement%statu%')");
                if (!empty($placementConstraints)) {
                    foreach ($placementConstraints as $c) {
                        \Illuminate\Support\Facades\DB::statement("ALTER TABLE [placement_reports] DROP CONSTRAINT [{$c->name}]");
                    }
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE [placement_reports] ADD CONSTRAINT [CK_placement_reports_status] CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_evaluated', 'approved', 'rejected'))");
                }

                // Proactively synchronize employer_accreditation status check constraint
                $accConstraints = \Illuminate\Support\Facades\DB::select("SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('employer_accreditation') AND (definition LIKE '%status%' OR name LIKE '%employer%statu%')");
                if (!empty($accConstraints)) {
                    foreach ($accConstraints as $c) {
                        \Illuminate\Support\Facades\DB::statement("ALTER TABLE [employer_accreditation] DROP CONSTRAINT [{$c->name}]");
                    }
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE [employer_accreditation] ADD CONSTRAINT [CK_employer_accreditation_status] CHECK ([status] IN ('pending', 'submitted_to_jpo', 'jpo_approved', 'supervisor_approved', 'admin_approved', 'rejected', 'auto_approved', 'manual_review'))");
                }
            } catch (\Throwable $e) {
                // Silently bypass during console bootstrap or if connection is establishing
            }
        }
    }
}