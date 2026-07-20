<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ustalar', function (Blueprint $table) {
            $table->id();
            $table->string('ad');
            $table->string('soyad');
            $table->string('telefon')->nullable();
            $table->decimal('gunluk_ucret', 10, 2)->default(0);
            $table->decimal('mesai_saatlik_ucret', 10, 2)->default(0);
            $table->string('uzmanlik')->nullable(); // örn: elektrikçi, sıvacı
            $table->enum('durum', ['aktif', 'pasif'])->default('aktif');
            $table->text('notlar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ustalar');
    }
};
