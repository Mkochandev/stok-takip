<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giriş Yap — Gazi Ustam</title>
    <meta name="description" content="Gazi Ustam Yönetim Paneli - Giriş Yapın">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* ===== Login Sayfası Özel Stilleri ===== */
        .login-page {
            min-height: 100vh;
            display: flex;
            background: var(--bg-primary);
            overflow: hidden;
            position: relative;
        }

        /* Sol panel - Dekoratif */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #0d1321 0%, #1a2035 40%, #1e1040 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(79,110,247,0.18) 0%, transparent 65%);
            top: -100px; left: -100px;
        }

        .login-left::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(124,93,249,0.15) 0%, transparent 65%);
            bottom: -50px; right: -50px;
        }

        .login-left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 420px;
        }

        .login-hero-icon {
            width: 100px; height: 100px;
            margin: 0 auto 28px;
            filter: drop-shadow(0 20px 40px rgba(16, 185, 129, 0.4));
            animation: float 3s ease-in-out infinite;
            object-fit: contain;
            display: block;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .login-left-content h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .login-left-content h2 span {
            background: linear-gradient(135deg, #10b981, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-left-content p {
            color: #cbd5e1;
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            text-align: left;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            padding: 12px 16px;
            backdrop-filter: blur(10px);
        }

        .feature-item .fi-icon {
            font-size: 1.2rem;
        }

        .feature-item .fi-text {
            font-size: 0.875rem;
            color: #f1f5f9;
            font-weight: 600;
        }

        /* Sağ panel - Login formu */
        .login-right {
            width: 480px;
            min-height: 100vh;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 48px;
            position: relative;
            border-left: 1px solid var(--border-color);
        }

        .login-form-wrap {
            width: 100%;
            max-width: 380px;
        }

        .login-form-header {
            margin-bottom: 36px;
        }

        .login-form-header .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .login-form-header .brand-icon {
            width: 44px; height: 44px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(16, 185, 129, 0.3));
        }

        .login-form-header .brand-name {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        .login-form-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .login-form-header p {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .login-input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .login-input-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 8px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .login-input-group .input-icon-wrap {
            position: relative;
        }

        .login-input-group .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            pointer-events: none;
        }

        .login-input-group input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: #ffffff;
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 500;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .login-input-group input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        .login-input-group input::placeholder {
            color: var(--text-muted);
        }

        .login-input-group .error-msg {
            font-size: 0.78rem;
            color: #ef4444;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Input with error border */
        .login-input-group.has-error input {
            border-color: #ef4444;
        }

        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            margin-top: -4px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #10b981;
            cursor: pointer;
        }

        .remember-me span {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .forgot-link {
            font-size: 0.85rem;
            color: #059669;
            font-weight: 600;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .forgot-link:hover {
            opacity: 0.8;
            color: #047857;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 8px 22px rgba(16, 185, 129, 0.4);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .session-status {
            padding: 12px 16px;
            background: rgba(34,197,94,0.12);
            border: 1px solid rgba(34,197,94,0.25);
            border-radius: 10px;
            color: var(--accent-success);
            font-size: 0.875rem;
            margin-bottom: 20px;
        }

        .error-banner {
            padding: 12px 16px;
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 10px;
            color: var(--accent-danger);
            font-size: 0.875rem;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .login-left { display: none; }
            .login-right {
                width: 100%;
                border-left: none;
                padding: 40px 24px;
            }
        }

        /* Animated background grid */
        .grid-bg {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(79,110,247,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79,110,247,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }
    </style>
</head>
<body>

<div class="login-page">

    {{-- Sol Dekoratif Panel --}}
    <div class="login-left">
        <div class="grid-bg"></div>
        <div class="login-left-content">
            <img src="{{ asset('images/logo.svg') }}" alt="Gazi Ustam Logo" class="login-hero-icon">
            <h2>Gazi Ustam <span>Yönetim</span> Sistemi</h2>
            <p>Ustalarınızı, işlerinizi ve finansal süreçlerinizi tek yerden kolayca yönetin.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <span class="fi-icon">👷</span>
                    <span class="fi-text">Usta ve çalışan takibi</span>
                </div>
                <div class="feature-item">
                    <span class="fi-icon">📅</span>
                    <span class="fi-text">Devam & mesai yönetimi</span>
                </div>
                <div class="feature-item">
                    <span class="fi-icon">💰</span>
                    <span class="fi-text">Gelir-gider analizi</span>
                </div>
                <div class="feature-item">
                    <span class="fi-icon">📊</span>
                    <span class="fi-text">Gerçek zamanlı dashboard</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sağ Login Formu --}}
    <div class="login-right">
        <div class="login-form-wrap">

            <div class="login-form-header">
                <div class="brand">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="brand-icon">
                    <div class="brand-name">Gazi Ustam</div>
                </div>
                <h1>Hoş Geldiniz</h1>
                <p>Devam etmek için hesabınıza giriş yapın</p>
            </div>

            {{-- Session Status --}}
            @if(session('status'))
                <div class="session-status">{{ session('status') }}</div>
            @endif

            {{-- Genel hata --}}
            @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="error-banner">❌ Lütfen hataları düzeltin.</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- E-posta --}}
                <div class="login-input-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email">E-Posta Adresi</label>
                    <div class="input-icon-wrap">
                        <span class="input-icon">✉️</span>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="ornek@email.com"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                    @error('email')
                        <div class="error-msg">⚠️ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Şifre --}}
                <div class="login-input-group {{ $errors->has('password') ? 'has-error' : '' }}">
                    <label for="password">Şifre</label>
                    <div class="input-icon-wrap">
                        <span class="input-icon">🔒</span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                    @error('password')
                        <div class="error-msg">⚠️ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Seçenekler --}}
                <div class="login-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember_me">
                        <span>Beni hatırla</span>
                    </label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Şifremi unuttum</a>
                    @endif
                </div>

                {{-- Giriş Butonu --}}
                <button type="submit" class="btn-login" id="loginBtn">
                    🚀 Giriş Yap
                </button>
            </form>

        </div>
    </div>
</div>

<script>
// Butona basıldığında loading efekti
document.getElementById('loginBtn').addEventListener('click', function() {
    this.innerHTML = '⏳ Giriş yapılıyor...';
    this.style.opacity = '0.7';
});
</script>

</body>
</html>
