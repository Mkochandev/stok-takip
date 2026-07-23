<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Usta;
use App\Models\Is;
use App\Models\DevamKaydi;
use App\Models\Gelir;
use App\Models\Gider;
use App\Models\Odeme;
use Illuminate\Console\Command;

class CleanAdminDataCommand extends Command
{
    protected $signature = 'clean:admin-data';
    protected $description = 'Admin kullanıcılarına ait tüm usta, iş, devam, gelir, gider ve ödeme verilerini temizler.';

    public function handle()
    {
        $adminIds = User::where('is_admin', true)->pluck('id');

        if ($adminIds->isEmpty()) {
            $this->info('Admin kullanıcısı bulunamadı.');
            return 0;
        }

        $devamCount = DevamKaydi::withoutGlobalScopes()->whereIn('user_id', $adminIds)->delete();
        $odemeCount = Odeme::withoutGlobalScopes()->whereIn('user_id', $adminIds)->delete();
        $gelirCount = Gelir::withoutGlobalScopes()->whereIn('user_id', $adminIds)->delete();
        $giderCount = Gider::withoutGlobalScopes()->whereIn('user_id', $adminIds)->delete();
        $isCount = Is::withoutGlobalScopes()->whereIn('user_id', $adminIds)->delete();
        $ustaCount = Usta::withoutGlobalScopes()->whereIn('user_id', $adminIds)->delete();

        $this->info("Admin kullanıcılarına ait tüm iş takibi verileri silindi:");
        $this->line("- Devam Kayıtları: {$devamCount}");
        $this->line("- Ödemeler: {$odemeCount}");
        $this->line("- Gelirler: {$gelirCount}");
        $this->line("- Giderler: {$giderCount}");
        $this->line("- İşler: {$isCount}");
        $this->line("- Ustalar: {$ustaCount}");

        return 0;
    }
}
