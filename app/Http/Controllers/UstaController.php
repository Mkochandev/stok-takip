<?php

namespace App\Http\Controllers;

use App\Models\Usta;
use App\Models\DevamKaydi;
use Illuminate\Http\Request;

class UstaController extends Controller
{
    public function index()
    {
        $ustalar = Usta::orderBy('ad')->paginate(20);
        return view('ustalar.index', compact('ustalar'));
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
            'gunluk_ucret'         => 'required|numeric|min:0',
            'mesai_saatlik_ucret'  => 'required|numeric|min:0',
            'uzmanlik'             => 'nullable|string|max:100',
            'durum'                => 'in:aktif,pasif',
            'notlar'               => 'nullable|string',
        ]);

        Usta::create($validated);

        return redirect()->route('ustalar.index')
            ->with('success', 'Usta başarıyla eklendi.');
    }

    public function show(Usta $usta)
    {
        $buAy = now()->month;
        $buYil = now()->year;

        $aylikKayitlar = $usta->devamKayitlari()
            ->with('is')
            ->whereMonth('tarih', $buAy)
            ->whereYear('tarih', $buYil)
            ->orderBy('tarih')
            ->get();

        $aylikHakedis = $usta->aylikHakedis($buAy, $buYil);
        $toplamOdenen = $usta->odemeler()->sum('odenen_tutar');
        $toplamHakedis = $usta->devamKayitlari()->sum('hesaplanan_ucret');

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
            'aylikKayitlar',
            'aylikHakedis',
            'toplamOdenen',
            'toplamHakedis',
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
            'gunluk_ucret'         => 'required|numeric|min:0',
            'mesai_saatlik_ucret'  => 'required|numeric|min:0',
            'uzmanlik'             => 'nullable|string|max:100',
            'durum'                => 'in:aktif,pasif',
            'notlar'               => 'nullable|string',
        ]);

        $usta->update($validated);

        return redirect()->route('ustalar.show', $usta)
            ->with('success', 'Usta bilgileri güncellendi.');
    }

    public function destroy(Usta $usta)
    {
        $usta->delete();
        return redirect()->route('ustalar.index')
            ->with('success', 'Usta silindi.');
    }
}
