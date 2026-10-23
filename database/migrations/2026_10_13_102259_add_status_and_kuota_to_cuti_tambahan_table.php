<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuti_tambahan', function (Blueprint $table) {
            // Status: aktif / dibatalkan
            $table->enum('status', ['aktif', 'dibatalkan'])
                  ->default('aktif')
                  ->after('jumlah_hari');

            // Info pembatalan
            $table->timestamp('dibatalkan_at')->nullable()->after('status');
            $table->foreignId('dibatalkan_oleh')
                  ->nullable()
                  ->after('dibatalkan_at')
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('alasan_batal')->nullable()->after('dibatalkan_oleh');

            // Info edit (oleh admin)
            $table->timestamp('diedit_at')->nullable()->after('alasan_batal');
            $table->foreignId('diedit_oleh')
                  ->nullable()
                  ->after('diedit_at')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cuti_tambahan', function (Blueprint $table) {
            // Drop foreign key dulu, baru kolom
            $table->dropForeign(['dibatalkan_oleh']);
            $table->dropForeign(['diedit_oleh']);

            $table->dropColumn([
                'status',
                'dibatalkan_at',
                'dibatalkan_oleh',
                'alasan_batal',
                'diedit_at',
                'diedit_oleh',
            ]);
        });
    }
};