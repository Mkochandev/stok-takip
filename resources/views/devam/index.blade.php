@extends('layouts.app')

@section('title', 'Devam Takibi')
@section('page-title', 'Devam Takibi')

@push('styles')
<style>
.date-nav {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 14px 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}
.date-nav input[type=date] {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    color: var(--text-primary);
    font-family: inherit;
    font-size: 0.95rem;
    font-weight: 700;
    padding: 8px 12px;
    cursor: pointer;
    outline: none;
}
.date-nav input[type=date]:focus { border-color: var(--accent-primary); }

.usta-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    margin-bottom: 12px;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}
.usta-card.selected {
    border-color: #10b981;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);
}
.usta-card.calismiyor {
    background: #fafafa;
    opacity: 0.85;
}

.usta-header-grid {
    display: grid;
    grid-template-columns: 240px 1fr 120px;
    gap: 16px;
    align-items: center;
}

@media (max-width: 992px) {
    .date-nav {
        flex-wrap: wrap;
        padding: 12px 14px;
        gap: 10px;
    }
    .date-nav-actions {
        width: 100%;
        display: flex;
        justify-content: space-between;
        gap: 6px;
    }
    .usta-header-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
}

.usta-info-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* 4-State Segmented Pill Control */
.segmented-control {
    display: flex;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 3px;
    gap: 4px;
    width: 100%;
}

.segmented-btn {
    flex: 1;
    padding: 8px 6px;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: var(--text-secondary);
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    transition: all var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-align: center;
    white-space: nowrap;
}

.segmented-btn:hover {
    color: var(--text-primary);
    background: rgba(255, 255, 255, 0.6);
}

/* Active Segment Pill States */
.segmented-btn.active-off {
    background: #ffffff;
    color: #475569;
    border: 1px solid #cbd5e1;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.segmented-btn.active-tam {
    background: #10b981;
    color: #ffffff;
    border: 1px solid #059669;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
}
.segmented-btn.active-yarim {
    background: #f59e0b;
    color: #ffffff;
    border: 1px solid #d97706;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
}
.segmented-btn.active-mesai {
    background: #6366f1;
    color: #ffffff;
    border: 1px solid #4f46e5;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
}

.mesai-input-wrap {
    display: none;
    margin-top: 8px;
}
.mesai-input-wrap.show {
    display: flex;
    align-items: center;
    gap: 8px;
}

.ucret-badge {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--text-muted);
    text-align: right;
}
.ucret-badge.active {
    color: #10b981;
}

@media (max-width: 992px) {
    .ucret-badge { text-align: left; }
}
</style>
@endpush

@section('content')

{{-- Tarih Seçici --}}
<div class="date-nav">
    <div style="display:flex; align-items:center; gap:8px;">
        <span style="font-size:1.1rem;">📅</span>
        <span class="text-muted fw-semibold">Tarih:</span>
        <input type="date" id="tarihInput" value="{{ $tarih }}"
               onchange="window.location.href='{{ route('devam.index') }}?tarih=' + this.value">
    </div>
    <span class="fw-bold text-primary" style="font-size:1.05rem;">
        {{ $carbonTarih->locale('tr')->isoFormat('D MMMM YYYY, dddd') }}
    </span>

    <div class="date-nav-actions" style="margin-left:auto; display:flex; gap:8px;">
        <a href="{{ route('devam.index', ['tarih' => $carbonTarih->copy()->subDay()->toDateString()]) }}"
           class="btn btn-secondary btn-sm">← Önceki</a>
        <a href="{{ route('devam.index', ['tarih' => now()->toDateString()]) }}"
           class="btn btn-secondary btn-sm">Bugün</a>
        <a href="{{ route('devam.index', ['tarih' => $carbonTarih->copy()->addDay()->toDateString()]) }}"
           class="btn btn-secondary btn-sm">Sonraki →</a>
    </div>
</div>

