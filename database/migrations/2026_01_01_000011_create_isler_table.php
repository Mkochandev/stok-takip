<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('isler', function (Blueprint $table) {
            $table->id();
            $table->string('is_adi');
            $table->string('musteri_adi')->nullable();
            $table->string('adres')->nullable();
            $table->date('baslangic_tarihi')->nullable();
            $table->date('bitis_tarihi')->nullable();
            $table->enum('durum', ['devam_ediyor', 'tamamlandi', 'iptal'])->default('devam_ediyor');
            $table->decimal('sozlesme_tutari', 12, 2)->nullable();
            $table->text('notlar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isler');
    }
};
