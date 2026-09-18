<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TrabaGo - DMDP Cebu City') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 min-h-screen">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-8 sm:py-12 px-4">
            <div class="mb-6">
                <a href="/">
                    @include('components.logo')
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white border border-gray-200 shadow-sm rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
