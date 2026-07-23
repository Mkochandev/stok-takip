<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. users tablosuna admin ve son geçerlilik tarihi ekleme
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('password');
            }
            if (!Schema::hasColumn('users', 'expires_at')) {
                $table->dateTime('expires_at')->nullable()->after('is_admin');
            }
        });

        // 2. Diğer tablolara user_id ekleme
        $tables = ['ustalar', 'isler', 'devam_kayitlari', 'gelirler', 'giderler', 'odemeler'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'user_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
                });
            }
        }

        // 3. Mevcut kayıtlara ilk kullanıcının ID'sini ata (varsa)
        $firstUserId = DB::table('users')->value('id');
        if ($firstUserId) {
            foreach ($tables as $tableName) {
                if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'user_id')) {
                    DB::table($tableName)->whereNull('user_id')->update(['user_id' => $firstUserId]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['ustalar', 'isler', 'devam_kayitlari', 'gelirler', 'giderler', 'odemeler'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'user_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                });
            }
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_admin')) {
                $table->dropColumn('is_admin');
            }
            if (Schema::hasColumn('users', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};
