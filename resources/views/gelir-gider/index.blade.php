@extends('layouts.app')

@section('title', 'Gelir & Gider')
@section('page-title', '💰 Gelir & Gider')

@section('header-actions')
    <a href="{{ route('gelir-gider.createGelir') }}" class="btn btn-success btn-sm">➕ Gelir Ekle</a>
    <a href="{{ route('gelir-gider.createGider') }}" class="btn btn-danger btn-sm">➖ Gider Ekle</a>
@endsection

@section('content')

{{-- Filtre --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('gelir-gider.index') }}" class="d-flex align-center gap-3" style="flex-wrap:wrap;">
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
        <div>
            <label class="form-label">Tip</label>
            <select name="tip" class="form-select" style="width:120px;">
                <option value="tumu" {{ $tip === 'tumu' ? 'selected' : '' }}>Tümü</option>
                <option value="gelir" {{ $tip === 'gelir' ? 'selected' : '' }}>Gelirler</option>
                <option value="gider" {{ $tip === 'gider' ? 'selected' : '' }}>Giderler</option>
            </select>
        </div>
        <div style="align-self:flex-end;">
            <button type="submit" class="btn btn-primary">Filtrele</button>
        </div>
    </form>
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
        <div class="stat-icon {{ $netBakiye >= 0 ? 'green' : 'red' }}">📊</div>
        <div>
            <div class="stat-value {{ $netBakiye >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($netBakiye, 0, ',', '.') }}₺
            </div>
            <div class="stat-label">Net Bakiye</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

    {{-- Gelirler --}}
    @if($tip === 'tumu' || $tip === 'gelir')
    <div class="card">
        <div class="card-header">
            <span class="card-title">💵 Gelirler ({{ $gelirler->count() }})</span>
            <a href="{{ route('gelir-gider.createGelir') }}" class="btn btn-success btn-sm">+ Ekle</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Tarih</th><th>Açıklama</th><th>Kategori</th><th>Tutar</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($gelirler as $gelir)
                    <tr>
                        <td class="text-muted fs-sm">{{ $gelir->tarih->locale('tr')->isoFormat('D MMM') }}</td>
                        <td>
                            {{ $gelir->aciklama }}
                            @if($gelir->ilgiliIs)
                                <div class="text-muted fs-sm">{{ $gelir->ilgiliIs->is_adi }}</div>
                            @endif
                        </td>
                        <td><span class="badge badge-info">{{ $gelir->kategori }}</span></td>
                        <td class="text-success fw-bold">{{ number_format($gelir->tutar, 0, ',', '.') }}₺</td>
                        <td>
                            <form action="{{ route('gelir-gider.destroyGelir', $gelir) }}" method="POST"
                                  onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted" style="padding:20px;">Kayıt yok</td></tr>
                    @endforelse
                </tbody>
                @if($gelirler->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right fw-semibold">Toplam:</td>
                        <td class="text-success fw-bold">{{ number_format($toplamGelir, 0, ',', '.') }}₺</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif

    {{-- Giderler --}}
    @if($tip === 'tumu' || $tip === 'gider')
    <div class="card">
        <div class="card-header">
            <span class="card-title">💸 Giderler ({{ $giderler->count() }})</span>
            <a href="{{ route('gelir-gider.createGider') }}" class="btn btn-danger btn-sm">+ Ekle</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Tarih</th><th>Açıklama</th><th>Kategori</th><th>Tutar</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($giderler as $gider)
                    <tr>
                        <td class="text-muted fs-sm">{{ $gider->tarih->locale('tr')->isoFormat('D MMM') }}</td>
                        <td>
                            {{ $gider->aciklama }}
                            @if($gider->ilgiliIs)
                                <div class="text-muted fs-sm">{{ $gider->ilgiliIs->is_adi }}</div>
                            @endif
                        </td>
                        <td><span class="badge badge-warning">{{ $gider->kategori }}</span></td>
                        <td class="text-danger fw-bold">{{ number_format($gider->tutar, 0, ',', '.') }}₺</td>
                        <td>
                            <form action="{{ route('gelir-gider.destroyGider', $gider) }}" method="POST"
                                  onsubmit="return confirm('Silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑️</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted" style="padding:20px;">Kayıt yok</td></tr>
                    @endforelse
                </tbody>
                @if($giderler->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right fw-semibold">Toplam:</td>
                        <td class="text-danger fw-bold">{{ number_format($toplamGider, 0, ',', '.') }}₺</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif
</div>

@endsection
