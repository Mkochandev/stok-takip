@extends('layouts.app')

@section('title', 'Müşteri Talepleri')
@section('page-title', 'Tanıtım Sitesi Müşteri Talepleri')

@section('content')
<div style="padding: 24px 0;">

    {{-- Flash Alerts --}}
    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;">✕</button>
        </div>
    @endif

    {{-- İstatistik Kartları --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: #1e293b; border: 1px solid #334155; border-radius: 14px; padding: 18px; color: #fff;">
            <div style="color: #94a3b8; font-size: 13px; font-weight: 600;">Toplam Talep</div>
            <div style="font-size: 28px; font-weight: 800; margin-top: 4px; color: #38bdf8;">{{ $stats['total'] }}</div>
        </div>
        <div style="background: #1e293b; border: 1px solid #334155; border-radius: 14px; padding: 18px; color: #fff;">
            <div style="color: #94a3b8; font-size: 13px; font-weight: 600;">Yeni / Bekleyen</div>
            <div style="font-size: 28px; font-weight: 800; margin-top: 4px; color: #f59e0b;">{{ $stats['yeni'] }}</div>
        </div>
        <div style="background: #1e293b; border: 1px solid #334155; border-radius: 14px; padding: 18px; color: #fff;">
            <div style="color: #94a3b8; font-size: 13px; font-weight: 600;">14 Gün Deneme Talebi</div>
            <div style="font-size: 28px; font-weight: 800; margin-top: 4px; color: #a855f7;">{{ $stats['trial'] }}</div>
        </div>
        <div style="background: #1e293b; border: 1px solid #334155; border-radius: 14px; padding: 18px; color: #fff;">
            <div style="color: #94a3b8; font-size: 13px; font-weight: 600;">İletişim / Paket Talebi</div>
            <div style="font-size: 28px; font-weight: 800; margin-top: 4px; color: #34d399;">{{ $stats['contact'] }}</div>
        </div>
        <div style="background: #1e293b; border: 1px solid #334155; border-radius: 14px; padding: 18px; color: #fff;">
            <div style="color: #94a3b8; font-size: 13px; font-weight: 600;">Müşteriye Dönüşenler</div>
            <div style="font-size: 28px; font-weight: 800; margin-top: 4px; color: #10b981;">{{ $stats['uye_yapildi'] }}</div>
        </div>
    </div>

    {{-- Filtreler & Tablo Konteyneri --}}
    <div style="background: #1e293b; border: 1px solid #334155; border-radius: 16px; overflow: hidden;">
        
        {{-- Üst Filtre Çubuğu --}}
        <div style="padding: 16px 20px; border-bottom: 1px solid #334155; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="{{ route('admin.requests.index') }}" 
                   style="padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; {{ !request('type') ? 'background: #3b82f6; color: #fff;' : 'background: #0f172a; color: #94a3b8;' }}">
                   Tüm Talepler
                </a>
                <a href="{{ route('admin.requests.index', ['type' => 'trial']) }}" 
                   style="padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; {{ request('type') === 'trial' ? 'background: #a855f7; color: #fff;' : 'background: #0f172a; color: #94a3b8;' }}">
                   ⚡ 14 Günlük Denemeler
                </a>
                <a href="{{ route('admin.requests.index', ['type' => 'contact']) }}" 
                   style="padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; {{ request('type') === 'contact' ? 'background: #10b981; color: #fff;' : 'background: #0f172a; color: #94a3b8;' }}">
                   📩 İletişim / Paket Talepleri
                </a>
            </div>

            {{-- Durum Filtresi --}}
            <form method="GET" action="{{ route('admin.requests.index') }}" style="display: flex; gap: 8px; align-items: center;">
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <select name="status" onchange="this.form.submit()" style="background: #0f172a; border: 1px solid #334155; color: #e2e8f0; padding: 8px 12px; border-radius: 8px; font-size: 13px;">
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
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; color: #cbd5e1; font-size: 14px;">
                <thead>
                    <tr style="background: #0f172a; border-bottom: 1px solid #334155; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <th style="padding: 14px 18px;">Tarih / ID</th>
                        <th style="padding: 14px 18px;">Tür</th>
                        <th style="padding: 14px 18px;">Müşteri Bilgileri</th>
                        <th style="padding: 14px 18px;">İstenen Paket / Mesaj</th>
                        <th style="padding: 14px 18px;">Durum</th>
                        <th style="padding: 14px 18px; text-align: right;">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr style="border-bottom: 1px solid #334155; transition: background 0.2s;" onmouseover="this.style.background='#273549'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 18px; white-space: nowrap;">
                                <div style="font-weight: 700; color: #fff;">#{{ $req->id }}</div>
                                <div style="font-size: 12px; color: #64748b;">{{ $req->created_at->format('d.m.Y H:i') }}</div>
                            </td>
                            <td style="padding: 14px 18px; white-space: nowrap;">
                                @if($req->type === 'trial')
                                    <span style="background: rgba(168, 85, 247, 0.2); border: 1px solid rgba(168, 85, 247, 0.4); color: #c084fc; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600;">
                                        ⚡ 14 Gün Deneme
                                    </span>
                                @else
                                    <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600;">
                                        📩 İletişim / Paket
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px;">
                                <div style="font-weight: 700; color: #f8fafc;">{{ $req->name }}</div>
                                @if($req->company_name)
                                    <div style="font-size: 12px; color: #38bdf8; font-weight: 600;">🏢 {{ $req->company_name }}</div>
                                @endif
                                <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">
                                    📞 <a href="tel:{{ $req->phone }}" style="color: #94a3b8; text-decoration: none;">{{ $req->phone }}</a>
                                    &nbsp;|&nbsp;
                                    ✉️ <a href="mailto:{{ $req->email }}" style="color: #94a3b8; text-decoration: none;">{{ $req->email }}</a>
                                </div>
                            </td>
                            <td style="padding: 14px 18px; max-width: 250px;">
                                <div style="font-weight: 600; color: #fbbf24; font-size: 13px;">{{ $req->package_name ?? 'Standart' }}</div>
                                @if($req->message)
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 4px; line-height: 1.4; word-break: break-word;">
                                        "{{ Str::limit($req->message, 80) }}"
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px 18px; white-space: nowrap;">
                                <form method="POST" action="{{ route('admin.requests.update-status', $req) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" 
                                            style="background: #0f172a; border: 1px solid #475569; color: #f1f5f9; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                        <option value="yeni" {{ $req->status === 'yeni' ? 'selected' : '' }}>🟡 Yeni Talep</option>
                                        <option value="arandi" {{ $req->status === 'arandi' ? 'selected' : '' }}>🔵 Görüşüldü</option>
                                        <option value="beklemede" {{ $req->status === 'beklemede' ? 'selected' : '' }}>🟣 Düşünüyor</option>
                                        <option value="uye_yapildi" {{ $req->status === 'uye_yapildi' ? 'selected' : '' }}>🟢 Müşteri Oldu</option>
                                        <option value="iptal" {{ $req->status === 'iptal' ? 'selected' : '' }}>🔴 İptal / Olumsuz</option>
                                    </select>
                                </form>
                            </td>
                            <td style="padding: 14px 18px; text-align: right; white-space: nowrap;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                    {{-- Tek Tıkla Üye Yap Butonu --}}
                                    @if($req->status !== 'uye_yapildi')
                                        <form method="POST" action="{{ route('admin.requests.convert', $req) }}" onsubmit="return confirm('Bu müşteriye 14 günlük deneme hesabı oluşturulacak. Devam edilsin mi?');">
                                            @csrf
                                            <button type="submit" title="Hızlı Kullanıcı Hesabı Aç" 
                                                    style="background: linear-gradient(135deg, #10b981, #059669); color: #fff; border: none; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                                                🚀 Üye Yap
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Sil Butonu --}}
                                    <form method="POST" action="{{ route('admin.requests.destroy', $req) }}" onsubmit="return confirm('Bu talebi silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Talebi Sil" 
                                                style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #ef4444; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px 18px; text-align: center; color: #64748b;">
                                <div style="font-size: 32px; margin-bottom: 8px;">📭</div>
                                <div style="font-weight: 600;">Henüz gösterilecek müşteri talebi bulunmuyor.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Sayfalama (Pagination) --}}
        @if($requests->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid #334155;">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
