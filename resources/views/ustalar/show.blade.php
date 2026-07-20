@extends('layouts.app')

@section('title', $usta->ad_soyad . ' — Usta Profili')
@section('page-title', '👷 ' . $usta->ad_soyad)

@section('header-actions')
    <a href="{{ route('ustalar.edit', $usta) }}" class="btn btn-warning btn-sm">✏️ Düzenle</a>
    <a href="{{ route('ustalar.index') }}" class="btn btn-secondary btn-sm">← Geri</a>
@endsection

@push('styles')
<style>
.chart-container { position: relative; height: 200px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.info-item label { font-size:0.75rem; color:var(--text-muted); display:block; margin-bottom:2px; }
.info-item span { font-size:0.95rem; font-weight:500; }
</style>
@endpush

@section('content')

<div style="display:grid; grid-template-columns: 1fr 2fr; gap:20px; align-items:start;">

    {{-- Sol: Usta Kartı --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        <div class="card text-center">
            <div class="avatar-circle" style="width:72px; height:72px; font-size:1.6rem; margin:0 auto 12px;">
                {{ strtoupper(mb_substr($usta->ad, 0, 1)) }}{{ strtoupper(mb_substr($usta->soyad, 0, 1)) }}
            </div>
            <div style="font-size:1.2rem; font-weight:700;">{{ $usta->ad_soyad }}</div>
            <div class="text-muted fs-sm">{{ $usta->uzmanlik ?? 'Genel İşçi' }}</div>

            <div style="margin:12px 0;">
                @if($usta->durum === 'aktif')
                    <span class="badge badge-success">● Aktif</span>
                @else
                    <span class="badge badge-secondary">● Pasif</span>
                @endif
            </div>

            @if($usta->telefon)
                <a href="tel:{{ $usta->telefon }}" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                    📞 {{ $usta->telefon }}
                </a>
            @endif
        </div>

        <div class="card">
            <div class="card-title mb-3">💰 Ücret Bilgisi</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Günlük Ücret</label>
                    <span class="text-success">{{ number_format($usta->gunluk_ucret, 0, ',', '.') }}₺</span>
                </div>
                <div class="info-item">
                    <label>Yarım Gün</label>
                    <span class="text-warning">{{ number_format($usta->gunluk_ucret / 2, 0, ',', '.') }}₺</span>
                </div>
                <div class="info-item">
                    <label>Mesai (saat)</label>
                    <span class="text-primary">{{ number_format($usta->mesai_saatlik_ucret, 0, ',', '.') }}₺</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title mb-3">📊 Genel Özet</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Toplam Hakedis</label>
                    <span class="text-success">{{ number_format($toplamHakedis, 0, ',', '.') }}₺</span>
                </div>
                <div class="info-item">
                    <label>Bu Ay Hakedis</label>
                    <span class="text-primary">{{ number_format($aylikHakedis, 0, ',', '.') }}₺</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sağ: Devam Kayıtları + Grafik --}}
    <div style="display:flex; flex-direction:column; gap:16px;">

        <div class="card">
            <div class="card-header">
                <span class="card-title">📊 Son 6 Ay Hakedis</span>
            </div>
            <div class="chart-container">
                <canvas id="ustaHakedisChart"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">📅 Bu Ay Devam Kaydı</span>
                <span class="badge badge-primary">{{ $aylikKayitlar->count() }} gün</span>
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
