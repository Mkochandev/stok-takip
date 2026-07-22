<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('isler', function (Blueprint $table) {
            $table->string('isveren_no', 50)->nullable()->after('musteri_adi')
                ->comment('İşveren sicil numarası / vergi no');
        });
    }

    public function down(): void
    {
        Schema::table('isler', function (Blueprint $table) {
            $table->dropColumn('isveren_no');
        });
    }
};
