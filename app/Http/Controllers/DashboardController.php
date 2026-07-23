<?php

namespace App\Http\Controllers;

use App\Models\DevamKaydi;
use App\Models\Gelir;
use App\Models\Gider;
use App\Models\Is;
use App\Models\Usta;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $buAy = now()->month;
        $buYil = now()->year;

        // Özet kartlar
        $aktifUstaSayisi = Usta::where('durum', 'aktif')->count();
        $aktifIsSayisi = Is::where('durum', 'devam_ediyor')->count();

        $buAyGelir = Gelir::whereMonth('tarih', $buAy)->whereYear('tarih', $buYil)->sum('tutar');
        $buAyGider = Gider::whereMonth('tarih', $buAy)->whereYear('tarih', $buYil)->sum('tutar');
        $buAyNet = $buAyGelir - $buAyGider;

        $buAyIsciMaliyeti = DevamKaydi::whereMonth('tarih', $buAy)
            ->whereYear('tarih', $buYil)
            ->sum('hesaplanan_ucret');

        // Bu ay çalışma günleri (toplam gün sayısı)
        $buAyCalismaGunu = DevamKaydi::whereMonth('tarih', $buAy)
            ->whereYear('tarih', $buYil)
            ->count();

        // Son 6 ay gelir/gider grafiği
        $aylikGrafikVeri = [];
        for ($i = 5; $i >= 0; $i--) {
            $tarih = Carbon::now()->subMonths($i);
            $aylikGrafikVeri[] = [
                'ay'    => $tarih->locale('tr')->isoFormat('MMM YYYY'),
                'gelir' => Gelir::whereMonth('tarih', $tarih->month)->whereYear('tarih', $tarih->year)->sum('tutar'),
                'gider' => Gider::whereMonth('tarih', $tarih->month)->whereYear('tarih', $tarih->year)->sum('tutar'),
            ];
        }

        // Bu ay en çok çalışan ustalar
        $enCokCalisan = DevamKaydi::with('usta')
            ->whereMonth('tarih', $buAy)
            ->whereYear('tarih', $buYil)
            ->selectRaw('usta_id, SUM(hesaplanan_ucret) as toplam_ucret, COUNT(*) as gun_sayisi')
            ->groupBy('usta_id')
            ->orderByDesc('toplam_ucret')
            ->limit(5)
            ->get();

        // Son devam kayıtları
        $sonKayitlar = DevamKaydi::with(['usta', 'ilgiliIs'])
            ->orderByDesc('tarih')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'aktifUstaSayisi',
            'aktifIsSayisi',
            'buAyGelir',
            'buAyGider',
            'buAyNet',
            'buAyIsciMaliyeti',
            'buAyCalismaGunu',
            'aylikGrafikVeri',
            'enCokCalisan',
            'sonKayitlar',
        ));
    }
}
