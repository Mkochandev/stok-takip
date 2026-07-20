@extends('layouts.app')

@section('title', 'Devam Takibi')
@section('page-title', '📅 Devam Takibi')

@push('styles')
<style>
.date-nav {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 12px 20px;
    margin-bottom: 20px;
}
.date-nav input[type=date] {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-family: inherit;
    font-size: 1rem;
    font-weight: 600;
    padding: 8px 12px;
    cursor: pointer;
    outline: none;
}
.date-nav input[type=date]:focus { border-color: var(--accent-primary); }

.usta-row {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 16px;
    margin-bottom: 10px;
    display: grid;
    grid-template-columns: 200px 1fr 180px 100px;
    gap: 16px;
    align-items: center;
    transition: border-color var(--transition);
}
.usta-row.selected {
    border-color: var(--accent-primary);
    background: rgba(79,110,247,0.05);
}
.usta-row.calismiyor {
    opacity: 0.5;
}

.usta-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.toggle-switch {
    position: relative;
    width: 48px;
    height: 26px;
    flex-shrink: 0;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute;
    inset: 0;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 13px;
    cursor: pointer;
    transition: var(--transition);
}
.toggle-slider::before {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    left: 3px;
    top: 3px;
    background: var(--text-muted);
    border-radius: 50%;
    transition: var(--transition);
}
.toggle-switch input:checked + .toggle-slider { background: rgba(79,110,247,0.2); border-color: var(--accent-primary); }
.toggle-switch input:checked + .toggle-slider::before { transform: translateX(22px); background: var(--accent-primary); }

.tip-selector {
    display: flex;
    gap: 6px;
}
.tip-btn {
    flex: 1;
    padding: 6px 4px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-primary);
    color: var(--text-muted);
    cursor: pointer;
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    font-family: inherit;
    transition: all var(--transition);
}
.tip-btn:hover { border-color: var(--text-muted); color: var(--text-primary); }
.tip-btn.active-tam    { background: rgba(34,197,94,0.15); border-color: var(--accent-success); color: var(--accent-success); }
.tip-btn.active-yarim  { background: rgba(245,158,11,0.15); border-color: var(--accent-warning); color: var(--accent-warning); }
.tip-btn.active-mesai  { background: rgba(79,110,247,0.15); border-color: var(--accent-primary); color: var(--accent-primary); }

.mesai-input-wrap {
    display: none;
}
.mesai-input-wrap.show { display: block; }

.ucret-display {
    text-align: right;
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-muted);
}
.ucret-display.active { color: var(--accent-success); }

.kayitli-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    background: rgba(34,197,94,0.15);
    border: 1px solid rgba(34,197,94,0.3);
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--accent-success);
}
</style>
@endpush

@section('content')

{{-- Tarih Seçici --}}
<div class="date-nav">
    <span class="text-muted">📅 Tarih:</span>
    <input type="date" id="tarihInput" value="{{ $tarih }}"
           onchange="window.location.href='{{ route('devam.index') }}?tarih=' + this.value">
    <span class="fw-semibold">{{ $carbonTarih->locale('tr')->isoFormat('D MMMM YYYY, dddd') }}</span>

    <div style="margin-left:auto; display:flex; gap:8px;">
        <a href="{{ route('devam.index', ['tarih' => $carbonTarih->copy()->subDay()->toDateString()]) }}"
           class="btn btn-secondary btn-sm">← Önceki Gün</a>
        <a href="{{ route('devam.index', ['tarih' => now()->toDateString()]) }}"
           class="btn btn-secondary btn-sm">Bugün</a>
        <a href="{{ route('devam.index', ['tarih' => $carbonTarih->copy()->addDay()->toDateString()]) }}"
           class="btn btn-secondary btn-sm">Sonraki Gün →</a>
    </div>
</div>

