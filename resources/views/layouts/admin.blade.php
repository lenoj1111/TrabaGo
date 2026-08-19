<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Admin Dashboard') - DMDP
    </title>

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')

</head>


<body>


<div class="admin-wrapper">


    {{-- ========================================= --}}
    {{-- SIDEBAR --}}
    {{-- ========================================= --}}

    <aside class="admin-sidebar">

        {{-- BRAND --}}
        <div class="sidebar-brand">
            <div class="brand-mark">
                <i class="bi bi-briefcase"></i>
            </div>
            <div class="brand-text">
                <strong>DMDP</strong>
                <span>ADMINISTRATION</span>
            </div>
        </div>


        {{-- NAVIGATION --}}
        <div class="sidebar-section">
            <span class="sidebar-label">MAIN</span>

            <nav class="sidebar-nav">

                {{-- Dashboard --}}
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                >
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>

                {{-- Job Postings --}}
                <a
                    href="{{ route('admin.job-postings') }}"
                    class="sidebar-link {{ request()->routeIs('admin.job-postings*') ? 'active' : '' }}"
                >
                    <i class="bi bi-briefcase"></i>
                    <span>Job Postings</span>
                </a>

                {{-- Users --}}
                <a
                    href="{{ route('admin.users.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                >
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                </a>

                {{-- Employers --}}
                <a
                    href="{{ route('admin.employers') }}"
                    class="sidebar-link {{ request()->routeIs('admin.employers') ? 'active' : '' }}"
                >
                    <i class="bi bi-building"></i>
                    <span>Employers</span>
                </a>

                {{-- Reports --}}
                <a
                    href="{{ route('admin.reports') }}"
                    class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}"
                >
                    <i class="bi bi-bar-chart"></i>
                    <span>Reports</span>
                </a>

            </nav>
        </div>


        {{-- SIDEBAR BOTTOM --}}
        <div class="sidebar-bottom">

            {{-- ADMIN PROFILE --}}
            <div class="sidebar-user">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->email ?? 'A', 0, 1)) }}
                </div>
                <div class="user-info">
                    <strong>Administrator</strong>
                    <span>{{ Auth::user()->email ?? 'admin@trabago.com' }}</span>
                </div>
            </div>

            {{-- LOGOUT --}}
            <form
                id="logout-form"
                action="{{ route('logout') }}"
                method="POST"
            >
                @csrf
                <button
                    type="submit"
                    class="logout-button"
                >
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </button>
            </form>

        </div>

    </aside>



    {{-- ========================================= --}}
    {{-- MAIN AREA --}}
    {{-- ========================================= --}}

    <main class="admin-main">


        {{-- TOP BAR --}}
        <header class="admin-topbar">

            <div>
                <span class="topbar-section">
                    @yield('title')
                </span>
            </div>

            <div class="topbar-right">
                <span class="topbar-date">
                    <i class="bi bi-calendar3"></i>
                    {{ now()->format('F d, Y') }}
                </span>

                <div class="topbar-divider"></div>

                <div class="topbar-user">
                    <div class="topbar-avatar">
                        {{ strtoupper(substr(Auth::user()->email ?? 'A', 0, 1)) }}
                    </div>
                    <span>Administrator</span>
                </div>
            </div>

        </header>


        {{-- ========================================= --}}
        {{-- PAGE CONTENT --}}
        {{-- ========================================= --}}

        <div class="admin-content">


            {{-- FLASH SUCCESS --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- FLASH ERROR --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif


            {{-- PAGE HEADER ACTIONS --}}
            @hasSection('header-actions')
                <div class="page-actions">
                    @yield('header-actions')
                </div>
            @endif


            {{-- ACTUAL PAGE --}}
            @yield('content')


        </div>

    </main>

</div>


{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')


<style>

/* =====================================================
   GLOBAL
===================================================== */

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family:
        Inter,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;
    background: #f5f6f8;
    color: #172033;
}


/* =====================================================
   LAYOUT
===================================================== */

.admin-wrapper {
    display: flex;
    min-height: 100vh;
}


/* =====================================================
   SIDEBAR
===================================================== */

.admin-sidebar {
    width: 245px;
    min-width: 245px;
    min-height: 100vh;
    background: #182536;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    padding: 25px 15px 17px;
}


/* =====================================================
   BRAND
===================================================== */

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 0 10px;
    margin-bottom: 40px;
}

.brand-mark {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: #2d3b4d;
    color: #dbe2e9;
    font-size: 15px;
}

.brand-text {
    display: flex;
    flex-direction: column;
}

.brand-text strong {
    font-size: 16px;
    letter-spacing: .04em;
    line-height: 1;
}

.brand-text span {
    margin-top: 4px;
    font-size: 8px;
    letter-spacing: .13em;
    color: #8996a5;
    font-weight: 600;
}


/* =====================================================
   SIDEBAR SECTION
===================================================== */

.sidebar-section {
    flex: 1;
}

.sidebar-label {
    display: block;
    padding: 0 11px;
    margin-bottom: 9px;
    color: #778597;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .13em;
}


/* =====================================================
   NAVIGATION
===================================================== */

.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.sidebar-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 11px;
    border-radius: 6px;
    color: #aeb8c4;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: all .18s ease;
}