{{-- Devam Formu --}}
<form action="{{ route('devam.store') }}" method="POST" id="devamForm">
    @csrf
    <input type="hidden" name="tarih" value="{{ $tarih }}">

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
        <div>
            <span class="fw-bold text-primary" style="font-size:1.1rem;">👷 Usta Devam Listesi</span>
            <span class="text-muted fs-sm ml-2">— Durum butonuna basarak hakediş tipini belirleyin</span>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="hepsiniTamGunYap()">⚡ Hepsini Tam Gün Yap</button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="hepsiniTemizle()">❌ Hepsini Temizle</button>
            <button type="submit" class="btn btn-success">💾 Kayıtları Kaydet</button>
        </div>
    </div>

    <div id="ustaListesi">
        @forelse($ustalar as $usta)
        @php
            $kayit = $gunKayitlari[$usta->id] ?? null;
            $secili = !is_null($kayit);
            $calismaTipi = $kayit ? $kayit->calisma_tipi : 'off';
        @endphp
        <div class="usta-card {{ $secili ? 'selected' : 'calismiyor' }}" id="card-{{ $usta->id }}">

            <div class="usta-header-grid">

                {{-- Usta Ad / Avatar --}}
                <div class="usta-info-wrap">
                    <div class="avatar" style="width:42px; height:42px; font-size:0.9rem;">
                        {{ strtoupper(mb_substr($usta->ad, 0, 1)) }}{{ strtoupper(mb_substr($usta->soyad, 0, 1)) }}
                    </div>
                    <div style="min-width:0;">
                        <a href="{{ route('ustalar.show', $usta->id) }}" target="_blank"
                           style="font-size:0.95rem; font-weight:800; color:var(--text-primary); text-decoration:none;"
                           title="Profilini Gör">
                            {{ $usta->ad_soyad }}
                        </a>
                        <div class="text-muted fs-sm">
                            {{ $usta->uzmanlik ?? 'Genel Usta' }}
                            @if($kayit)
                                <span class="badge badge-success" style="font-size:0.65rem; margin-left:4px;">✓ Kayıtlı</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 4-State Segmented Control Pill Group --}}
                <div>
                    {{-- Hidden Form Inputs --}}
                    <input type="hidden" name="kayitlar[{{ $usta->id }}][usta_id]"
                           id="input-usta-{{ $usta->id }}"
                           value="{{ $usta->id }}"
                           {{ !$secili ? 'disabled' : '' }}>
                    <input type="hidden" name="kayitlar[{{ $usta->id }}][calisma_tipi]"
                           id="input-tip-{{ $usta->id }}"
                           value="{{ $calismaTipi }}"
                           {{ !$secili ? 'disabled' : '' }}>

                    <div class="segmented-control">
                        <button type="button" class="segmented-btn {{ $calismaTipi === 'off' ? 'active-off' : '' }}"
                                id="btn-off-{{ $usta->id }}"
                                onclick="setDurum({{ $usta->id }}, 'off')">
                            ❌ Çalışmadı
                        </button>
                        <button type="button" class="segmented-btn {{ $calismaTipi === 'tam' ? 'active-tam' : '' }}"
                                id="btn-tam-{{ $usta->id }}"
                                onclick="setDurum({{ $usta->id }}, 'tam')">
                            ✅ Tam Gün
                        </button>
                        <button type="button" class="segmented-btn {{ $calismaTipi === 'yarim' ? 'active-yarim' : '' }}"
                                id="btn-yarim-{{ $usta->id }}"
                                onclick="setDurum({{ $usta->id }}, 'yarim')">
                            🌗 Yarım Gün
                        </button>
                        <button type="button" class="segmented-btn {{ $calismaTipi === 'mesai' ? 'active-mesai' : '' }}"
                                id="btn-mesai-{{ $usta->id }}"
                                onclick="setDurum({{ $usta->id }}, 'mesai')">
                            ⏰ Mesai
                        </button>
                    </div>

                    {{-- Mesai Saat Input & İş Seçimi Ekstra Alanlar --}}
                    <div class="mesai-input-wrap {{ $calismaTipi === 'mesai' ? 'show' : '' }}" id="mesai-wrap-{{ $usta->id }}">
                        <input type="number" name="kayitlar[{{ $usta->id }}][mesai_saati]"
                               id="mesai-saat-{{ $usta->id }}"
                               class="form-control" placeholder="Kaç saat mesai?" min="0.5" max="24" step="0.5"
                               value="{{ $kayit?->mesai_saati ?? 1 }}"
                               oninput="hesaplaUcret({{ $usta->id }})"
                               {{ $calismaTipi !== 'mesai' ? 'disabled' : '' }}
                               style="max-width:140px; padding:6px 10px;">
                        <span class="fs-sm text-muted">saat</span>
                    </div>

                    <div style="margin-top:6px;">
                        <select name="kayitlar[{{ $usta->id }}][is_id]"
                                id="is-select-{{ $usta->id }}"
                                class="form-select" style="padding:4px 8px; font-size:0.8rem;"
                                {{ !$secili ? 'disabled' : '' }}>
                            <option value="">— İş Seçin (Opsiyonel) —</option>
                            @foreach($isler as $is)
                                <option value="{{ $is->id }}" {{ ($kayit && $kayit->is_id === $is->id) ? 'selected' : '' }}>
                                    {{ $is->is_adi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Hakediş Ücret Göstergesi --}}
                <div>
                    <div class="ucret-badge {{ $secili ? 'active' : '' }}" id="ucret-{{ $usta->id }}">
                        @if($kayit)
                            {{ number_format($kayit->hesaplanan_ucret, 0, ',', '.') }}₺
                        @else
                            0₺
                        @endif
                    </div>
                    <div class="fs-sm text-muted text-right" style="font-size:0.75rem;">
                        Günlük: {{ number_format($usta->gunluk_ucret, 0, ',', '.') }}₺
                    </div>
                </div>

            </div>
        </div>
        @empty
        <div class="card text-center" style="padding:50px;">
            <div style="font-size:2.5rem; margin-bottom:10px;">👷</div>
            <div class="fw-bold text-primary" style="font-size:1.1rem;">Aktif usta bulunamadı</div>
            <p class="text-muted fs-sm mt-1">Devam kaydı almak için önce sisteminize usta ekleyin.</p>
            <div style="margin-top:16px;">
                <a href="{{ route('ustalar.create') }}" class="btn btn-primary">Usta Ekle →</a>
            </div>
        </div>
        @endforelse
    </div>

    @if($ustalar->count() > 0)
    <div style="margin-top:20px; text-align:right;">
        <button type="submit" class="btn btn-success btn-lg" style="padding:12px 28px; font-size:1rem;">
            💾 Tüm Kayıtları Kaydet
        </button>
    </div>
    @endif
