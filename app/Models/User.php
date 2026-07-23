<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Admin mi?
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Üyelik süresi dolmuş mu?
     */
    public function isExpired(): bool
    {
        if ($this->isAdmin()) {
            return false;
        }

        if (is_null($this->expires_at)) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    /**
     * Kalan gün sayısı
     */
    public function remainingDays(): ?int
    {
        if ($this->isAdmin() || is_null($this->expires_at)) {
            return null; // Süresiz
        }

        if ($this->expires_at->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->expires_at, false);
    }

    // İlişkiler (Yedekleme ve ilişkisel yönetim için)
    public function ustalar()
    {
        return $this->hasMany(Usta::class);
    }

    public function isler()
    {
        return $this->hasMany(Is::class);
    }

    public function devamKayitlari()
    {
        return $this->hasMany(DevamKaydi::class);
    }

    public function gelirler()
    {
        return $this->hasMany(Gelir::class);
    }

    public function giderler()
    {
        return $this->hasMany(Gider::class);
    }

    public function odemeler()
    {
        return $this->hasMany(Odeme::class);
    }
}
