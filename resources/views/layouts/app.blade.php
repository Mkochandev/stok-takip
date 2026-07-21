<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Gazi Ustam</title>
    <meta name="description" content="Gazi Ustam - Usta ve iş takip yönetim sistemi">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>

<div class="app-wrapper">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">⚙️</div>
            <div>
                <div class="logo-text">Gazi Ustam</div>
                <div class="logo-sub">Yönetim Paneli</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Ana Menü</div>

            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('ustalar.index') }}"
               class="nav-item {{ request()->routeIs('ustalar.*') ? 'active' : '' }}">
                <span class="nav-icon">👷</span>
                <span>Ustalar</span>
            </a>

            <a href="{{ route('isler.index') }}"
               class="nav-item {{ request()->routeIs('isler.*') ? 'active' : '' }}">
                <span class="nav-icon">🏢</span>
                <span>İşler</span>
            </a>

            <div class="nav-section">Takip</div>

            <a href="{{ route('devam.index') }}"
               class="nav-item {{ request()->routeIs('devam.*') ? 'active' : '' }}">
                <span class="nav-icon">📅</span>
                <span>Devam Takibi</span>
            </a>

            <a href="{{ route('aylik-hesap.index') }}"
               class="nav-item {{ request()->routeIs('aylik-hesap.*') ? 'active' : '' }}">
                <span class="nav-icon">💰</span>
                <span>Aylık Hesap</span>
            </a>

            <div class="nav-section">Finans</div>

            <a href="{{ route('gelir-gider.index') }}"
               class="nav-item {{ request()->routeIs('gelir-gider.*') ? 'active' : '' }}">
                <span class="nav-icon">📈</span>
                <span>Gelir / Gider</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="avatar">
                    {{ strtoupper(mb_substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ Auth::user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" title="Çıkış Yap"
                            style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1.1rem;padding:4px;transition:color 0.2s;"
                            onmouseover="this.style.color='var(--accent-danger)'"
                            onmouseout="this.style.color='var(--text-muted)'">
                        🚪
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="main-content">

        {{-- Top Header --}}
        <header class="top-header">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" onclick="toggleSidebar()">☰</button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="header-actions">
                @yield('header-actions')
                <span style="font-size:0.8rem; color:var(--text-muted); margin-left:8px;">
                    {{ now()->locale('tr')->isoFormat('D MMMM YYYY, dddd') }}
                </span>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div style="margin: 16px 24px 0;" id="flashMsg">
                <div class="alert alert-success fade-in">✅ {{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div style="margin: 16px 24px 0;" id="flashMsg">
                <div class="alert alert-danger fade-in">❌ {{ session('error') }}</div>
            </div>
        @endif

        {{-- Page Content --}}
        <main class="page-content fade-in">
            @yield('content')
        </main>
    </div>

    {{-- Mobile overlay --}}
    <div id="sidebarOverlay" onclick="toggleSidebar()"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:99;backdrop-filter:blur(2px);"></div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isOpen = sidebar.classList.contains('open');
    if (isOpen) {
        sidebar.classList.remove('open');
        overlay.style.display = 'none';
    } else {
        sidebar.classList.add('open');
        overlay.style.display = 'block';
    }
}

// Flash mesajını otomatik kapat
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
