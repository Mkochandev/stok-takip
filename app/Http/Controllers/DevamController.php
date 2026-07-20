<?php

namespace App\Http\Controllers;

use App\Models\DevamKaydi;
use App\Models\Is;
use App\Models\Usta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DevamController extends Controller
{
    public function index(Request $request)
    {
        $tarih = $request->get('tarih', now()->toDateString());
        $carbonTarih = Carbon::parse($tarih);

        $ustalar = Usta::where('durum', 'aktif')->orderBy('ad')->get();
        $isler = Is::where('durum', 'devam_ediyor')->orderBy('is_adi')->get();

        // O günkü kayıtlar
        $gunKayitlari = DevamKaydi::with(['usta', 'is'])
            ->whereDate('tarih', $tarih)
            ->get()
            ->keyBy('usta_id');

        // O ay toplam çalışma özeti
        $ayOzeti = DevamKaydi::with('usta')
            ->whereMonth('tarih', $carbonTarih->month)
            ->whereYear('tarih', $carbonTarih->year)
            ->selectRaw('usta_id, COUNT(*) as gun_sayisi, SUM(hesaplanan_ucret) as toplam_ucret')
            ->groupBy('usta_id')
            ->get();

        return view('devam.index', compact(
            'tarih',
            'carbonTarih',
            'ustalar',
            'isler',
            'gunKayitlari',
            'ayOzeti',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tarih'        => 'required|date',
            'kayitlar'     => 'required|array',
            'kayitlar.*.usta_id'       => 'required|exists:ustalar,id',
            'kayitlar.*.calisma_tipi'  => 'required|in:tam,yarim,mesai',
            'kayitlar.*.is_id'         => 'nullable|exists:isler,id',
            'kayitlar.*.mesai_saati'   => 'nullable|numeric|min:0.5|max:24',
            'kayitlar.*.notlar'        => 'nullable|string',
        ]);

        $tarih = $request->tarih;

        foreach ($request->kayitlar as $kayit) {
            $usta = Usta::findOrFail($kayit['usta_id']);
            $ucret = DevamKaydi::hesaplaUcret(
                $usta,
                $kayit['calisma_tipi'],
                isset($kayit['mesai_saati']) ? (float)$kayit['mesai_saati'] : null
            );

            DevamKaydi::updateOrCreate(
                ['usta_id' => $kayit['usta_id'], 'tarih' => $tarih],
                [
                    'is_id'           => $kayit['is_id'] ?? null,
                    'calisma_tipi'    => $kayit['calisma_tipi'],
                    'mesai_saati'     => $kayit['calisma_tipi'] === 'mesai' ? ($kayit['mesai_saati'] ?? null) : null,
                    'hesaplanan_ucret'=> $ucret,
                    'notlar'          => $kayit['notlar'] ?? null,
                ]
            );
        }

        return redirect()->route('devam.index', ['tarih' => $tarih])
            ->with('success', 'Devam kayıtları kaydedildi.');
    }

    public function destroy(DevamKaydi $devam)
    {
        $tarih = $devam->tarih->toDateString();
        $devam->delete();
        return redirect()->route('devam.index', ['tarih' => $tarih])
            ->with('success', 'Kayıt silindi.');
    }

    /**
     * AJAX: belirli bir usta için devam kaydı sil
     */
    public function destroyByUstaAndTarih(Request $request)
    {
        $request->validate([
            'usta_id' => 'required|exists:ustalar,id',
            'tarih'   => 'required|date',
        ]);

        DevamKaydi::where('usta_id', $request->usta_id)
            ->whereDate('tarih', $request->tarih)
            ->delete();

        return response()->json(['success' => true]);
    }
}
