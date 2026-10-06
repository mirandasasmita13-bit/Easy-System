<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom pendaftaran_dibuka
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('pendaftaran_dibuka')->default(false)->after('status');
        });

        // 2. Ubah enum role → varchar (biar fleksibel, bisa nambah role apapun)
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'ppnpn'");
    }

    public function down(): void
    {
        // Balikin enum ke bentuk semula
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'ppnpn', 'pegawai') NOT NULL DEFAULT 'ppnpn'");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pendaftaran_dibuka');
        });
    }
};