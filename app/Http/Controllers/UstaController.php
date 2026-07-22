<?php

namespace App\Http\Controllers;

use App\Models\Usta;
use App\Models\DevamKaydi;
use App\Models\Odeme;
use Illuminate\Http\Request;

class UstaController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');

        $ustalar = Usta::when($query, function ($q) use ($query) {
                $q->where(function ($q2) use ($query) {
                    $q2->where('ad', 'like', '%' . $query . '%')
                       ->orWhere('soyad', 'like', '%' . $query . '%')
                       ->orWhere('uzmanlik', 'like', '%' . $query . '%')
                       ->orWhere('telefon', 'like', '%' . $query . '%');
                });
            })
            ->orderBy('ad')
            ->paginate(20)
            ->withQueryString();

        return view('ustalar.index', compact('ustalar', 'query'));
    }

    public function create()
    {
        return view('ustalar.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad'                   => 'required|string|max:100',
            'soyad'                => 'required|string|max:100',
            'telefon'              => 'nullable|string|max:20',
            'iban'                 => 'nullable|string|max:34|regex:/^TR[0-9]{24}$/',
            'gunluk_ucret'         => 'required|numeric|min:0',
            'mesai_saatlik_ucret'  => 'required|numeric|min:0',
            'uzmanlik'             => 'nullable|string|max:100',
            'durum'                => 'in:aktif,pasif',
            'notlar'               => 'nullable|string',
        ]);

        $usta = Usta::create($validated);

        return redirect()->route('ustalar.show', $usta->id)
            ->with('success', $usta->ad_soyad . ' başarıyla eklendi.');
    }

    public function show(Request $request, Usta $usta)
    {
        $aylar = [
            1=>'Ocak',2=>'Şubat',3=>'Mart',4=>'Nisan',
            5=>'Mayıs',6=>'Haziran',7=>'Temmuz',8=>'Ağustos',
            9=>'Eylül',10=>'Ekim',11=>'Kasım',12=>'Aralık',
        ];

        // Ay/yıl filtresi — request'ten ya da bu ay
        $seciliAy  = (int) $request->get('ay', now()->month);
        $seciliYil = (int) $request->get('yil', now()->year);

        $aylikKayitlar = $usta->devamKayitlari()
            ->with('ilgiliIs')
            ->whereMonth('tarih', $seciliAy)
            ->whereYear('tarih', $seciliYil)
            ->orderBy('tarih')
            ->get();

        $aylikHakedis  = $usta->aylikHakedis($seciliAy, $seciliYil);
        $toplamOdenen  = $usta->odemeler()->sum('odenen_tutar');
        $toplamHakedis = $usta->devamKayitlari()->sum('hesaplanan_ucret');

        // Fark: pozitif = biz ustaya borçluyuz, negatif = usta bize borçlu (fazla ödendi)
        $fark       = $toplamHakedis - $toplamOdenen;
        $toplamBorç = max(0, $fark);
        $fazlaOdeme = $fark < 0 ? abs($fark) : 0;

        // Tüm ödeme geçmişi
        $odemeler = $usta->odemeler()
            ->orderByDesc('yil')
            ->orderByDesc('ay')
            ->get();

        // Seçili ay ödeme durumu
        $buAyOdeme = Odeme::where('usta_id', $usta->id)
            ->where('ay', $seciliAy)
            ->where('yil', $seciliYil)
            ->first();

        // Son 6 ay hakedis grafiği
        $aylikGrafikVeri = [];
        for ($i = 5; $i >= 0; $i--) {
            $tarih = now()->subMonths($i);
            $aylikGrafikVeri[] = [
                'ay'      => $tarih->locale('tr')->isoFormat('MMM'),
                'hakedis' => $usta->aylikHakedis($tarih->month, $tarih->year),
            ];
        }

        return view('ustalar.show', compact(
            'usta',
            'aylar',
            'seciliAy',
            'seciliYil',
            'aylikKayitlar',
            'aylikHakedis',
            'toplamOdenen',
            'toplamHakedis',
            'toplamBorç',
            'fazlaOdeme',
            'odemeler',
            'buAyOdeme',
            'aylikGrafikVeri',
        ));
    }

    public function edit(Usta $usta)
    {
        return view('ustalar.edit', compact('usta'));
    }

    public function update(Request $request, Usta $usta)
    {
        $validated = $request->validate([
            'ad'                   => 'required|string|max:100',
            'soyad'                => 'required|string|max:100',
            'telefon'              => 'nullable|string|max:20',
            'iban'                 => 'nullable|string|max:34|regex:/^TR[0-9]{24}$/',
            'gunluk_ucret'         => 'required|numeric|min:0',
            'mesai_saatlik_ucret'  => 'required|numeric|min:0',
            'uzmanlik'             => 'nullable|string|max:100',
            'durum'                => 'in:aktif,pasif',
            'notlar'               => 'nullable|string',
        ]);

        $usta->update($validated);

        return redirect()->route('ustalar.show', $usta->id)
            ->with('success', 'Usta bilgileri güncellendi.');
    }

    public function destroy(Usta $usta)
    {
        $ad = $usta->ad_soyad;
        $usta->delete();
        return redirect()->route('ustalar.index')
            ->with('success', $ad . ' silindi.');
    }
}
