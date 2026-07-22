@extends('layouts.app')

@section('title', 'Aylık Hesap')
@section('page-title', '📋 Aylık Hesap Kapatma')

@push('styles')
<style>
.hesap-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 20px;
    margin-bottom: 12px;
    transition: border-color var(--transition);
}
.hesap-card.kapali {
    opacity: 0.7;
    border-color: rgba(34,197,94,0.3);
    background: rgba(34,197,94,0.03);
}
.hesap-card.odeme-bekliyor {
    border-color: rgba(245,158,11,0.4);
    background: rgba(245,158,11,0.03);
}
.hesap-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
}
.hesap-body {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 12px;
    margin-bottom: 16px;
}
.hesap-item {
    background: var(--bg-primary);
    border-radius: var(--radius-sm);
    padding: 10px 12px;
}
.hesap-item .label { font-size:0.7rem; color:var(--text-muted); margin-bottom:4px; }
.hesap-item .val { font-size:1rem; font-weight:700; }
.hesap-footer {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--border-color);
}
.odeme-form {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}
.odeme-form input[type=number] {
    width: 140px;
    padding: 7px 10px;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-family: inherit;
    font-size: 0.9rem;
    outline: none;
}
.odeme-form input[type=number]:focus { border-color: var(--accent-primary); }
</style>
@endpush

@section('content')

