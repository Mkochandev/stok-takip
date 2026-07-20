<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin kullanıcısı
        User::firstOrCreate(
            ['email' => 'admin@gaziustam.com'],
            [
                'name'              => 'Gazi Usta',
                'password'          => Hash::make('gazi1234'),
                'email_verified_at' => now(),
            ]
        );
    }
}
