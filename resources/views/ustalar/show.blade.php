@extends('layouts.app')

@section('title', $usta->ad_soyad . ' — Usta Profili')
@section('page-title', '👷 ' . $usta->ad_soyad)

@section('header-actions')
    <button onclick="document.getElementById('odemeModal').classList.add('open')"
            class="btn btn-success btn-sm">💸 Ödeme Yap</button>
    <a href="{{ route('ustalar.edit', $usta->id) }}" class="btn btn-warning btn-sm">✏️ Düzenle</a>
    <a href="{{ route('ustalar.index') }}" class="btn btn-secondary btn-sm">← Geri</a>
@endsection

@push('styles')
<style>
.chart-container { position: relative; height: 200px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.info-item label {
    font-size:0.72rem; color:var(--text-muted); display:block;
    margin-bottom:3px; text-transform:uppercase; letter-spacing:0.05em;
}
.info-item span { font-size:0.95rem; font-weight:600; }

.durum-banner {
    padding: 14px 18px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.durum-banner.biz-borclu  { background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); }
.durum-banner.usta-borclu { background:rgba(124,93,249,0.1); border:1px solid rgba(124,93,249,0.35); }
.durum-banner.temiz       { background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.25); }

.odeme-gecmis-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 11px 0; border-bottom: 1px solid var(--border-color); gap: 12px;
}
.odeme-gecmis-item:last-child { border-bottom: none; }

/* Ay Seçici Tabs */
.ay-tabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px; }
.ay-tab {
    padding:5px 12px; border-radius:20px; font-size:0.8rem; font-weight:600;
    background:var(--bg-primary); border:1px solid var(--border-color);
    color:var(--text-muted); cursor:pointer; transition:all 0.2s; text-decoration:none;
}
.ay-tab:hover { border-color:var(--accent-primary); color:var(--accent-primary); }
.ay-tab.active { background:rgba(79,110,247,0.15); border-color:var(--accent-primary); color:var(--accent-primary); }

/* Ödeme Modal */
.odeme-modal-body { display:flex; flex-direction:column; gap:14px; }
.odeme-modal-info {
    background:var(--bg-primary); border-radius:var(--radius-sm);
    padding:12px 16px; display:flex; justify-content:space-between; align-items:center;
}
.odeme-confirm-overlay {
    display:none; position:fixed; inset:0; z-index:2000;
    background:rgba(0,0,0,0.8); align-items:center; justify-content:center; padding:20px;
}
.odeme-confirm-overlay.show { display:flex; }
.odeme-confirm-box {
    background:var(--bg-card); border:1px solid rgba(239,68,68,0.4);
    border-radius:var(--radius-xl); padding:32px; max-width:420px; width:100%;
    text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.5);
}
</style>
@endpush

@section('content')

