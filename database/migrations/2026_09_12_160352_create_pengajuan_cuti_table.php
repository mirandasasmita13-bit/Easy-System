<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_cuti', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('jenis_cuti', [
                'tahunan',
                'alasan_penting',
                'tambahan',
            ]);

            // Tanggal pengajuan (untuk validasi & arsip)
            $table->date('tanggal_pengajuan');

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->unsignedInteger('jumlah_hari');

            $table->text('keterangan')->nullable();

            // Surat hard copy yang sudah di-approve atasan
            $table->string('surat');
            $table->string('nama_surat')->nullable();

            // CATATAN: Tidak ada kolom approval di sini.
            // Cuti tahunan di-approve via hard copy,
            // upload = langsung tercatat sebagai cuti resmi.

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cuti');
    }
};