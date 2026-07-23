<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 👑 Ana Admin Kullanıcısı
        $admin = User::firstOrCreate(
            ['email' => 'admin@gaziustam.com'],
            [
                'name'              => 'Gazi Usta Admin',
                'password'          => Hash::make('gazi1234'),
                'email_verified_at' => now(),
                'is_admin'          => true,
                'expires_at'        => null, // Admin için süre kısıtlaması yok (Süresiz)
            ]
        );

        if (!$admin->is_admin) {
            $admin->update([
                'is_admin'   => true,
                'expires_at' => null,
            ]);
        }

        // 👷 Örnek Müşteri (SaaS Üyesi) Hesabı
        User::firstOrCreate(
            ['email' => 'musteri@gaziustam.com'],
            [
                'name'              => 'Örnek Müşteri Usta',
                'password'          => Hash::make('gazi1234'),
                'email_verified_at' => now(),
                'is_admin'          => false,
                'expires_at'        => now()->addYear(), // 1 Yıllık aktif abonelik
            ]
        );
    }
}
