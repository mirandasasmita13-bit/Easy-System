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

            // Pemilik absensi
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tanggal absensi
            $table->date('tanggal');

            // Shift kerja
            $table->string('shift')->nullable();

            // Jam masuk dan pulang
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();

            // Keterangan absensi
            // H, DL, CT, I, X, TL, IS
            $table->string('keterangan')->default('H');

            // Lokasi saat melakukan absensi
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Jarak user dari lokasi kantor (meter)
            $table->decimal('jarak', 8, 2)->nullable();
            
            // Ambil foto untuk absen
            $table->string('foto_masuk')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
    
};
