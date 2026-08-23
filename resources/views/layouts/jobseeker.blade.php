<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
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
            background-color: #059669;
            color: #ffffff;
        }
        /* Subtle Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #10b981;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col bg-slate-50 text-slate-900 antialiased">

    <!-- Top Navigation -->
    @include('partials.jobseeker-navbar')

    <!-- Flash Message Toasts -->
    <div class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-md w-full px-4 pointer-events-none">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-emerald-900 text-white p-4 shadow-xl border border-emerald-700 transition-all duration-300 transform translate-y-0">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-emerald-300 hover:text-white">&times;</button>
            </div>
        @endif

        @if (session('info'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-teal-900 text-white p-4 shadow-xl border border-teal-700">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('info') }}</p>
                </div>
                <button @click="show = false" class="text-teal-300 hover:text-white">&times;</button>
            </div>
        @endif

        @if (session('warning'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-amber-900 text-white p-4 shadow-xl border border-amber-700">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('warning') }}</p>
                </div>
                <button @click="show = false" class="text-amber-300 hover:text-white">&times;</button>
            </div>
        @endif

        @if (session('error') || $errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)" 
                 class="pointer-events-auto flex items-center justify-between gap-3 rounded-xl bg-rose-900 text-white p-4 shadow-xl border border-rose-700">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <p class="text-sm font-medium">{{ session('error') ?: $errors->first() }}</p>
                </div>
                <button @click="show = false" class="text-rose-300 hover:text-white">&times;</button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-900">DMDP TrabaGo</span>
                <span>&copy; {{ date('Y') }} Cebu City Department of Manpower Development and Placement.</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('jobseeker.jobs') }}" class="hover:text-slate-900">Explore Jobs</a>
                <a href="{{ route('jobseeker.training') }}" class="hover:text-slate-900">Training Courses</a>
                <a href="{{ route('jobseeker.profile') }}" class="hover:text-slate-900">Skills Matrix</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>