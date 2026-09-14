<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tanggal');
            $table->string('shift')->nullable();

            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();

            $table->string('keterangan')->default('H');

            // Untuk tandai merah kalau ada pengajuan lupa absen pending
            $table->enum('status_approval', [
                'normal',
                'pending',
            ])->default('normal');

            // ====== LOKASI ABSEN MASUK ======
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('jarak', 8, 2)->nullable();

            // ====== LOKASI ABSEN PULANG (BARU) ======
            // Dipisah supaya data lokasi masuk tidak tertimpa saat pulang
            $table->decimal('latitude_pulang', 10, 7)->nullable();
            $table->decimal('longitude_pulang', 10, 7)->nullable();
            $table->decimal('jarak_pulang', 8, 2)->nullable();

            $table->string('foto_masuk')->nullable();
            $table->string('foto_pulang')->nullable();

            $table->timestamps();

            // Unique per user + tanggal + shift
            // (karena 1 user bisa absen shift pagi DAN malam di hari yang sama)
            $table->unique(['user_id', 'tanggal', 'shift'], 'absensi_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};