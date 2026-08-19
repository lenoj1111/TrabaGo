<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics from your database
        $stats = [
            'total_jobs' => DB::table('job_postings')->count(),
            'pending_jobs' => DB::table('job_postings')->where('status', 'pending')->count(),
            'total_employers' => DB::table('employers')->count(),
            'total_jobseekers' => DB::table('jobseekers')->count(),
            'total_applications' => DB::table('job_applications')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}