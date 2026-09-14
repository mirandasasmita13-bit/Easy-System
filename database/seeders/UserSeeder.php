<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'                   => 'Admin Easy System',
            'username'               => 'admin',
            'password'               => Hash::make('admin12345'),
            'role'                   => 'admin',
            'status'                 => 'aktif',
            'jatah_cuti_tahunan'     => 12,
            'cuti_tahunan_sebelumnya'=> 0,
            'tahun_cuti'             => 2026,
        ]);

        User::create([
            'name'                   => 'Pegawai Testing',
            'username'               => 'pegawai',
            'password'               => Hash::make('pegawai12345'),
            'role'                   => 'pegawai',
            'status'                 => 'aktif',
            'jatah_cuti_tahunan'     => 12,
            'cuti_tahunan_sebelumnya'=> 0,
            'tahun_cuti'             => 2026,
        ]);

        User::create([
            'name'                   => 'PPNPN Testing',
            'username'               => 'ppnpn',
            'password'               => Hash::make('ppnpn12345'),
            'role'                   => 'ppnpn',
            'status'                 => 'aktif',
            'jatah_cuti_tahunan'     => 12,
            'cuti_tahunan_sebelumnya'=> 0,
            'tahun_cuti'             => 2026,
        ]);
    }
}