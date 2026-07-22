@extends('layouts.app')

@section('title', 'İşler')
@section('page-title', '🏢 İşler / Şantiyeler')

@section('header-actions')
    <a href="{{ route('isler.create') }}" class="btn btn-primary btn-sm">➕ İş Ekle</a>
@endsection

@section('content')

{{-- Arama Çubuğu --}}
<div class="card" style="margin-bottom:16px; padding:16px;">
    <form method="GET" action="{{ route('isler.index') }}" style="display:flex; gap:10px; align-items:center;">
        <div style="position:relative; flex:1; max-width:420px;">
            <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:1rem; pointer-events:none;">🔍</span>
            <input
                type="text"
                name="q"
                value="{{ $query ?? '' }}"
                placeholder="İş adı, müşteri, işveren no veya adres ara..."
                class="form-control"
                style="padding-left:38px;"
                id="isArama"
            >
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Ara</button>
        @if($query)
            <a href="{{ route('isler.index') }}" class="btn btn-secondary btn-sm">✕ Temizle</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            Tüm İşler
            @if($query)
                <span class="badge badge-primary" style="margin-left:8px;">"{{ $query }}" araması</span>
            @endif
        </span>
        <span class="badge badge-secondary">{{ $isler->total() }} iş</span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>İş Adı</th>
                    <th>Müşteri / İşveren No</th>
                    <th>Durum</th>
                    <th>Başlangıç</th>
                    <th>Gelir</th>
                    <th>Gider</th>
                    <th>Net</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($isler as $is)
                @php
                    $gelir = $is->gelirler_sum_tutar ?? 0;
                    $gider = $is->giderler_sum_tutar ?? 0;
                    $net = $gelir - $gider;
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $is->is_adi }}</div>
                        @if($is->adres)
                            <div class="text-muted fs-sm">📍 {{ Str::limit($is->adres, 40) }}</div>
                        @endif
                    </td>
                    <td class="text-muted">
                        <div>{{ $is->musteri_adi ?? '—' }}</div>
                        @if($is->isveren_no)
                            <div class="fs-sm text-muted">🏢 No: {{ $is->isveren_no }}</div>
                        @endif
                    </td>
                    <td>
                        @if($is->durum === 'devam_ediyor')
                            <span class="badge badge-success">⚙️ Devam Ediyor</span>
                        @elseif($is->durum === 'tamamlandi')
                            <span class="badge badge-info">✅ Tamamlandı</span>
                        @else
                            <span class="badge badge-danger">❌ İptal</span>
                        @endif
                    </td>
                    <td class="text-muted fs-sm">
                        {{ $is->baslangic_tarihi?->locale('tr')->isoFormat('D MMM YYYY') ?? '—' }}
                    </td>
                    <td class="text-success">{{ number_format($gelir, 0, ',', '.') }}₺</td>
                    <td class="text-danger">{{ number_format($gider, 0, ',', '.') }}₺</td>
                    <td class="{{ $net >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                        {{ number_format($net, 0, ',', '.') }}₺
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('isler.show', $is->id) }}" class="btn btn-secondary btn-sm" title="Detay">👁️</a>
                            <a href="{{ route('isler.edit', $is->id) }}" class="btn btn-warning btn-sm" title="Düzenle">✏️</a>
                            <form action="{{ route('isler.destroy', $is->id) }}" method="POST"
                                  onsubmit="return confirm('{{ $is->is_adi }} silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Sil">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding:40px;">
                        @if($query)
                            "<strong>{{ $query }}</strong>" için sonuç bulunamadı.
                            <a href="{{ route('isler.index') }}">Tümünü göster →</a>
                        @else
                            Henüz iş eklenmemiş. <a href="{{ route('isler.create') }}">İlk işi ekle →</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($isler->hasPages())
        <div class="pagination">{{ $isler->links() }}</div>
    @endif
</div>

@endsection
