<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Gazi Ustam</title>
    <meta name="description" content="Gazi Ustam - Usta ve iş takip yönetim sistemi">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">

    @stack('styles')
</head>

<body>

    <div class="app-wrapper">

        {{-- ===== SIDEBAR ===== --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <img src="{{ asset('images/logo.svg') }}" alt="Gazi Ustam Logo" style="width: 38px; height: 38px; border-radius: 10px; object-fit: contain;">
                <div style="flex: 1;">
                    <div class="logo-text">Gazi Ustam</div>
                    <div class="logo-sub">Yönetim Paneli</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                @if(Auth::user()->isAdmin())
                    <div class="nav-section">Admin Yönetim Paneli</div>

                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" />
                                <rect x="14" y="3" width="7" height="7" />
                                <rect x="14" y="14" width="7" height="7" />
                                <rect x="3" y="14" width="7" height="7" />
                            </svg>
                        </span>
                        <span>Admin Panel</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                        class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            </svg>
                        </span>
                        <span>Üye Yönetimi</span>
                    </a>

                    <a href="{{ route('admin.requests.index') }}"
                        class="nav-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </span>
                        <span>Müşteri Talepleri</span>
                        @php
                            $pendingLeadCount = \App\Models\ContactRequest::where('status', 'yeni')->count();
                        @endphp
                        @if($pendingLeadCount > 0)
                            <span style="margin-left: auto; background: #f59e0b; color: #fff; font-weight: 700; padding: 2px 8px; border-radius: 999px; font-size: 11px;">{{ $pendingLeadCount }}</span>
                        @endif
                    </a>
                @else
                    <div class="nav-section">Ana Menü</div>

                    <a href="{{ route('dashboard') }}"
                        class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7" />
                                <rect x="14" y="3" width="7" height="7" />
                                <rect x="14" y="14" width="7" height="7" />
                                <rect x="3" y="14" width="7" height="7" />
                            </svg>
                        </span>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('ustalar.index') }}"
                        class="nav-item {{ request()->routeIs('ustalar.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </span>
                        <span>Ustalar</span>
                    </a>

                    <a href="{{ route('isler.index') }}"
                        class="nav-item {{ request()->routeIs('isler.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                            </svg>
                        </span>
                        <span>İşler</span>
                    </a>

                    <div class="nav-section">Takip & Finans</div>

                    <a href="{{ route('devam.index') }}"
                        class="nav-item {{ request()->routeIs('devam.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </span>
                        <span>Devam Takibi</span>
                    </a>

                    <a href="{{ route('aylik-hesap.index') }}"
                        class="nav-item {{ request()->routeIs('aylik-hesap.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23" />
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                        </span>
                        <span>Aylık Hesap</span>
                    </a>

                    <a href="{{ route('gelir-gider.index') }}"
                        class="nav-item {{ request()->routeIs('gelir-gider.*') ? 'active' : '' }}">
                        <span class="nav-icon">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                                <polyline points="17 6 23 6 23 12" />
                            </svg>
                        </span>
                        <span>Gelir / Gider</span>
                    </a>
                @endif

                <div class="nav-section">Hesabım</div>

                <a href="{{ route('profile.edit') }}"
                    class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </span>
                    <span>Profilim & Abonelik</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('profile.edit') }}" class="user-info"
                    style="text-decoration:none; color:inherit; display:flex; align-items:center; width:100%;">
                    <div class="avatar">
                        {{ strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div style="flex:1; min-width:0; margin-left:8px;">
                        <div class="user-name"
                            style="font-weight:700; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ Auth::user()->name }}</div>
                        <div class="user-role" style="font-size:0.75rem; color:var(--text-muted);">
                            @if(Auth::user()->isAdmin())
                                Admin (Süresiz)
                            @elseif(Auth::user()->expires_at)
                                {{ Auth::user()->remainingDays() }} Gün Kaldı
                            @else
                                Süresiz
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="main-content">

            {{-- Top Header --}}
            <header class="top-header">
                <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                    <a href="{{ route('profile.edit') }}" class="mobile-profile-btn" title="Profilim & Abonelik"
                        aria-label="Profilim"
                        style="display:none; text-decoration:none; align-items:center; justify-content:center; flex-shrink:0;">
                        <div class="avatar"
                            style="width:34px; height:34px; font-size:0.8rem; font-weight:800; background:var(--accent-dark); color:#ffffff;">
                            {{ strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                    </a>
                    <h1 class="page-title" style="margin:0;">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="header-actions" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    @yield('header-actions')
                    <span class="header-date">
                        {{ now()->locale('tr')->isoFormat('D MMMM YYYY, dddd') }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" title="Çıkış Yap"
                            style="padding:6px 12px; font-weight:600;">
                            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                style="width:16px; height:16px; margin-right:4px;">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            <span>Çıkış</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="flash-wrapper" id="flashMsg">
                    <div class="alert alert-success">✓ {{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="flash-wrapper" id="flashMsg">
                    <div class="alert alert-danger">✕ {{ session('error') }}</div>
                </div>
            @endif

            {{-- Page Content --}}
            <main class="page-content">
                @yield('content')
            </main>
        </div>

        {{-- Mobile overlay --}}
        <div id="sidebarOverlay" onclick="toggleSidebar()"
            style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;backdrop-filter:blur(2px);">
        </div>
    </div>

    {{-- ===== MOBILE BOTTOM NAVIGATION BAR ===== --}}
    <nav class="mobile-bottom-nav">
        @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
                class="bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span class="bottom-nav-label">Admin</span>
            </a>
            <a href="{{ route('admin.users.index') }}"
                class="bottom-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
                <span class="bottom-nav-label">Üyeler</span>
            </a>
            <a href="{{ route('admin.requests.index') }}"
                class="bottom-nav-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}" style="position: relative;">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <span class="bottom-nav-label">Talepler</span>
                @php
                    $pendingLeadCount = \App\Models\ContactRequest::where('status', 'yeni')->count();
                @endphp
                @if($pendingLeadCount > 0)
                    <span style="position: absolute; top: 2px; right: 8px; background: #f59e0b; color: #fff; font-weight: 700; padding: 1px 5px; border-radius: 999px; font-size: 10px;">{{ $pendingLeadCount }}</span>
                @endif
            </a>
            <a href="{{ route('profile.edit') }}"
                class="bottom-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <span class="bottom-nav-label">Profil</span>
            </a>
        @else
            <a href="{{ route('dashboard') }}"
                class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span class="bottom-nav-label">Dashboard</span>
            </a>
            <a href="{{ route('ustalar.index') }}"
                class="bottom-nav-item {{ request()->routeIs('ustalar.*') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <span class="bottom-nav-label">Ustalar</span>
            </a>
            <a href="{{ route('isler.index') }}"
                class="bottom-nav-item {{ request()->routeIs('isler.*') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                </svg>
                <span class="bottom-nav-label">İşler</span>
            </a>
            <a href="{{ route('devam.index') }}"
                class="bottom-nav-item {{ request()->routeIs('devam.*') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
                <span class="bottom-nav-label">Devam</span>
            </a>
            <a href="{{ route('aylik-hesap.index') }}"
                class="bottom-nav-item {{ request()->routeIs('aylik-hesap.*') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23" />
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
                <span class="bottom-nav-label">Hesap</span>
            </a>
            <a href="{{ route('gelir-gider.index') }}"
                class="bottom-nav-item {{ request()->routeIs('gelir-gider.*') ? 'active' : '' }}">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                    <polyline points="17 6 23 6 23 12" />
                </svg>
                <span class="bottom-nav-label">Finans</span>
            </a>
        @endif
    </nav>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const isOpen = sidebar.classList.contains('open');
            if (isOpen) {
                sidebar.classList.remove('open');
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('open');
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }

        // Flash mesajı otomatik kapat
        setTimeout(() => {
            const msg = document.getElementById('flashMsg');
            if (msg) {
                msg.style.transition = 'opacity 0.5s';
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            }
        }, 4000);
    </script>

    @stack('scripts')
</body>

</html>