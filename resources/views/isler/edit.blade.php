@extends('layouts.app')

@section('title', 'İş Düzenle')
@section('page-title', '✏️ İş Düzenle')

@section('content')
<div style="max-width: 640px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">{{ $is->is_adi }}</span>
            <a href="{{ route('isler.show', $is) }}" class="btn btn-secondary btn-sm">← Geri</a>
        </div>
        <form action="{{ route('isler.update', $is) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label" for="is_adi">İş Adı *</label>
                <input type="text" id="is_adi" name="is_adi" class="form-control" value="{{ old('is_adi', $is->is_adi) }}" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="musteri_adi">Müşteri Adı</label>
                    <input type="text" id="musteri_adi" name="musteri_adi" class="form-control" value="{{ old('musteri_adi', $is->musteri_adi) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="sozlesme_tutari">Sözleşme Tutarı (₺)</label>
                    <input type="number" id="sozlesme_tutari" name="sozlesme_tutari" class="form-control" value="{{ old('sozlesme_tutari', $is->sozlesme_tutari) }}" min="0" step="0.01">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="adres">Adres</label>
                <input type="text" id="adres" name="adres" class="form-control" value="{{ old('adres', $is->adres) }}">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="baslangic_tarihi">Başlangıç Tarihi</label>
                    <input type="date" id="baslangic_tarihi" name="baslangic_tarihi" class="form-control" value="{{ old('baslangic_tarihi', $is->baslangic_tarihi?->toDateString()) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="bitis_tarihi">Bitiş Tarihi</label>
                    <input type="date" id="bitis_tarihi" name="bitis_tarihi" class="form-control" value="{{ old('bitis_tarihi', $is->bitis_tarihi?->toDateString()) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="durum">Durum</label>
                <select id="durum" name="durum" class="form-select">
                    <option value="devam_ediyor" {{ old('durum', $is->durum) === 'devam_ediyor' ? 'selected' : '' }}>⚙️ Devam Ediyor</option>
                    <option value="tamamlandi" {{ old('durum', $is->durum) === 'tamamlandi' ? 'selected' : '' }}>✅ Tamamlandı</option>
                    <option value="iptal" {{ old('durum', $is->durum) === 'iptal' ? 'selected' : '' }}>❌ İptal</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="notlar">Notlar</label>
                <textarea id="notlar" name="notlar" class="form-control">{{ old('notlar', $is->notlar) }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">💾 Güncelle</button>
                <a href="{{ route('isler.show', $is) }}" class="btn btn-secondary">İptal</a>
            </div>
        </form>
    </div>
</div>
@endsection
