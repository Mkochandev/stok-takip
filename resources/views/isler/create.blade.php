@extends('layouts.app')

@section('title', 'İş Ekle')
@section('page-title', '🏢 İş Ekle')

@section('content')

<div style="max-width: 640px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Yeni İş / Şantiye</span>
            <a href="{{ route('isler.index') }}" class="btn btn-secondary btn-sm">← Geri</a>
        </div>

        <form action="{{ route('isler.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="is_adi">İş Adı *</label>
                <input type="text" id="is_adi" name="is_adi" class="form-control {{ $errors->has('is_adi') ? 'input-error' : '' }}"
                       value="{{ old('is_adi') }}" required placeholder="Örn: Çamlık Sitesi 3. Blok">
                @error('is_adi') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="musteri_adi">Müşteri Adı</label>
                    <input type="text" id="musteri_adi" name="musteri_adi" class="form-control"
                           value="{{ old('musteri_adi') }}" placeholder="Müşteri / Firma adı">
                </div>
                <div class="form-group">
                    <label class="form-label" for="sozlesme_tutari">Sözleşme Tutarı (₺)</label>
                    <input type="number" id="sozlesme_tutari" name="sozlesme_tutari" class="form-control"
                           value="{{ old('sozlesme_tutari') }}" min="0" step="0.01" placeholder="0.00">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="adres">Adres / Konum</label>
                <input type="text" id="adres" name="adres" class="form-control"
                       value="{{ old('adres') }}" placeholder="Şantiye adresi">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="baslangic_tarihi">Başlangıç Tarihi</label>
                    <input type="date" id="baslangic_tarihi" name="baslangic_tarihi" class="form-control"
                           value="{{ old('baslangic_tarihi', now()->toDateString()) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="bitis_tarihi">Bitiş Tarihi (Tahmini)</label>
                    <input type="date" id="bitis_tarihi" name="bitis_tarihi" class="form-control"
                           value="{{ old('bitis_tarihi') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="durum">Durum</label>
                <select id="durum" name="durum" class="form-select">
                    <option value="devam_ediyor" {{ old('durum', 'devam_ediyor') === 'devam_ediyor' ? 'selected' : '' }}>⚙️ Devam Ediyor</option>
                    <option value="tamamlandi" {{ old('durum') === 'tamamlandi' ? 'selected' : '' }}>✅ Tamamlandı</option>
                    <option value="iptal" {{ old('durum') === 'iptal' ? 'selected' : '' }}>❌ İptal</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="notlar">Notlar</label>
                <textarea id="notlar" name="notlar" class="form-control" placeholder="İş ile ilgili notlar...">{{ old('notlar') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">💾 Kaydet</button>
                <a href="{{ route('isler.index') }}" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>

@endsection
