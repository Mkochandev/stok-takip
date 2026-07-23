@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Genel Bakış')

@push('styles')
<style>
.chart-container { position: relative; height: 280px; }
.top-usta-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}
.top-usta-item:last-child { border-bottom: none; }
.rank-badge {
    width: 26px; height: 26px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; font-weight: 800;
    color: var(--text-secondary);
    flex-shrink: 0;
}
.rank-badge.rank-1 { background: var(--accent-light); color: var(--accent-primary); }
.rank-badge.rank-2 { background: #eff6ff; color: #3b82f6; }
.rank-badge.rank-3 { background: #f5f3ff; color: #8b5cf6; }
.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
}
@media (max-width: 992px) {
    .dashboard-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

{{-- Stat Kartlar (PlanIQ Pure White Light Style) --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon mint">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $aktifUstaSayisi }}</div>
            <div class="stat-label">Aktif Usta</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon dark">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $aktifIsSayisi }}</div>
            <div class="stat-label">Devam Eden İş</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $buAyCalismaGunu }}</div>
            <div class="stat-label">Bu Ay Devam Kaydı</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon mint">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--accent-primary);">{{ number_format($buAyGelir, 0, ',', '.') }}₺</div>
            <div class="stat-label">Bu Ay Gelir</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#fef2f2; color:#ef4444; border-color:#fee2e2;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color:var(--accent-danger);">{{ number_format($buAyGider, 0, ',', '.') }}₺</div>
            <div class="stat-label">Bu Ay Gider</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color: {{ $buAyNet >= 0 ? 'var(--accent-primary)' : 'var(--accent-danger)' }};">
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
            <span class="card-title">Son 6 Ay Gelir / Gider</span>
        </div>
        <div class="chart-container">
            <canvas id="gelirGiderChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Bu Ay En Çok Çalışan</span>
        </div>
        @forelse($enCokCalisan as $i => $kayit)
            <a href="{{ route('ustalar.show', $kayit->usta_id) }}" class="top-usta-item" style="color:inherit; text-decoration:none;">
                <div class="rank-badge rank-{{ $i+1 }}">{{ $i+1 }}</div>
                <div class="avatar" style="width:34px; height:34px; font-size:0.8rem; font-weight:800;">
                    {{ strtoupper(mb_substr($kayit->usta->ad ?? '?', 0, 1)) }}{{ strtoupper(mb_substr($kayit->usta->soyad ?? '', 0, 1)) }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:0.875rem; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--text-primary);">
                        {{ $kayit->usta->ad_soyad ?? '-' }}
                    </div>
                    <div style="font-size:0.75rem; color:var(--text-muted); font-weight:500;">{{ $kayit->gun_sayisi }} gün</div>
                </div>
                <div style="font-size:0.85rem; font-weight:700; color:var(--accent-primary);">
                    {{ number_format($kayit->toplam_ucret, 0, ',', '.') }}₺
                </div>
            </a>
        @empty
            <div style="text-align:center; color:var(--text-muted); padding:30px 0; font-size:0.85rem;">
                Bu ay henüz devam kaydı bulunmuyor
            </div>
        @endforelse
    </div>
</div>

{{-- Son Devam Kayıtları --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Son Devam Kayıtları</span>
        <a href="{{ route('devam.index') }}" class="btn btn-secondary btn-sm">Tümünü Gör →</a>
    </div>
    <div class="table-responsive">
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
                        <a href="{{ route('ustalar.show', $kayit->usta_id) }}" style="display:flex; align-items:center; gap:10px; color:inherit; text-decoration:none;">
                            <div class="avatar" style="width:30px; height:30px; font-size:0.75rem; font-weight:700;">
                                {{ strtoupper(mb_substr($kayit->usta->ad ?? '?', 0, 1)) }}{{ strtoupper(mb_substr($kayit->usta->soyad ?? '', 0, 1)) }}
                            </div>
                            <span style="color:var(--text-primary); font-weight:700;">{{ $kayit->usta->ad_soyad ?? '-' }}</span>
                        </a>
                    </td>
                    <td style="color:var(--text-secondary); font-weight:500;">{{ $kayit->tarih->locale('tr')->isoFormat('D MMMM') }}</td>
                    <td>
                        @if($kayit->calisma_tipi === 'tam')
                            <span class="badge badge-mint">Tam Gün</span>
                        @elseif($kayit->calisma_tipi === 'yarim')
                            <span class="badge badge-warning">Yarım Gün</span>
                        @else
                            <span class="badge badge-dark">Mesai ({{ $kayit->mesai_saati }}s)</span>
                        @endif
                    </td>
                    <td style="color:var(--text-muted); font-size:0.85rem;">{{ $kayit->ilgiliIs->is_adi ?? '—' }}</td>
                    <td style="color:var(--accent-primary); font-weight:800;">{{ number_format($kayit->hesaplanan_ucret, 0, ',', '.') }}₺</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:30px; color:var(--text-muted);">
                        Henüz devam kaydı girilmemiş. <a href="{{ route('devam.index') }}" style="color:var(--accent-primary); font-weight:700;">Ekle →</a>
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
                backgroundColor: '#10b981',
                borderRadius: 6,
            },
            {
                label: 'Gider',
                data: giderData,
                backgroundColor: '#ef4444',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: '#475569', font: { family: 'Plus Jakarta Sans', size: 12, weight: 600 } } },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': ' + ctx.raw.toLocaleString('tr-TR') + '₺'
                }
            }
        },
        scales: {
            x: { ticks: { color: '#94a3b8' }, grid: { color: '#f1f5f9' } },
            y: {
                ticks: { color: '#94a3b8', callback: v => v.toLocaleString('tr-TR') + '₺' },
                grid: { color: '#f1f5f9' },
                border: { color: 'transparent' }
            }
        }
    }
});
</script>
@endpush
