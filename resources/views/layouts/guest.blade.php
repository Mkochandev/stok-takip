<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gazi Ustam</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        .guest-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-primary);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        .guest-page::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(79,110,247,0.1) 0%, transparent 70%);
            top: -150px; right: -150px;
            pointer-events: none;
        }
        .guest-page::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(124,93,249,0.08) 0%, transparent 70%);
            bottom: -100px; left: -100px;
            pointer-events: none;
        }
        .guest-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: var(--shadow-lg);
            position: relative;
            z-index: 1;
        }
        .guest-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .guest-logo .logo-icon-big {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: var(--radius-lg);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 12px;
            box-shadow: 0 10px 30px rgba(79,110,247,0.3);
        }
        .guest-logo h1 { font-size: 1.4rem; font-weight: 700; }
        .guest-logo p { color: var(--text-muted); font-size: 0.875rem; margin-top: 4px; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 20px;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: var(--accent-primary); }
    </style>
</head>
<body>
<div class="guest-page">
    <div class="guest-card">
        <div class="guest-logo">
            <div class="logo-icon-big">⚙️</div>
            <h1>Gazi Ustam</h1>
            <p>Yönetim Paneli</p>
        </div>
        {{ $slot }}
        <div style="text-align:center;">
            <a href="{{ route('login') }}" class="back-link">← Giriş sayfasına dön</a>
        </div>
    </div>
</div>
</body>
</html>
