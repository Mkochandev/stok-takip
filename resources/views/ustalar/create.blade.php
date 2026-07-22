@extends('layouts.app')

@section('title', 'Usta Ekle')
@section('page-title', '👷 Usta Ekle')

@section('content')

<div style="max-width: 640px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Yeni Usta Bilgileri</span>
            <a href="{{ route('ustalar.index') }}" class="btn btn-secondary btn-sm">← Geri</a>
        </div>

        <form action="{{ route('ustalar.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="ad">Ad *</label>
                    <input type="text" id="ad" name="ad" class="form-control {{ $errors->has('ad') ? 'input-error' : '' }}"
                           value="{{ old('ad') }}" required placeholder="Ahmet">
                    @error('ad') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="soyad">Soyad *</label>
                    <input type="text" id="soyad" name="soyad" class="form-control {{ $errors->has('soyad') ? 'input-error' : '' }}"
                           value="{{ old('soyad') }}" required placeholder="Yıldız">
                    @error('soyad') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="telefon">Telefon</label>
                    <input type="text" id="telefon" name="telefon" class="form-control"
                           value="{{ old('telefon') }}" placeholder="0532 000 00 00">
                </div>
                <div class="form-group">
                    <label class="form-label" for="uzmanlik">Uzmanlık Alanı</label>
                    <input type="text" id="uzmanlik" name="uzmanlik" class="form-control"
                           value="{{ old('uzmanlik') }}" placeholder="Elektrikçi, Sıvacı...">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="iban">IBAN Numarası (Opsiyonel)</label>
                <input type="text" id="iban" name="iban" class="form-control {{ $errors->has('iban') ? 'input-error' : '' }}"
                       value="{{ old('iban') }}" placeholder="TR000000000000000000000000">
                @error('iban') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="gunluk_ucret">Günlük Ücret (₺) *</label>
                    <input type="number" id="gunluk_ucret" name="gunluk_ucret" class="form-control {{ $errors->has('gunluk_ucret') ? 'input-error' : '' }}"
                           value="{{ old('gunluk_ucret', 0) }}" min="0" step="0.01" required>
                    @error('gunluk_ucret') <div class="error-text">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="mesai_saatlik_ucret">Mesai Saatlik Ücret (₺) *</label>
                    <input type="number" id="mesai_saatlik_ucret" name="mesai_saatlik_ucret" class="form-control {{ $errors->has('mesai_saatlik_ucret') ? 'input-error' : '' }}"
                           value="{{ old('mesai_saatlik_ucret', 0) }}" min="0" step="0.01" required>
                    @error('mesai_saatlik_ucret') <div class="error-text">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="durum">Durum</label>
                <select id="durum" name="durum" class="form-select">
                    <option value="aktif" {{ old('durum', 'aktif') === 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
                    <option value="pasif" {{ old('durum') === 'pasif' ? 'selected' : '' }}>⚪ Pasif</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="notlar">Notlar</label>
                <textarea id="notlar" name="notlar" class="form-control" placeholder="Ek bilgiler...">{{ old('notlar') }}</textarea>
            </div>

            <div class="d-flex gap-2" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary">💾 Kaydet</button>
                <a href="{{ route('ustalar.index') }}" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>

@endsection
