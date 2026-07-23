@extends('layouts.app')

@section('title', 'Yeni Üye Ekle')
@section('page-title', 'Yeni Üye Tanımla')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
        ⬅️ Üye Listesine Dön
    </a>
@endsection

@section('content')
<div class="card fade-in" style="max-width: 680px; margin: 0 auto;">
    <h2 style="font-size: 1.25rem; font-weight:700; margin: 0 0 20px 0; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
        ➕ Yeni Üye Hesabı ve Üyelik Süresi Oluştur
    </h2>

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <ul style="margin:0; padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        <div style="margin-bottom: 16px;">
            <label class="form-label" style="font-weight:600;">Ad Soyad / Firma Adı <span style="color:red;">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="Örn: Ahmet Usta Veya Gazi İnşaat">
        </div>

        <div style="margin-bottom: 16px;">
            <label class="form-label" style="font-weight:600;">E-Posta Adresi (Giriş için) <span style="color:red;">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required placeholder="ahmet@gmail.com">
        </div>

        <div style="margin-bottom: 16px;">
            <label class="form-label" style="font-weight:600;">Giriş Şifresi <span style="color:red;">*</span></label>
            <input type="password" name="password" class="form-control" required placeholder="En az 6 karakter">
        </div>

        <div style="margin-bottom: 20px; background: rgba(255,255,255,0.03); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
            <label class="form-label" style="font-weight:700; color:var(--text-primary); display:block; margin-bottom:8px;">
                ⏱️ Üyelik Geçerlilik Süresi
            </label>
            
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-bottom: 12px;">
                <label style="background:var(--bg-card-hover); padding:10px; border-radius:8px; border:1px solid var(--border-color); cursor:pointer; font-size:0.85rem; font-weight:600;">
                    <input type="radio" name="preset_süre" value="14_gun" checked> 14 Gün Deneme
                </label>
                <label style="background:var(--bg-card-hover); padding:10px; border-radius:8px; border:1px solid var(--border-color); cursor:pointer; font-size:0.85rem; font-weight:600;">
                    <input type="radio" name="preset_süre" value="1_ay"> 1 Ay Üyelik
                </label>
                <label style="background:var(--bg-card-hover); padding:10px; border-radius:8px; border:1px solid var(--border-color); cursor:pointer; font-size:0.85rem; font-weight:600;">
                    <input type="radio" name="preset_süre" value="3_ay"> 3 Ay Üyelik
                </label>
                <label style="background:var(--bg-card-hover); padding:10px; border-radius:8px; border:1px solid var(--border-color); cursor:pointer; font-size:0.85rem; font-weight:600;">
                    <input type="radio" name="preset_süre" value="1_yil"> 1 Yıl Üyelik
                </label>
                <label style="background:var(--bg-card-hover); padding:10px; border-radius:8px; border:1px solid var(--border-color); cursor:pointer; font-size:0.85rem; font-weight:600;">
                    <input type="radio" name="preset_süre" value="suresiz"> ∞ Süresiz
                </label>
            </div>

            <div style="margin-top: 10px;">
                <label class="form-label" style="font-size:0.85rem; color:var(--text-muted);">Veya Özel Bir Son Kullanma Tarihi Seçin:</label>
                <input type="date" name="expires_at" class="form-control" style="font-size:0.9rem;">
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }} style="width:18px; height:18px;">
                <span>👑 Bu Kullanıcıyı Ana Admin (Yönetici) Yap</span>
            </label>
            <div style="font-size:0.8rem; color:var(--text-muted); margin-left:26px; margin-top:2px;">
                İşaretlenirse bu üye tüm diğer üyeleri yönetebilir ve süre kısıtlamasına takılmaz.
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">💾 Üyeyi Kaydet</button>
        </div>
    </form>
</div>
@endsection
