@extends('layouts.app')

@section('title', 'Usta Düzenle — ' . $usta->ad_soyad)
@section('page-title', '✏️ Usta Düzenle')

@section('content')

<div style="max-width: 640px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $usta->ad_soyad }}</span>
            <a href="{{ route('ustalar.show', $usta->id) }}" class="btn btn-secondary btn-sm">← Geri</a>
        </div>

        <form action="{{ route('ustalar.update', $usta->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="ad">Ad *</label>
                    <input type="text" id="ad" name="ad" class="form-control {{ $errors->has('ad') ? 'input-error' : '' }}"
                           value="{{ old('ad', $usta->ad) }}" required>
                    @error('ad') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="soyad">Soyad *</label>
                    <input type="text" id="soyad" name="soyad" class="form-control {{ $errors->has('soyad') ? 'input-error' : '' }}"
                           value="{{ old('soyad', $usta->soyad) }}" required>
                    @error('soyad') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="telefon">Telefon</label>
                    <input type="text" id="telefon" name="telefon" class="form-control"
                           value="{{ old('telefon', $usta->telefon) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="uzmanlik">Uzmanlık Alanı</label>
                    <input type="text" id="uzmanlik" name="uzmanlik" class="form-control"
                           value="{{ old('uzmanlik', $usta->uzmanlik) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="iban">IBAN Numarası (Opsiyonel)</label>
                <input type="text" id="iban" name="iban" class="form-control {{ $errors->has('iban') ? 'input-error' : '' }}"
                       value="{{ old('iban', $usta->iban) }}" placeholder="TR000000000000000000000000">
                @error('iban') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="gunluk_ucret">Günlük Ücret (₺) *</label>
                    <input type="number" id="gunluk_ucret" name="gunluk_ucret" class="form-control"
                           value="{{ old('gunluk_ucret', $usta->gunluk_ucret) }}" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="mesai_saatlik_ucret">Mesai Saatlik Ücret (₺) *</label>
                    <input type="number" id="mesai_saatlik_ucret" name="mesai_saatlik_ucret" class="form-control"
                           value="{{ old('mesai_saatlik_ucret', $usta->mesai_saatlik_ucret) }}" min="0" step="0.01" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="durum">Durum</label>
                <select id="durum" name="durum" class="form-select">
                    <option value="aktif" {{ old('durum', $usta->durum) === 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
                    <option value="pasif" {{ old('durum', $usta->durum) === 'pasif' ? 'selected' : '' }}>⚪ Pasif</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="notlar">Notlar</label>
                <textarea id="notlar" name="notlar" class="form-control">{{ old('notlar', $usta->notlar) }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">💾 Güncelle</button>
                <a href="{{ route('ustalar.show', $usta->id) }}" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>

@endsection
