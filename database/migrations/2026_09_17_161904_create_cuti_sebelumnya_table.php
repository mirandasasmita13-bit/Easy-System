<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuti_sebelumnya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('jumlah_hari');

            $table->enum('jenis_cuti', ['tahunan', 'alasan_penting'])
                  ->default('tahunan');

            $table->text('alasan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'tanggal_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti_sebelumnya');
    }
};