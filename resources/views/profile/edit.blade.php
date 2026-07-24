@extends('layouts.app')

@section('title', 'Profilim & Üyelik Bilgileri')
@section('page-title', 'Profilim & Abonelik')

@section('content')
<div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    {{-- ===== ABONELİK BİLGİLERİ KARTI ===== --}}
    {{-- ===== ABONELİK BİLGİLERİ KARTI ===== --}}
    <div class="card fade-in" style="background: #ffffff; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h2 style="font-size: 1.25rem; font-weight:800; color:var(--text-primary); margin:0; display:flex; align-items:center; gap:8px;">
                💳 Üyelik ve Paket Durumu
            </h2>
            @if(auth()->user()->isAdmin())
                <span class="badge badge-info" style="padding: 6px 12px; font-weight:700;">
                    👑 Ana Admin (Süresiz Tam Yetki)
                </span>
            @elseif(auth()->user()->isExpired())
                <span class="badge badge-danger" style="padding: 6px 12px; font-weight:700;">
                    ✕ Üyelik Süresi Doldu
                </span>
            @else
                <span class="badge badge-success" style="padding: 6px 12px; font-weight:700;">
                    ● Aktif Abonelik
                </span>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
            <div style="background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size:0.8rem; color:var(--text-muted); font-weight:600; margin-bottom:4px;">Hesap Türü</div>
                <div style="font-weight:800; font-size:1rem; color:var(--text-primary);">
                    {{ auth()->user()->isAdmin() ? 'Ana Yönetici (Admin)' : 'Usta / İş Takip Hesabı' }}
                </div>
            </div>

            <div style="background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size:0.8rem; color:var(--text-muted); font-weight:600; margin-bottom:4px;">Son Geçerlilik Tarihi</div>
                <div style="font-weight:800; font-size:1rem; color: {{ auth()->user()->isExpired() ? '#ef4444' : '#059669' }};">
                    @if(auth()->user()->isAdmin() || is_null(auth()->user()->expires_at))
                        Süresiz (Sınırsız Erişilebilir)
                    @else
                        {{ auth()->user()->expires_at->locale('tr')->isoFormat('D MMMM YYYY, HH:mm') }}
                    @endif
                </div>
            </div>

            <div style="background: #f8fafc; padding: 14px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="font-size:0.8rem; color:var(--text-muted); font-weight:600; margin-bottom:4px;">Kalan Kullanım Süresi</div>
                <div style="font-weight:800; font-size:1rem; color:var(--text-primary);">
                    @if(auth()->user()->isAdmin() || is_null(auth()->user()->expires_at))
                        ∞ Sınırsız
                    @elseif(auth()->user()->isExpired())
                        <span style="color:#ef4444;">0 Gün (Süresi Doldu)</span>
                    @else
                        <span style="color:#2563eb;">{{ auth()->user()->remainingDays() }} Gün</span>
                    @endif
                </div>
            </div>
        </div>

        @if(!auth()->user()->isAdmin())
            <div style="font-size:0.85rem; color:#475569; background:#eff6ff; padding:12px 16px; border-radius:8px; border:1px dashed #bfdbfe;">
                ℹ️ Üyeliğinizi uzatmak veya paket yenilemek için lütfen sistem yöneticisi ile iletişime geçiniz.
            </div>
        @endif
    </div>

    {{-- ===== PROFİL BİLGİLERİ GÜNCELLEME ===== --}}
    <div class="card fade-in">
        <h3 style="font-size: 1.1rem; font-weight:700; margin-bottom:16px;">👤 Profil Bilgileri</h3>
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- ===== ŞİFRE GÜNCELLEME ===== --}}
    <div class="card fade-in">
        <h3 style="font-size: 1.1rem; font-weight:700; margin-bottom:16px;">🔑 Şifre Değiştir</h3>
        @include('profile.partials.update-password-form')
    </div>

</div>
@endsection
