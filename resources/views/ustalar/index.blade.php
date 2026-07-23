@extends('layouts.app')

@section('title', 'Ustalar')
@section('page-title', 'Ustalar')

@section('header-actions')
    <a href="{{ route('ustalar.create') }}" class="btn btn-primary btn-sm">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Usta Ekle</span>
    </a>
@endsection

@section('content')

{{-- Arama Çubuğu --}}
<div class="card" style="margin-bottom:20px; padding:16px 24px;">
    <form method="GET" action="{{ route('ustalar.index') }}" style="display:flex; gap:10px; align-items:center;">
        <div style="position:relative; flex:1; max-width:420px;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); width:16px; height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input
                type="text"
                name="q"
                value="{{ $query ?? '' }}"
                placeholder="Ad, soyad, uzmanlık veya telefon ara..."
                class="form-control"
                style="padding-left:38px;"
                id="ustaArama"
            >
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Ara</button>
        @if($query)
            <a href="{{ route('ustalar.index') }}" class="btn btn-secondary btn-sm">Temizle</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            Tüm Ustalar
            @if($query)
                <span class="badge badge-mint" style="margin-left:8px;">"{{ $query }}" araması</span>
            @endif
        </span>
        <span class="badge badge-gray">{{ $ustalar->total() }} usta kayıtlı</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Usta Adı</th>
                    <th>Uzmanlık</th>
                    <th>Telefon</th>
                    <th>Günlük Ücret</th>
                    <th>Mesai Saati</th>
                    <th>Durum</th>
                    <th style="text-align:right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ustalar as $usta)
                <tr>
                    <td>
                        <a href="{{ route('ustalar.show', $usta) }}" style="display:flex; align-items:center; gap:10px; color:inherit; font-weight:700;">
                            <div class="avatar" style="width:34px; height:34px; font-size:0.8rem; font-weight:800;">
                                {{ strtoupper(mb_substr($usta->ad, 0, 1)) }}{{ strtoupper(mb_substr($usta->soyad, 0, 1)) }}
                            </div>
                            <span>{{ $usta->ad_soyad }}</span>
                        </a>
                    </td>
                    <td style="color:var(--text-secondary); font-weight:500;">{{ $usta->uzmanlik ?? '—' }}</td>
                    <td style="color:var(--text-secondary); font-weight:500;">{{ $usta->telefon ?? '—' }}</td>
                    <td style="font-weight:700;">{{ number_format($usta->gunluk_ucret, 0, ',', '.') }}₺</td>
                    <td style="font-weight:700;">{{ number_format($usta->mesai_saatlik_ucret, 0, ',', '.') }}₺ / saat</td>
                    <td>
                        @if($usta->durum === 'aktif')
                            <span class="badge badge-mint">● Aktif</span>
                        @else
                            <span class="badge badge-gray">Pasif</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="display:inline-flex; gap:6px;">
                            <a href="{{ route('ustalar.show', $usta) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px;">Detay</a>
                            <a href="{{ route('ustalar.edit', $usta) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px;">Düzenle</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);">
                        Henüz usta kaydı yok.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ustalar->hasPages())
        <div style="margin-top:20px;">
            {{ $ustalar->links() }}
        </div>
    @endif
</div>
@endsection
