@extends('layouts.app')

@section('title', 'Sistem Admin Paneli')
@section('page-title', 'Sistem Admin Paneli')

@section('header-actions')
    <form method="POST" action="{{ route('admin.backups.create') }}" style="display:inline-block; margin:0;">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:4px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            <span>Anlık Sistem Yedeği Al</span>
        </button>
    </form>
    <button type="button" onclick="openApiModal()" class="btn btn-secondary btn-sm">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:4px;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Altyapı API Ayarları</span>
    </button>
@endsection

@push('styles')
<style>
.storage-bar-bg {
    width: 100%;
    height: 8px;
    background: #f1f5f9;
    border-radius: var(--radius-full);
    overflow: hidden;
    margin-top: 4px;
}
.storage-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--accent-primary), #34d399);
    border-radius: var(--radius-full);
}
.infra-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
    gap: 20px;
    margin-top: 24px;
}
.infra-card {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 24px;
    box-shadow: var(--shadow-sm);
}
.infra-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
}
.infra-title {
    font-size: 1.05rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-primary);
}
.metric-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px dashed var(--border-color);
    font-size: 0.875rem;
}
.metric-row:last-child {
    border-bottom: none;
}
.metric-label {
    color: var(--text-secondary);
    font-weight: 600;
}
.metric-value {
    font-weight: 700;
    color: var(--text-primary);
}

/* Modal Overlay */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    z-index: 99999;
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
}
.modal-content {
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    width: 90%;
    max-width: 580px;
    padding: 28px;
    color: var(--text-primary);
    box-shadow: var(--shadow-lg);
}
</style>
@endpush

@section('content')

{{-- 1. SISTEM İSTATİSTİK KARTLARI --}}
<div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon dark">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Toplam Kayıtlı Üye</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                Bu Hafta: <strong>+{{ $newUsersThisWeekCount }}</strong> | Bu Ay: <strong>+{{ $newUsersThisMonthCount }}</strong>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon mint">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color: var(--accent-primary);">{{ $activeUsersCount }}</div>
            <div class="stat-label">Aktif Kullanıcı</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                Süresi Devam Edenler & Admin
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:#fef2f2; color:#ef4444; border-color:#fee2e2;">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div>
            <div class="stat-value" style="color: var(--accent-danger);">{{ $expiredUsersCount }}</div>
            <div class="stat-label">Süresi Dolduran Üye</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                Yenileme Bekleyenler
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $formattedSystemBytes }}</div>
            <div class="stat-label">Toplam Veritabanı Kullanımı</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                Tüm Üye Verileri Toplamı
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">
            <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
            <div class="stat-value" style="font-size:1.05rem; line-height:1.4; color:var(--accent-indigo);">{{ $lastBackupTime }}</div>
            <div class="stat-label">Son Günlük Otomatik Yedek</div>
        </div>
    </div>
</div>

{{-- 2. KULLANICI DEPOLAMA VE VERİ KULLANIMI TABLOSU --}}
<div class="card fade-in" style="margin-bottom: 24px;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <span class="card-title">Kullanıcı Veri Depolama Kullanımı</span>
            <p style="color: var(--text-secondary); font-size:0.85rem; margin:4px 0 0 0; font-weight:500;">
                Hangi kullanıcının veritabanında ne kadar yer kapladığını ve toplam verilerin oranını inceleyin.
            </p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Üye Yönetimine Git →</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Üye Adı / E-Posta</th>
                    <th>Rol & Durum</th>
                    <th>Toplam Veri Satırı</th>
                    <th>Veri Boyutu</th>
                    <th style="width: 200px;">Sistemdeki Payı (%)</th>
                    <th style="text-align:right;">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usersStorage as $user)
                    <tr>
                        <td>
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">
                                {{ $user->name }}
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight:500;">
                                {{ $user->email }}
                            </div>
                        </td>

                        <td>
                            @if($user->isAdmin())
                                <span class="badge badge-dark">👑 Ana Admin</span>
                            @elseif($user->isExpired())
                                <span class="badge badge-danger">✕ Süresi Doldu</span>
                            @else
                                <span class="badge badge-mint">● Aktif Üye</span>
                            @endif
                        </td>

                        <td>
                            <div style="font-weight:700; font-size:0.9rem;">
                                {{ number_format($user->total_records, 0, ',', '.') }} Kayıt
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted); font-weight:500;">
                                Ustalar: {{ $user->ustalar_count }} | İşler: {{ $user->isler_count }} | Devam: {{ $user->devam_kayitlari_count }}
                            </div>
                        </td>

                        <td>
                            <span class="badge badge-gray">
                                {{ $user->formatted_size }}
                            </span>
                        </td>

                        <td>
                            <div style="display:flex; justify-content:space-between; font-size:0.8rem; font-weight:700; color:var(--text-secondary);">
                                <span>Kullanım</span>
                                <span>%{{ $user->storage_percentage }}</span>
                            </div>
                            <div class="storage-bar-bg">
                                <div class="storage-bar-fill" style="width: {{ max($user->storage_percentage, 2) }}%;"></div>
                            </div>
                        </td>

                        <td style="text-align:right;">
                            <a href="{{ route('admin.users.backup', $user->id) }}" class="btn btn-sm btn-secondary">
                                Yedeğini Al
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 30px; color: var(--text-muted);">
                            Kayıtlı üye verisi bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 3. GÜNLÜK OTOMATİK SİSTEM YEDEKLERİ İLE ALTYAPI PANELERİ --}}