.sidebar-link i {
    width: 18px;
    text-align: center;
    font-size: 14px;
}

.sidebar-link:hover {
    background: #223143;
    color: #ffffff;
}

.sidebar-link.active {
    background: #29394b;
    color: #ffffff;
}

.sidebar-link.active::before {
    content: "";
    position: absolute;
    left: 0;
    top: 8px;
    bottom: 8px;
    width: 3px;
    border-radius: 0 3px 3px 0;
    background: #8da99b;
}


/* =====================================================
   SIDEBAR BOTTOM
===================================================== */

.sidebar-bottom {
    border-top: 1px solid #2a394a;
    padding-top: 15px;
}

.sidebar-user {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 7px 6px 12px;
}

.user-avatar {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #334357;
    color: #e1e6eb;
    font-size: 11px;
    font-weight: 700;
}

.user-info {
    min-width: 0;
}

.user-info strong {
    display: block;
    color: #e4e8ec;
    font-size: 10px;
    font-weight: 600;
}

.user-info span {
    display: block;
    max-width: 155px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #7f8d9d;
    font-size: 9px;
    margin-top: 2px;
}

.logout-button {
    width: 100%;
    border: 0;
    background: transparent;
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 9px 11px;
    border-radius: 6px;
    color: #8f9aa8;
    font-size: 11px;
    cursor: pointer;
    text-align: left;
    transition: .18s ease;
}

.logout-button:hover {
    background: #222f3f;
    color: #e5e9ed;
}


/* =====================================================
   MAIN
===================================================== */

.admin-main {
    flex: 1;
    min-width: 0;
}


/* =====================================================
   TOPBAR
===================================================== */

.admin-topbar {
    height: 67px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 32px;
    background: #ffffff;
    border-bottom: 1px solid #e4e7eb;
}

.topbar-section {
    color: #1a2433;
    font-size: 13px;
    font-weight: 650;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 14px;
}

.topbar-date {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #8993a1;
    font-size: 10px;
}

.topbar-divider {
    width: 1px;
    height: 20px;
    background: #e4e7eb;
}

.topbar-user {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #667283;
    font-size: 10px;
    font-weight: 600;
}

.topbar-avatar {
    width: 27px;
    height: 27px;
    border-radius: 50%;
    background: #eef0f2;
    color: #566273;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 700;
}


/* =====================================================
   CONTENT
===================================================== */

.admin-content {
    padding: 0;
}

.page-actions {
    padding: 20px 32px 0;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 900px) {
    .admin-sidebar {
        width: 205px;
        min-width: 205px;
    }
    .admin-topbar {
        padding: 0 20px;
    }
}

@media (max-width: 700px) {
    .admin-sidebar {
        width: 68px;
        min-width: 68px;
        padding: 20px 8px;
    }

    .brand-text,
    .sidebar-label,
    .sidebar-link span,
    .user-info,
    .logout-button span {
        display: none;
    }

    .sidebar-brand {
        justify-content: center;
        padding: 0;
    }

    .sidebar-link {
        justify-content: center;
        padding: 11px;
    }

    .sidebar-link.active::before {
        left: -1px;
    }

    .sidebar-user {
        justify-content: center;
    }

    .logout-button {
        justify-content: center;
    }

    .topbar-date,
    .topbar-divider,
    .topbar-user > span {
        display: none;
    }

    .admin-topbar {
        height: 58px;
    }
}

</style>


</body>

</html>