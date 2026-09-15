<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN PERTAMA — SELALU DIBUAT 
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name'                    => 'Admin Utama',
                'password'                => Hash::make('admin2525'),
                'role'                    => 'admin',
                'status'                  => 'aktif',
                'jatah_cuti_tahunan'      => 12,
                'cuti_tahunan_sebelumnya' => 0,
                'tahun_cuti'              => now()->year,
            ]
        );

        // 2. DUMMY USER — HANYA DI LOKAL
        if (app()->environment('local', 'testing')) {

            User::firstOrCreate(
                ['username' => 'pegawai'],
                [
                    'name'                    => 'Pegawai Testing',
                    'password'                => Hash::make('pegawai12345'),
                    'role'                    => 'pegawai',
                    'status'                  => 'aktif',
                    'jatah_cuti_tahunan'      => 12,
                    'cuti_tahunan_sebelumnya' => 0,
                    'tahun_cuti'              => now()->year,
                ]
            );

            User::firstOrCreate(
                ['username' => 'ppnpn'],
                [
                    'name'                    => 'PPNPN Testing',
                    'password'                => Hash::make('ppnpn12345'),
                    'role'                    => 'ppnpn',
                    'status'                  => 'aktif',
                    'jatah_cuti_tahunan'      => 12,
                    'cuti_tahunan_sebelumnya' => 0,
                    'tahun_cuti'              => now()->year,
                ]
            );
        }
    }
}