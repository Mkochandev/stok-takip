<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ustalar', function (Blueprint $table) {
            $table->string('iban', 34)->nullable()->after('telefon')
                ->comment('TR ile başlayan 26 karakter IBAN');
        });
    }

    public function down(): void
    {
        Schema::table('ustalar', function (Blueprint $table) {
            $table->dropColumn('iban');
        });
    }
};
