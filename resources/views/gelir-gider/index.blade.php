@extends('layouts.app')

@section('title', 'Gelir & Gider')
@section('page-title', 'Gelir & Gider Yönetimi')

@section('header-actions')
    <a href="{{ route('gelir-gider.createGelir') }}" class="btn btn-primary btn-sm">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Gelir Ekle</span>
    </a>
    <a href="{{ route('gelir-gider.createGider') }}" class="btn btn-secondary btn-sm">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:4px;"><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Gider Ekle</span>
    </a>
@endsection

@section('content')

{{-- Filtre --}}
<div class="card" style="margin-bottom:24px; padding:16px 24px;">
    <form method="GET" action="{{ route('gelir-gider.index') }}" style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:0.85rem; font-weight:700; color:var(--text-secondary);">Ay:</label>
            <select name="ay" class="form-select" style="width:130px;">
                @foreach($aylar as $num => $isim)
                    <option value="{{ $num }}" {{ $ay == $num ? 'selected' : '' }}>{{ $isim }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:0.85rem; font-weight:700; color:var(--text-secondary);">Yıl:</label>
            <select name="yil" class="form-select" style="width:100px;">
                @foreach($yillar as $y)
                    <option value="{{ $y }}" {{ $yil == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:0.85rem; font-weight:700; color:var(--text-secondary);">Tip:</label>
            <select name="tip" class="form-select" style="width:120px;">
                <option value="tumu" {{ $tip === 'tumu' ? 'selected' : '' }}>Tümü</option>
                <option value="gelir" {{ $tip === 'gelir' ? 'selected' : '' }}>Gelirler</option>
                <option value="gider" {{ $tip === 'gider' ? 'selected' : '' }}>Giderler</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary btn-sm">Filtrele</button>
        </div>
    </form>
</div>

{{-- Özet Kartlar --}}
<div class="stats-grid" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon mint">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--accent-primary);">{{ number_format($toplamGelir, 0, ',', '.') }}₺</div>
            <div class="stat-label">Toplam Gelir</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#fef2f2; color:#ef4444; border-color:#fee2e2;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--accent-danger);">{{ number_format($toplamGider, 0, ',', '.') }}₺</div>
            <div class="stat-label">Toplam Gider</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon dark">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color: {{ $netDurum >= 0 ? 'var(--accent-primary)' : 'var(--accent-danger)' }};">
                {{ number_format($netDurum, 0, ',', '.') }}₺
            </div>
            <div class="stat-label">Net Durum</div>
        </div>
    </div>
</div>

{{-- İşlemler Tablosu --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Gelir & Gider Hareketleri</span>
        <span class="badge badge-gray">{{ count($hareketler) }} Hareket</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Tip</th>
                    <th>Tarih</th>
                    <th>Açıklama / Kategori</th>
                    <th>İlişkili İş</th>
                    <th>Tutar</th>
                    <th style="text-align:right;">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hareketler as $h)
                <tr>
                    <td>
                        @if($h['tip'] === 'gelir')
                            <span class="badge badge-mint">+ Gelir</span>
                        @else
                            <span class="badge badge-danger">- Gider</span>
                        @endif
                    </td>
                    <td style="color:var(--text-secondary); font-weight:500;">
                        {{ $h['tarih']->locale('tr')->isoFormat('D MMMM YYYY') }}
                    </td>
                    <td>
                        <div style="font-weight:700; color:var(--text-primary);">{{ $h['aciklama'] }}</div>
                        <div style="font-size:0.75rem; color:var(--text-muted); font-weight:500;">{{ $h['kategori'] ?? 'Genel' }}</div>
                    </td>
                    <td style="color:var(--text-secondary); font-size:0.85rem; font-weight:500;">
                        {{ $h['is_adi'] ?? '—' }}
                    </td>
                    <td style="font-weight:800; color: {{ $h['tip'] === 'gelir' ? 'var(--accent-primary)' : 'var(--accent-danger)' }};">
                        {{ $h['tip'] === 'gelir' ? '+' : '-' }}{{ number_format($h['tutar'], 0, ',', '.') }}₺
                    </td>
                    <td style="text-align:right;">
                        @if($h['tip'] === 'gelir')
                            <form action="{{ route('gelir-gider.destroyGelir', $h['id']) }}" method="POST" style="margin:0;" onsubmit="return confirm('Bu geliri silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        @else
                            <form action="{{ route('gelir-gider.destroyGider', $h['id']) }}" method="POST" style="margin:0;" onsubmit="return confirm('Bu gideri silmek istediğinize emin misiniz?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">
                        Seçilen dönem için kayıtlı gelir/gider hareketi yok.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
