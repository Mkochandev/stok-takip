@extends('layouts.app')

@section('title', 'Kullanıcı & Üyelik Yönetimi')
@section('page-title', 'Üye & Abonelik Yönetimi')

@section('header-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <svg class="svg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px; height:16px; margin-right:4px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Yeni Üye Ekle</span>
    </a>
@endsection

@section('content')
<div class="card" style="margin-bottom: 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div>
            <h2 style="font-size: 1.15rem; font-weight:800; margin:0 0 4px 0;">Üye Hesapları ve Geçerlilik Süreleri</h2>
            <p style="color: var(--text-secondary); font-size:0.85rem; margin:0; font-weight:500;">
                Sistemdeki tüm müşterilerinizin/üyelerinizin hesaplarını yönetebilir, sürelerini uzatabilir ve kayıtlarını yedekleyebilirsiniz.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; gap:8px; min-width:260px;">
            <input type="text" name="q" value="{{ $query }}" class="form-control" placeholder="Üye adı veya e-posta ara..." style="padding: 8px 12px; font-size: 0.9rem;">
            <button type="submit" class="btn btn-secondary btn-sm">Ara</button>
            @if($query)
                <a href="{{ route('admin.users.index') }}" class="btn btn-danger btn-sm" title="Temizle">✕</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Üye Adı / E-Posta</th>
                    <th>Rol</th>
                    <th>Geçerlilik Tarihi</th>
                    <th>Durum</th>
                    <th>Veri Sayıları</th>
                    <th style="text-align:center;">Hızlı Süre Uzat</th>
                    <th style="text-align:right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        {{-- Üye Adı / E-Posta --}}
                        <td>
                            <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">
                                {{ $user->name }}
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight:500;">
                                {{ $user->email }}
                            </div>
                        </td>

                        {{-- Rol --}}
                        <td>
                            @if($user->isAdmin())
                                <span class="badge badge-dark">
                                    👑 Ana Admin
                                </span>
                            @else
                                <span class="badge badge-gray">
                                    Müşteri (SaaS Üyesi)
                                </span>
                            @endif
                        </td>

                        {{-- Geçerlilik Tarihi --}}
                        <td>
                            @if($user->isAdmin() || is_null($user->expires_at))
                                <span style="color: var(--accent-primary); font-weight:700; font-size:0.85rem;">Süresiz Erişim</span>
                            @else
                                <div style="font-weight:700; font-size:0.85rem;">
                                    {{ $user->expires_at->locale('tr')->isoFormat('D MMMM YYYY') }}
                                </div>
                                <div style="font-size:0.75rem; color: var(--text-muted); font-weight:500;">
                                    {{ $user->expires_at->format('H:i') }}
                                </div>
                            @endif
                        </td>

                        {{-- Durum --}}
                        <td>
                            @if($user->isAdmin() || is_null($user->expires_at))
                                <span class="badge badge-mint">
                                    ● Aktif
                                </span>
                            @elseif($user->isExpired())
                                <span class="badge badge-danger">
                                    ✕ Süresi Doldu
                                </span>
                            @else
                                <span class="badge badge-mint">
                                    ● {{ $user->remainingDays() }} Gün Kaldı
                                </span>
                            @endif
                        </td>

                        {{-- Veri Sayıları --}}
                        <td style="font-size: 0.8rem; color: var(--text-secondary); font-weight:600;">
                            <span>Ustalar: {{ $user->ustalar_count }}</span> | 
                            <span>İşler: {{ $user->isler_count }}</span> | 
                            <span>Devam: {{ $user->devam_kayitlari_count }}</span>
                        </td>

                        {{-- Hızlı Süre Uzat --}}
                        <td style="text-align:center;">
                            @if(!$user->isAdmin())
                                <div style="display:inline-flex; gap:4px;">
                                    <form method="POST" action="{{ route('admin.users.extend', $user->id) }}" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="süre" value="1_ay">
                                        <button type="submit" class="btn btn-sm btn-secondary" style="padding:4px 8px; font-size:0.75rem;">+1 Ay</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.extend', $user->id) }}" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="süre" value="6_ay">
                                        <button type="submit" class="btn btn-sm btn-secondary" style="padding:4px 8px; font-size:0.75rem;">+6 Ay</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.extend', $user->id) }}" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="süre" value="1_yil">
                                        <button type="submit" class="btn btn-sm btn-secondary" style="padding:4px 8px; font-size:0.75rem;">+1 Yıl</button>
                                    </form>
                                </div>
                            @else
                                <span style="font-size:0.75rem; color:var(--text-muted);">-</span>
                            @endif
                        </td>

                        {{-- İşlemler --}}
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:6px; align-items:center;">
                                {{-- Yedekle / İndir --}}
                                <a href="{{ route('admin.users.backup', $user->id) }}" class="btn btn-sm btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;" title="Yedek İndir">
                                    Yedekle
                                </a>

                                {{-- Düzenle --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;" title="Düzenle">
                                    Düzenle
                                </a>

                                {{-- Sil --}}
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="margin:0;" onsubmit="return confirm('Bu üye hesabını ve tüm verilerini silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 4px 8px; font-size: 0.8rem;">
                                            Sil
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
