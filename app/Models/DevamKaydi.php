<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevamKaydi extends Model
{
    protected $table = 'devam_kayitlari';

    protected $fillable = [
        'usta_id',
        'is_id',
        'tarih',
        'calisma_tipi',
        'mesai_saati',
        'hesaplanan_ucret',
        'notlar',
    ];

    protected $casts = [
        'tarih' => 'date',
        'mesai_saati' => 'decimal:2',
        'hesaplanan_ucret' => 'decimal:2',
    ];

    public function usta(): BelongsTo
    {
        return $this->belongsTo(Usta::class);
    }

    public function ilgiliIs(): BelongsTo
    {
        return $this->belongsTo(Is::class, 'is_id');
    }

    /**
     * Ücret hesaplama: usta ve çalışma tipine göre otomatik hesapla
     */
    public static function hesaplaUcret(Usta $usta, string $tip, ?float $mesaiSaati = null): float
    {
        return match ($tip) {
            'tam'   => (float) $usta->gunluk_ucret,
            'yarim' => (float) $usta->gunluk_ucret / 2,
            'mesai' => (float) $usta->mesai_saatlik_ucret * ($mesaiSaati ?? 0),
            default => 0,
        };
    }

    public function getCalismaEtiketAttribute(): string
    {
        return match ($this->calisma_tipi) {
            'tam'   => 'Tam Gün',
            'yarim' => 'Yarım Gün',
            'mesai' => 'Mesai (' . $this->mesai_saati . ' saat)',
            default => '-',
        };
    }
}
