@extends('layouts.app')

@section('title', $is->is_adi)
@section('page-title', '🏢 ' . $is->is_adi)

@section('header-actions')
    <a href="{{ route('isler.edit', $is) }}" class="btn btn-warning btn-sm">✏️ Düzenle</a>
    <a href="{{ route('isler.index') }}" class="btn btn-secondary btn-sm">← Geri</a>
@endsection

@section('content')

{{-- İş Genel Bilgileri --}}
<div class="card" style="margin-bottom:20px;">
    <div style="display:flex; flex-wrap:wrap; gap:20px; align-items:center; justify-content:space-between;">
        <div>
            <div style="font-size:1.2rem; font-weight:700;">{{ $is->is_adi }}</div>
            <div class="text-muted fs-sm" style="margin-top:2px;">
                @if($is->musteri_adi) 👤 Müşteri: <strong>{{ $is->musteri_adi }}</strong> @endif
                @if($is->isveren_no) &nbsp;·&nbsp; 🏢 İşveren No: <strong>{{ $is->isveren_no }}</strong> @endif
                @if($is->adres) &nbsp;·&nbsp; 📍 Adres: {{ $is->adres }} @endif
            </div>
        </div>
        <div>
            <span class="badge badge-{{ $is->durum_renk }}" style="font-size:0.9rem; padding:6px 12px;">
                {{ $is->durum_etiket }}
            </span>
            @if($is->sozlesme_tutari)
                <div class="fs-sm text-muted text-right" style="margin-top:4px;">
                    Sözleşme: <strong class="text-primary">{{ number_format($is->sozlesme_tutari, 0, ',', '.') }}₺</strong>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Özet Kartlar --}}
<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-icon green">💵</div>
        <div>
            <div class="stat-value">{{ number_format($toplamGelir, 0, ',', '.') }}₺</div>
            <div class="stat-label">Toplam Gelir</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">💸</div>
        <div>
            <div class="stat-value">{{ number_format($toplamGider, 0, ',', '.') }}₺</div>
            <div class="stat-label">Toplam Gider</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon {{ $netKar >= 0 ? 'green' : 'red' }}">📊</div>
        <div>
            <div class="stat-value {{ $netKar >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netKar, 0, ',', '.') }}₺</div>
            <div class="stat-label">Net Kâr/Zarar</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan">👷</div>
        <div>
            <div class="stat-value">{{ $is->devamKayitlari->count() }}</div>
            <div class="stat-label">Devam Kaydı</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    {{-- Gelirler --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">💵 Gelirler</span>
            <a href="{{ route('gelir-gider.createGelir') }}?is_id={{ $is->id }}" class="btn btn-success btn-sm">+ Ekle</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Tarih</th><th>Açıklama</th><th>Tutar</th></tr>
                </thead>
                <tbody>
                    @forelse($is->gelirler as $gelir)
                    <tr>
                        <td class="text-muted fs-sm">{{ $gelir->tarih->locale('tr')->isoFormat('D MMM') }}</td>
                        <td>{{ $gelir->aciklama }}</td>
                        <td class="text-success fw-semibold">{{ number_format($gelir->tutar, 0, ',', '.') }}₺</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted" style="padding:20px;">Kayıt yok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Giderler --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">💸 Giderler</span>
            <a href="{{ route('gelir-gider.createGider') }}?is_id={{ $is->id }}" class="btn btn-danger btn-sm">+ Ekle</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Tarih</th><th>Açıklama</th><th>Tutar</th></tr>
                </thead>
                <tbody>
                    @forelse($is->giderler as $gider)
                    <tr>
                        <td class="text-muted fs-sm">{{ $gider->tarih->locale('tr')->isoFormat('D MMM') }}</td>
                        <td>{{ $gider->aciklama }}</td>
                        <td class="text-danger fw-semibold">{{ number_format($gider->tutar, 0, ',', '.') }}₺</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted" style="padding:20px;">Kayıt yok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Çalışan Ustalar --}}
<div class="card mt-4">
    <div class="card-header">
        <span class="card-title">👷 Bu İşte Çalışan Ustalar</span>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Usta</th><th>Tarih</th><th>Çalışma</th><th>Ücret</th></tr>
            </thead>
            <tbody>
                @forelse($is->devamKayitlari as $kayit)
                <tr>
                    <td>{{ $kayit->usta->ad_soyad ?? '—' }}</td>
                    <td class="text-muted">{{ $kayit->tarih->locale('tr')->isoFormat('D MMMM YYYY') }}</td>
                    <td>
                        @if($kayit->calisma_tipi === 'tam')
                            <span class="badge badge-success">Tam Gün</span>
                        @elseif($kayit->calisma_tipi === 'yarim')
                            <span class="badge badge-warning">Yarım Gün</span>
                        @else
                            <span class="badge badge-primary">Mesai ({{ $kayit->mesai_saati }}s)</span>
                        @endif
                    </td>
                    <td class="text-success">{{ number_format($kayit->hesaplanan_ucret, 0, ',', '.') }}₺</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted" style="padding:20px;">Bu işte devam kaydı yok</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
