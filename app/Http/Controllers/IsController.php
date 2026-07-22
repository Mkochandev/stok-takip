<?php

namespace App\Http\Controllers;

use App\Models\Is;
use Illuminate\Http\Request;

class IsController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');

        $isler = Is::withCount('devamKayitlari')
            ->withSum('gelirler', 'tutar')
            ->withSum('giderler', 'tutar')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q2) use ($query) {
                    $q2->where('is_adi', 'like', '%' . $query . '%')
                       ->orWhere('musteri_adi', 'like', '%' . $query . '%')
                       ->orWhere('isveren_no', 'like', '%' . $query . '%')
                       ->orWhere('adres', 'like', '%' . $query . '%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('isler.index', compact('isler', 'query'));
    }

    public function create()
    {
        return view('isler.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'is_adi'          => 'required|string|max:200',
            'musteri_adi'     => 'nullable|string|max:200',
            'isveren_no'      => 'nullable|string|max:50',
            'adres'           => 'nullable|string|max:500',
            'baslangic_tarihi'=> 'nullable|date',
            'bitis_tarihi'    => 'nullable|date|after_or_equal:baslangic_tarihi',
            'durum'           => 'in:devam_ediyor,tamamlandi,iptal',
            'sozlesme_tutari' => 'nullable|numeric|min:0',
            'notlar'          => 'nullable|string',
        ]);

        Is::create($validated);

        return redirect()->route('isler.index')
            ->with('success', 'İş kaydı oluşturuldu.');
    }

    public function show(Is $is)
    {
        $is->load(['devamKayitlari.usta', 'gelirler', 'giderler']);
        $toplamGelir = $is->gelirler->sum('tutar');
        $toplamGider = $is->giderler->sum('tutar');
        $netKar = $toplamGelir - $toplamGider;

        return view('isler.show', compact('is', 'toplamGelir', 'toplamGider', 'netKar'));
    }

    public function edit(Is $is)
    {
        return view('isler.edit', compact('is'));
    }

    public function update(Request $request, Is $is)
    {
        $validated = $request->validate([
            'is_adi'          => 'required|string|max:200',
            'musteri_adi'     => 'nullable|string|max:200',
            'isveren_no'      => 'nullable|string|max:50',
            'adres'           => 'nullable|string|max:500',
            'baslangic_tarihi'=> 'nullable|date',
            'bitis_tarihi'    => 'nullable|date|after_or_equal:baslangic_tarihi',
            'durum'           => 'in:devam_ediyor,tamamlandi,iptal',
            'sozlesme_tutari' => 'nullable|numeric|min:0',
            'notlar'          => 'nullable|string',
        ]);

        $is->update($validated);

        return redirect()->route('isler.show', $is->id)
            ->with('success', 'İş bilgileri güncellendi.');
    }

    public function destroy(Is $is)
    {
        $is->delete();
        return redirect()->route('isler.index')
            ->with('success', 'İş kaydı silindi.');
    }
}
