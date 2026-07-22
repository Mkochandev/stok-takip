<?php

namespace App\Http\Controllers;

use App\Models\DevamKaydi;
use App\Models\Gider;
use App\Models\Odeme;
use App\Models\Usta;
use Illuminate\Http\Request;

class AylikHesapController extends Controller
{
    public function index(Request $request)
    {
        $ay  = $request->get('ay', now()->month);
        $yil = $request->get('yil', now()->year);
        $aramaQuery = $request->get('q', '');

        $aylar = [
            1=>'Ocak',2=>'Şubat',3=>'Mart',4=>'Nisan',
            5=>'Mayıs',6=>'Haziran',7=>'Temmuz',8=>'Ağustos',
            9=>'Eylül',10=>'Ekim',11=>'Kasım',12=>'Aralık',
        ];
        $yillar = range(now()->year, now()->year - 3);

        // O ay çalışan ustalar
        $ustaIdleri = DevamKaydi::whereMonth('tarih', $ay)
            ->whereYear('tarih', $yil)
            ->distinct()
            ->pluck('usta_id');

        $ustalar = Usta::whereIn('id', $ustaIdleri)->orderBy('ad')->get();

        // Her usta için hesap özeti
        $hesaplar = $ustalar->map(function (Usta $usta) use ($ay, $yil) {
            return $this->hesapOzetiHazirla($usta, $ay, $yil);
        });

        // Usta arama filtresi
        if ($aramaQuery) {
            $aramaLower = mb_strtolower($aramaQuery);
            $hesaplar = $hesaplar->filter(function ($h) use ($aramaLower) {
                return str_contains(mb_strtolower($h['usta']->ad_soyad), $aramaLower)
                    || str_contains(mb_strtolower($h['usta']->uzmanlik ?? ''), $aramaLower);
            });
        }

        return view('aylik-hesap.index', compact('ay', 'yil', 'aylar', 'yillar', 'hesaplar', 'aramaQuery'));
    }

    /**
     * Usta için belirli ay/yıl hesap özeti hazırla
     */
    private function hesapOzetiHazirla(Usta $usta, int $ay, int $yil): array
    {
        $kayitlar = $usta->devamKayitlari()
            ->whereMonth('tarih', $ay)
            ->whereYear('tarih', $yil)
            ->get();

        $tamGun     = $kayitlar->where('calisma_tipi', 'tam')->count();
        $yarimGun   = $kayitlar->where('calisma_tipi', 'yarim')->count();
        $mesaiSaati = $kayitlar->where('calisma_tipi', 'mesai')->sum('mesai_saati');
        $toplam     = $kayitlar->sum('hesaplanan_ucret');

        $odeme = Odeme::where('usta_id', $usta->id)
            ->where('ay', $ay)
            ->where('yil', $yil)
            ->first();

        $kalan = $odeme ? $odeme->kalan_bakiye : $toplam;

        return [
            'usta'           => $usta,
            'tam_gun'        => $tamGun,
            'yarim_gun'      => $yarimGun,
            'mesai_saati'    => $mesaiSaati,
            'toplam_hakedis' => $toplam,
            'odeme'          => $odeme,
            'kalan'          => $kalan,
            'kapandi'        => $odeme?->kapandi ?? false,
        ];
    }

    /**
     * Ödeme yap — hem aylık hesap sayfasından hem usta profilinden çağrılabilir
     */
    public function odemeYap(Request $request)
    {
        $validated = $request->validate([
            'usta_id'       => 'required|exists:ustalar,id',
            'ay'            => 'required|integer|between:1,12',
            'yil'           => 'required|integer|min:2020',
            'odenen_tutar'  => 'required|numeric|min:0.01',
            'odeme_yontemi' => 'nullable|in:nakit,havale,çek,diger',
            'notlar'        => 'nullable|string|max:500',
        ]);

        $usta   = Usta::findOrFail($validated['usta_id']);
        $toplam = $usta->aylikHakedis($validated['ay'], $validated['yil']);

        $odeme = Odeme::firstOrNew([
            'usta_id' => $validated['usta_id'],
            'ay'      => $validated['ay'],
            'yil'     => $validated['yil'],
        ]);

        $oncekiOdenen = $odeme->exists ? (float)$odeme->odenen_tutar : 0.0;
        $yeniOdenen   = (float)$validated['odenen_tutar'];

        $odeme->toplam_hakkedis = $toplam;
        $odeme->odenen_tutar    = $oncekiOdenen + $yeniOdenen;
        $odeme->kalan_bakiye    = $toplam - $odeme->odenen_tutar;
        $odeme->odeme_tarihi    = now()->toDateString();
        $odeme->odeme_yontemi   = $validated['odeme_yontemi'] ?? 'nakit';
        $odeme->notlar          = $validated['notlar'] ?? null;
        $odeme->kapandi         = $odeme->kalan_bakiye <= 0;
        $odeme->save();

        // ✅ Gider kaydı oluştur (işçi ödemesi olarak)
        $aylar = [
            1=>'Ocak',2=>'Şubat',3=>'Mart',4=>'Nisan',
            5=>'Mayıs',6=>'Haziran',7=>'Temmuz',8=>'Ağustos',
            9=>'Eylül',10=>'Ekim',11=>'Kasım',12=>'Aralık',
        ];
        $ayAdi = $aylar[$validated['ay']] ?? $validated['ay'];

        Gider::create([
            'usta_id'       => $usta->id,
            'tarih'         => now()->toDateString(),
            'tutar'         => $yeniOdenen,
            'aciklama'      => $usta->ad_soyad . ' — ' . $ayAdi . ' ' . $validated['yil'] . ' işçi ödemesi',
            'kategori'      => 'isci_odemesi',
            'odeme_yontemi' => $validated['odeme_yontemi'] ?? 'nakit',
        ]);

        // Redirect: profil sayfasından geldiyse profile dön
        $redirect_to = $request->get('redirect_to', 'aylik');
        if ($redirect_to === 'profil') {
            return redirect()->route('ustalar.show', $usta->id)
                ->with('success', number_format($yeniOdenen, 0, ',', '.') . '₺ ödeme kaydedildi ve giderlere işlendi.');
        }

        return redirect()->route('aylik-hesap.index', ['ay' => $validated['ay'], 'yil' => $validated['yil']])
            ->with('success', $usta->ad_soyad . ' için ' . number_format($yeniOdenen, 0, ',', '.') . '₺ ödeme kaydedildi.');
    }

