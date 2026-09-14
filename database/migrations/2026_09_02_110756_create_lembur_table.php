<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembur', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tanggal');

            $table->time('jam_mulai');
            $table->time('jam_selesai');

            // POIN 7: Total jam lembur (dihitung otomatis)
            $table->decimal('total_jam', 5, 2)->nullable();

            $table->string('kegiatan');
            $table->text('keterangan')->nullable();
            $table->string('foto');

            // POIN 3a: Sistem approval lembur
            $table->enum('status_approval', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

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
        Schema::dropIfExists('lembur');
    }
};