@extends('layouts.app')

@section('title', 'İşler')
@section('page-title', 'İşler & Şantiyeler')

@section('header-actions')
    <a href="{{ route('isler.create') }}" class="btn btn-primary btn-sm">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>İş Ekle</span>
    </a>
@endsection

@section('content')

{{-- Arama Çubuğu --}}
<div class="card" style="margin-bottom:20px; padding:16px 24px;">
    <form method="GET" action="{{ route('isler.index') }}" style="display:flex; gap:10px; align-items:center;">
        <div style="position:relative; flex:1; max-width:420px;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); width:16px; height:16px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input
                type="text"
                name="q"
                value="{{ $query ?? '' }}"
                placeholder="İş adı, müşteri, işveren tel veya adres ara..."
                class="form-control"
                style="padding-left:38px;"
                id="isArama"
            >
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Ara</button>
        @if($query)
            <a href="{{ route('isler.index') }}" class="btn btn-secondary btn-sm">Temizle</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            Tüm İşler
            @if($query)
                <span class="badge badge-mint" style="margin-left:8px;">"{{ $query }}" araması</span>
            @endif
        </span>
        <span class="badge badge-gray">{{ $isler->total() }} iş kayıtlı</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>İş Adı</th>
                    <th>Müşteri / İşveren Tel</th>
                    <th>Durum</th>
                    <th>Başlangıç</th>
                    <th>Gelir</th>
                    <th>Gider</th>
                    <th>Net</th>
                    <th style="text-align:right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($isler as $is)
                <tr>
                    <td>
                        <a href="{{ route('isler.show', $is) }}" style="font-weight:700; color:var(--text-primary);">
                            {{ $is->is_adi }}
                        </a>
                    </td>
                    <td style="color:var(--text-secondary); font-weight:500;">
                        {{ $is->musteri_adi ?? '—' }}
                        @if($is->isveren_telefon)
                            <div style="font-size:0.75rem; color:var(--text-muted);">{{ $is->isveren_telefon }}</div>
                        @endif
                    </td>
                    <td>
                        @if($is->durum === 'devam_ediyor')
                            <span class="badge badge-mint">● Devam Ediyor</span>
                        @elseif($is->durum === 'tamamlandi')
                            <span class="badge badge-dark">✓ Tamamlandı</span>
                        @else
                            <span class="badge badge-gray">İptal</span>
                        @endif
                    </td>
                    <td style="color:var(--text-secondary); font-weight:500;">
                        {{ $is->baslangic_tarihi ? $is->baslangic_tarihi->locale('tr')->isoFormat('D MMMM YYYY') : '—' }}
                    </td>
                    <td style="color:var(--accent-primary); font-weight:700;">{{ number_format($is->toplamGelir(), 0, ',', '.') }}₺</td>
                    <td style="color:var(--accent-danger); font-weight:700;">{{ number_format($is->toplamGider(), 0, ',', '.') }}₺</td>
                    <td style="font-weight:800; color: {{ $is->netKar() >= 0 ? 'var(--accent-primary)' : 'var(--accent-danger)' }};">
                        {{ number_format($is->netKar(), 0, ',', '.') }}₺
                    </td>
                    <td style="text-align:right;">
                        <div style="display:inline-flex; gap:6px;">
                            <a href="{{ route('isler.show', $is) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px;">Detay</a>
                            <a href="{{ route('isler.edit', $is) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px;">Düzenle</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted);">
                        Henüz kayıtlı iş yok.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($isler->hasPages())
        <div style="margin-top:20px;">
            {{ $isler->links() }}
        </div>
    @endif
</div>
@endsection
