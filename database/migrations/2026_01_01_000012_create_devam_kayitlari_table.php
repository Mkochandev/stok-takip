<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devam_kayitlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usta_id')->constrained('ustalar')->cascadeOnDelete();
            $table->foreignId('is_id')->nullable()->constrained('isler')->nullOnDelete();
            $table->date('tarih');
            $table->enum('calisma_tipi', ['tam', 'yarim', 'mesai'])->default('tam');
            $table->decimal('mesai_saati', 5, 2)->nullable()->comment('Sadece mesai tipinde kullanılır');
            $table->decimal('hesaplanan_ucret', 10, 2)->default(0)->comment('O anki ücrete göre hesaplanan tutar');
            $table->text('notlar')->nullable();
            $table->timestamps();

            // Aynı usta aynı gün iki kez girilemez
            $table->unique(['usta_id', 'tarih']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devam_kayitlari');
    }
};