{{-- Ay/Yıl Seçici + Arama --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('aylik-hesap.index') }}" class="filter-bar">
        <div>
            <label class="form-label">Ay</label>
            <select name="ay" class="form-select" style="width:130px;">
                @foreach($aylar as $num => $isim)
                    <option value="{{ $num }}" {{ $ay == $num ? 'selected' : '' }}>{{ $isim }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Yıl</label>
            <select name="yil" class="form-select" style="width:100px;">
                @foreach($yillar as $y)
                    <option value="{{ $y }}" {{ $yil == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1; min-width:180px;">
            <label class="form-label">Usta Ara</label>
            <div style="position:relative;">
                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); pointer-events:none;">🔍</span>
                <input type="text" name="q" value="{{ $aramaQuery ?? '' }}"
                       placeholder="Usta adı ara..."
                       class="form-control" style="padding-left:36px;">
            </div>
        </div>
        <div style="align-self:flex-end;">
            <button type="submit" class="btn btn-primary">Göster</button>
            @if($aramaQuery)
                <a href="{{ route('aylik-hesap.index', ['ay' => $ay, 'yil' => $yil]) }}" class="btn btn-secondary btn-sm" style="margin-left:4px;">✕</a>
            @endif
        </div>
        <div class="filter-period" style="margin-left:auto; align-self:flex-end; font-size:0.85rem; color:var(--text-muted);">
            {{ $aylar[$ay] ?? '' }} {{ $yil }} dönemi
        </div>
    </form>
</div>


{{-- Özet --}}
@php
    $toplamHakedis = $hesaplar->sum('toplam_hakedis');
    $toplamOdenen  = $hesaplar->sum(fn($h) => $h['odeme'] ? $h['odeme']->odenen_tutar : 0);
    $toplamKalan   = $hesaplar->sum('kalan');
@endphp

<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon blue">📋</div>
        <div>
            <div class="stat-value">{{ $hesaplar->count() }}</div>
            <div class="stat-label">Çalışan Usta</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">💰</div>
        <div>
            <div class="stat-value">{{ number_format($toplamHakedis, 0, ',', '.') }}₺</div>
            <div class="stat-label">Toplam Hakedis</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div>
            <div class="stat-value">{{ number_format($toplamOdenen, 0, ',', '.') }}₺</div>
            <div class="stat-label">Ödenen</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon {{ $toplamKalan > 0 ? 'red' : 'green' }}">⏳</div>
        <div>
            <div class="stat-value {{ $toplamKalan > 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format($toplamKalan, 0, ',', '.') }}₺
            </div>
            <div class="stat-label">Kalan Borç</div>
        </div>
    </div>
</div>

{{-- Usta Hesap Kartları --}}
@forelse($hesaplar as $hesap)
@php $h = $hesap; @endphp
<div class="hesap-card {{ $h['kapandi'] ? 'kapali' : ($h['kalan'] > 0 ? 'odeme-bekliyor' : '') }}">

    <div class="hesap-header">
        <div class="avatar-circle" style="width:48px;height:48px;font-size:1.1rem;">
            {{ strtoupper(mb_substr($h['usta']->ad, 0, 1)) }}{{ strtoupper(mb_substr($h['usta']->soyad, 0, 1)) }}
        </div>
        <div style="flex:1;">
            <div class="fw-semibold" style="font-size:1.05rem;">{{ $h['usta']->ad_soyad }}</div>
            <div class="text-muted fs-sm">{{ $h['usta']->uzmanlik ?? 'Genel İşçi' }}</div>
        </div>
        @if($h['kapandi'])
            <span class="badge badge-success" style="font-size:0.85rem;">✅ Hesap Kapalı</span>
        @elseif($h['kalan'] <= 0)
            <span class="badge badge-info">Tam Ödendi</span>
        @else
            <span class="badge badge-warning">⏳ {{ number_format($h['kalan'], 0, ',', '.') }}₺ Kalan</span>
        @endif
        <a href="{{ route('ustalar.show', $h['usta']->id) }}" class="btn btn-secondary btn-sm">👁️</a>
    </div>

    {{-- Çalışma Dökümü --}}
    <div class="hesap-body">
        <div class="hesap-item">
            <div class="label">✅ Tam Gün</div>
            <div class="val">{{ $h['tam_gun'] }} gün</div>
            <div class="text-muted fs-sm">{{ number_format($h['tam_gun'] * $h['usta']->gunluk_ucret, 0, ',', '.') }}₺</div>
        </div>
        <div class="hesap-item">
            <div class="label">🌗 Yarım Gün</div>
            <div class="val">{{ $h['yarim_gun'] }} gün</div>
            <div class="text-muted fs-sm">{{ number_format($h['yarim_gun'] * ($h['usta']->gunluk_ucret / 2), 0, ',', '.') }}₺</div>
        </div>
        <div class="hesap-item">
            <div class="label">⏰ Mesai</div>
            <div class="val">{{ $h['mesai_saati'] }} saat</div>
            <div class="text-muted fs-sm">{{ number_format($h['mesai_saati'] * $h['usta']->mesai_saatlik_ucret, 0, ',', '.') }}₺</div>
        </div>
        <div class="hesap-item" style="border: 1px solid rgba(79,110,247,0.3); background: rgba(79,110,247,0.05);">
            <div class="label">💰 Toplam Hakedis</div>
            <div class="val text-primary" style="font-size:1.2rem;">{{ number_format($h['toplam_hakedis'], 0, ',', '.') }}₺</div>
            @if($h['odeme'])
                <div class="text-muted fs-sm">Ödenen: {{ number_format($h['odeme']->odenen_tutar, 0, ',', '.') }}₺</div>
            @endif
        </div>
    </div>

    {{-- Ödeme Formu --}}
    @if(!$h['kapandi'])
    <div class="hesap-footer">
        <form action="{{ route('aylik-hesap.odeme') }}" method="POST" class="odeme-form">
            @csrf
            <input type="hidden" name="usta_id" value="{{ $h['usta']->id }}">
            <input type="hidden" name="ay" value="{{ $ay }}">
            <input type="hidden" name="yil" value="{{ $yil }}">
            <input type="number" name="odenen_tutar" placeholder="Ödeme tutarı ₺"
                   min="0" step="0.01" required
                   value="{{ $h['kalan'] > 0 ? number_format($h['kalan'], 2, '.', '') : '' }}">
            <select name="odeme_yontemi" class="form-select" style="width:120px;">
                <option value="nakit">💵 Nakit</option>
                <option value="havale">🏦 Havale</option>
                <option value="çek">📄 Çek</option>
            </select>
            <button type="submit" class="btn btn-success btn-sm">💸 Ödeme Yap</button>
        </form>

        <form action="{{ route('aylik-hesap.kapat') }}" method="POST"
              onsubmit="return confirm('Hesabı kapatmak istediğinizden emin misiniz?')">
            @csrf
            <input type="hidden" name="usta_id" value="{{ $h['usta']->id }}">
            <input type="hidden" name="ay" value="{{ $ay }}">
            <input type="hidden" name="yil" value="{{ $yil }}">
            <button type="submit" class="btn btn-secondary btn-sm">🔒 Hesabı Kapat</button>
        </form>
    </div>
    @else
    <div class="hesap-footer" style="justify-content:flex-end;">
        <span class="text-muted fs-sm">
            Kapatıldı: {{ $h['odeme']?->odeme_tarihi?->locale('tr')->isoFormat('D MMMM YYYY') ?? '—' }}
        </span>
    </div>
    @endif
</div>
@empty
<div class="card text-center" style="padding:60px;">
    <div style="font-size:3rem;">📋</div>
    <div class="fw-semibold mt-3" style="font-size:1.1rem;">Bu ay çalışan usta yok</div>
    <div class="text-muted mt-2">Devam kaydı girilmemiş. Önce devam takibinden kayıt ekleyin.</div>
    <a href="{{ route('devam.index') }}" class="btn btn-primary mt-3">📅 Devam Takibine Git</a>
</div>
@endforelse

@endsection
