<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odemeler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usta_id')->constrained('ustalar')->cascadeOnDelete();
            $table->tinyInteger('ay'); // 1-12
            $table->smallInteger('yil');
            $table->decimal('toplam_hakkedis', 12, 2)->default(0);
            $table->decimal('odenen_tutar', 12, 2)->default(0);
            $table->decimal('kalan_bakiye', 12, 2)->default(0);
            $table->date('odeme_tarihi')->nullable();
            $table->enum('odeme_yontemi', ['nakit', 'havale', 'çek', 'diger'])->default('nakit');
            $table->boolean('kapandi')->default(false);
            $table->text('notlar')->nullable();
            $table->timestamps();

            $table->unique(['usta_id', 'ay', 'yil']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odemeler');
    }
};
