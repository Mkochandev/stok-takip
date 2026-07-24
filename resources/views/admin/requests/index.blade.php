@extends('layouts.app')

@section('title', 'Müşteri Talepleri')
@section('page-title', 'Tanıtım Sitesi Müşteri Talepleri')

@section('content')
<div style="padding-bottom: 24px;">

    {{-- Flash Alerts --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <span>✓ {{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <span>✕ {{ session('error') }}</span>
        </div>
    @endif

    {{-- İstatistik Kartları (PlanIQ Modern Light Theme) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        
        <div class="card" style="padding: 18px; display: flex; align-items: center; gap: 14px; margin: 0;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                📊
            </div>
            <div>
                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Toplam Talep</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: var(--text-primary); margin-top: 2px;">{{ $stats['total'] }}</div>
            </div>
        </div>

        <div class="card" style="padding: 18px; display: flex; align-items: center; gap: 14px; margin: 0;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                ⏳
            </div>
            <div>
                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Yeni / Bekleyen</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #d97706; margin-top: 2px;">{{ $stats['yeni'] }}</div>
            </div>
        </div>

        <div class="card" style="padding: 18px; display: flex; align-items: center; gap: 14px; margin: 0;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: #f3e8ff; color: #9333ea; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                ⚡
            </div>
            <div>
                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">14 Gün Deneme</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #9333ea; margin-top: 2px;">{{ $stats['trial'] }}</div>
            </div>
        </div>

        <div class="card" style="padding: 18px; display: flex; align-items: center; gap: 14px; margin: 0;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                📩
            </div>
            <div>
                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">İletişim / Paket</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #059669; margin-top: 2px;">{{ $stats['contact'] }}</div>
            </div>
        </div>

        <div class="card" style="padding: 18px; display: flex; align-items: center; gap: 14px; margin: 0;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                🚀
            </div>
            <div>
                <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Müşteriye Dönüşen</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #16a34a; margin-top: 2px;">{{ $stats['uye_yapildi'] }}</div>
            </div>
        </div>

    </div>

    {{-- Ana Filtre ve Tablo Kartı --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        
        {{-- Üst Filtre Çubuğu --}}
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; background: #fafafa;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="{{ route('admin.requests.index') }}" 
                   class="btn btn-sm {{ !request('type') ? 'btn-primary' : 'btn-secondary' }}" style="font-weight: 600;">
                   Tüm Talepler
                </a>
                <a href="{{ route('admin.requests.index', ['type' => 'trial']) }}" 
                   class="btn btn-sm {{ request('type') === 'trial' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight: 600;">
                   ⚡ 14 Günlük Denemeler
                </a>
                <a href="{{ route('admin.requests.index', ['type' => 'contact']) }}" 
                   class="btn btn-sm {{ request('type') === 'contact' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight: 600;">
                   📩 İletişim / Paket Talepleri
                </a>
            </div>

            {{-- Durum Filtresi --}}
            <form method="GET" action="{{ route('admin.requests.index') }}" style="display: flex; gap: 8px; align-items: center;">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <select name="status" onchange="this.form.submit()" style="background: #ffffff; border: 1px solid var(--border-color); color: var(--text-primary); padding: 8px 12px; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600;">
                    <option value="">-- Tüm Durumlar --</option>
                    <option value="yeni" {{ request('status') === 'yeni' ? 'selected' : '' }}>Yeni Talep</option>
                    <option value="arandi" {{ request('status') === 'arandi' ? 'selected' : '' }}>Görüşüldü</option>
                    <option value="beklemede" {{ request('status') === 'beklemede' ? 'selected' : '' }}>Düşünüyor</option>
                    <option value="uye_yapildi" {{ request('status') === 'uye_yapildi' ? 'selected' : '' }}>Müşteri Oldu</option>
                    <option value="iptal" {{ request('status') === 'iptal' ? 'selected' : '' }}>İptal</option>
                </select>
            </form>
        </div>

        {{-- Talep Tablosu --}}
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tarih / ID</th>
                        <th>Tür</th>
                        <th>Müşteri Bilgileri</th>
                        <th>İstenen Paket / Mesaj</th>
                        <th>Durum</th>
                        <th style="text-align: right;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td style="white-space: nowrap;">
                                <div style="font-weight: 800; color: var(--text-primary);">#{{ $req->id }}</div>
                                <div style="font-size: 0.78rem; color: var(--text-muted); font-weight: 500;">{{ $req->created_at->format('d.m.Y H:i') }}</div>
                            </td>
                            <td style="white-space: nowrap;">
                                @if($req->type === 'trial')
                                    <span class="badge" style="background: #f3e8ff; color: #9333ea; border: 1px solid #e9d5ff; font-weight: 700;">
                                        ⚡ 14 Gün Deneme
                                    </span>
                                @else
                                    <span class="badge badge-mint" style="font-weight: 700;">
                                        📩 İletişim / Paket
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 0.92rem;">{{ $req->name }}</div>
                                @if($req->company_name)
                                    <div style="font-size: 0.8rem; color: #0284c7; font-weight: 600; margin-top: 1px;">🏢 {{ $req->company_name }}</div>
                                @endif
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                                    📞 <a href="tel:{{ $req->phone }}" style="color: var(--text-secondary); text-decoration: none; font-weight: 600;">{{ $req->phone }}</a>
                                    &nbsp;|&nbsp;
                                    ✉️ <a href="mailto:{{ $req->email }}" style="color: var(--text-secondary); text-decoration: none; font-weight: 500;">{{ $req->email }}</a>
                                </div>
                            </td>
                            <td style="max-width: 250px;">
                                <div style="font-weight: 700; color: #d97706; font-size: 0.85rem;">{{ $req->package_name ?? 'Standart' }}</div>
                                @if($req->message)
                                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 3px; line-height: 1.4; word-break: break-word;">
                                        "{{ Str::limit($req->message, 80) }}"
                                    </div>
                                @endif
                            </td>
                            <td style="white-space: nowrap;">
                                <form method="POST" action="{{ route('admin.requests.update-status', $req) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" 
                                            style="background: #ffffff; border: 1px solid var(--border-color); color: var(--text-primary); padding: 6px 10px; border-radius: var(--radius-sm); font-size: 0.82rem; font-weight: 600; cursor: pointer;">
                                        <option value="yeni" {{ $req->status === 'yeni' ? 'selected' : '' }}>🟡 Yeni Talep</option>
                                        <option value="arandi" {{ $req->status === 'arandi' ? 'selected' : '' }}>🔵 Görüşüldü</option>
                                        <option value="beklemede" {{ $req->status === 'beklemede' ? 'selected' : '' }}>🟣 Düşünüyor</option>
                                        <option value="uye_yapildi" {{ $req->status === 'uye_yapildi' ? 'selected' : '' }}>🟢 Müşteri Oldu</option>
                                        <option value="iptal" {{ $req->status === 'iptal' ? 'selected' : '' }}>🔴 İptal / Olumsuz</option>
                                    </select>
                                </form>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                    {{-- Tek Tıkla Üye Yap Butonu --}}
                                    @if($req->status !== 'uye_yapildi')
                                        <form method="POST" action="{{ route('admin.requests.convert', $req) }}" onsubmit="return confirm('Bu müşteriye 14 günlük deneme hesabı oluşturulacak. Devam edilsin mi?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" style="font-size: 0.8rem; font-weight: 700; padding: 6px 12px;">
                                                🚀 Üye Yap
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Sil Butonu --}}
                                    <form method="POST" action="{{ route('admin.requests.destroy', $req) }}" onsubmit="return confirm('Bu talebi silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 6px 10px; font-size: 0.8rem;" title="Talebi Sil">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 48px 18px; text-align: center; color: var(--text-muted);">
                                <div style="font-size: 36px; margin-bottom: 8px;">📭</div>
                                <div style="font-weight: 700; font-size: 1rem; color: var(--text-primary);">Henüz gösterilecek müşteri talebi bulunmuyor.</div>
                                <div style="font-size: 0.85rem; margin-top: 4px;">Tanıtım sitesinden yeni bir talep veya 14 günlük ücretsiz deneme oluşturulduğunda burada listelenecektir.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Sayfalama (Pagination) --}}
        @if($requests->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
