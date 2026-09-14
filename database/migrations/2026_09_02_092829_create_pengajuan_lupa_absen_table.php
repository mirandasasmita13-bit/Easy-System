<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_lupa_absen', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // POIN 5: Link ke absensi yang mau diperbaiki
            $table->foreignId('absensi_id')
                ->nullable()
                ->constrained('absensis')
                ->nullOnDelete();

            $table->date('tanggal');

            $table->enum('jenis_absen', [
                'masuk',
                'pulang',
            ]);

            // Jam yang seharusnya (perbaikan)
            $table->time('jam');

            // Bukti (rekam CCTV, dll) — POIN 8
            $table->string('bukti')->nullable();

            $table->text('alasan');

            // POIN 3b & 5: Sistem approval
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            // Admin yang approve (5 admin concurrent — pakai lockForUpdate)
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->text('catatan_admin')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_lupa_absen');
    }
};