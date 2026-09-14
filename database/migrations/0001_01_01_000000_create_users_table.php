<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            // POIN 1: Email diganti username (ID login)
            $table->string('username')->unique();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            $table->enum('role', [
                'admin',
                'ppnpn',
                'pegawai',
            ])->default('ppnpn');

            // POIN 6: Status aktif/nonaktif
            $table->enum('status', [
                'aktif',
                'nonaktif',
            ])->default('aktif');

            $table->date('tanggal_nonaktif')->nullable();

            // ============================================================
            // BARU: Manajemen cuti tahunan (untuk sistem yang mulai
            // di tengah tahun — admin bisa input cuti yang sudah terpakai)
            // ============================================================
            // Total jatah cuti tahunan per tahun (biasanya 12)
            $table->unsignedInteger('jatah_cuti_tahunan')->default(12);

            // Cuti yang sudah terpakai SEBELUM sistem ini berjalan
            // (diinput manual oleh admin saat setup awal)
            $table->unsignedInteger('cuti_tahunan_sebelumnya')->default(0);

            // Tahun berlaku (untuk auto-reset tiap Januari)
            $table->year('tahun_cuti')->default(2026);
            // ============================================================

            $table->rememberToken();

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('username')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent');
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};