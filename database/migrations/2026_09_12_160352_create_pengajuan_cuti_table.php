<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('pengajuan_cuti', function (Blueprint $table) {

            $table->id();

            // PPNPN yang mencatat cuti
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Jenis cuti
            $table->enum('jenis_cuti', [
                'tahunan',
                'alasan_penting',
                'tambahan',
            ]);

            // Periode cuti
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            // Jumlah hari kerja
            $table->unsignedInteger('jumlah_hari');

            // Keterangan
            $table->text('keterangan')->nullable();

            // File surat cuti
            $table->string('surat');

            // Nama asli file
            $table->string('nama_surat')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cuti');
    }
};