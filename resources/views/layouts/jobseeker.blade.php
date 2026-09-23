<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TrabaGo - Jobseeker Portal')</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js CDN for interactive UI -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        [x-cloak] { display: none !important; }
        ::selection {
            background-color: #16a34a;
            color: #ffffff;
        }
        /* Subtle Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f3f4f6;
        }
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #16a34a;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col bg-gray-50 text-gray-900 antialiased">

    <!-- Top Navigation -->
    @include('partials.jobseeker-navbar')

    <!-- Flash Message Toasts -->
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-md w-full px-4 pointer-events-none">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-gray-900 text-white p-4 shadow-lg border border-gray-800 transition-all duration-300">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-white">&times;</button>
            </div>
        @endif

        @if (session('info'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-gray-900 text-white p-4 shadow-lg border border-gray-800">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('info') }}</p>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-white">&times;</button>
            </div>
        @endif

        @if (session('warning'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-gray-900 text-white p-4 shadow-lg border border-gray-800">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('warning') }}</p>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-white">&times;</button>
            </div>
        @endif

        @if (session('error') || (isset($errors) && $errors->any()))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-gray-900 text-white p-4 shadow-lg border border-gray-800">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('error') ?: (isset($errors) ? $errors->first() : '') }}</p>
                </div>
                <button @click="show = false" class="text-gray-400 hover:text-white">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-gray-200 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-900">DMDP TrabaGo</span>
                <span>&copy; {{ date('Y') }} Cebu City Department of Manpower Development and Placement.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('jobseeker.jobs') }}" class="hover:text-green-700 transition-colors">Explore Jobs</a>
                <a href="{{ route('jobseeker.training') }}" class="hover:text-green-700 transition-colors">Training Courses</a>
                <a href="{{ route('jobseeker.profile') }}" class="hover:text-green-700 transition-colors">Skills Matrix</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>