{{-- Devam Formu --}}
<form action="{{ route('devam.store') }}" method="POST" id="devamForm">
    @csrf
    <input type="hidden" name="tarih" value="{{ $tarih }}">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div>
            <span class="fw-semibold">👷 Aktif Ustalar</span>
            <span class="text-muted fs-sm ml-2">— Çalışanları seçin ve tipini belirleyin</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-secondary btn-sm" onclick="hepsiniSec()">Hepsini Seç</button>
            <button type="submit" class="btn btn-primary">💾 Kaydet</button>
        </div>
    </div>

    <div id="ustaListesi">
        @forelse($ustalar as $usta)
        @php
            $kayit = $gunKayitlari[$usta->id] ?? null;
            $secili = !is_null($kayit);
        @endphp
        <div class="usta-row {{ $secili ? 'selected' : '' }}" id="row-{{ $usta->id }}">

            {{-- Checkbox + Usta Bilgi --}}
            <div class="d-flex align-center gap-3">
                <label class="toggle-switch">
                    <input type="checkbox" name="kayitlar[{{ $usta->id }}][usta_id]"
                           value="{{ $usta->id }}"
                           id="chk-{{ $usta->id }}"
                           {{ $secili ? 'checked' : '' }}
                           onchange="toggleUsta({{ $usta->id }})">
                    <span class="toggle-slider"></span>
                </label>
                <div class="usta-info">
                    <div class="avatar-circle" style="width:36px;height:36px;font-size:0.8rem;">
                        {{ strtoupper(mb_substr($usta->ad, 0, 1)) }}{{ strtoupper(mb_substr($usta->soyad, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:0.9rem;">{{ $usta->ad_soyad }}</div>
                        <div class="text-muted fs-sm">{{ $usta->uzmanlik ?? 'Genel' }}</div>
                    </div>
                </div>
                @if($kayit)
                    <span class="kayitli-badge">✓ Kayıtlı</span>
                @endif
            </div>

            {{-- Çalışma Tipi --}}
            <div class="usta-detay {{ !$secili ? 'd-none' : '' }}" id="detay-{{ $usta->id }}">
                <input type="hidden" name="kayitlar[{{ $usta->id }}][usta_id]" value="{{ $usta->id }}">

                <div class="tip-selector" style="margin-bottom:8px;">
                    @foreach(['tam' => '✅ Tam', 'yarim' => '🌗 Yarım', 'mesai' => '⏰ Mesai'] as $tip => $etiket)
                    <button type="button" class="tip-btn {{ ($kayit && $kayit->calisma_tipi === $tip) || (!$kayit && $tip === 'tam') ? 'active-'.$tip : '' }}"
                            onclick="secTip({{ $usta->id }}, '{{ $tip }}',
                                {{ $usta->gunluk_ucret }},
                                {{ $usta->gunluk_ucret / 2 }},
                                {{ $usta->mesai_saatlik_ucret }})">
                        {{ $etiket }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="kayitlar[{{ $usta->id }}][calisma_tipi]"
                       id="tip-{{ $usta->id }}"
                       value="{{ $kayit->calisma_tipi ?? 'tam' }}">

                <div class="mesai-input-wrap {{ ($kayit && $kayit->calisma_tipi === 'mesai') ? 'show' : '' }}" id="mesai-wrap-{{ $usta->id }}">
                    <input type="number" name="kayitlar[{{ $usta->id }}][mesai_saati]"
                           id="mesai-saat-{{ $usta->id }}"
                           class="form-control" placeholder="Kaç saat?" min="0.5" max="24" step="0.5"
                           value="{{ $kayit?->mesai_saati }}"
                           oninput="hesaplaUcret({{ $usta->id }}, {{ $usta->mesai_saatlik_ucret }})"
                           style="margin-top:4px;">
                </div>

                {{-- İş Seçici --}}
                <select name="kayitlar[{{ $usta->id }}][is_id]" class="form-select" style="margin-top:8px;">
                    <option value="">— İş Seçin (Opsiyonel) —</option>
                    @foreach($isler as $is)
                        <option value="{{ $is->id }}" {{ ($kayit && $kayit->is_id === $is->id) ? 'selected' : '' }}>
                            {{ $is->is_adi }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Boş alanlar (seçili değilken) --}}
            <div class="{{ $secili ? 'd-none' : '' }}" id="bos-{{ $usta->id }}" style="color:var(--text-muted);font-size:0.85rem;">
                Bugün çalışmadı
            </div>

            {{-- Ücret Göstergesi --}}
            <div class="ucret-display {{ $secili ? 'active' : '' }}" id="ucret-{{ $usta->id }}">
                @if($kayit)
                    {{ number_format($kayit->hesaplanan_ucret, 0, ',', '.') }}₺
                @else
                    {{ number_format($usta->gunluk_ucret, 0, ',', '.') }}₺
                @endif
            </div>
        </div>
        @empty
        <div class="card text-center" style="padding:40px;">
            <div style="font-size:2rem;">👷</div>
            <div class="fw-semibold mt-2">Aktif usta bulunamadı</div>
            <a href="{{ route('ustalar.create') }}" class="btn btn-primary mt-3">Usta Ekle</a>
        </div>
        @endforelse
    </div>

    @if($ustalar->count() > 0)
    <div style="margin-top:16px; text-align:right;">
        <button type="submit" class="btn btn-primary btn-lg">💾 Tüm Kayıtları Kaydet</button>
    </div>
    @endif
</form>

{{-- Bu Ay Özeti --}}
@if($ayOzeti->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <span class="card-title">📊 {{ $carbonTarih->locale('tr')->isoFormat('MMMM YYYY') }} Ay Özeti</span>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Usta</th><th>Gün Sayısı</th><th>Toplam Hakedis</th></tr>
            </thead>
            <tbody>
                @foreach($ayOzeti as $ozet)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-2">
                            <div class="avatar-circle" style="width:30px;height:30px;font-size:0.7rem;">
                                {{ strtoupper(mb_substr($ozet->usta->ad ?? '?', 0, 1)) }}{{ strtoupper(mb_substr($ozet->usta->soyad ?? '', 0, 1)) }}
                            </div>
                            {{ $ozet->usta->ad_soyad ?? '—' }}
                        </div>
                    </td>
                    <td><span class="badge badge-primary">{{ $ozet->gun_sayisi }} gün</span></td>
                    <td class="text-success fw-bold">{{ number_format($ozet->toplam_ucret, 0, ',', '.') }}₺</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
const ustaUcretler = {
    @foreach($ustalar as $usta)
    {{ $usta->id }}: { tam: {{ $usta->gunluk_ucret }}, yarim: {{ $usta->gunluk_ucret / 2 }}, mesai: {{ $usta->mesai_saatlik_ucret }} },
    @endforeach
};

function toggleUsta(ustaId) {
    const chk = document.getElementById('chk-' + ustaId);
    const row  = document.getElementById('row-' + ustaId);
    const detay = document.getElementById('detay-' + ustaId);
    const bos   = document.getElementById('bos-' + ustaId);
    const ucret = document.getElementById('ucret-' + ustaId);

    if (chk.checked) {
        row.classList.add('selected');
        row.classList.remove('calismiyor');
        detay.classList.remove('d-none');
        bos.classList.add('d-none');
        ucret.classList.add('active');
        // Varsayılan tam gün
        const tip = document.getElementById('tip-' + ustaId).value || 'tam';
        ucret.textContent = formatPara(ustaUcretler[ustaId][tip]);
    } else {
        row.classList.remove('selected');
        row.classList.add('calismiyor');
        detay.classList.add('d-none');
        bos.classList.remove('d-none');
        ucret.classList.remove('active');
        ucret.textContent = formatPara(ustaUcretler[ustaId].tam);
    }
}

function secTip(ustaId, tip, tam, yarim, mesaiSaat) {
    // Input güncelle
    document.getElementById('tip-' + ustaId).value = tip;

    // Butonları güncelle
    const row = document.getElementById('detay-' + ustaId);
    row.querySelectorAll('.tip-btn').forEach(btn => {
        btn.className = 'tip-btn';
    });
    event.target.classList.add('active-' + tip);

    // Mesai inputu
    const mesaiWrap = document.getElementById('mesai-wrap-' + ustaId);
    if (tip === 'mesai') {
        mesaiWrap.classList.add('show');
    } else {
        mesaiWrap.classList.remove('show');
    }

    // Ücret güncelle
    const ucretEl = document.getElementById('ucret-' + ustaId);
    if (tip === 'tam') ucretEl.textContent = formatPara(tam);
    else if (tip === 'yarim') ucretEl.textContent = formatPara(yarim);
    else ucretEl.textContent = '? ₺';
}

function hesaplaUcret(ustaId, saatlikUcret) {
    const saat = parseFloat(document.getElementById('mesai-saat-' + ustaId).value) || 0;
    const ucretEl = document.getElementById('ucret-' + ustaId);
    ucretEl.textContent = formatPara(saat * saatlikUcret);
}

function formatPara(sayi) {
    return Math.round(sayi).toLocaleString('tr-TR') + '₺';
}

function hepsiniSec() {
    document.querySelectorAll('.toggle-switch input[type=checkbox]').forEach(chk => {
        if (!chk.checked) {
            chk.checked = true;
            toggleUsta(parseInt(chk.value));
        }
    });
}
</script>
@endpush
