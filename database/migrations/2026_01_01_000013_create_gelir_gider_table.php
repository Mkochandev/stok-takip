<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gelirler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('is_id')->nullable()->constrained('isler')->nullOnDelete();
            $table->date('tarih');
            $table->decimal('tutar', 12, 2);
            $table->string('aciklama');
            $table->enum('kategori', ['hakedis', 'avans', 'fatura', 'diger'])->default('hakedis');
            $table->string('odeme_yontemi')->nullable(); // nakit, havale, çek
            $table->timestamps();
        });

        Schema::create('giderler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('is_id')->nullable()->constrained('isler')->nullOnDelete();
            $table->date('tarih');
            $table->decimal('tutar', 12, 2);
            $table->string('aciklama');
            $table->enum('kategori', ['malzeme', 'alet_ekipman', 'nakil', 'yakıt', 'kira', 'vergi', 'diger'])->default('diger');
            $table->string('odeme_yontemi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giderler');
        Schema::dropIfExists('gelirler');
    }
};