{{-- ===== ÖDEME MODAL ===== --}}
<div class="modal-overlay" id="odemeModal">
  <div class="modal" style="max-width:500px;">
    <div class="modal-header">
        <div class="modal-title">💸 Ödeme Yap — {{ $usta->ad_soyad }}</div>
        <button class="modal-close" onclick="document.getElementById('odemeModal').classList.remove('open')">✕</button>
    </div>

    <form id="odemeForm" action="{{ route('aylik-hesap.odeme') }}" method="POST">
        @csrf
        <input type="hidden" name="usta_id" value="{{ $usta->id }}">
        <input type="hidden" name="redirect_to" value="profil">

        <div class="odeme-modal-body">

            {{-- Ay / Yıl seçimi --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Ay *</label>
                    <select name="ay" id="modalAy" class="form-select" onchange="guncelleHakedis()">
                        @foreach($aylar as $num => $isim)
                            <option value="{{ $num }}" {{ $num == $seciliAy ? 'selected' : '' }}>
                                {{ $isim }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin:0;">
                    <label class="form-label">Yıl *</label>
                    <select name="yil" id="modalYil" class="form-select" onchange="guncelleHakedis()">
                        @foreach(range(now()->year, now()->year - 3) as $y)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Ay hakedis bilgisi --}}
            <div class="odeme-modal-info" id="modalHakedisInfo">
                <div>
                    <div class="text-muted fs-sm">Bu Ay Hakedis</div>
                    <div class="fw-bold text-primary" id="modalHakedisVal">Yükleniyor...</div>
                </div>
                <div>
                    <div class="text-muted fs-sm">Ödenen</div>
                    <div class="fw-bold text-success" id="modalOdenenVal">—</div>
                </div>
                <div>
                    <div class="text-muted fs-sm">Kalan</div>
                    <div class="fw-bold" id="modalKalanVal">—</div>
                </div>
            </div>

            {{-- Ödeme tutarı --}}
            <div class="form-group" style="margin:0;">
                <label class="form-label">Ödenecek Tutar (₺) *</label>
                <input type="number" name="odenen_tutar" id="modalTutar"
                       class="form-control" min="0.01" step="0.01"
                       placeholder="Ödeme miktarını girin..." required>
                <div style="margin-top:6px; display:flex; gap:8px;">
                    <button type="button" class="btn btn-secondary btn-sm"
                            onclick="tutarDoldur('tam')">Tamamını Öde</button>
                    <button type="button" class="btn btn-secondary btn-sm"
                            onclick="tutarDoldur('yari')">Yarısını Öde</button>
                </div>
            </div>

            {{-- Ödeme yöntemi --}}
            <div class="form-group" style="margin:0;">
                <label class="form-label">Ödeme Yöntemi</label>
                <select name="odeme_yontemi" class="form-select">
                    <option value="nakit">💵 Nakit</option>
                    <option value="havale">🏦 Havale</option>
                    <option value="çek">📄 Çek</option>
                    <option value="diger">🔄 Diğer</option>
                </select>
            </div>

            {{-- Not --}}
            <div class="form-group" style="margin:0;">
                <label class="form-label">Not (opsiyonel)</label>
                <input type="text" name="notlar" class="form-control" placeholder="Açıklama...">
            </div>

            {{-- Onay Butonu --}}
            <div style="display:flex; gap:10px; margin-top:4px;">
                <button type="button" class="btn btn-success" style="flex:1;" onclick="odemeyiOnayla()">
                    💸 Ödemeyi Onayla
                </button>
                <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('odemeModal').classList.remove('open')">
                    İptal
                </button>
            </div>
        </div>
    </form>
  </div>
</div>

{{-- ===== ONAY POPUP ===== --}}
<div class="odeme-confirm-overlay" id="onayOverlay">
    <div class="odeme-confirm-box">
        <div style="font-size:3rem; margin-bottom:12px;">⚠️</div>
        <div style="font-size:1.2rem; font-weight:700; margin-bottom:8px; color:var(--text-primary);">
            Ödemeyi Onaylıyor Musunuz?
        </div>
        <div style="color:var(--text-muted); margin-bottom:20px; font-size:0.9rem;" id="onayAciklama">
            <!-- JS ile doldurulur -->
        </div>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button class="btn btn-success" onclick="odemeyiGonder()">✅ Evet, Onayla</button>
            <button class="btn btn-secondary" onclick="onayiIptal()">❌ İptal</button>
        </div>
    </div>
</div>

{{-- ===== ANA SAYFA İÇERİĞİ ===== --}}
<div class="profile-grid">

    {{-- ===== Sol Panel ===== --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Profil Kartı --}}
        <div class="card text-center">
            <div class="avatar-circle" style="width:80px;height:80px;font-size:1.8rem;margin:0 auto 16px;">
                {{ strtoupper(mb_substr($usta->ad, 0, 1)) }}{{ strtoupper(mb_substr($usta->soyad, 0, 1)) }}
            </div>
            <div style="font-size:1.25rem;font-weight:700;margin-bottom:4px;">{{ $usta->ad_soyad }}</div>
            <div class="text-muted fs-sm" style="margin-bottom:12px;">{{ $usta->uzmanlik ?? 'Genel İşçi' }}</div>
            @if($usta->durum === 'aktif')
                <span class="badge badge-success" style="font-size:0.85rem;">● Aktif</span>
            @else
                <span class="badge badge-secondary" style="font-size:0.85rem;">● Pasif</span>
            @endif
            @if($usta->telefon)
                <div style="margin-top:14px;">
                    <a href="tel:{{ $usta->telefon }}" class="btn btn-secondary btn-sm"
                       style="width:100%;justify-content:center;">📞 {{ $usta->telefon }}</a>
                </div>
            @endif
            @if($usta->iban)
                <div style="margin-top:10px;padding:10px;background:var(--bg-primary);border-radius:var(--radius-sm);text-align:left;">
                    <div class="text-muted fs-sm" style="margin-bottom:4px;display:flex;justify-content:space-between;align-items:center;">
                        <span>💳 IBAN</span>
                        <button type="button" class="btn btn-secondary btn-sm" style="padding:2px 8px;font-size:0.75rem;" onclick="copyIban('{{ $usta->iban }}', this)">📋 Kopyala</button>
                    </div>
                    <div style="font-size:0.85rem;font-weight:600;font-family:monospace;word-break:break-all;" id="ibanText">{{ $usta->iban }}</div>
                </div>
            @endif
            @if($usta->notlar)
                <div style="margin-top:12px;padding:10px;background:var(--bg-primary);border-radius:var(--radius-sm);text-align:left;">
                    <div class="text-muted fs-sm" style="margin-bottom:4px;">📝 Notlar</div>
                    <div style="font-size:0.875rem;">{{ $usta->notlar }}</div>
                </div>
            @endif
        </div>

        {{-- Ücret Bilgisi --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:14px;">💰 Ücret Bilgisi</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Tam Gün</label>
                    <span class="text-success">{{ number_format($usta->gunluk_ucret, 0, ',', '.') }}₺</span>
                </div>
                <div class="info-item">
                    <label>Yarım Gün</label>
                    <span class="text-warning">{{ number_format($usta->gunluk_ucret / 2, 0, ',', '.') }}₺</span>
                </div>
                <div class="info-item">
                    <label>Mesai / Saat</label>
                    <span class="text-primary">{{ number_format($usta->mesai_saatlik_ucret, 0, ',', '.') }}₺</span>
                </div>
            </div>
        </div>

        {{-- Mali Özet --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:14px;">📊 Mali Özet</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Toplam Hakedis</label>
                    <span class="text-primary">{{ number_format($toplamHakedis, 0, ',', '.') }}₺</span>
                </div>
                <div class="info-item">
                    <label>Toplam Ödenen</label>
                    <span class="text-success">{{ number_format($toplamOdenen, 0, ',', '.') }}₺</span>
                </div>
                <div class="info-item">
                    <label>Bu Ay Hakedis</label>
                    <span class="text-warning">{{ number_format($aylikHakedis, 0, ',', '.') }}₺</span>
                </div>
                <div class="info-item">
                    <label>Net Durum</label>
                    @if($fazlaOdeme > 0)
                        <span class="text-secondary" style="color:#a78bfa!important;">
                            Fazla: {{ number_format($fazlaOdeme, 0, ',', '.') }}₺
                        </span>
                    @elseif($toplamBorç > 0)
                        <span class="text-danger">{{ number_format($toplamBorç, 0, ',', '.') }}₺ borç</span>
                    @else
                        <span class="text-success">✓ Temiz</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Durum Banneri --}}
        @if($fazlaOdeme > 0)
        <div class="durum-banner usta-borclu">
            <div>
                <div style="font-weight:700; color:#a78bfa; font-size:1rem;">🔄 Usta Bize Borçlu</div>
                <div class="text-muted fs-sm">Fazla ödeme yapıldı — usta alacaklı değil</div>
            </div>
            <div style="font-size:1.3rem; font-weight:800; color:#a78bfa;">
                {{ number_format($fazlaOdeme, 0, ',', '.') }}₺
            </div>
        </div>
        @elseif($toplamBorç > 0)
        <div class="durum-banner biz-borclu">
            <div>
                <div style="font-weight:700; color:var(--accent-danger); font-size:1rem;">⚠️ Ödenmemiş Bakiye</div>
                <div class="text-muted fs-sm">Ustaya ödenecek tutar</div>
            </div>
            <div style="font-size:1.3rem; font-weight:800; color:var(--accent-danger);">
                {{ number_format($toplamBorç, 0, ',', '.') }}₺
            </div>
        </div>
        @else
        <div class="durum-banner temiz">
            <div>
                <div style="font-weight:700; color:var(--accent-success);">✅ Hesap Temiz</div>
                <div class="text-muted fs-sm">Tüm ödemeler tamamlanmış</div>
            </div>
            <div style="font-size:1.4rem;">🎉</div>
        </div>
        @endif

    </div>

    {{-- ===== Sağ Panel ===== --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Hakedis Grafiği --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📊 Son 6 Ay Hakedis</span>
            </div>
            <div class="chart-container">
                <canvas id="ustaHakedisChart"></canvas>
            </div>
        </div>

        {{-- Bu Ay Devam + Ay Seçici --}}
        <div class="card">
            <div class="card-header" style="flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <span class="card-title">📅 Ay Devam Kaydı</span>
                    {{-- Ay seçici --}}
                    <form method="GET" action="{{ route('ustalar.show', $usta->id) }}"
                          style="display:flex; gap:6px; align-items:center;" id="aySecForm">
                        <select name="ay" class="form-select" style="width:110px; padding:5px 8px; font-size:0.8rem;"
                                onchange="document.getElementById('aySecForm').submit()">
                        @foreach($aylar as $num => $isim)
                                <option value="{{ $num }}" {{ $num == $seciliAy ? 'selected' : '' }}>{{ $isim }}</option>
                            @endforeach
                        </select>
                        <select name="yil" class="form-select" style="width:85px; padding:5px 8px; font-size:0.8rem;"
                                onchange="document.getElementById('aySecForm').submit()">
                            @foreach(range(now()->year, now()->year - 3) as $y)
                                <option value="{{ $y }}" {{ $y == $seciliYil ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="badge badge-primary">{{ $aylikKayitlar->count() }} kayıt</span>
                    @if($buAyOdeme)
                        @if($buAyOdeme->kapandi)
                            <span class="badge badge-success">✅ Ödendi</span>
                        @elseif($buAyOdeme->kalan_bakiye > 0)
                            <span class="badge badge-warning">⏳ {{ number_format($buAyOdeme->kalan_bakiye, 0, ',', '.') }}₺ Kalan</span>
                        @endif
                    @elseif($aylikHakedis > 0)
                        <span class="badge badge-danger">⚠️ Ödenmedi</span>
                    @endif
                    {{-- PDF Butonu --}}
                    <a href="{{ route('aylik-hesap.pdf', ['usta' => $usta->id, 'ay' => $seciliAy, 'yil' => $seciliYil]) }}"
                       target="_blank"
                       class="btn btn-secondary btn-sm" title="PDF / Yazdır" style="gap:4px;">
                        🖨️ PDF
                    </a>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tarih</th>
                            <th>Çalışma</th>
                            <th>İş</th>
                            <th>Ücret</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aylikKayitlar as $kayit)
                        <tr>
                            <td>{{ $kayit->tarih->locale('tr')->isoFormat('D MMMM dddd') }}</td>
                            <td>
                                @if($kayit->calisma_tipi === 'tam')
                                    <span class="badge badge-success">✅ Tam Gün</span>
                                @elseif($kayit->calisma_tipi === 'yarim')
                                    <span class="badge badge-warning">🌗 Yarım Gün</span>
                                @else
                                    <span class="badge badge-primary">⏰ Mesai ({{ $kayit->mesai_saati }}s)</span>
                                @endif
                            </td>
                            <td class="text-muted fs-sm">{{ $kayit->ilgiliIs->is_adi ?? '—' }}</td>
                            <td class="text-success fw-semibold">{{ number_format($kayit->hesaplanan_ucret, 0, ',', '.') }}₺</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted" style="padding:20px;">Bu ay kayıt yok</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($aylikKayitlar->count() > 0)
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right fw-semibold">{{ $aylar[$seciliAy] ?? '' }} Toplamı:</td>
                            <td class="text-success fw-bold">{{ number_format($aylikHakedis, 0, ',', '.') }}₺</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Ödeme Geçmişi --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">💳 Ödeme Geçmişi</span>
                <span class="badge badge-secondary">{{ $odemeler->count() }} kayıt</span>
            </div>

            @forelse($odemeler as $odeme)
            <div class="odeme-gecmis-item">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:42px;height:42px;border-radius:var(--radius-sm);
                                background:{{ $odeme->kapandi ? 'rgba(34,197,94,0.12)' : 'rgba(245,158,11,0.12)' }};
                                display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                        {{ $odeme->kapandi ? '✅' : '⏳' }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $odeme->ay_ad }}</div>
                        <div class="text-muted fs-sm">
                            {{ $odeme->odeme_tarihi?->locale('tr')->isoFormat('D MMMM YYYY') ?? '—' }}
                            @if($odeme->odeme_yontemi)· {{ ucfirst($odeme->odeme_yontemi) }}@endif
                        </div>
                        @if($odeme->notlar)
                            <div class="text-muted fs-sm" style="font-style:italic;">{{ $odeme->notlar }}</div>
                        @endif
                    </div>
                </div>
                <div style="text-align:right;">
                    <div class="text-muted fs-sm">Hakedis: {{ number_format($odeme->toplam_hakkedis, 0, ',', '.') }}₺</div>
                    <div class="text-success fw-semibold">Ödenen: {{ number_format($odeme->odenen_tutar, 0, ',', '.') }}₺</div>
                    @if($odeme->kalan_bakiye > 0)
                        <div class="text-danger fs-sm">Kalan: {{ number_format($odeme->kalan_bakiye, 0, ',', '.') }}₺</div>
                    @elseif($odeme->kalan_bakiye < 0)
                        <div style="color:#a78bfa; font-size:0.78rem;">Fazla: {{ number_format(abs($odeme->kalan_bakiye), 0, ',', '.') }}₺</div>
                    @endif
                    @if($odeme->kapandi)
                        <span class="badge badge-success" style="margin-top:2px;">Kapalı</span>
                    @endif
                    {{-- Aylık PDF butonu --}}
                    <a href="{{ route('aylik-hesap.pdf', ['usta' => $usta->id, 'ay' => $odeme->ay, 'yil' => $odeme->yil]) }}"
                       target="_blank" class="btn btn-secondary btn-sm" style="margin-top:4px;">🖨️</a>
                </div>
            </div>
            @empty
            <div class="text-center text-muted" style="padding:30px 0;">
                <div style="font-size:2rem; margin-bottom:8px;">💳</div>
                <div>Henüz ödeme kaydı yok</div>
                <button onclick="document.getElementById('odemeModal').classList.add('open')"
                        class="btn btn-success btn-sm" style="margin-top:12px;">Ödeme Yap →</button>
            </div>
            @endforelse
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ===== Hakedis Grafiği =====
const labels = @json(array_column($aylikGrafikVeri, 'ay'));
const data   = @json(array_column($aylikGrafikVeri, 'hakedis'));

new Chart(document.getElementById('ustaHakedisChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Hakedis',
            data,
            borderColor: '#4f6ef7',
            backgroundColor: 'rgba(79,110,247,0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#4f6ef7',
            pointRadius: 5,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ctx.raw.toLocaleString('tr-TR') + '₺' } }
        },
        scales: {
            x: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.05)' } },
            y: {
                ticks: { color: '#64748b', callback: v => v.toLocaleString('tr-TR') + '₺' },
                grid: { color: 'rgba(255,255,255,0.05)' }
            }
        }
    }
});

