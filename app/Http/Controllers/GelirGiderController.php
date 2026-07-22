<?php

namespace App\Http\Controllers;

use App\Models\Gelir;
use App\Models\Gider;
use App\Models\Is;
use Illuminate\Http\Request;

class GelirGiderController extends Controller
{
    public function index(Request $request)
    {
        $ay   = $request->get('ay', now()->month);
        $yil  = $request->get('yil', now()->year);
        $tip  = $request->get('tip', 'tumu'); // tumu, gelir, gider

        $gelirler = collect();
        $giderler = collect();

        if ($tip === 'tumu' || $tip === 'gelir') {
            $gelirler = Gelir::with('ilgiliIs')
                ->whereMonth('tarih', $ay)
                ->whereYear('tarih', $yil)
                ->orderByDesc('tarih')
                ->get();
        }

        if ($tip === 'tumu' || $tip === 'gider') {
            $giderler = Gider::with(['ilgiliIs', 'usta'])
                ->whereMonth('tarih', $ay)
                ->whereYear('tarih', $yil)
                ->orderByDesc('tarih')
                ->get();
        }

        $toplamGelir = $gelirler->sum('tutar');
        $toplamGider = $giderler->sum('tutar');
        $netBakiye   = $toplamGelir - $toplamGider;

        $yillar = range(now()->year, now()->year - 3);
        $aylar = [
            1=>'Ocak',2=>'Şubat',3=>'Mart',4=>'Nisan',
            5=>'Mayıs',6=>'Haziran',7=>'Temmuz',8=>'Ağustos',
            9=>'Eylül',10=>'Ekim',11=>'Kasım',12=>'Aralık',
        ];

        return view('gelir-gider.index', compact(
            'gelirler', 'giderler', 'toplamGelir', 'toplamGider', 'netBakiye',
            'ay', 'yil', 'tip', 'yillar', 'aylar'
        ));
    }

    public function createGelir()
    {
        $isler = Is::orderBy('is_adi')->get();
        return view('gelir-gider.create-gelir', compact('isler'));
    }

    public function storeGelir(Request $request)
    {
        $validated = $request->validate([
            'is_id'        => 'nullable|exists:isler,id',
            'tarih'        => 'required|date',
            'tutar'        => 'required|numeric|min:0.01',
            'aciklama'     => 'required|string|max:500',
            'kategori'     => 'in:hakedis,avans,fatura,diger',
            'odeme_yontemi'=> 'nullable|string|max:50',
        ]);

        Gelir::create($validated);

        return redirect()->route('gelir-gider.index')
            ->with('success', 'Gelir kaydedildi.');
    }

    public function createGider()
    {
        $isler = Is::orderBy('is_adi')->get();
        return view('gelir-gider.create-gider', compact('isler'));
    }

    public function storeGider(Request $request)
    {
        $validated = $request->validate([
            'is_id'        => 'nullable|exists:isler,id',
            'tarih'        => 'required|date',
            'tutar'        => 'required|numeric|min:0.01',
            'aciklama'     => 'required|string|max:500',
            'kategori'     => 'in:malzeme,alet_ekipman,nakil,yakıt,kira,vergi,diger',
            'odeme_yontemi'=> 'nullable|string|max:50',
        ]);

        Gider::create($validated);

        return redirect()->route('gelir-gider.index')
            ->with('success', 'Gider kaydedildi.');
    }

    public function destroyGelir(Gelir $gelir)
    {
        $gelir->delete();
        return back()->with('success', 'Gelir kaydı silindi.');
    }

    public function destroyGider(Gider $gider)
    {
        $gider->delete();
        return back()->with('success', 'Gider kaydı silindi.');
    }
}
