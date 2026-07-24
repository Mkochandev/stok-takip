<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    use HasFactory;

    protected $table = 'contact_requests';

    protected $fillable = [
        'type',
        'name',
        'company_name',
        'email',
        'phone',
        'package_name',
        'message',
        'status',
        'ip_address',
    ];

    /**
     * Get badge color class according to status.
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'yeni'        => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
            'arandi'      => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
            'beklemede'   => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
            'uye_yapildi' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
            'iptal'       => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
            default       => 'bg-slate-500/20 text-slate-400 border-slate-500/30',
        };
    }

    /**
     * Get human readable Turkish status label.
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'yeni'        => 'Yeni Talep',
            'arandi'      => 'Görüşüldü',
            'beklemede'   => 'Düşünüyor',
            'uye_yapildi' => 'Müşteri Oldu',
            'iptal'       => 'İptal / İlgilenmiyor',
            default       => ucfirst($this->status),
        };
    }
}
