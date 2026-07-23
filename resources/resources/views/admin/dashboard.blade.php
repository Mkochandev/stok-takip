@extends('layouts.app')

@section('title', 'Sistem Admin Paneli')
@section('page-title', '👑 Sistem Admin Paneli')

@section('header-actions')
    <form method="POST" action="{{ route('admin.backups.create') }}" style="display:inline-block; margin:0;">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
            💾 Anlık Sistem Yedeği Al
        </button>
    </form>
    <button type="button" onclick="openApiModal()" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
        ⚙️ Altyapı API Ayarları
    </button>
@endsection

@push('styles')
<style>
.admin-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-success-glow {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
    border: 1px solid rgba(16, 185, 129, 0.3);
}
.badge-warning-glow {
    background: rgba(245, 158, 11, 0.15);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.3);
}
.badge-danger-glow {
    background: rgba(239, 68, 68, 0.15);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.3);
}
.badge-purple-glow {
    background: rgba(99, 102, 241, 0.15);
    color: #a5b4fc;
    border: 1px solid rgba(99, 102, 241, 0.3);
}
.storage-bar-bg {
    width: 100%;
    height: 8px;
    background: rgba(255,255,255,0.06);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 4px;
}
.storage-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #6366f1, #3b82f6);
    border-radius: 4px;
}
.infra-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
    gap: 20px;
    margin-top: 24px;
}
.infra-card {
    background: var(--bg-card, #1e293b);
    border: 1px solid var(--border-color, rgba(255,255,255,0.1));
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.infra-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.1));
}
.infra-title {
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-primary, #f8fafc);
}
.metric-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px dashed rgba(255,255,255,0.06);
    font-size: 0.875rem;
}
.metric-row:last-child {
    border-bottom: none;
}
.metric-label {
    color: var(--text-muted, #94a3b8);
}
.metric-value {
    font-weight: 600;
    color: var(--text-primary, #f1f5f9);
}

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.75);
    z-index: 99999;
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
}
.modal-content {
    background: #1e293b;
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 16px;
    width: 90%;
    max-width: 580px;
    padding: 24px;
    color: #f8fafc;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}
</style>
@endpush

@section('content')

{{-- 1. SISTEM İSTATİSTİK KARTLARI --}}
<div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Toplam Kayıtlı Üye</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                Bu Hafta: <strong>+{{ $newUsersThisWeekCount }}</strong> | Bu Ay: <strong>+{{ $newUsersThisMonthCount }}</strong>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div>
            <div class="stat-value" style="color: #34d399;">{{ $activeUsersCount }}</div>
            <div class="stat-label">Aktif Kullanıcı</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                Süresi Devam Edenler & Admin
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red">⚠️</div>
        <div>
            <div class="stat-value" style="color: #f87171;">{{ $expiredUsersCount }}</div>
            <div class="stat-label">Süresi Dolduran Üye</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                Yenileme Bekleyenler
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">💾</div>
        <div>
            <div class="stat-value">{{ $formattedSystemBytes }}</div>
            <div class="stat-label">Toplam Veritabanı Kullanımı</div>
            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">
                Tüm Üye Verileri Toplamı
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon cyan">📅</div>
        <div>
            <div class="stat-value" style="font-size:1.1rem; line-height:1.4; color:#60a5fa;">{{ $lastBackupTime }}</div>
            <div class="stat-label">Son Günlük Otomatik Yedek</div>
        </div>
    </div>
</div>

{{-- 2. KULLANICI DEPOLAMA VE VERİ KULLANIMI TABLOSU --}}
<div class="card fade-in" style="margin-bottom: 24px;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <span class="card-title">💾 Kullanıcı Veri Depolama Kullanımı</span>
            <p style="color: var(--text-muted); font-size:0.85rem; margin:4px 0 0 0;">
                Hangi kullanıcının veritabanında ne kadar yer kapladığını ve toplam verilerin oranını inceleyin.
            </p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Üye Yönetimine Git →</a>
    </div>

    <div class="table-responsive">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align:left;">
                    <th style="padding: 12px;">Üye Adı / E-Posta</th>
                    <th style="padding: 12px;">Rol & Durum</th>
                    <th style="padding: 12px;">Toplam Veri Satırı</th>
                    <th style="padding: 12px;">Veri Boyutu</th>
                    <th style="padding: 12px; width: 220px;">Sistemdeki Payı (%)</th>
                    <th style="padding: 12px; text-align:right;">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usersStorage as $user)
                    <tr style="border-bottom: 1px solid var(--border-color); vertical-align: middle;">
                        <td style="padding: 14px 12px;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">
                                {{ $user->name }}
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                {{ $user->email }}
                            </div>
                        </td>

                        <td style="padding: 12px;">
                            @if($user->isAdmin())
                                <span class="admin-badge badge-purple-glow">👑 Ana Admin</span>
                            @elseif($user->isExpired())
                                <span class="admin-badge badge-danger-glow">✕ Süresi Doldu</span>
                            @else
                                <span class="admin-badge badge-success-glow">● Aktif Üye</span>
                            @endif
                        </td>

                        <td style="padding: 12px;">
                            <div style="font-weight:600; font-size:0.9rem;">
                                {{ number_format($user->total_records, 0, ',', '.') }} Kayıt
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">
                                👷 {{ $user->ustalar_count }} | 🏢 {{ $user->isler_count }} | 📅 {{ $user->devam_kayitlari_count }}
                            </div>
                        </td>

                        <td style="padding: 12px;">
                            <span class="admin-badge badge-warning-glow" style="font-size:0.85rem;">
                                📦 {{ $user->formatted_size }}
                            </span>
                        </td>

                        <td style="padding: 12px;">
                            <div style="display:flex; justify-content:space-between; font-size:0.8rem; font-weight:600; color:var(--text-muted);">
                                <span>Kullanım</span>
                                <span style="color:var(--text-primary);">%{{ $user->storage_percentage }}</span>
                            </div>
                            <div class="storage-bar-bg">
                                <div class="storage-bar-fill" style="width: {{ max($user->storage_percentage, 2) }}%;"></div>
                            </div>
                        </td>

                        <td style="padding: 12px; text-align:right;">
                            <a href="{{ route('admin.users.backup', $user->id) }}" 
                               class="btn btn-sm btn-secondary" 
                               style="font-size:0.8rem; padding: 4px 8px;"
                               title="Üyenin Özel Yedeğini İndir (JSON)">
                                💾 Yedeğini Al
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
                <span>📂 Günlük Otomatik Sistem Yedekleri</span>
            </div>
            <form method="POST" action="{{ route('admin.backups.create') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                    ➕ Şimdi Sistem Yedeği Al
                </button>
            </form>
        </div>

        <p style="color: var(--text-muted); font-size:0.85rem; margin-top:-8px; margin-bottom:16px;">
            Her gece otomatik alınan ve anlık oluşturulan tüm veritabanı yedeği dosyalarınız (JSON formatında) burada listelenir.
        </p>

        <div class="table-responsive">
            <table class="table" style="width: 100%;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align:left;">
                        <th style="padding: 10px;">Yedek Dosya Adı</th>
                        <th style="padding: 10px;">Dosya Boyutu</th>
                        <th style="padding: 10px;">Oluşturulma Tarihi</th>
                        <th style="padding: 10px; text-align:right;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backupFiles as $backup)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 10px; font-weight:600; font-size:0.9rem; color:var(--text-primary);">
                                📄 {{ $backup['filename'] }}
                            </td>
                            <td style="padding: 10px;">
                                <span class="badge" style="background:rgba(59, 130, 246, 0.15); color:#60a5fa; padding:3px 8px; border-radius:4px; font-size:0.8rem;">
                                    {{ $backup['size'] }}
                                </span>
                            </td>
                            <td style="padding: 10px; font-size:0.85rem; color:var(--text-muted);">
                                🕒 {{ $backup['formatted_date'] }}
                            </td>
                            <td style="padding: 10px; text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <a href="{{ route('admin.backups.download', $backup['filename']) }}" class="btn btn-sm btn-secondary" style="padding:4px 8px; font-size:0.8rem;">
                                        ⬇️ İndir
                                    </a>
                                    <form method="POST" action="{{ route('admin.backups.delete', $backup['filename']) }}" style="margin:0;" onsubmit="return confirm('Bu yedek dosyasını silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding:4px 8px; font-size:0.8rem;">
                                            🗑️ Sil
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
                <span>🌐 Cloudflare (Domain & DNS)</span>
            </div>
            <span class="admin-badge badge-success-glow">
                ● {{ $cloudflareData['status'] }}
            </span>
        </div>

        <div class="metric-row">
            <span class="metric-label">Domain Adı</span>
            <span class="metric-value">{{ $cloudflareData['domain'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">SSL / TLS Durumu</span>
            <span class="metric-value" style="color: #34d399;">🔒 {{ $cloudflareData['ssl_status'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">DNS & Proxy Koruma</span>
            <span class="metric-value">🛡️ {{ $cloudflareData['dns_status'] }}</span>
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

        <div style="margin-top:16px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:0.75rem; color:var(--text-muted);">Son Kontrol: {{ $cloudflareData['last_check'] }}</span>
            <button onclick="openApiModal()" class="btn btn-secondary btn-sm" style="font-size:0.75rem; padding:3px 8px;">⚙️ API Yapılandır</button>
        </div>
    </div>

    {{-- DIGITALOCEAN SUNUCU (DROPLET) METRİKLERİ KARTI --}}
    <div class="infra-card">
        <div class="infra-header">
            <div class="infra-title">
                <span>🌊 DigitalOcean (Sunucu & Cloud)</span>
            </div>
            <span class="admin-badge badge-success-glow">
                ● {{ strtoupper($digitalOceanData['status']) }}
            </span>
        </div>

        <div class="metric-row">
            <span class="metric-label">Droplet Sunucu Adı</span>
            <span class="metric-value">{{ $digitalOceanData['droplet_name'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">Sunucu IP Adresi</span>
            <span class="metric-value" style="color: #60a5fa;">🖥️ {{ $digitalOceanData['ip_address'] }}</span>
        </div>
        <div class="metric-row">
            <span class="metric-label">Lokasyon / Bölge</span>
            <span class="metric-value">📍 {{ $digitalOceanData['region'] }}</span>
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
            <span class="metric-value" style="color: #34d399;">💾 {{ $digitalOceanData['daily_backups'] }}</span>
        </div>

        <div style="margin-top:16px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:0.75rem; color:var(--text-muted);">Çalışma Süresi: {{ $digitalOceanData['uptime'] }}</span>
            <button onclick="openApiModal()" class="btn btn-secondary btn-sm" style="font-size:0.75rem; padding:3px 8px;">⚙️ API Yapılandır</button>
        </div>
    </div>

</div>

{{-- 4. ALTYAPI API AYARLARI MODALI --}}
<div class="modal-overlay" id="apiModal">
    <div class="modal-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0; font-size:1.2rem; font-weight:700;">⚙️ Cloudflare & DigitalOcean API Ayarları</h3>
            <button type="button" onclick="closeApiModal()" style="background:none; border:none; color:#94a3b8; font-size:1.4rem; cursor:pointer;">✕</button>
        </div>
        
        <form method="POST" action="{{ route('admin.settings.infrastructure') }}">
            @csrf
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:4px; color:#cbd5e1;">Cloudflare Domain Adı</label>
                <input type="text" name="cloudflare_domain" value="{{ config('services.cloudflare.domain', 'gaziustam.com') }}" class="form-control" placeholder="gaziustam.com" style="width:100%; padding:8px 12px;">
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:4px; color:#cbd5e1;">Cloudflare Zone ID</label>
                <input type="text" name="cloudflare_zone_id" value="{{ config('services.cloudflare.zone_id') }}" class="form-control" placeholder="zone_id_string" style="width:100%; padding:8px 12px;">
            </div>

            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:4px; color:#cbd5e1;">Cloudflare API Token</label>
                <input type="password" name="cloudflare_api_token" value="{{ config('services.cloudflare.token') }}" class="form-control" placeholder="Cloudflare API Token" style="width:100%; padding:8px 12px;">
            </div>

            <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin:16px 0;">

            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:4px; color:#cbd5e1;">DigitalOcean API Token</label>
                <input type="password" name="digitalocean_token" value="{{ config('services.digitalocean.token') }}" class="form-control" placeholder="DigitalOcean API Personal Token" style="width:100%; padding:8px 12px;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:4px; color:#cbd5e1;">DigitalOcean Droplet ID</label>
                <input type="text" name="digitalocean_droplet" value="{{ config('services.digitalocean.droplet_id') }}" class="form-control" placeholder="Droplet ID (ör: 34589211)" style="width:100%; padding:8px 12px;">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" onclick="closeApiModal()" class="btn btn-secondary btn-sm">İptal</button>
                <button type="submit" class="btn btn-primary btn-sm">💾 Ayarları Kaydet</button>
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
