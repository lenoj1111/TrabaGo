<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            <div class="flex items-center gap-4">
                <span class="text-gray-600 text-sm">{{ Auth::user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-700 transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Jobs</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_jobs'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-xs font-medium uppercase tracking-wider">Pending Jobs</h3>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending_jobs'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-xs font-medium uppercase tracking-wider">Employers</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_employers'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="text-gray-500 text-xs font-medium uppercase tracking-wider">Jobseekers</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_jobseekers'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <h2 class="text-base font-bold mb-4">Quick Actions</h2>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.job-postings.index') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-green-700 transition-colors">
                    Manage Job Postings
                </a>
                <a href="{{ route('admin.job-postings.create') }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-gray-800 transition-colors">
                    Create New Job
                </a>
            </div>
        </div>
    </div>
</body>
</html>