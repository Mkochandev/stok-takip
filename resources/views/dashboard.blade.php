@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', '📊 Dashboard')

@push('styles')
<style>
.chart-container { position: relative; height: 280px; }
.top-usta-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
}
.top-usta-item:last-child { border-bottom: none; }
.rank-badge {
    width: 28px; height: 28px;
    background: var(--bg-primary);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; font-weight: 700;
    color: var(--text-muted);
    flex-shrink: 0;
}
.rank-badge.rank-1 { background: rgba(245,158,11,0.2); color: var(--accent-warning); }
.rank-badge.rank-2 { background: rgba(148,163,184,0.2); color: #94a3b8; }
.rank-badge.rank-3 { background: rgba(180,120,60,0.2); color: #c07850; }
</style>
@endpush

@section('content')

{{-- Stat Kartlar --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">👷</div>
        <div>
            <div class="stat-value">{{ $aktifUstaSayisi }}</div>
            <div class="stat-label">Aktif Usta</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">🏢</div>
        <div>
            <div class="stat-value">{{ $aktifIsSayisi }}</div>
            <div class="stat-label">Devam Eden İş</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan">📅</div>
        <div>
            <div class="stat-value">{{ $buAyCalismaGunu }}</div>
            <div class="stat-label">Bu Ay Devam Kaydı</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">💵</div>
        <div>
            <div class="stat-value">{{ number_format($buAyGelir, 0, ',', '.') }}₺</div>
            <div class="stat-label">Bu Ay Gelir</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">💸</div>
        <div>
            <div class="stat-value">{{ number_format($buAyGider, 0, ',', '.') }}₺</div>
            <div class="stat-label">Bu Ay Gider</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon {{ $buAyNet >= 0 ? 'green' : 'red' }}">📈</div>
        <div>
            <div class="stat-value {{ $buAyNet >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($buAyNet, 0, ',', '.') }}₺
            </div>
            <div class="stat-label">Bu Ay Net Kâr</div>
        </div>
    </div>
</div>

{{-- Grafik + Top Ustalar --}}
<div class="dashboard-grid">

    <div class="card">
        <div class="card-header">
            <span class="card-title">📊 Son 6 Ay Gelir / Gider</span>
        </div>
        <div class="chart-container">
            <canvas id="gelirGiderChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">🏆 Bu Ay En Çok Çalışan</span>
        </div>
        @forelse($enCokCalisan as $i => $kayit)
            <div class="top-usta-item">
                <div class="rank-badge rank-{{ $i+1 }}">{{ $i+1 }}</div>
                <div class="avatar-circle" style="width:32px;height:32px;font-size:0.75rem;">
                    {{ strtoupper(mb_substr($kayit->usta->ad ?? '?', 0, 1)) }}{{ strtoupper(mb_substr($kayit->usta->soyad ?? '', 0, 1)) }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:0.875rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $kayit->usta->ad_soyad ?? '-' }}
                    </div>
                    <div class="text-muted fs-sm">{{ $kayit->gun_sayisi }} gün</div>
                </div>
                <div class="text-success fw-semibold fs-sm">
                    {{ number_format($kayit->toplam_ucret, 0, ',', '.') }}₺
                </div>
            </div>
        @empty
            <div class="text-center text-muted" style="padding:30px 0;">
                Bu ay devam kaydı yok
            </div>
        @endforelse
    </div>
</div>

{{-- Son Devam Kayıtları --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">📋 Son Devam Kayıtları</span>
        <a href="{{ route('devam.index') }}" class="btn btn-secondary btn-sm">Tümünü Gör →</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Usta</th>
                    <th>Tarih</th>
                    <th>Çalışma Tipi</th>
                    <th>İş</th>
                    <th>Ücret</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sonKayitlar as $kayit)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-2">
                            <div class="avatar-circle">
                                {{ strtoupper(mb_substr($kayit->usta->ad ?? '?', 0, 1)) }}{{ strtoupper(mb_substr($kayit->usta->soyad ?? '', 0, 1)) }}
                            </div>
                            <span>{{ $kayit->usta->ad_soyad ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="text-muted">{{ $kayit->tarih->locale('tr')->isoFormat('D MMMM') }}</td>
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
                    <td colspan="5" class="text-center text-muted" style="padding:30px;">
                        Henüz devam kaydı yok.
                        <a href="{{ route('devam.index') }}">Ekle →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json(array_column($aylikGrafikVeri, 'ay'));
const gelirData = @json(array_column($aylikGrafikVeri, 'gelir'));
const giderData = @json(array_column($aylikGrafikVeri, 'gider'));

const ctx = document.getElementById('gelirGiderChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Gelir',
                data: gelirData,
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 6,
            },
            {
                label: 'Gider',
                data: giderData,
                backgroundColor: 'rgba(239,68,68,0.6)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: '#94a3b8', font: { family: 'Inter', size: 12 } } },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': ' + ctx.raw.toLocaleString('tr-TR') + '₺'
                }
            }
        },
        scales: {
            x: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.05)' } },
            y: {
                ticks: { color: '#64748b', callback: v => v.toLocaleString('tr-TR') + '₺' },
                grid: { color: 'rgba(255,255,255,0.05)' },
                border: { color: 'transparent' }
            }
        }
    }
});
</script>
@endpush