</form>

{{-- Bu Ay Özeti --}}
@if($ayOzeti->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <span class="card-title">📊 {{ $carbonTarih->locale('tr')->isoFormat('MMMM YYYY') }} Ay Özeti</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Usta</th>
                    <th>Toplam Gün</th>
                    <th>Toplam Hakediş</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ayOzeti as $ozet)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="avatar" style="width:32px; height:32px; font-size:0.75rem;">
                                {{ strtoupper(mb_substr($ozet->usta->ad ?? '?', 0, 1)) }}{{ strtoupper(mb_substr($ozet->usta->soyad ?? '', 0, 1)) }}
                            </div>
                            <span class="fw-bold text-primary">{{ $ozet->usta->ad_soyad ?? '—' }}</span>
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
    {{ $usta->id }}: {
        tam: {{ $usta->gunluk_ucret }},
        yarim: {{ $usta->gunluk_ucret / 2 }},
        mesaiSaat: {{ $usta->mesai_saatlik_ucret }}
    },
    @endforeach
};

function setDurum(ustaId, tip) {
    const card = document.getElementById('card-' + ustaId);
    const inputUsta = document.getElementById('input-usta-' + ustaId);
    const inputTip = document.getElementById('input-tip-' + ustaId);
    const mesaiWrap = document.getElementById('mesai-wrap-' + ustaId);
    const mesaiInput = document.getElementById('mesai-saat-' + ustaId);
    const isSelect = document.getElementById('is-select-' + ustaId);
    const ucretBadge = document.getElementById('ucret-' + ustaId);

    // Tüm buton aktif class'larını temizle
    const tips = ['off', 'tam', 'yarim', 'mesai'];
    tips.forEach(t => {
        const btn = document.getElementById(`btn-${t}-${ustaId}`);
        if (btn) btn.className = 'segmented-btn';
    });

    // Tıklanan butonu aktif yap
    const activeBtn = document.getElementById(`btn-${tip}-${ustaId}`);
    if (activeBtn) activeBtn.classList.add('active-' + tip);

    if (tip === 'off') {
        // Çalışmadı
        card.className = 'usta-card calismiyor';
        inputUsta.disabled = true;
        inputTip.disabled = true;
        inputTip.value = 'off';
        if (isSelect) isSelect.disabled = true;
        mesaiWrap.classList.remove('show');
        if (mesaiInput) mesaiInput.disabled = true;
        ucretBadge.textContent = '0₺';
        ucretBadge.classList.remove('active');
    } else {
        // Çalıştı (tam, yarim, mesai)
        card.className = 'usta-card selected';
        inputUsta.disabled = false;
        inputTip.disabled = false;
        inputTip.value = tip;
        if (isSelect) isSelect.disabled = false;

        if (tip === 'mesai') {
            mesaiWrap.classList.add('show');
            if (mesaiInput) mesaiInput.disabled = false;
            hesaplaUcret(ustaId);
        } else {
            mesaiWrap.classList.remove('show');
            if (mesaiInput) mesaiInput.disabled = true;
            const ucret = ustaUcretler[ustaId][tip] || 0;
            ucretBadge.textContent = formatPara(ucret);
        }
        ucretBadge.classList.add('active');
    }
}

function hesaplaUcret(ustaId) {
    const mesaiInput = document.getElementById('mesai-saat-' + ustaId);
    const ucretBadge = document.getElementById('ucret-' + ustaId);
    const saat = parseFloat(mesaiInput ? mesaiInput.value : 0) || 0;
    const saatlikUcret = ustaUcretler[ustaId]?.mesaiSaat || 0;
    ucretBadge.textContent = formatPara(saat * saatlikUcret);
}

function formatPara(sayi) {
    return Math.round(sayi).toLocaleString('tr-TR') + '₺';
}

function hepsiniTamGunYap() {
    Object.keys(ustaUcretler).forEach(id => {
        setDurum(parseInt(id), 'tam');
    });
}

function hepsiniTemizle() {
    Object.keys(ustaUcretler).forEach(id => {
        setDurum(parseInt(id), 'off');
    });
}
</script>
@endpush
