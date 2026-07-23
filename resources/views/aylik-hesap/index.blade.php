@extends('layouts.app')

@section('title', 'Aylık Hesap & Ödeme Yönetimi')
@section('page-title', 'Aylık Hesap & Ödeme Yönetimi')

@push('styles')
<style>
.payment-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}
.payment-card.kapali {
    background: #f8fafc;
    border-color: #e2e8f0;
}
.payment-card.bekliyor {
    border-left: 4px solid var(--accent-warning);
}
.payment-card.tamamlandi {
    border-left: 4px solid var(--accent-primary);
}
.progress-bar-bg {
    width: 100%;
    height: 10px;
    background: #f1f5f9;
    border-radius: var(--radius-full);
    overflow: hidden;
    margin: 12px 0 16px;
}
.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--accent-primary), #34d399);
    border-radius: var(--radius-full);
    transition: width 0.4s ease;
}
.work-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}
.work-chip {
    background: #f8fafc;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 12px 14px;
}
.work-chip .chip-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
.work-chip .chip-val { font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin-top: 2px; }
.work-chip .chip-sub { font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); }

.action-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
    flex-wrap: wrap;
}
.pay-form {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    flex-wrap: wrap;
}
</style>
@endpush

@section('content')

{{-- Dönem Seçici ve Arama Filtresi --}}
<div class="card" style="padding:16px 24px; margin-bottom:24px;">
    <form method="GET" action="{{ route('aylik-hesap.index') }}" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:0.85rem; font-weight:700; color:var(--text-secondary);">Dönem:</label>
                <select name="ay" class="form-select" style="width:140px;">
                    @foreach($aylar as $num => $isim)
                        <option value="{{ $num }}" {{ $ay == $num ? 'selected' : '' }}>{{ $isim }}</option>
                    @endforeach
                </select>
                <select name="yil" class="form-select" style="width:100px;">
                    @foreach($yillar as $y)
                        <option value="{{ $y }}" {{ $yil == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div style="position:relative; width:220px;">
                <input type="text" name="q" value="{{ $aramaQuery ?? '' }}" placeholder="Usta adı ara..." class="form-control" style="padding-left:36px;">
                <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-muted); width:16px; height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Filtrele</button>
            @if($aramaQuery)
                <a href="{{ route('aylik-hesap.index', ['ay' => $ay, 'yil' => $yil]) }}" class="btn btn-secondary btn-sm">Temizle</a>
            @endif
        </div>

        <div style="font-size:0.85rem; font-weight:700; color:var(--text-muted);">
            {{ $aylar[$ay] ?? '' }} {{ $yil }} Hesap Dönemi
        </div>
    </form>
</div>

{{-- Genel Dönem Özet Kartları --}}
@php
    $toplamHakedis = $hesaplar->sum('toplam_hakedis');
    $toplamOdenen  = $hesaplar->sum(fn($h) => $h['odeme'] ? $h['odeme']->odenen_tutar : 0);
    $toplamKalan   = $hesaplar->sum('kalan');
    $odemeYuzdesi  = $toplamHakedis > 0 ? round(($toplamOdenen / $toplamHakedis) * 100) : 100;
@endphp

<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon dark">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $hesaplar->count() }}</div>
            <div class="stat-label">Çalışan Usta</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ number_format($toplamHakedis, 0, ',', '.') }}₺</div>
            <div class="stat-label">Toplam Hakediş</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon mint">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--accent-primary);">{{ number_format($toplamOdenen, 0, ',', '.') }}₺</div>
            <div class="stat-label">Toplam Ödenen (%{{ $odemeYuzdesi }})</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon {{ $toplamKalan > 0 ? '' : 'mint' }}" style="{{ $toplamKalan > 0 ? 'background:#fef2f2; color:#ef4444; border-color:#fee2e2;' : '' }}">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color: {{ $toplamKalan > 0 ? 'var(--accent-danger)' : 'var(--accent-primary)' }};">
                {{ number_format($toplamKalan, 0, ',', '.') }}₺
            </div>
            <div class="stat-label">Kalan Borç</div>
        </div>
    </div>
</div>

{{-- Usta Ödeme Kartları --}}
@forelse($hesaplar as $hesap)
@php 
    $h = $hesap; 
    $ustaYuzde = $h['toplam_hakedis'] > 0 
        ? round((($h['odeme'] ? $h['odeme']->odenen_tutar : 0) / $h['toplam_hakedis']) * 100) 
        : 100;
