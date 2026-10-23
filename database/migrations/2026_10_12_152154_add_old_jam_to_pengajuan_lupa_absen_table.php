<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_lupa_absen', function (Blueprint $table) {
            // Simpan jam sebelum di-approve — buat restore saat cancel
            $table->time('old_jam')->nullable()->after('jam');

            // Flag — biar tau apakah approve sudah pernah update absensis
            $table->boolean('sudah_update_absensi')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_lupa_absen', function (Blueprint $table) {
            $table->dropColumn(['old_jam', 'sudah_update_absensi']);
        });
    }
};