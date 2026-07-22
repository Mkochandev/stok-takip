<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $usta->ad_soyad }} — {{ $aylar[$ay] }} {{ $yil }} Hesap Özeti</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            font-size: 11.5px;
            color: #0f172a;
            background: #f8fafc;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            background: #fff;
            max-width: 840px;
            margin: 0 auto;
            padding: 40px 48px;
            min-height: 100vh;
        }

        /* ── HEADER ───────────────────────────── */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            margin-bottom: 24px;
            border-bottom: 2px solid #0f172a;
        }
        .logo {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.03em;
            text-transform: uppercase;
        }
        .logo span { color: #2563eb; }
        .doc-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .doc-info { text-align: right; }
        .doc-info .period {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .doc-info .meta { font-size: 10.5px; color: #64748b; line-height: 1.6; }
        .doc-info .doc-no { font-size: 11px; color: #0f172a; font-weight: 700; }

        /* ── USTA CARD ──────────────────────────── */
        .usta-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }
        .usta-avatar {
            width: 44px; height: 44px;
            background: #0f172a;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 16px; font-weight: 700;
            flex-shrink: 0;
        }
        .usta-name { font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
        .usta-meta { font-size: 11px; color: #64748b; display: flex; gap: 14px; }
        .usta-rates { margin-left: auto; text-align: right; }
        .usta-rates .rate-val { font-size: 18px; font-weight: 800; color: #0f172a; }
        .usta-rates .rate-label { font-size: 9.5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; font-weight: 600; }
        .usta-rates .rate-mesai { font-size: 10.5px; color: #475569; margin-top: 1px; }

        /* ── STATS ROW ──────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1.4fr;
            gap: 10px;
            margin-bottom: 24px;
        }
        .stat-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 14px;
            text-align: center;
            background: #ffffff;
        }
        .stat-box.highlight {
            border-color: #0f172a;
            background: #f8fafc;
        }
        .stat-box .stat-lbl {
            font-size: 9px; text-transform: uppercase; letter-spacing: 0.08em;
            color: #64748b; font-weight: 700; margin-bottom: 4px;
        }
        .stat-box .stat-num { font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1; }
        .stat-box.highlight .stat-num { color: #0f172a; }
        .stat-box .stat-sub { font-size: 10px; color: #64748b; margin-top: 3px; font-weight: 500; }

        /* ── SECTION TITLE ─────────────────────── */
        .section-title {
            font-size: 10px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title::after {
            content: ''; flex: 1; height: 1px; background: #cbd5e1;
        }

        /* ── TABLE ─────────────────────────────── */
        table { width: 100%; border-collapse: collapse; font-size: 11px; }

        .devam-table thead th {
            background: #0f172a;
            color: #fff;
            padding: 8px 12px;
            text-align: left;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .devam-table thead th:last-child { text-align: right; }

        .devam-table tbody tr { border-bottom: 1px solid #e2e8f0; }
        .devam-table tbody tr:nth-child(even) { background: #f8fafc; }
        .devam-table tbody td { padding: 8px 12px; vertical-align: middle; }
        .devam-table tbody td:last-child { text-align: right; font-weight: 700; }

        .devam-table tfoot td {
            padding: 10px 12px;
            border-top: 2px solid #0f172a;
            font-weight: 700;
            font-size: 12px;
        }
        .devam-table tfoot td:last-child {
            text-align: right;
            color: #0f172a;
            font-size: 14px;
        }

        /* Çalışma tipi badge */
        .badge {
            display: inline-block;
            padding: 2px 8px; border-radius: 4px;
            font-size: 9.5px; font-weight: 700; white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .badge-tam    { background: #e2e8f0; color: #0f172a; }
        .badge-yarim  { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .badge-mesai  { background: #334155; color: #ffffff; }

        .td-tarih .date-main { font-weight: 700; color: #0f172a; }

        /* ── ÖDEME TABLOSU ──────────────────────── */
        .odeme-table { margin-top: 20px; }
        .odeme-table table thead th {
            background: #334155;
            color: #fff;
            padding: 8px 12px;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .odeme-table table thead th:last-child { text-align: right; }
        .odeme-table table tbody tr { border-bottom: 1px solid #e2e8f0; }
        .odeme-table table tbody tr:nth-child(even) { background: #f8fafc; }
        .odeme-table table tbody td { padding: 8px 12px; }
        .odeme-table table tbody td:last-child { text-align: right; font-weight: 700; color: #0f172a; }
        .odeme-table table tfoot td { padding: 10px 12px; border-top: 2px solid #334155; font-weight: 700; font-size: 12px; }
        .odeme-table table tfoot td:last-child { text-align: right; color: #0f172a; font-size: 14px; }

        /* ── FİNAL ÖZET ─────────────────────────── */
        .final-ozet {
            margin-top: 22px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #cbd5e1;
        }

        .final-ozet-header {
            padding: 8px 16px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            background: #f1f5f9;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
        }

        .final-ozet-body {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1.2fr;
            gap: 0;
            background: #fff;
        }
        .final-ozet-cell {
            padding: 12px 16px;
            border-right: 1px solid #e2e8f0;
        }
        .final-ozet-cell:last-child { border-right: none; }
        .final-ozet-cell .foc-label { font-size: 9.5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; font-weight: 700; }
        .final-ozet-cell .foc-val   { font-size: 16px; font-weight: 800; color: #0f172a; }
        .final-ozet-cell .foc-sub   { font-size: 9.5px; color: #64748b; margin-top: 3px; }

        /* ── İMZA ───────────────────────────────── */
        .imza-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 32px;
            margin-top: 40px;
        }
        .imza-box { text-align: center; }
        .imza-line { border-top: 1px solid #94a3b8; padding-top: 8px; margin-bottom: 4px; margin-top: 36px; }
        .imza-label { font-size: 9.5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; }
        .imza-val { font-size: 11.5px; font-weight: 700; color: #0f172a; margin-top: 2px; }

        /* ── FOOTER ─────────────────────────────── */
        .doc-footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            color: #94a3b8;
        }

        /* ── PRINT BAR ───────────────────────────── */
        .print-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #0f172a;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 999;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }
        .print-bar .pb-title { color: #cbd5e1; font-size: 13px; font-family: inherit; }
        .print-bar .pb-title strong { color: #fff; }
        .print-bar .pb-btns { display: flex; gap: 8px; }
        .btn-pdf {
            background: #2563eb; color: #fff;
            border: none; border-radius: 4px;
            padding: 8px 18px; font-size: 12.5px; font-weight: 600;
            cursor: pointer; font-family: inherit;
        }
        .btn-pdf:hover { background: #1d4ed8; }
        .btn-close {
            background: #334155; color: #cbd5e1;
            border: none; border-radius: 4px;
            padding: 8px 14px; font-size: 12.5px;
            cursor: pointer; font-family: inherit;
        }
        .btn-close:hover { background: #475569; color: #fff; }

        .empty-devam {
            text-align: center; padding: 24px;
            border: 1px dashed #cbd5e1; border-radius: 6px;
            color: #64748b; font-size: 11.5px;
        }

        @media print {
            .print-bar { display: none !important; }
            body { background: #fff; }
            .page {
                padding: 0;
                max-width: 100%;
                margin: 0;
            }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

{{-- Print Bar --}}
<div class="print-bar" id="printBar">
    <div class="pb-title">
        <strong>{{ $usta->ad_soyad }}</strong> — {{ $aylar[$ay] }} {{ $yil }} Hesap Özeti Belgesi
    </div>
    <div class="pb-btns">
        <button class="btn-pdf" onclick="window.print()">Yazdır / PDF Kaydet</button>
        <button class="btn-close" onclick="window.close()">Kapat</button>
    </div>
</div>

<div class="page" style="margin-top:56px;">

    {{-- ── BAŞLIK ── --}}
    <div class="doc-header">
        <div class="logo-area">
            <div class="logo">GAZİ <span>USTAM</span></div>
            <div class="doc-sub">RESMİ USTA HAKEDİŞ VE HESAP EKSTRESİ</div>
        </div>
        <div class="doc-info">
            <div class="period">{{ $aylar[$ay] }} {{ $yil }}</div>
            <div class="meta">
                Düzenleme Tarihi: {{ now()->locale('tr')->isoFormat('D MMMM YYYY') }}<br>
                <span class="doc-no">Belge No: UST-{{ $usta->id }}-{{ $yil }}-{{ str_pad($ay, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>

    {{-- ── USTA KART ── --}}
    <div class="usta-card">
        <div class="usta-avatar">
            {{ strtoupper(mb_substr($usta->ad, 0, 1)) }}{{ strtoupper(mb_substr($usta->soyad, 0, 1)) }}
        </div>
        <div>
            <div class="usta-name">{{ $usta->ad_soyad }}</div>
            <div class="usta-meta">
                @if($usta->uzmanlik)
                    <span>Uzmanlık: {{ $usta->uzmanlik }}</span>
                @endif
                @if($usta->telefon)
                    <span>Tel: {{ $usta->telefon }}</span>
                @endif
            </div>
        </div>
        <div class="usta-rates">
            <div class="rate-label">Günlük Yevmiye</div>
            <div class="rate-val">{{ number_format($usta->gunluk_ucret, 0, ',', '.') }}₺</div>
            <div class="rate-mesai">Mesai: {{ number_format($usta->mesai_saatlik_ucret, 0, ',', '.') }}₺/saat</div>
        </div>
    </div>

    {{-- ── İSTATİSTİK KUTULARI ── --}}
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-lbl">Tam Gün</div>
            <div class="stat-num">{{ $tamGun }}</div>
            <div class="stat-sub">{{ number_format($tamGun * $usta->gunluk_ucret, 0, ',', '.') }}₺</div>
        </div>
        <div class="stat-box">
            <div class="stat-lbl">Yarım Gün</div>
            <div class="stat-num">{{ $yarimGun }}</div>
            <div class="stat-sub">{{ number_format($yarimGun * ($usta->gunluk_ucret / 2), 0, ',', '.') }}₺</div>
        </div>
        <div class="stat-box">
            <div class="stat-lbl">Mesai Saati</div>
            <div class="stat-num">{{ $mesaiSaati }}</div>
            <div class="stat-sub">{{ number_format($mesaiSaati * $usta->mesai_saatlik_ucret, 0, ',', '.') }}₺</div>
        </div>
        <div class="stat-box highlight">
            <div class="stat-lbl">Toplam Hakediş</div>
            <div class="stat-num">{{ number_format($hakedis, 0, ',', '.') }}₺</div>
            <div class="stat-sub">{{ $tamGun + $yarimGun }} Gün Çalışma</div>
        </div>
    </div>

    {{-- ── GÜNLÜK DEVAM KAYDI ── --}}
    <div class="section-title">GÜNLÜK DEVAM VE ÇALIŞMA KAYDI</div>

    @if($kayitlar->count() > 0)
    <table class="devam-table">
        <thead>
            <tr>
                <th>Tarih</th>
                <th>Gün</th>
                <th>Çalışma Tipi</th>
                <th>Şantiye / İş Adı</th>
                <th>Tutar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kayitlar as $kayit)
            <tr>
                <td class="td-tarih">
                    <div class="date-main">{{ $kayit->tarih->locale('tr')->isoFormat('D MMMM YYYY') }}</div>
                </td>
                <td style="color:#64748b; font-size:10.5px;">{{ $kayit->tarih->locale('tr')->isoFormat('dddd') }}</td>
                <td>
                    @if($kayit->calisma_tipi === 'tam')
                        <span class="badge badge-tam">Tam Gün</span>
                    @elseif($kayit->calisma_tipi === 'yarim')
                        <span class="badge badge-yarim">Yarım Gün</span>
                    @else
                        <span class="badge badge-mesai">Mesai ({{ $kayit->mesai_saati }}s)</span>
                    @endif
                </td>
                <td style="color:#475569;">{{ $kayit->ilgiliIs->is_adi ?? '—' }}</td>
                <td style="color:#0f172a; font-weight:700;">{{ number_format($kayit->hesaplanan_ucret, 0, ',', '.') }}₺</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">TOPLAM HAKEDİŞ:</td>
                <td>{{ number_format($hakedis, 0, ',', '.') }}₺</td>
            </tr>
        </tfoot>
    </table>
    @else
    <div class="empty-devam">Bu dönem için henüz devam kaydı bulunmamaktadır.</div>
    @endif

    {{-- ── ÖDEME GEÇMİŞİ ── --}}
    @if($odeme && $odenenTutar > 0)
    <div class="odeme-table" style="margin-top:24px;">
        <div class="section-title">ÖDEME GEÇMİŞİ VE MAKBUZ DETAYI</div>
        <table>
            <thead>
                <tr>
                    <th>Ödeme Tarihi</th>
                    <th>Hesap Dönemi</th>
                    <th>Ödeme Yöntemi</th>
                    <th style="text-align:right;">Ödenen Tutar</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:600;">
                        {{ $odeme->odeme_tarihi?->locale('tr')->isoFormat('D MMMM YYYY') ?? '—' }}
                    </td>
                    <td style="color:#64748b;">{{ $aylar[$ay] }} {{ $yil }}</td>
                    <td style="color:#64748b;">{{ ucfirst($odeme->odeme_yontemi ?? 'Nakit') }}</td>
                    <td style="text-align:right; font-weight:700; color:#0f172a;">
                        {{ number_format($odenenTutar, 0, ',', '.') }}₺
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">TOPLAM ÖDENEN:</td>
                    <td style="text-align:right; font-weight:800; font-size:14px; color:#0f172a;">{{ number_format($odenenTutar, 0, ',', '.') }}₺</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- ── FİNAL HESAP ÖZETİ ── --}}
    @php
        $durum = $kalanBakiye > 0 ? 'borclu' : ($kalanBakiye < 0 ? 'fazla' : 'odendi');
    @endphp
    <div class="final-ozet" style="margin-top:24px;">
        <div class="final-ozet-header">
            @if($durum === 'borclu') ÖDENMEMİŞ BAKİYE (HESAP AÇIK)
            @elseif($durum === 'fazla') FAZLA ÖDEME (MAHSUP GEREKLİ)
            @else HESAP KAPALI (TÜM ÖDEMELER TAMAMLANDI)
            @endif
        </div>
        <div class="final-ozet-body">
            <div class="final-ozet-cell">
                <div class="foc-label">Toplam Hakediş</div>
                <div class="foc-val">{{ number_format($hakedis, 0, ',', '.') }}₺</div>
                <div class="foc-sub">{{ $tamGun + $yarimGun }} Gün Çalışıldı</div>
            </div>
            <div class="final-ozet-cell">
                <div class="foc-label">Ödenen Tutar</div>
                <div class="foc-val">{{ number_format($odenenTutar, 0, ',', '.') }}₺</div>
                @if($odeme?->odeme_tarihi)
                    <div class="foc-sub">{{ $odeme->odeme_tarihi->locale('tr')->isoFormat('D MMM YYYY') }}</div>
                @else
                    <div class="foc-sub">—</div>
                @endif
            </div>
            <div class="final-ozet-cell">
                <div class="foc-label">
                    @if($durum === 'borclu') Kalan Borç
                    @elseif($durum === 'fazla') Fazla Ödeme
                    @else Bakiye
                    @endif
                </div>
                @if($durum === 'borclu')
                    <div class="foc-val" style="color:#b91c1c;">{{ number_format($kalanBakiye, 0, ',', '.') }}₺</div>
                    <div class="foc-sub">Ödeme Bekliyor</div>
                @elseif($durum === 'fazla')
                    <div class="foc-val">{{ number_format(abs($kalanBakiye), 0, ',', '.') }}₺</div>
                    <div class="foc-sub">Alacak Bakiyesi</div>
                @else
                    <div class="foc-val">0₺</div>
                    <div class="foc-sub">Tam Ödendi</div>
                @endif
            </div>
            <div class="final-ozet-cell">
                <div class="foc-label">Hesap Durumu</div>
                @if($durum === 'borclu')
                    <div class="foc-val" style="font-size:14px; color:#b91c1c;">AÇIK</div>
                    <div class="foc-sub">%{{ number_format(($odenenTutar / max($hakedis, 1)) * 100, 0) }} ödendi</div>
                @elseif($durum === 'fazla')
                    <div class="foc-val" style="font-size:14px;">FAZLA</div>
                    <div class="foc-sub">Mahsup Gerekli</div>
                @else
                    <div class="foc-val" style="font-size:14px; color:#15803d;">KAPALI</div>
                    <div class="foc-sub">Kapatıldı</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── İMZA ── --}}
    <div class="imza-row">
        <div class="imza-box">
            <div class="imza-line"></div>
            <div class="imza-label">Teslim Eden (Usta)</div>
            <div class="imza-val">{{ $usta->ad_soyad }}</div>
        </div>
        <div class="imza-box">
            <div class="imza-line"></div>
            <div class="imza-label">Teslim Alan / Hazırlayan</div>
            <div class="imza-val">{{ Auth::user()->name }}</div>
        </div>
        <div class="imza-box">
            <div class="imza-line"></div>
            <div class="imza-label">Onay Tarihi</div>
            <div class="imza-val">{{ now()->locale('tr')->isoFormat('D MMMM YYYY') }}</div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="doc-footer">
        <span>Gazi Ustam — İş ve Usta Yönetim Sistemi</span>
        <span>Bu belge {{ now()->locale('tr')->isoFormat('D MMMM YYYY, HH:mm') }} tarihinde sistem tarafından üretilmiştir.</span>
    </div>

</div>

</body>
</html>
