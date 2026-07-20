<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Odeme extends Model
{
    protected $table = 'odemeler';

    protected $fillable = [
        'usta_id',
        'ay',
        'yil',
        'toplam_hakkedis',
        'odenen_tutar',
        'kalan_bakiye',
        'odeme_tarihi',
        'odeme_yontemi',
        'kapandi',
        'notlar',
    ];

    protected $casts = [
        'odeme_tarihi' => 'date',
        'toplam_hakkedis' => 'decimal:2',
        'odenen_tutar' => 'decimal:2',
        'kalan_bakiye' => 'decimal:2',
        'kapandi' => 'boolean',
    ];

    public function usta(): BelongsTo
    {
        return $this->belongsTo(Usta::class);
    }

    public function getAyAdAttribute(): string
    {
        $aylar = [
            1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
            5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
            9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık',
        ];
        return ($aylar[$this->ay] ?? '-') . ' ' . $this->yil;
    }
}
