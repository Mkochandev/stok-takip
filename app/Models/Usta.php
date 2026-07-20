<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usta extends Model
{
    protected $fillable = [
        'ad',
        'soyad',
        'telefon',
        'gunluk_ucret',
        'mesai_saatlik_ucret',
        'uzmanlik',
        'durum',
        'notlar',
    ];

    protected $casts = [
        'gunluk_ucret' => 'decimal:2',
        'mesai_saatlik_ucret' => 'decimal:2',
    ];

    public function devamKayitlari(): HasMany
    {
        return $this->hasMany(DevamKaydi::class);
    }

    public function odemeler(): HasMany
    {
        return $this->hasMany(Odeme::class);
    }

    public function getAdSoyadAttribute(): string
    {
        return $this->ad . ' ' . $this->soyad;
    }

    /**
     * Belirtilen ay/yıl için toplam hakedişi hesapla
     */
    public function aylikHakedis(int $ay, int $yil): float
    {
        return $this->devamKayitlari()
            ->whereMonth('tarih', $ay)
            ->whereYear('tarih', $yil)
            ->sum('hesaplanan_ucret');
    }

    /**
     * Belirtilen ay/yıl için toplam çalışılan gün sayısı (tam gün cinsinden)
     */
    public function aylikCalismaGunu(int $ay, int $yil): float
    {
        $kayitlar = $this->devamKayitlari()
            ->whereMonth('tarih', $ay)
            ->whereYear('tarih', $yil)
            ->get();

        $toplam = 0;
        foreach ($kayitlar as $k) {
            if ($k->calisma_tipi === 'tam') $toplam += 1;
            elseif ($k->calisma_tipi === 'yarim') $toplam += 0.5;
            elseif ($k->calisma_tipi === 'mesai') $toplam += ($k->mesai_saati / 8);
        }
        return $toplam;
    }
}
