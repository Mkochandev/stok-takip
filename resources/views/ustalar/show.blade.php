@extends('layouts.app')

@section('title', $usta->ad_soyad . ' — Usta Profili')
@section('page-title', '👷 ' . $usta->ad_soyad)

@section('header-actions')
    <a href="{{ route('ustalar.edit', $usta->id) }}" class="btn btn-warning btn-sm">✏️ Düzenle</a>
    <a href="{{ route('aylik-hesap.index') }}" class="btn btn-success btn-sm">💸 Ödeme Yap</a>
    <a href="{{ route('ustalar.index') }}" class="btn btn-secondary btn-sm">← Geri</a>
@endsection

@push('styles')
<style>
.chart-container { position: relative; height: 200px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.info-item label { font-size:0.75rem; color:var(--text-muted); display:block; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.04em; }
.info-item span { font-size:0.95rem; font-weight:600; }

.profile-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}
.borç-banner {
    padding: 16px 20px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.borç-banner.kirmizi {
    background: rgba(239,68,68,0.1);
    border: 1px solid rgba(239,68,68,0.3);
}
.borç-banner.yesil {
    background: rgba(34,197,94,0.08);
    border: 1px solid rgba(34,197,94,0.25);
}
.odeme-gecmis-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
    gap: 12px;
}
.odeme-gecmis-item:last-child { border-bottom: none; }
</style>
@endpush

@section('content')

<div style="display:grid; grid-template-columns: 320px 1fr; gap:20px; align-items:start;">

    {{-- ===== Sol Panel ===== --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Profil Kartı --}}
        <div class="card text-center">
            <div class="avatar-circle" style="width:80px; height:80px; font-size:1.8rem; margin:0 auto 16px;">
                {{ strtoupper(mb_substr($usta->ad, 0, 1)) }}{{ strtoupper(mb_substr($usta->soyad, 0, 1)) }}
            </div>
            <div style="font-size:1.3rem; font-weight:700; margin-bottom:4px;">{{ $usta->ad_soyad }}</div>
            <div class="text-muted fs-sm" style="margin-bottom:12px;">{{ $usta->uzmanlik ?? 'Genel İşçi' }}</div>

            @if($usta->durum === 'aktif')
                <span class="badge badge-success" style="font-size:0.85rem;">● Aktif</span>
            @else
                <span class="badge badge-secondary" style="font-size:0.85rem;">● Pasif</span>
            @endif

            @if($usta->telefon)
                <div style="margin-top:16px;">
                    <a href="tel:{{ $usta->telefon }}" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                        📞 {{ $usta->telefon }}
                    </a>
                </div>
            @endif

            @if($usta->notlar)
                <div style="margin-top:12px; padding:10px; background:var(--bg-primary); border-radius:var(--radius-sm); text-align:left;">
                    <div class="text-muted fs-sm" style="margin-bottom:4px;">📝 Notlar</div>
                    <div style="font-size:0.875rem;">{{ $usta->notlar }}</div>
                </div>
            @endif
        </div>

        {{-- Ücret Bilgisi --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">💰 Ücret Bilgisi</div>
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
            <div class="card-title" style="margin-bottom:16px;">📊 Mali Özet</div>
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
                    <label>Kalan Borç</label>
                    <span class="{{ $toplamBorç > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $toplamBorç > 0 ? number_format($toplamBorç, 0, ',', '.') . '₺' : '✓ Ödenmiş' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Borç Durumu Banneri --}}
        @if($toplamBorç > 0)
        <div class="borç-banner kirmizi">
            <div>
                <div style="font-weight:700; color:var(--accent-danger); font-size:1rem;">⚠️ Ödenmemiş Bakiye</div>
                <div class="text-muted fs-sm">Toplam kalan hakedis borcu</div>
            </div>
            <div style="font-size:1.4rem; font-weight:800; color:var(--accent-danger);">
                {{ number_format($toplamBorç, 0, ',', '.') }}₺
            </div>
        </div>
        @else
        <div class="borç-banner yesil">
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

        {{-- Bu Ay Devam Kayıtları --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📅 Bu Ay Devam Kaydı</span>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="badge badge-primary">{{ $aylikKayitlar->count() }} kayıt</span>
                    @if($buAyOdeme)
                        @if($buAyOdeme->kapandi)
                            <span class="badge badge-success">✅ Ödendi</span>
                        @else
                            <span class="badge badge-warning">⏳ {{ number_format($buAyOdeme->kalan_bakiye, 0, ',', '.') }}₺ Kalan</span>
                        @endif
                    @elseif($aylikHakedis > 0)
                        <span class="badge badge-danger">⚠️ Ödenmedi</span>
                    @endif
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
                            <td colspan="3" class="text-right fw-semibold">Bu Ay Toplam:</td>
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
                    <div style="width:44px; height:44px; border-radius:var(--radius-sm);
                                background:{{ $odeme->kapandi ? 'rgba(34,197,94,0.12)' : 'rgba(245,158,11,0.12)' }};
                                display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0;">
                        {{ $odeme->kapandi ? '✅' : '⏳' }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $odeme->ay_ad }}</div>
                        <div class="text-muted fs-sm">
                            {{ $odeme->odeme_tarihi?->locale('tr')->isoFormat('D MMMM YYYY') ?? '—' }}
                            @if($odeme->odeme_yontemi)
                                · {{ ucfirst($odeme->odeme_yontemi) }}
                            @endif
                        </div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div class="text-muted fs-sm">Hakedis: {{ number_format($odeme->toplam_hakkedis, 0, ',', '.') }}₺</div>
                    <div class="text-success fw-semibold">Ödenen: {{ number_format($odeme->odenen_tutar, 0, ',', '.') }}₺</div>
                    @if($odeme->kalan_bakiye > 0)
                        <div class="text-danger fs-sm">Kalan: {{ number_format($odeme->kalan_bakiye, 0, ',', '.') }}₺</div>
                    @endif
                    @if($odeme->kapandi)
                        <span class="badge badge-success" style="margin-top:2px;">Kapalı</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center text-muted" style="padding:30px 0;">
                <div style="font-size:2rem; margin-bottom:8px;">💳</div>
                <div>Henüz ödeme kaydı yok</div>
                <a href="{{ route('aylik-hesap.index') }}" class="btn btn-success btn-sm" style="margin-top:12px;">Ödeme Yap →</a>
            </div>
            @endforelse
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
</script>
@endpush