@endphp
<div class="payment-card {{ $h['kapandi'] ? 'kapali' : ($h['kalan'] > 0 ? 'bekliyor' : 'tamamlandi') }}">

    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div class="avatar" style="width:46px; height:46px; background:var(--accent-dark); color:#ffffff; font-size:1.1rem; font-weight:800; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                {{ strtoupper(mb_substr($h['usta']->ad, 0, 1)) }}{{ strtoupper(mb_substr($h['usta']->soyad, 0, 1)) }}
            </div>
            <div>
                <a href="{{ route('ustalar.show', $h['usta']->id) }}" style="font-size:1.1rem; font-weight:800; color:var(--text-primary);">
                    {{ $h['usta']->ad_soyad }}
                </a>
                <div style="font-size:0.8rem; font-weight:600; color:var(--text-muted);">
                    {{ $h['usta']->uzmanlik ?? 'Genel Usta' }}
                </div>
            </div>
        </div>

        <div>
            @if($h['kapandi'])
                <span class="badge badge-gray">Hesap Kapatıldı</span>
            @elseif($h['kalan'] <= 0)
                <span class="badge badge-mint">✓ Tam Ödendi (%100)</span>
            @else
                <span class="badge badge-warning">Ödeme Bekliyor (%{{ $ustaYuzde }} Ödendi)</span>
            @endif
        </div>
    </div>

    {{-- İlerleme Çubuğu --}}
    <div class="progress-bar-bg">
        <div class="progress-bar-fill" style="width: {{ max($ustaYuzde, 2) }}%;"></div>
    </div>

    {{-- Çalışma Detay Çipleri --}}
    <div class="work-summary-grid">
        <div class="work-chip">
            <div class="chip-label">Tam Gün</div>
            <div class="chip-val">{{ $h['tam_gun'] }} gün</div>
            <div class="chip-sub">{{ number_format($h['tam_gun'] * $h['usta']->gunluk_ucret, 0, ',', '.') }}₺</div>
        </div>
        <div class="work-chip">
            <div class="chip-label">Yarım Gün</div>
            <div class="chip-val">{{ $h['yarim_gun'] }} gün</div>
            <div class="chip-sub">{{ number_format($h['yarim_gun'] * ($h['usta']->gunluk_ucret / 2), 0, ',', '.') }}₺</div>
        </div>
        <div class="work-chip">
            <div class="chip-label">Mesai Saati</div>
            <div class="chip-val">{{ $h['mesai_saati'] }} saat</div>
            <div class="chip-sub">{{ number_format($h['mesai_saati'] * $h['usta']->mesai_saatlik_ucret, 0, ',', '.') }}₺</div>
        </div>
        <div class="work-chip" style="background:var(--accent-light); border-color:rgba(16, 185, 129, 0.2);">
            <div class="chip-label" style="color:var(--accent-primary);">Toplam Hakediş</div>
            <div class="chip-val" style="color:var(--accent-primary);">{{ number_format($h['toplam_hakedis'], 0, ',', '.') }}₺</div>
            @if($h['odeme'])
                <div class="chip-sub" style="color:#047857;">Ödenen: {{ number_format($h['odeme']->odenen_tutar, 0, ',', '.') }}₺</div>
            @else
                <div class="chip-sub" style="color:var(--text-muted);">Ödeme Yapılmadı</div>
            @endif
        </div>
    </div>

    {{-- Ödeme Aksiyon Alanı --}}
    @if(!$h['kapandi'])
    <div class="action-row">
        <form action="{{ route('aylik-hesap.odeme') }}" method="POST" class="pay-form">
            @csrf
            <input type="hidden" name="usta_id" value="{{ $h['usta']->id }}">
            <input type="hidden" name="ay" value="{{ $ay }}">
            <input type="hidden" name="yil" value="{{ $yil }}">
            
            <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:0.8rem; font-weight:700; color:var(--text-secondary);">Tutar:</label>
                <input type="number" name="odenen_tutar" placeholder="0.00 ₺" min="0" step="0.01" required
                       value="{{ $h['kalan'] > 0 ? number_format($h['kalan'], 2, '.', '') : '' }}"
                       class="form-control" style="width:140px; padding:6px 12px;">
            </div>

            <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:0.8rem; font-weight:700; color:var(--text-secondary);">Yöntem:</label>
                <select name="odeme_yontemi" class="form-select" style="width:130px; padding:6px 12px;">
                    <option value="nakit">Nakit</option>
                    <option value="havale">Banka / Havale</option>
                    <option value="çek">Çek</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="padding:8px 16px;">
                Ödeme Kaydet
            </button>
        </form>

        <form action="{{ route('aylik-hesap.kapat') }}" method="POST" onsubmit="return confirm('Hesabı kapatmak istediğinizden emin misiniz?');">
            @csrf
            <input type="hidden" name="usta_id" value="{{ $h['usta']->id }}">
            <input type="hidden" name="ay" value="{{ $ay }}">
            <input type="hidden" name="yil" value="{{ $yil }}">
            <button type="submit" class="btn btn-dark btn-sm">
                Hesabı Kapat
            </button>
        </form>
    </div>
    @else
    <div class="action-row" style="justify-content:flex-end;">
        <span style="font-size:0.85rem; font-weight:600; color:var(--text-muted);">
            Kapatıldı: {{ $h['odeme']?->odeme_tarihi?->locale('tr')->isoFormat('D MMMM YYYY') ?? '—' }}
        </span>
    </div>
    @endif
</div>
@empty
<div class="card text-center" style="padding:60px 24px;">
    <div style="font-size:1.2rem; font-weight:800; color:var(--text-primary);">Bu ay çalışan usta bulunmuyor</div>
    <p style="color:var(--text-muted); font-size:0.9rem; margin-top:6px;">Seçilen dönem için devam kaydı girilmemiş.</p>
    <div style="margin-top:16px;">
        <a href="{{ route('devam.index') }}" class="btn btn-primary">Devam Takibine Git →</a>
    </div>
</div>
@endforelse

@endsection
