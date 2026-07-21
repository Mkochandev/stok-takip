@extends('layouts.app')

@section('title', 'Ustalar')
@section('page-title', '👷 Ustalar')

@section('header-actions')
    <a href="{{ route('ustalar.create') }}" class="btn btn-primary btn-sm">
        ➕ Usta Ekle
    </a>
@endsection

@section('content')

{{-- Arama Çubuğu --}}
<div class="card" style="margin-bottom:16px; padding:16px;">
    <form method="GET" action="{{ route('ustalar.index') }}" style="display:flex; gap:10px; align-items:center;">
        <div style="position:relative; flex:1; max-width:420px;">
            <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:1rem; pointer-events:none;">🔍</span>
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
            <a href="{{ route('ustalar.index') }}" class="btn btn-secondary btn-sm">✕ Temizle</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">
            Tüm Ustalar
            @if($query)
                <span class="badge badge-primary" style="margin-left:8px;">"{{ $query }}" araması</span>
            @endif
        </span>
        <span class="badge badge-secondary">{{ $ustalar->total() }} usta</span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Usta</th>
                    <th>Uzmanlık</th>
                    <th>Telefon</th>
                    <th>Günlük Ücret</th>
                    <th>Mesai Saati</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ustalar as $usta)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-2">
                            <div class="avatar-circle">
                                {{ strtoupper(mb_substr($usta->ad, 0, 1)) }}{{ strtoupper(mb_substr($usta->soyad, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $usta->ad_soyad }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">{{ $usta->uzmanlik ?? '—' }}</td>
                    <td class="text-muted">
                        @if($usta->telefon)
                            <a href="tel:{{ $usta->telefon }}" style="color:var(--text-muted);">{{ $usta->telefon }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-success fw-semibold">{{ number_format($usta->gunluk_ucret, 0, ',', '.') }}₺</td>
                    <td class="text-muted">{{ number_format($usta->mesai_saatlik_ucret, 0, ',', '.') }}₺/s</td>
                    <td>
                        @if($usta->durum === 'aktif')
                            <span class="badge badge-success">● Aktif</span>
                        @else
                            <span class="badge badge-secondary">● Pasif</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('ustalar.show', $usta->id) }}" class="btn btn-secondary btn-sm" title="Profil">👁️</a>
                            <a href="{{ route('ustalar.edit', $usta->id) }}" class="btn btn-warning btn-sm" title="Düzenle">✏️</a>
                            <form action="{{ route('ustalar.destroy', $usta->id) }}" method="POST"
                                  onsubmit="return confirm('{{ $usta->ad_soyad }} silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" title="Sil">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding:40px;">
                        @if($query)
                            "<strong>{{ $query }}</strong>" için sonuç bulunamadı.
                            <a href="{{ route('ustalar.index') }}">Tümünü göster →</a>
                        @else
                            Henüz usta eklenmemiş.
                            <a href="{{ route('ustalar.create') }}">İlk ustayı ekle →</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ustalar->hasPages())
        <div class="pagination" style="margin-top:16px;">
            {{ $ustalar->links() }}
        </div>
    @endif
</div>

@endsection
