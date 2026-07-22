<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('isler', function (Blueprint $table) {
            $table->renameColumn('isveren_no', 'isveren_telefon');
        });
    }

    public function down(): void
    {
        Schema::table('isler', function (Blueprint $table) {
            $table->renameColumn('isveren_telefon', 'isveren_no');
        });
    }
};
