<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DMDP Admin')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Admin Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl font-bold text-gray-800">DMDP</span>
                <span class="text-sm text-gray-500">Admin Panel</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">{{ auth()->user()->email ?? 'Admin' }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Admin Sidebar -->
        <aside class="w-52 bg-white border-r border-gray-200 min-h-[calc(100vh-56px)] p-3 flex-shrink-0">
            <nav class="space-y-0.5">
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-1.5 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2 bg-gray-100 font-medium">
                    👥 Users
                </a>
                <a href="#" class="block px-3 py-1.5 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
                    📊 Dashboard
                </a>
                <a href="#" class="block px-3 py-1.5 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
                    🏢 Employers
                </a>
                <a href="#" class="block px-3 py-1.5 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
                    📝 Reports
                </a>
                <a href="#" class="block px-3 py-1.5 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
                    ⚙️ Settings
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>