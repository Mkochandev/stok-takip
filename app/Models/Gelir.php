<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gelir extends Model
{
    protected $table = 'gelirler';

    protected $fillable = [
        'is_id',
        'tarih',
        'tutar',
        'aciklama',
        'kategori',
        'odeme_yontemi',
    ];

    protected $casts = [
        'tarih' => 'date',
        'tutar' => 'decimal:2',
    ];

    public function ilgiliIs(): BelongsTo
    {
        return $this->belongsTo(Is::class, 'is_id');
    }
}
