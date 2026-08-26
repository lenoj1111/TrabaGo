<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DMDP Admin')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --admin-navy: #101d2d; --admin-navy-light: #1d3147; --admin-bg: #f4f6f9; --admin-blue: #1769e8; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--admin-bg); color: #152238; font-family: 'Inter', sans-serif; font-size: 14px; }
        .admin-shell { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 230px; flex: 0 0 230px; background: var(--admin-navy); color: #a9b8c9; display: flex; flex-direction: column; padding: 22px 14px 18px; }
        .admin-brand { display: flex; align-items: center; gap: 12px; padding: 0 9px 36px; color: #fff; }
        .admin-brand-mark { width: 32px; height: 32px; display: grid; place-items: center; border-radius: 7px; background: var(--admin-navy-light); color: #c8d8e8; }
        .admin-brand strong { display: block; font-size: 16px; letter-spacing: .02em; }
        .admin-brand small { display: block; margin-top: 2px; color: #71869e; font-size: 8px; letter-spacing: .08em; }
        .admin-label { padding: 0 10px 12px; color: #6d839b; font-size: 8px; font-weight: 700; letter-spacing: .16em; }
        .admin-nav { display: grid; gap: 4px; }
        .admin-nav a { display: flex; align-items: center; gap: 12px; padding: 11px 10px; border-radius: 6px; color: #aebccc; font-size: 12px; text-decoration: none; }
        .admin-nav a:hover, .admin-nav a.active { background: var(--admin-navy-light); color: #fff; }
        .admin-nav a.active { border-left: 3px solid #9bd4bd; padding-left: 7px; }
        .admin-nav i { width: 16px; text-align: center; font-size: 13px; }
        .admin-main { min-width: 0; flex: 1; }
        .admin-topbar { height: 51px; display: flex; align-items: center; justify-content: space-between; padding: 0 28px; background: #fff; border-bottom: 1px solid #e5e9ee; }
        .admin-topbar-title { font-size: 12px; font-weight: 700; }
        .admin-topbar-meta { display: flex; align-items: center; gap: 14px; color: #7e8b9a; font-size: 10px; }
        .admin-topbar-divider { height: 19px; border-left: 1px solid #e4e7eb; }
        .admin-avatar { width: 26px; height: 26px; display: grid; place-items: center; border-radius: 50%; background: #f0f2f5; color: #7d8997; font-size: 10px; font-weight: 700; }
        .admin-content { padding: 18px 22px 30px; }
        .admin-content > .p-4 { padding: 0 !important; }
        .admin-content .card { border: 1px solid #e8ebef !important; border-radius: 6px !important; box-shadow: 0 2px 7px rgba(24, 42, 64, .06) !important; }
        .admin-content .btn-primary { background: var(--admin-blue); border-color: var(--admin-blue); }
        .admin-content .table { font-size: 13px; }
        .admin-logout { padding: 10px 10px 0; }
        .admin-logout button { border: 0; background: transparent; color: #93a5b8; font-size: 12px; }
        @media (max-width: 768px) { .admin-sidebar { width: 68px; flex-basis: 68px; padding-left: 8px; padding-right: 8px; } .admin-brand { padding-left: 10px; } .admin-brand > div:last-child, .admin-label, .admin-nav a span { display: none; } .admin-nav a { justify-content: center; } .admin-topbar { padding: 0 14px; } .admin-content { padding: 14px 10px; } }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <div class="admin-brand-mark"><i class="bi bi-briefcase"></i></div>
                <div><strong>DMDP</strong><small>ADMINISTRATION</small></div>
            </div>
            <div class="admin-label">MAIN</div>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a>
                <a href="{{ route('admin.job-postings') }}" class="{{ request()->routeIs('admin.job-postings*') ? 'active' : '' }}"><i class="bi bi-briefcase"></i><span>Job Postings</span></a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Users</span></a>
                <a href="{{ route('admin.employers') }}" class="{{ request()->routeIs('admin.employers*') ? 'active' : '' }}"><i class="bi bi-building"></i><span>Employers</span></a>
                <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i><span>Reports</span></a>
            </nav>
            <div class="admin-logout mt-auto">
                <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit"><i class="bi bi-box-arrow-left me-2"></i><span>Logout</span></button></form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <span class="admin-topbar-title">@yield('title', 'Admin Dashboard')</span>
                <div class="admin-topbar-meta"><span><i class="bi bi-calendar3 me-1"></i>{{ now()->format('F d, Y') }}</span><span class="admin-topbar-divider"></span><span class="admin-avatar">{{ strtoupper(substr(session('user_email') ?? 'A', 0, 1)) }}</span><span>Administrator</span></div>
            </header>
            <main class="admin-content">
                @hasSection('header-actions')
                    <div class="d-flex justify-content-end mb-3">@yield('header-actions')</div>
                @endif
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
            </main>
        </div>
    </div>
</body>
</html>