<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — AI Workspace</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|figtree:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="admin-body">
<div class="admin-shell">

    {{-- ── Sidebar ─────────────────────────────── --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="brand-text">
                <span class="brand-name">{{ config('app.name') }}</span>
                <span class="brand-sub">Private knowledge workspace</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <p class="nav-section-label">Workspace</p>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('conversations.index') }}"
               class="nav-item {{ request()->routeIs('conversations.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <span>Conversations</span>
            </a>

            <a href="{{ route('knowledge.index') }}"
               class="nav-item {{ request()->routeIs('knowledge.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <span>Knowledge Base</span>
            </a>

            <p class="nav-section-label">Account</p>

            <a href="{{ route('profile.edit') }}"
               class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>Profile</span>
            </a>

            @if (Auth::check() && Auth::user()->is_admin)
                <p class="nav-section-label">Admin Access</p>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="17" height="17" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span>Manage Users</span>
                </a>
                <a href="{{ route('admin.telegram-bots.index') }}" class="nav-item {{ request()->routeIs('admin.telegram-bots.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <span>Telegram Bots</span>
                </a>
            @endif

        </nav>


        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div class="user-info">
                    <p class="user-name">{{ auth()->user()->name }}</p>
                    <p class="user-role">{{ auth()->user()->email }}</p>
                </div>
                <span class="user-status-dot"></span>
            </div>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    {{-- ── Main wrapper ─────────────────────────── --}}
    <div class="main-wrapper">

        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="sidebar-toggle" type="button" aria-label="Toggle sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <nav class="topbar-breadcrumb" aria-label="Breadcrumb">
                    @yield('breadcrumb')
                </nav>
            </div>
            <div class="topbar-right">
                @yield('topbar-actions')
                <div class="topbar-divider"></div>
                <div class="topbar-user">
                    <div class="topbar-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                    {{-- redirect to profile --}}
                    <span class="topbar-user-name"> <a href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a></span>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="display:inline" id="logout-form">
                    @csrf
                    <button type="button" class="btn btn-ghost btn-sm" id="logout-trigger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="page-content">
            <div class="page-heading">
                <div>
                    <h1 class="page-title">@yield('page-title')</h1>
                    <p class="page-subtitle">@yield('page-subtitle')</p>
                </div>
                @hasSection('page-heading-actions')
                <div class="page-heading-actions">
                    @yield('page-heading-actions')
                </div>
                @endif
            </div>

            @yield('content')
        </main>
    </div>
</div>

@include('layouts.partials.toasts')

<div class="confirm-modal" id="logout-modal" style="display:none">
    <div class="confirm-modal-backdrop" id="logout-modal-backdrop"></div>

    <div class="confirm-modal-box">
        <h3 class="confirm-modal-title">Log out?</h3>

        <p class="confirm-modal-body">
            You will be signed out of your AI workspace and returned to the login screen.
        </p>

        <div class="confirm-modal-actions">
            <button type="button" class="btn btn-ghost" id="logout-cancel">
                Cancel
            </button>

            <button type="button" class="btn btn-danger-solid" id="logout-confirm">
                Logout
            </button>
        </div>
    </div>
</div>

<script>
    const _sidebar = document.getElementById('sidebar');
    const _overlay = document.getElementById('sidebar-overlay');
    document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
        _sidebar.classList.toggle('sidebar-open');
        _overlay.classList.toggle('active');
    });
    _overlay?.addEventListener('click', () => {
        _sidebar.classList.remove('sidebar-open');
        _overlay.classList.remove('active');
    });

    const _logoutForm = document.getElementById('logout-form');
    const _logoutTrigger = document.getElementById('logout-trigger');
    const _logoutModal = document.getElementById('logout-modal');
    const _logoutBackdrop = document.getElementById('logout-modal-backdrop');
    const _logoutCancel = document.getElementById('logout-cancel');
    const _logoutConfirm = document.getElementById('logout-confirm');

    const _openLogoutModal = () => {
        if (_logoutModal) _logoutModal.style.display = '';
    };

    const _closeLogoutModal = () => {
        if (_logoutModal) _logoutModal.style.display = 'none';
    };

    _logoutTrigger?.addEventListener('click', _openLogoutModal);
    _logoutCancel?.addEventListener('click', _closeLogoutModal);
    _logoutBackdrop?.addEventListener('click', _closeLogoutModal);
    _logoutConfirm?.addEventListener('click', () => _logoutForm?.submit());

</script>
@stack('scripts')
</body>
</html>
