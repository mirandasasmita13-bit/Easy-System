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

        // 2. ADMIN KEDUA — TAMBAHAN
        User::firstOrCreate(
            ['username' => 'icanbbn'], // pastikan username beda
            [
                'name'                    => 'Ica',
                'password'                => Hash::make('Gayo122#'), 
                'role'                    => 'admin',
                'status'                  => 'aktif',
                'jatah_cuti_tahunan'      => 12,
                'cuti_tahunan_sebelumnya' => 0,
                'tahun_cuti'              => now()->year,
            ]
        );

        // 3. ADMIN KETIGA — TAMBAHAN
        User::firstOrCreate(
            ['username' => 'fachri.irvanto'], 
            [
                'name'                    => 'Fachri',
                'password'                => Hash::make('fachri1010#'), 
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
                    'name'                    => 'Pegawai Test',
                    'password'                => Hash::make('pegawai12345'),
                    'role'                    => 'pegawai',
                    'status'                  => 'aktif',
                    'jatah_cuti_tahunan'      => 12,
                    'cuti_tahunan_sebelumnya' => 0,
                    'tahun_cuti'              => now()->year,
                ]
            );
        }
    }
}