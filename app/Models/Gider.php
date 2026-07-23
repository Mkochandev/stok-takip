<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToUser;

class Gider extends Model
{
    use BelongsToUser;

    protected $table = 'giderler';

    protected $fillable = [
        'user_id',
        'is_id',
        'usta_id',
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

    public function usta(): BelongsTo
    {
        return $this->belongsTo(Usta::class, 'usta_id');
    }

    /**
     * İşçi ödemesi mi?
     */
    public function isIsciOdemesi(): bool
    {
        return $this->kategori === 'isci_odemesi';
    }
}