// ===== Ödeme Modal Mantığı =====
// Ay/Yıl bazlı hakedis verisi (server'dan)
const ustaId = {{ $usta->id }};
let modalHakedis = 0;
let modalKalan   = 0;

async function guncelleHakedis() {
    const ay  = document.getElementById('modalAy').value;
    const yil = document.getElementById('modalYil').value;

    document.getElementById('modalHakedisVal').textContent = '...';
    document.getElementById('modalOdenenVal').textContent  = '...';
    document.getElementById('modalKalanVal').textContent   = '...';

    try {
        const resp = await fetch(`/aylik-hesap/hakedis-bilgi?usta_id=${ustaId}&ay=${ay}&yil=${yil}`);
        const veri = await resp.json();

        modalHakedis = veri.hakedis ?? 0;
        const odenen = veri.odenen ?? 0;
        modalKalan   = veri.kalan ?? modalHakedis;

        document.getElementById('modalHakedisVal').textContent = formatPara(modalHakedis);
        document.getElementById('modalOdenenVal').textContent  = formatPara(odenen);

        const kalanEl = document.getElementById('modalKalanVal');
        kalanEl.textContent  = formatPara(Math.abs(modalKalan));
        kalanEl.className    = 'fw-bold ' + (modalKalan > 0 ? 'text-danger' : modalKalan < 0 ? '' : 'text-success');
        if (modalKalan < 0) kalanEl.style.color = '#a78bfa';
        else kalanEl.style.color = '';

        // Otomatik kalan tutarı doldur
        if (modalKalan > 0) {
            document.getElementById('modalTutar').value = modalKalan.toFixed(2);
        } else {
            document.getElementById('modalTutar').value = '';
        }
    } catch(e) {
        document.getElementById('modalHakedisVal').textContent = 'Hata';
    }
}

