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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class SystemBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm sistem veritabanını ve kullanıcı kayıtlarını JSON olarak günlük yedekler.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sistem yedeği oluşturuluyor...');

        $data = [
            'yedek_bilgisi' => [
                'tarih'           => now()->toIso8601String(),
                'sistem'          => 'Gazi Ustam SaaS Admin Backup',
                'laravel_version' => app()->version(),
                'php_version'     => PHP_VERSION,
            ],
            'kullanicilar'    => User::all(),
            'ustalar'         => Usta::withoutGlobalScopes()->get(),
            'isler'           => Is::withoutGlobalScopes()->get(),
            'devam_kayitlari' => DevamKaydi::withoutGlobalScopes()->get(),
            'gelirler'        => Gelir::withoutGlobalScopes()->get(),
            'giderler'        => Gider::withoutGlobalScopes()->get(),
            'odemeler'        => Odeme::withoutGlobalScopes()->get(),
        ];

        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'sistem_yedek_' . now()->format('Y-m-d_H-i-s') . '.json';
        $fullPath = $backupDir . '/' . $filename;

        file_put_contents($fullPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("Yedek başarıyla oluşturuldu: {$filename}");
        return 0;
    }
}
