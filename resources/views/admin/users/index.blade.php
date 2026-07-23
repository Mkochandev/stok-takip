@extends('layouts.app')

@section('title', 'Kullanıcı & Üyelik Yönetimi')
@section('page-title', 'Üye & Abonelik Yönetimi')

@section('header-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
        <span>➕</span> Yeni Üye Ekle
    </a>
@endsection

@section('content')
<div class="card fade-in" style="margin-bottom: 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight:700; margin:0 0 4px 0;">👑 Üye Hesapları ve Geçerlilik Süreleri</h2>
            <p style="color: var(--text-muted); font-size:0.875rem; margin:0;">
                Sistemdeki tüm müşterilerinizin/üyelerinizin hesaplarını yönetebilir, sürelerini uzatabilir ve kayıtlarını bilgisayarınıza yedekleyebilirsiniz.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; gap:8px; min-width:260px;">
            <input type="text" name="q" value="{{ $query }}" class="form-control" placeholder="Üye adı veya e-posta ara..." style="padding: 8px 12px; font-size: 0.9rem;">
            <button type="submit" class="btn btn-secondary btn-sm">🔍 Ara</button>
            @if($query)
                <a href="{{ route('admin.users.index') }}" class="btn btn-danger btn-sm" title="Temizle">✕</a>
            @endif
        </form>
    </div>
</div>

<div class="card fade-in">
    <div class="table-responsive">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align:left;">
                    <th style="padding: 12px;">Üye Adı / E-Posta</th>
                    <th style="padding: 12px;">Rol</th>
                    <th style="padding: 12px;">Geçerlilik Tarihi</th>
                    <th style="padding: 12px;">Durum</th>
                    <th style="padding: 12px;">Veri Sayıları</th>
                    <th style="padding: 12px; text-align:center;">Hızlı Süre Uzat</th>
                    <th style="padding: 12px; text-align:right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom: 1px solid var(--border-color); vertical-align: middle;">
                        {{-- Üye Adı / E-Posta --}}
                        <td style="padding: 14px 12px;">
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">
                                {{ $user->name }}
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                {{ $user->email }}
                            </div>
                        </td>

                        {{-- Rol --}}
                        <td style="padding: 12px;">
                            @if($user->isAdmin())
                                <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3); padding: 4px 8px; border-radius: 6px; font-weight:600;">
                                    👑 Ana Admin
                                </span>
                            @else
                                <span class="badge" style="background: rgba(148, 163, 184, 0.15); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.2); padding: 4px 8px; border-radius: 6px; font-weight:500;">
                                    👷 Müşteri (Usta)
                                </span>
                            @endif
                        </td>

                        {{-- Geçerlilik Tarihi --}}
                        <td style="padding: 12px;">
                            @if($user->isAdmin() || is_null($user->expires_at))
                                <span style="color: #34d399; font-weight:600; font-size:0.85rem;">∞ Süresiz Erişim</span>
                            @else
                                <div style="font-weight:600; font-size:0.85rem;">
                                    {{ $user->expires_at->locale('tr')->isoFormat('D MMMM YYYY') }}
                                </div>
                                <div style="font-size:0.75rem; color: var(--text-muted);">
                                    {{ $user->expires_at->format('H:i') }}
                                </div>
                            @endif
                        </td>

                        {{-- Durum --}}
                        <td style="padding: 12px;">
                            @if($user->isAdmin() || is_null($user->expires_at))
                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399; padding: 4px 8px; border-radius: 6px; font-size:0.8rem; font-weight:600;">
                                    ● Aktif
                                </span>
                            @elseif($user->isExpired())
                                <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #f87171; padding: 4px 8px; border-radius: 6px; font-size:0.8rem; font-weight:600;">
                                    ✕ Süresi Doldu
                                </span>
                            @else
                                <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; padding: 4px 8px; border-radius: 6px; font-size:0.8rem; font-weight:600;">
                                    ● {{ $user->remainingDays() }} Gün Kaldı
                                </span>
                            @endif
                        </td>

                        {{-- Veri Sayıları --}}
                        <td style="padding: 12px; font-size: 0.8rem; color: var(--text-muted);">
                            <span title="Ustalar">👷 {{ $user->ustalar_count }}</span> | 
                            <span title="İşler">🏢 {{ $user->isler_count }}</span> | 
                            <span title="Devam">📅 {{ $user->devam_kayitlari_count }}</span>
                        </td>

                        {{-- Hızlı Süre Uzat --}}
                        <td style="padding: 12px; text-align:center;">
                            @if(!$user->isAdmin())
                                <div style="display:inline-flex; gap:4px;">
                                    <form method="POST" action="{{ route('admin.users.extend', $user->id) }}" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="süre" value="1_ay">
                                        <button type="submit" class="btn btn-sm btn-secondary" title="+1 Ay Uzat" style="padding:3px 8px; font-size:0.75rem;">+1 Ay</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.extend', $user->id) }}" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="süre" value="6_ay">
                                        <button type="submit" class="btn btn-sm btn-secondary" title="+6 Ay Uzat" style="padding:3px 8px; font-size:0.75rem;">+6 Ay</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.extend', $user->id) }}" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="süre" value="1_yil">
                                        <button type="submit" class="btn btn-sm btn-secondary" title="+1 Yıl Uzat" style="padding:3px 8px; font-size:0.75rem;">+1 Yıl</button>
                                    </form>
                                </div>
                            @else
                                <span style="font-size:0.75rem; color:var(--text-muted);">-</span>
                            @endif
                        </td>

                        {{-- İşlemler --}}
                        <td style="padding: 12px; text-align:right;">
                            <div style="display:inline-flex; gap:6px; align-items:center;">
                                {{-- Yedekle / İndir --}}
                                <a href="{{ route('admin.users.backup', $user->id) }}" 
                                   class="btn btn-sm" 
                                   style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 8px; font-size: 0.8rem;"
                                   title="Üyenin Tüm Kayıtlarını Yedekle / JSON İndir">
                                    💾 Yedekle
                                </a>

                                {{-- Düzenle --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-secondary" 
                                   style="padding: 4px 8px; font-size: 0.8rem;"
                                   title="Üye Bilgilerini ve Süresini Düzenle">
                                    ✏️ Düzenle
                                </a>

                                {{-- Sil --}}
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" 
                                          style="margin:0;" 
                                          onsubmit="return confirm('Bu üye hesabını ve bu üyeye ait TÜM VERİLERİ silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 4px 8px; font-size: 0.8rem;" title="Hesabı Sil">
                                            🗑️
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 40px; color: var(--text-muted);">
                            Kayıtlı üye bulunamadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $users->links() }}
    </div>
</div>
@endsection
