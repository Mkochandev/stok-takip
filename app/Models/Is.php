<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToUser;

class Is extends Model
{
    use BelongsToUser;

    protected $table = 'isler';

    protected $fillable = [
        'user_id',
        'is_adi',
        'musteri_adi',
        'isveren_telefon',
        'adres',
        'baslangic_tarihi',
        'bitis_tarihi',
        'durum',
        'sozlesme_tutari',
        'notlar',
    ];

    protected $casts = [
        'baslangic_tarihi' => 'date',
        'bitis_tarihi' => 'date',
        'sozlesme_tutari' => 'decimal:2',
    ];

    public function devamKayitlari(): HasMany
    {
        return $this->hasMany(DevamKaydi::class, 'is_id');
    }

    public function gelirler(): HasMany
    {
        return $this->hasMany(Gelir::class, 'is_id');
    }

    public function giderler(): HasMany
    {
        return $this->hasMany(Gider::class, 'is_id');
    }

    public function getDurumRenkAttribute(): string
    {
        return match ($this->durum) {
            'devam_ediyor' => 'success',
            'tamamlandi' => 'info',
            'iptal' => 'danger',
            default => 'secondary',
        };
    }

    public function getDurumEtiketAttribute(): string
    {
        return match ($this->durum) {
            'devam_ediyor' => 'Devam Ediyor',
            'tamamlandi' => 'Tamamlandı',
            'iptal' => 'İptal',
            default => 'Bilinmiyor',
        };
    }
}
