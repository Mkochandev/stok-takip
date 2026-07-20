@extends('layouts.app')

@section('title', 'İşler')
@section('page-title', '🏢 İşler / Şantiyeler')

@section('header-actions')
    <a href="{{ route('isler.create') }}" class="btn btn-primary btn-sm">➕ İş Ekle</a>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <span class="card-title">Tüm İşler</span>
        <span class="badge badge-secondary">{{ $isler->total() }} iş</span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>İş Adı</th>
                    <th>Müşteri</th>
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
                    <td class="text-muted">{{ $is->musteri_adi ?? '—' }}</td>
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
                            <a href="{{ route('isler.show', $is) }}" class="btn btn-secondary btn-sm">👁️</a>
                            <a href="{{ route('isler.edit', $is) }}" class="btn btn-warning btn-sm">✏️</a>
                            <form action="{{ route('isler.destroy', $is) }}" method="POST"
                                  onsubmit="return confirm('{{ $is->is_adi }} silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding:40px;">
                        Henüz iş eklenmemiş. <a href="{{ route('isler.create') }}">İlk işi ekle →</a>
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
