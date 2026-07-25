<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üyelik Süreniz Doldu — Gazi Ustam</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            font-family: 'Inter', sans-serif;
            color: #f8fafc;
        }
        .expired-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .lock-icon {
            font-size: 64px;
            margin-bottom: 20px;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }
        .title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 12px;
            color: #ffffff;
        }
        .description {
            color: #94a3b8;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .info-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 28px;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .info-box span {
            color: #fca5a5;
            font-size: 14px;
        }
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="expired-card">
        <div class="lock-icon">🔐</div>
        <h1 class="title">Üyelik Süreniz Doldu</h1>
        <p class="description">
            Sayın <strong>{{ auth()->user()->name }}</strong>, hesabınızın kullanım süresi dolmuştur. Usta iş takip panelinizi ve kayıtlarınızı kullanmaya devam edebilmek için lütfen yönetici ile iletişime geçiniz.
        </p>

        <div class="info-box">
            <div style="font-size: 24px;">⚠️</div>
            <div>
                <strong style="color: #ef4444; display: block; font-size: 14px; margin-bottom: 2px;">Son Geçerlilik Tarihi:</strong>
                <span>{{ auth()->user()->expires_at ? auth()->user()->expires_at->locale('tr')->isoFormat('D MMMM YYYY, HH:mm') : 'Belirtilmedi' }}</span>
            </div>
        </div>

        <div class="btn-group">
            <a href="{{ route('profile.edit') }}" class="btn-secondary">
                👤 Profilimi Göster
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-primary">
                    🚪 Çıkış Yap
                </button>
            </form>
        </div>
    </div>
</body>
</html>