    public function hesabiKapat(Request $request)
    {
        $request->validate([
            'usta_id' => 'required|exists:ustalar,id',
            'ay'      => 'required|integer|between:1,12',
            'yil'     => 'required|integer|min:2020',
        ]);

        Odeme::updateOrCreate(
            ['usta_id' => $request->usta_id, 'ay' => $request->ay, 'yil' => $request->yil],
            ['kapandi' => true, 'odeme_tarihi' => now()->toDateString()]
        );

        $redirect_to = $request->get('redirect_to', 'aylik');
        if ($redirect_to === 'profil') {
            $usta = Usta::findOrFail($request->usta_id);
            return redirect()->route('ustalar.show', $usta->id)
                ->with('success', 'Hesap kapatıldı.');
        }

        return back()->with('success', 'Hesap kapatıldı.');
    }

    /**
     * AJAX: Usta + ay/yıl hakedis bilgisi döndür (ödeme modali için)
     */
    public function hakedisJson(Request $request)
    {
        $request->validate([
            'usta_id' => 'required|exists:ustalar,id',
            'ay'      => 'required|integer|between:1,12',
            'yil'     => 'required|integer|min:2020',
        ]);

        $usta   = Usta::findOrFail($request->usta_id);
        $ay     = (int) $request->ay;
        $yil    = (int) $request->yil;

        $hakedis = $usta->aylikHakedis($ay, $yil);

        $odeme = Odeme::where('usta_id', $usta->id)
            ->where('ay', $ay)
            ->where('yil', $yil)
            ->first();

        $odenen = $odeme ? (float)$odeme->odenen_tutar : 0.0;
        $kalan  = $hakedis - $odenen;

        return response()->json([
            'hakedis' => $hakedis,
            'odenen'  => $odenen,
            'kalan'   => $kalan,
            'kapandi' => $odeme?->kapandi ?? false,
        ]);
    }

    /**
     * PDF çıktısı için usta hesap özeti
     */
    public function pdf(Request $request, Usta $usta)
    {
        $ay  = (int) $request->get('ay', now()->month);
        $yil = (int) $request->get('yil', now()->year);

        $aylar = [
            1=>'Ocak',2=>'Şubat',3=>'Mart',4=>'Nisan',
            5=>'Mayıs',6=>'Haziran',7=>'Temmuz',8=>'Ağustos',
            9=>'Eylül',10=>'Ekim',11=>'Kasım',12=>'Aralık',
        ];

        $kayitlar = $usta->devamKayitlari()
            ->with('ilgiliIs')
            ->whereMonth('tarih', $ay)
            ->whereYear('tarih', $yil)
            ->orderBy('tarih')
            ->get();

        $odeme       = Odeme::where('usta_id', $usta->id)->where('ay', $ay)->where('yil', $yil)->first();
        $hakedis     = $kayitlar->sum('hesaplanan_ucret');
        $tamGun      = $kayitlar->where('calisma_tipi', 'tam')->count();
        $yarimGun    = $kayitlar->where('calisma_tipi', 'yarim')->count();
        $mesaiSaati  = $kayitlar->where('calisma_tipi', 'mesai')->sum('mesai_saati');
        $odenenTutar = $odeme ? (float)$odeme->odenen_tutar : 0.0;
        $kalanBakiye = $hakedis - $odenenTutar;

        return view('ustalar.pdf', compact(
            'usta', 'kayitlar', 'ay', 'yil', 'aylar',
            'hakedis', 'tamGun', 'yarimGun', 'mesaiSaati',
            'odeme', 'odenenTutar', 'kalanBakiye'
        ));
    }
}
