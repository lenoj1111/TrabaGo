<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DMDP Cebu City - Department of Manpower Development and Placement')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::selection { background-color: #059669; color: #ffffff; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #10b981; }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-white text-slate-900 antialiased">
    @include('partials.public-navbar')
    
    <main class="flex-1">
        @yield('content')
    </main>
    
    @include('partials.public-footer')

    @stack('scripts')
</body>
</html>