<div class="infra-grid">

    {{-- GÜNLÜK SİSTEM YEDEKLERİ KARTI --}}
    <div class="infra-card" style="grid-column: span 1 / -1;">
        <div class="infra-header">
            <div class="infra-title">
                <span>Günlük Otomatik Sistem Yedekleri</span>
            </div>
            <form method="POST" action="{{ route('admin.backups.create') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    Anlık Sistem Yedeği Al
                </button>
            </form>
        </div>

        <p style="color: var(--text-secondary); font-size:0.85rem; margin-top:-6px; margin-bottom:16px; font-weight:500;">
            Her gece otomatik alınan ve anlık oluşturulan tüm veritabanı yedeği dosyalarınız (JSON formatında) burada listelenir.
        </p>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Yedek Dosya Adı</th>
                        <th>Dosya Boyutu</th>
                        <th>Oluşturulma Tarihi</th>
                        <th style="text-align:right;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backupFiles as $backup)
                        <tr>
                            <td style="font-weight:700; font-size:0.9rem; color:var(--text-primary);">
                                {{ $backup['filename'] }}
                            </td>
                            <td>
                                <span class="badge badge-mint">
                                    {{ $backup['size'] }}
                                </span>
                            </td>
                            <td style="font-size:0.85rem; color:var(--text-secondary); font-weight:500;">
                                {{ $backup['formatted_date'] }}
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <a href="{{ route('admin.backups.download', $backup['filename']) }}" class="btn btn-sm btn-secondary">
                                        İndir
                                    </a>
                                    <form method="POST" action="{{ route('admin.backups.delete', $backup['filename']) }}" style="margin:0;" onsubmit="return confirm('Bu yedek dosyasını silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Sil
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 24px; color:var(--text-muted);">
                                Henüz yedek dosyası oluşturulmamış. Yukarıdaki butona tıklayarak hemen yedek alabilirsiniz.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- CLOUDFLARE DOMAIN & METRİKLERİ KARTI --}}
    <div class="infra-card">
        <div class="infra-header">
            <div class="infra-title">
                <span>Cloudflare (Domain & DNS)</span>
            </div>
            <span class="badge badge-mint">
                ● {{ $cloudflareData['status'] }}
            </span>
        </div>

        <div class="metric-row">
            <span class="metric-label">Domain Adı</span>
            <span class="metric-value">{{ $cloudflareData['domain'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">SSL / TLS Durumu</span>
            <span class="metric-value" style="color: var(--accent-primary);">{{ $cloudflareData['ssl_status'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">DNS & Proxy Koruma</span>
            <span class="metric-value">{{ $cloudflareData['dns_status'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">Güvenlik Seviyesi</span>
            <span class="metric-value">{{ $cloudflareData['security_level'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">24 Saatteki İstekler</span>
            <span class="metric-value">{{ $cloudflareData['daily_requests'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">Bant Genişliği</span>
            <span class="metric-value">{{ $cloudflareData['bandwidth'] }}</span>
        </div>

        <div style="margin-top:16px; padding-top:12px; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:0.75rem; color:var(--text-muted); font-weight:500;">Son Kontrol: {{ $cloudflareData['last_check'] }}</span>
            <button onclick="openApiModal()" class="btn btn-secondary btn-sm">API Yapılandır</button>
        </div>
    </div>

    {{-- DIGITALOCEAN SUNUCU (DROPLET) METRİKLERİ KARTI --}}
    <div class="infra-card">
        <div class="infra-header">
            <div class="infra-title">
                <span>DigitalOcean (Sunucu & Cloud)</span>
            </div>
            <span class="badge badge-mint">
                ● {{ strtoupper($digitalOceanData['status']) }}
            </span>
        </div>

        <div class="metric-row">
            <span class="metric-label">Droplet Sunucu Adı</span>
            <span class="metric-value">{{ $digitalOceanData['droplet_name'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">Sunucu IP Adresi</span>
            <span class="metric-value" style="color: var(--accent-indigo);">{{ $digitalOceanData['ip_address'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">Lokasyon / Bölge</span>
            <span class="metric-value">{{ $digitalOceanData['region'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">Donanım Özellikleri</span>
            <span class="metric-value">{{ $digitalOceanData['specs'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">Disk Doluluk Oranı</span>
            <span class="metric-value">{{ $digitalOceanData['disk_usage'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">Sunucu Günlük Yedeği</span>
            <span class="metric-value" style="color: var(--accent-primary);">{{ $digitalOceanData['daily_backups'] }}</span>
        </div>

        <div style="margin-top:16px; padding-top:12px; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:0.75rem; color:var(--text-muted); font-weight:500;">Çalışma Süresi: {{ $digitalOceanData['uptime'] }}</span>
            <button onclick="openApiModal()" class="btn btn-secondary btn-sm">API Yapılandır</button>
        </div>
    </div>

</div>

{{-- 4. ALTYAPI API AYARLARI MODALI --}}
<div class="modal-overlay" id="apiModal">
    <div class="modal-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="margin:0; font-size:1.15rem; font-weight:800;">Cloudflare & DigitalOcean API Ayarları</h3>
            <button type="button" onclick="closeApiModal()" style="background:none; border:none; color:var(--text-muted); font-size:1.4rem; cursor:pointer;">✕</button>
        </div>
        
        <form method="POST" action="{{ route('admin.settings.infrastructure') }}">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:var(--text-primary);">Cloudflare Domain Adı</label>
                <input type="text" name="cloudflare_domain" value="{{ config('services.cloudflare.domain', 'gaziustam.com') }}" class="form-control" placeholder="gaziustam.com">
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:var(--text-primary);">Cloudflare Zone ID</label>
                <input type="text" name="cloudflare_zone_id" value="{{ config('services.cloudflare.zone_id') }}" class="form-control" placeholder="zone_id_string">
            </div>

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:var(--text-primary);">Cloudflare API Token</label>
                <input type="password" name="cloudflare_api_token" value="{{ config('services.cloudflare.token') }}" class="form-control" placeholder="Cloudflare API Token">
            </div>

            <hr style="border:0; border-top:1px solid var(--border-color); margin:18px 0;">

            <div style="margin-bottom:14px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:var(--text-primary);">DigitalOcean API Token</label>
                <input type="password" name="digitalocean_token" value="{{ config('services.digitalocean.token') }}" class="form-control" placeholder="DigitalOcean Personal Access Token">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:0.85rem; font-weight:700; margin-bottom:6px; color:var(--text-primary);">DigitalOcean Droplet ID</label>
                <input type="text" name="digitalocean_droplet" value="{{ config('services.digitalocean.droplet_id') }}" class="form-control" placeholder="Droplet ID (ör: 34589211)">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeApiModal()" class="btn btn-secondary btn-sm">İptal</button>
                <button type="submit" class="btn btn-primary btn-sm">Ayarları Kaydet</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openApiModal() {
    document.getElementById('apiModal').style.display = 'flex';
}
function closeApiModal() {
    document.getElementById('apiModal').style.display = 'none';
}
</script>
@endpush
