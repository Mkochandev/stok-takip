<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('giderler', function (Blueprint $table) {
            // İşçi ödemesi kaydı için usta referansı
            $table->foreignId('usta_id')->nullable()->after('is_id')
                ->constrained('ustalar')->nullOnDelete();

            // Kategoriye işçi_odemesi ekle — mevcut ENUM'u değiştiriyoruz
            // MySQL'de ENUM değiştirmek için modify kullanıyoruz
            $table->string('kategori')->default('diger')->change();
        });
    }

    public function down(): void
    {
        Schema::table('giderler', function (Blueprint $table) {
            $table->dropForeign(['usta_id']);
            $table->dropColumn('usta_id');
            $table->enum('kategori', ['malzeme', 'alet_ekipman', 'nakil', 'yakıt', 'kira', 'vergi', 'diger'])
                ->default('diger')->change();
        });
    }
};
