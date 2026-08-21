<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DMDP Admin')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand-700 { background: #1b2739; }
        .bg-brand-800 { background: #141e2e; }
        .bg-brand-900 { background: #0d1420; }
        .from-brand-700 { --tw-gradient-from: #1b2739; }
        .to-brand-900 { --tw-gradient-to: #0d1420; }
        .hover\:shadow-lg:hover { box-shadow: 0 10px 40px rgba(0,0,0,0.15); }
        .hover\:scale-105:hover { transform: scale(1.05); }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Admin Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl font-bold text-gray-800">DMDP</span>
                <span class="text-sm text-gray-500 hidden sm:inline">Admin Panel</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600 hidden md:inline">{{ session('user_email') ?? 'Admin' }}</span>
                <span class="text-sm text-gray-600 md:hidden">{{ substr(session('user_email') ?? 'Admin', 0, 10) }}...</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Admin Content - Sidebar + Main -->
    <div class="flex">
        <!-- Admin Sidebar -->
        <aside class="w-52 bg-white border-r border-gray-200 min-h-[calc(100vh-56px)] p-3 flex-shrink-0 hidden md:block">
            <nav class="space-y-0.5">
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2 bg-gray-100 font-medium">
                    👥 Users
                </a>
                <a href="#" class="block px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
                    📊 Dashboard
                </a>
                <a href="#" class="block px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
                    🏢 Employers
                </a>
                <a href="#" class="block px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
                    📝 Reports
                </a>
                <a href="#" class="block px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition flex items-center gap-2">
                    ⚙️ Settings
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-h-[calc(100vh-56px)]">
            <div class="max-w-7xl mx-auto p-4 md:p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
                        <span>{{ session('error') }}</span>
                        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>