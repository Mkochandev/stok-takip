@extends('layouts.app')

@section('title', 'Üye Düzenle — ' . $user->name)
@section('page-title', 'Üyelik Düzenleme')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
        ⬅️ Üye Listesine Dön
    </a>
@endsection

@section('content')
<div class="card fade-in" style="max-width: 680px; margin: 0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
        <h2 style="font-size: 1.25rem; font-weight:700; margin:0;">
            ✏️ Üye Bilgileri ve Üyelik Süresi Düzenle
        </h2>
        <a href="{{ route('admin.users.backup', $user->id) }}" class="btn btn-sm" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
            💾 Kayıtları Yedekle (JSON)
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 16px;">
            <label class="form-label" style="font-weight:600;">Ad Soyad / Firma Adı <span style="color:red;">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
        </div>

        <div style="margin-bottom: 16px;">
            <label class="form-label" style="font-weight:600;">E-Posta Adresi <span style="color:red;">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>

        <div style="margin-bottom: 16px;">
            <label class="form-label" style="font-weight:600;">Yeni Şifre (Değiştirmek istemiyorsanız boş bırakın)</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••">
        </div>

        <div style="margin-bottom: 20px; background: rgba(255,255,255,0.03); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
            <label class="form-label" style="font-weight:700; color:var(--text-primary); display:block; margin-bottom:8px;">
                ⏱️ Üyelik Son Geçerlilik Tarihi
            </label>

            <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:10px;">
                Mevcut Durum: 
                @if($user->isAdmin() || is_null($user->expires_at))
                    <strong style="color:#34d399;">∞ Süresiz</strong>
                @elseif($user->isExpired())
                    <strong style="color:#f87171;">✕ Süresi Dolmuş ({{ $user->expires_at->format('d.m.Y H:i') }})</strong>
                @else
                    <strong style="color:#60a5fa;">● {{ $user->remainingDays() }} Gün Kaldı ({{ $user->expires_at->format('d.m.Y H:i') }})</strong>
                @endif
            </div>

            <input type="date" name="expires_at" value="{{ old('expires_at', $user->expires_at ? $user->expires_at->format('Y-m-d') : '') }}" class="form-control">
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">
                Tarihi boş bırakırsanız üyenin erişimi <strong>Süresiz</strong> olur.
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} style="width:18px; height:18px;">
                <span>👑 Ana Admin (Yönetici) Yetkisi Ver</span>
            </label>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">💾 Değişiklikleri Kaydet</button>
        </div>
    </form>
</div>
@endsection
