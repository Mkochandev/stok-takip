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
            font-size: 12px;
            color: #111827;
            background: #f3f4f6;
            line-height: 1.5;
        }

        .page {
            background: #fff;
            max-width: 820px;
            margin: 0 auto;
            padding: 40px 48px 36px;
            min-height: 100vh;
        }

        /* ── HEADER ───────────────────────────── */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 18px;
            margin-bottom: 24px;
            border-bottom: 3px solid #1d4ed8;
        }
        .logo-area {}
        .logo {
            font-size: 20px;
            font-weight: 800;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            gap: 6px;
            letter-spacing: -0.02em;
        }
        .logo span { color: #111827; }
        .doc-sub { font-size: 11px; color: #6b7280; margin-top: 3px; font-weight: 500; letter-spacing: 0.03em; text-transform: uppercase; }

        .doc-info { text-align: right; }
        .doc-info .period {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 3px;
        }
        .doc-info .meta { font-size: 11px; color: #6b7280; line-height: 1.7; }
        .doc-info .doc-no { font-size: 11px; color: #1d4ed8; font-weight: 600; }

        /* ── USTA CARD ──────────────────────────── */
        .usta-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }
        .usta-avatar {
            width: 50px; height: 50px;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px; font-weight: 800;
            flex-shrink: 0; letter-spacing: -0.03em;
        }
        .usta-name { font-size: 17px; font-weight: 800; color: #111827; margin-bottom: 2px; }
        .usta-meta { font-size: 11.5px; color: #6b7280; display: flex; gap: 12px; }
        .usta-meta span { display: flex; align-items: center; gap: 3px; }
        .usta-rates { margin-left: auto; text-align: right; }
        .usta-rates .rate-val { font-size: 20px; font-weight: 800; color: #1d4ed8; }
        .usta-rates .rate-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.04em; }
        .usta-rates .rate-mesai { font-size: 11px; color: #6b7280; margin-top: 1px; }

        /* ── STATS ROW ──────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1.4fr;
            gap: 10px;
            margin-bottom: 24px;
        }
        .stat-box {
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
            text-align: center;
        }
        .stat-box.highlight {
            border-color: #bfdbfe;
            background: #eff6ff;
        }
        .stat-box .stat-ico { font-size: 15px; margin-bottom: 2px; }
        .stat-box .stat-lbl {
            font-size: 9px; text-transform: uppercase; letter-spacing: 0.07em;
            color: #9ca3af; font-weight: 700; margin-bottom: 4px;
        }
        .stat-box .stat-num { font-size: 22px; font-weight: 800; color: #111827; line-height: 1; }
        .stat-box.highlight .stat-num { color: #1d4ed8; }
        .stat-box .stat-sub { font-size: 10.5px; color: #6b7280; margin-top: 3px; }

        /* ── SECTION TITLE ─────────────────────── */
        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title::after {
            content: ''; flex: 1; height: 1px; background: #e5e7eb;
        }

        /* ── TABLE ─────────────────────────────── */
        table { width: 100%; border-collapse: collapse; font-size: 11.5px; }

        .devam-table thead th {
            background: #1d4ed8;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .devam-table thead th:last-child { text-align: right; }

        .devam-table tbody tr { border-bottom: 1px solid #f1f5f9; }
        .devam-table tbody tr:nth-child(even) { background: #f9fafb; }
        .devam-table tbody tr:last-child { border-bottom: none; }
        .devam-table tbody td { padding: 7px 10px; vertical-align: middle; }
        .devam-table tbody td:last-child { text-align: right; font-weight: 700; }

        .devam-table tfoot td {
            padding: 9px 10px;
            border-top: 2px solid #1d4ed8;
            font-weight: 700;
            font-size: 13px;
        }
        .devam-table tfoot td:last-child {
            text-align: right;
            color: #1d4ed8;
            font-size: 15px;
        }

        /* Çalışma tipi badge */
        .badge {
            display: inline-flex; align-items: center; gap: 3px;
            padding: 2px 8px; border-radius: 20px;
            font-size: 10px; font-weight: 700; white-space: nowrap;
        }
        .badge-tam    { background: #dcfce7; color: #15803d; }
        .badge-yarim  { background: #fef9c3; color: #a16207; }
        .badge-mesai  { background: #dbeafe; color: #1d4ed8; }

        /* Tarih kolonu */
        .td-tarih .date-main { font-weight: 700; color: #111827; }
        .td-tarih .date-gun  { font-size: 10px; color: #9ca3af; margin-top: 1px; }

        /* ── ÖDEME TABLOSU ──────────────────────── */
        .odeme-table { margin-top: 20px; }
        .odeme-table thead th {
            background: #0f766e;
            color: #fff;
            padding: 7px 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .odeme-table thead th:last-child { text-align: right; }
        .odeme-table tbody tr { border-bottom: 1px solid #f0fdf9; }
        .odeme-table tbody tr:nth-child(even) { background: #f0fdfa; }
        .odeme-table tbody td { padding: 7px 10px; }
        .odeme-table tbody td:last-child { text-align: right; font-weight: 700; color: #0f766e; }
        .odeme-table tfoot td { padding: 9px 10px; border-top: 2px solid #0f766e; font-weight: 700; font-size: 12px; }
        .odeme-table tfoot td:last-child { text-align: right; color: #0f766e; font-size: 14px; }

        /* ── FİNAL ÖZET ─────────────────────────── */
        .final-ozet {
            margin-top: 22px;
            border-radius: 10px;
            overflow: hidden;
            border: 1.5px solid;
        }
        .final-ozet.borclu { border-color: #fca5a5; }
        .final-ozet.odendi { border-color: #86efac; }
        .final-ozet.fazla  { border-color: #c4b5fd; }

        .final-ozet-header {
            padding: 10px 18px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .final-ozet.borclu .final-ozet-header { background: #fee2e2; color: #b91c1c; }
        .final-ozet.odendi .final-ozet-header { background: #dcfce7; color: #15803d; }
        .final-ozet.fazla  .final-ozet-header { background: #ede9fe; color: #6d28d9; }

        .final-ozet-body {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1.2fr;
            gap: 0;
        }
        .final-ozet-cell {
            padding: 14px 18px;
            border-right: 1px solid #f3f4f6;
        }
        .final-ozet-cell:last-child { border-right: none; }
        .final-ozet-cell .foc-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; font-weight: 600; }
        .final-ozet-cell .foc-val   { font-size: 18px; font-weight: 800; }
        .final-ozet-cell .foc-val.blue   { color: #1d4ed8; }
        .final-ozet-cell .foc-val.green  { color: #15803d; }
        .final-ozet-cell .foc-val.red    { color: #b91c1c; }
        .final-ozet-cell .foc-val.purple { color: #6d28d9; }
        .final-ozet-cell .foc-val.gray   { color: #15803d; font-size: 15px; }
        .final-ozet-cell .foc-sub   { font-size: 10px; color: #9ca3af; margin-top: 3px; }

        /* ── İMZA ───────────────────────────────── */
        .imza-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            margin-top: 36px;
        }
        .imza-box { text-align: center; }
        .imza-line { border-top: 1.5px solid #d1d5db; padding-top: 8px; margin-bottom: 4px; margin-top: 30px; }
        .imza-label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
        .imza-val { font-size: 12px; font-weight: 700; color: #111827; margin-top: 2px; }

        /* ── FOOTER ─────────────────────────────── */
        .doc-footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #9ca3af;
        }
        .doc-footer a { color: #1d4ed8; text-decoration: none; }

        /* ── PRINT BAR (sadece ekran) ────────────── */
        .print-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #1e293b;
            padding: 10px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 999;
            box-shadow: 0 2px 12px rgba(0,0,0,0.35);
        }
        .print-bar .pb-title { color: #94a3b8; font-size: 13px; font-family: inherit; }
        .print-bar .pb-title strong { color: #fff; }
        .print-bar .pb-btns { display: flex; gap: 8px; }
        .btn-pdf {
            background: #1d4ed8; color: #fff;
            border: none; border-radius: 6px;
            padding: 8px 20px; font-size: 13px; font-weight: 600;
            cursor: pointer; font-family: inherit; letter-spacing: -0.01em;
        }
        .btn-pdf:hover { background: #1e40af; }
        .btn-close {
            background: #334155; color: #94a3b8;
            border: none; border-radius: 6px;
            padding: 8px 14px; font-size: 13px;
            cursor: pointer; font-family: inherit;
        }
        .btn-close:hover { background: #475569; color: #fff; }

        /* Boş durum */
        .empty-devam {
            text-align: center; padding: 28px;
            border: 1.5px dashed #e5e7eb; border-radius: 8px;
            color: #9ca3af; font-size: 12px;
        }

        @media print {
            .print-bar { display: none !important; }
            body { background: #fff; }
            .page {
                padding: 20px 28px;
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
        📄 <strong>{{ $usta->ad_soyad }}</strong> — {{ $aylar[$ay] }} {{ $yil }} Hesap Özeti
    </div>
    <div class="pb-btns">
        <button class="btn-pdf" onclick="window.print()">🖨️ Yazdır / PDF Kaydet</button>
        <button class="btn-close" onclick="window.close()">✕ Kapat</button>
    </div>
</div>

<div class="page" style="margin-top:56px;">

    {{-- ── BAŞLIK ── --}}
    <div class="doc-header">
        <div class="logo-area">
            <div class="logo">⚙️ Gazi <span>Ustam</span></div>
            <div class="doc-sub">Usta Aylık Hesap Özeti</div>
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
                    <span>{{ $usta->uzmanlik }}</span>
                @endif
                @if($usta->telefon)
                    <span>📞 {{ $usta->telefon }}</span>
                @endif
            </div>
        </div>
        <div class="usta-rates">
            <div class="rate-label">Günlük Ücret</div>
            <div class="rate-val">{{ number_format($usta->gunluk_ucret, 0, ',', '.') }}₺</div>
            <div class="rate-mesai">Mesai: {{ number_format($usta->mesai_saatlik_ucret, 0, ',', '.') }}₺/s</div>
        </div>
    </div>

    {{-- ── İSTATİSTİK KUTULARI ── --}}
    <div class="stats-row">
        <div class="stat-box">
            <div class="stat-ico">✅</div>
            <div class="stat-lbl">Tam Gün</div>
            <div class="stat-num">{{ $tamGun }}</div>
            <div class="stat-sub">{{ number_format($tamGun * $usta->gunluk_ucret, 0, ',', '.') }}₺</div>
        </div>
        <div class="stat-box">
            <div class="stat-ico">🌗</div>
            <div class="stat-lbl">Yarım Gün</div>
            <div class="stat-num">{{ $yarimGun }}</div>
            <div class="stat-sub">{{ number_format($yarimGun * ($usta->gunluk_ucret / 2), 0, ',', '.') }}₺</div>
        </div>
        <div class="stat-box">
            <div class="stat-ico">⏰</div>
            <div class="stat-lbl">Mesai Saati</div>
            <div class="stat-num">{{ $mesaiSaati }}</div>
            <div class="stat-sub">{{ number_format($mesaiSaati * $usta->mesai_saatlik_ucret, 0, ',', '.') }}₺</div>
        </div>
        <div class="stat-box highlight">
            <div class="stat-ico">💰</div>
            <div class="stat-lbl">Toplam Hakedis</div>
            <div class="stat-num">{{ number_format($hakedis, 0, ',', '.') }}₺</div>
            <div class="stat-sub">{{ $tamGun + $yarimGun }} gün çalışma</div>
        </div>
    </div>

    {{-- ── GÜNLÜK DEVAM KAYDI ── --}}
    <div class="section-title">📅 Günlük Devam Kaydı</div>

    @if($kayitlar->count() > 0)
    <table class="devam-table">
        <thead>
            <tr>
                <th>Tarih</th>
                <th>Gün</th>
                <th>Çalışma Tipi</th>
                <th>İş / Şantiye</th>
                <th>Ücret</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kayitlar as $kayit)
            <tr>
                <td class="td-tarih">
                    <div class="date-main">{{ $kayit->tarih->locale('tr')->isoFormat('D MMMM YYYY') }}</div>
                </td>
                <td style="color:#6b7280; font-size:11px;">{{ $kayit->tarih->locale('tr')->isoFormat('dddd') }}</td>
                <td>
                    @if($kayit->calisma_tipi === 'tam')
                        <span class="badge badge-tam">✅ Tam Gün</span>
                    @elseif($kayit->calisma_tipi === 'yarim')
                        <span class="badge badge-yarim">🌗 Yarım Gün</span>
                    @else
                        <span class="badge badge-mesai">⏰ Mesai ({{ $kayit->mesai_saati }}s)</span>
                    @endif
                </td>
                <td style="color:#6b7280;">{{ $kayit->ilgiliIs->is_adi ?? '—' }}</td>
                <td style="color:#15803d;">{{ number_format($kayit->hesaplanan_ucret, 0, ',', '.') }}₺</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>TOPLAM HAKEDİŞ:</strong></td>
                <td>{{ number_format($hakedis, 0, ',', '.') }}₺</td>
            </tr>
        </tfoot>
    </table>
    @else
    <div class="empty-devam">Bu ay için devam kaydı bulunamadı.</div>
    @endif

    {{-- ── ÖDEME GEÇMİŞİ ── --}}
    @if($odeme && $odenenTutar > 0)
    <div class="odeme-table" style="margin-top:24px;">
        <div class="section-title">💳 Ödeme Geçmişi</div>
        <table>
            <thead class="odeme-table">
                <tr>
                    <th style="background:#0f766e;color:#fff;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">Ödeme Tarihi</th>
                    <th style="background:#0f766e;color:#fff;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">Ay / Dönem</th>
                    <th style="background:#0f766e;color:#fff;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">Yöntem</th>
                    <th style="background:#0f766e;color:#fff;padding:8px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;text-align:right;">Tutar</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom:1px solid #f0fdf9;">
                    <td style="padding:8px 10px;font-weight:600;">
                        {{ $odeme->odeme_tarihi?->locale('tr')->isoFormat('D MMMM YYYY') ?? '—' }}
                    </td>
                    <td style="padding:8px 10px;color:#6b7280;">{{ $aylar[$ay] }} {{ $yil }}</td>
                    <td style="padding:8px 10px;color:#6b7280;">{{ ucfirst($odeme->odeme_yontemi ?? 'nakit') }}</td>
                    <td style="padding:8px 10px;text-align:right;font-weight:700;color:#0f766e;">
                        {{ number_format($odenenTutar, 0, ',', '.') }}₺
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="padding:9px 10px;border-top:2px solid #0f766e;font-weight:700;font-size:12px;"><strong>TOPLAM ÖDENEN:</strong></td>
                    <td style="padding:9px 10px;border-top:2px solid #0f766e;text-align:right;font-weight:800;font-size:14px;color:#0f766e;">{{ number_format($odenenTutar, 0, ',', '.') }}₺</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- ── FİNAL HESAP ÖZETİ ── --}}
    @php
        $durum = $kalanBakiye > 0 ? 'borclu' : ($kalanBakiye < 0 ? 'fazla' : 'odendi');
    @endphp
    <div class="final-ozet {{ $durum }}" style="margin-top:24px;">
        <div class="final-ozet-header">
            @if($durum === 'borclu') ⚠️ Ödenmemiş Bakiye — Hesap Açık
            @elseif($durum === 'fazla') 🔄 Fazla Ödeme — Usta Bizden Alacaklı
            @else ✅ Hesap Kapalı — Tüm Ödemeler Tamamlandı
            @endif
        </div>
        <div class="final-ozet-body">
            <div class="final-ozet-cell">
                <div class="foc-label">💰 Toplam Hakedis</div>
                <div class="foc-val blue">{{ number_format($hakedis, 0, ',', '.') }}₺</div>
                <div class="foc-sub">{{ $tamGun + $yarimGun }} gün çalışıldı</div>
            </div>
            <div class="final-ozet-cell">
                <div class="foc-label">✅ Ödenen Tutar</div>
                <div class="foc-val green">{{ number_format($odenenTutar, 0, ',', '.') }}₺</div>
                @if($odeme?->odeme_tarihi)
                    <div class="foc-sub">{{ $odeme->odeme_tarihi->locale('tr')->isoFormat('D MMM YYYY') }}</div>
                @else
                    <div class="foc-sub">—</div>
                @endif
            </div>
            <div class="final-ozet-cell">
                <div class="foc-label">
                    @if($durum === 'borclu') ⚠️ Kalan Borç
                    @elseif($durum === 'fazla') 🔄 Fazla Ödeme
                    @else ✓ Bakiye
                    @endif
                </div>
                @if($durum === 'borclu')
                    <div class="foc-val red">{{ number_format($kalanBakiye, 0, ',', '.') }}₺</div>
                    <div class="foc-sub">Ödeme bekleniyor</div>
                @elseif($durum === 'fazla')
                    <div class="foc-val purple">{{ number_format(abs($kalanBakiye), 0, ',', '.') }}₺</div>
                    <div class="foc-sub">Usta bize borçlu</div>
                @else
                    <div class="foc-val green">0₺</div>
                    <div class="foc-sub">Hesap temiz</div>
                @endif
            </div>
            <div class="final-ozet-cell">
                <div class="foc-label">📋 Hesap Durumu</div>
                @if($durum === 'borclu')
                    <div class="foc-val red" style="font-size:14px;">🔴 Açık</div>
                    <div class="foc-sub">{{ number_format(($odenenTutar / max($hakedis, 1)) * 100, 0) }}% ödendi</div>
                @elseif($durum === 'fazla')
                    <div class="foc-val purple" style="font-size:14px;">🟣 Fazla</div>
                    <div class="foc-sub">Mahsup gerekli</div>
                @else
                    <div class="foc-val green" style="font-size:14px;">🟢 Kapalı</div>
                    <div class="foc-sub">{{ $odeme?->odeme_tarihi?->locale('tr')->isoFormat('D MMM') ?? '' }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── İMZA ── --}}
    <div class="imza-row">
        <div class="imza-box">
            <div class="imza-line"></div>
            <div class="imza-label">Ustanın İmzası</div>
            <div class="imza-val">{{ $usta->ad_soyad }}</div>
        </div>
        <div class="imza-box">
            <div class="imza-line"></div>
            <div class="imza-label">Hazırlayan</div>
            <div class="imza-val">{{ Auth::user()->name }}</div>
        </div>
        <div class="imza-box">
            <div class="imza-line"></div>
            <div class="imza-label">Tarih</div>
            <div class="imza-val">{{ now()->locale('tr')->isoFormat('D MMMM YYYY') }}</div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="doc-footer">
        <span>Gazi Ustam — Usta Yönetim Sistemi</span>
        <span>{{ now()->locale('tr')->isoFormat('D MMMM YYYY, HH:mm') }} tarihinde oluşturuldu</span>
    </div>

</div>

</body>
</html>
