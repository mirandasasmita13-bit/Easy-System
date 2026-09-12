<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profils', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Khusus PPNPN
            $table->string('nik')->nullable();

            // Khusus Pegawai
            $table->string('nip')->nullable();
            $table->string('pangkat_gol')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('unit_kerja')->nullable();

            // Semua pengguna
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profils');
    }
};