@extends('layouts.app')

@section('title', 'Gider Ekle')
@section('page-title', '💸 Gider Ekle')

@section('content')
<div style="max-width:540px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Yeni Gider Kaydı</span>
            <a href="{{ route('gelir-gider.index') }}" class="btn btn-secondary btn-sm">← Geri</a>
        </div>
        <form action="{{ route('gelir-gider.storeGider') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="tarih">Tarih *</label>
                <input type="date" id="tarih" name="tarih" class="form-control" value="{{ old('tarih', now()->toDateString()) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="tutar">Tutar (₺) *</label>
                <input type="number" id="tutar" name="tutar" class="form-control" value="{{ old('tutar') }}" min="0.01" step="0.01" required placeholder="0.00">
                @error('tutar') <div class="error-text">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="aciklama">Açıklama *</label>
                <input type="text" id="aciklama" name="aciklama" class="form-control" value="{{ old('aciklama') }}" required placeholder="Gider açıklaması">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="kategori">Kategori</label>
                    <select id="kategori" name="kategori" class="form-select">
                        <option value="malzeme">🧱 Malzeme</option>
                        <option value="alet_ekipman">🔧 Alet & Ekipman</option>
                        <option value="nakil">🚛 Nakliye</option>
                        <option value="yakıt">⛽ Yakıt</option>
                        <option value="kira">🏠 Kira</option>
                        <option value="vergi">📑 Vergi</option>
                        <option value="diger">📌 Diğer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="odeme_yontemi">Ödeme Yöntemi</label>
                    <select id="odeme_yontemi" name="odeme_yontemi" class="form-select">
                        <option value="">— Seçin —</option>
                        <option value="nakit">💵 Nakit</option>
                        <option value="havale">🏦 Havale/EFT</option>
                        <option value="çek">📄 Çek</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="is_id">İlgili İş (Opsiyonel)</label>
                <select id="is_id" name="is_id" class="form-select">
                    <option value="">— Genel Gider —</option>
                    @foreach($isler as $is)
                        <option value="{{ $is->id }}" {{ (old('is_id') == $is->id || request('is_id') == $is->id) ? 'selected' : '' }}>
                            {{ $is->is_adi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">💾 Gider Kaydet</button>
                <a href="{{ route('gelir-gider.index') }}" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>
@endsection
