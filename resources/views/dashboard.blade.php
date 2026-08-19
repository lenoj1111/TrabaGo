<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Admin Dashboard</h1>
            <div class="flex items-center gap-4">
                <span class="text-gray-600">{{ Auth::user()->email }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Logout
                    </button>
                </form>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Total Jobs</h3>
                <p class="text-2xl font-bold">{{ $stats['total_jobs'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Pending Jobs</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_jobs'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Employers</h3>
                <p class="text-2xl font-bold">{{ $stats['total_employers'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Jobseekers</h3>
                <p class="text-2xl font-bold">{{ $stats['total_jobseekers'] ?? 0 }}</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
            <div class="flex gap-4 flex-wrap">
                <a href="{{ route('admin.job-postings.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Manage Job Postings
                </a>
                <a href="{{ route('admin.job-postings.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Create New Job
                </a>
            </div>
        </div>
    </div>
</body>
</html>