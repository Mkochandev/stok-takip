<?php

namespace App\Http\Controllers;

use App\Models\DevamKaydi;
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

            return [
                'usta'           => $usta,
                'tam_gun'        => $tamGun,
                'yarim_gun'      => $yarimGun,
                'mesai_saati'    => $mesaiSaati,
                'toplam_hakedis' => $toplam,
                'odeme'          => $odeme,
                'kalan'          => $odeme ? $odeme->kalan_bakiye : $toplam,
                'kapandi'        => $odeme?->kapandi ?? false,
            ];
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

    public function odemeYap(Request $request)
    {
        $validated = $request->validate([
            'usta_id'       => 'required|exists:ustalar,id',
            'ay'            => 'required|integer|between:1,12',
            'yil'           => 'required|integer|min:2020',
            'odenen_tutar'  => 'required|numeric|min:0',
            'odeme_yontemi' => 'in:nakit,havale,çek,diger',
            'notlar'        => 'nullable|string',
        ]);

        $usta = Usta::findOrFail($validated['usta_id']);
        $toplam = $usta->aylikHakedis($validated['ay'], $validated['yil']);

        $odeme = Odeme::firstOrNew([
            'usta_id' => $validated['usta_id'],
            'ay'      => $validated['ay'],
            'yil'     => $validated['yil'],
        ]);

        $odeme->toplam_hakkedis = $toplam;
        $odeme->odenen_tutar   += (float)$validated['odenen_tutar'];
        $odeme->kalan_bakiye    = $toplam - $odeme->odenen_tutar;
        $odeme->odeme_tarihi    = now()->toDateString();
        $odeme->odeme_yontemi   = $validated['odeme_yontemi'] ?? 'nakit';
        $odeme->notlar          = $validated['notlar'] ?? null;
        $odeme->kapandi         = $odeme->kalan_bakiye <= 0;
        $odeme->save();

        return redirect()->route('aylik-hesap.index', ['ay' => $validated['ay'], 'yil' => $validated['yil']])
            ->with('success', $usta->ad . ' ' . $usta->soyad . ' için ödeme kaydedildi.');
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

        return back()->with('success', 'Hesap kapatıldı.');
    }
}