function tutarDoldur(tip) {
    const input = document.getElementById('modalTutar');
    if (tip === 'tam' && modalKalan > 0) {
        input.value = modalKalan.toFixed(2);
    } else if (tip === 'yari' && modalKalan > 0) {
        input.value = (modalKalan / 2).toFixed(2);
    }
}

function formatPara(sayi) {
    return Math.round(sayi).toLocaleString('tr-TR') + '₺';
}

// Modal açıldığında hakedis bilgisini yükle
document.getElementById('odemeModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});

// İlk yükleme
document.addEventListener('DOMContentLoaded', () => {
    // Modal trigger'dan sonra yükle
    document.querySelector('[onclick*="odemeModal"]')?.addEventListener('click', () => {
        setTimeout(guncelleHakedis, 50);
    });
});

// ===== Onay Sistemi =====
function odemeyiOnayla() {
    const tutar  = parseFloat(document.getElementById('modalTutar').value);
    const ay     = document.getElementById('modalAy').selectedOptions[0].text;
    const yil    = document.getElementById('modalYil').value;

    if (!tutar || tutar <= 0) {
        alert('Lütfen geçerli bir tutar girin.');
        return;
    }

    document.getElementById('onayAciklama').innerHTML =
        `<strong>{{ $usta->ad_soyad }}</strong> için <br>` +
        `<strong style="font-size:1.3rem; color:#22c55e;">${formatPara(tutar)}</strong><br>` +
        `${ay} ${yil} dönemi ödemesi yapılacak ve giderlere kaydedilecek.`;

    document.getElementById('odemeModal').classList.remove('open');
    document.getElementById('onayOverlay').classList.add('show');
}

function odemeyiGonder() {
    document.getElementById('odemeForm').submit();
}

function onayiIptal() {
    document.getElementById('onayOverlay').classList.remove('show');
    document.getElementById('odemeModal').classList.add('open');
}

function copyIban(text, btnElement) {
    navigator.clipboard.writeText(text).then(() => {
        const oldText = btnElement.innerText;
        btnElement.innerText = '✅ Kopyalandı!';
        btnElement.classList.remove('btn-secondary');
        btnElement.classList.add('btn-success');
        setTimeout(() => {
            btnElement.innerText = oldText;
            btnElement.classList.remove('btn-success');
            btnElement.classList.add('btn-secondary');
        }, 2000);
    }).catch(err => {
        alert('Kopyalanırken bir hata oluştu: ' + err);
    });
}
</script>
@endpush
