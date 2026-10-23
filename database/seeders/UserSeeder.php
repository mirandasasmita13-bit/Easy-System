<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN
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

        User::firstOrCreate(
            ['username' => 'icanbbn'],
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

        // Super admin
        User::firstOrCreate(
            ['username' => 'mrnda'],
            [
                'name'                    => 'Mirandaa Yeppo',
                'password'                => Hash::make('kodokzumaa'),
                'role'                    => 'admin',
                'status'                  => 'aktif',
                'jatah_cuti_tahunan'      => 12,
                'cuti_tahunan_sebelumnya' => 0,
                'tahun_cuti'              => now()->year,
            ]
        );


        // DATAPEGAWAI
        $pegawai = [
            ['username' => 'ulum',       'name' => 'Bachrul Ulum'],
            ['username' => 'shafiq',     'name' => 'Achmad Shafiq Bafadhal'],
            ['username' => 'jefrizal',   'name' => 'Jefrizal'],
            ['username' => 'triyono',    'name' => 'Triyono'],
            ['username' => 'harunsyah',  'name' => 'Harunsyah H. Galung'],
            ['username' => 'rianda',     'name' => 'Rianda Imanullah'],
            ['username' => 'fakhri',     'name' => 'Fakhri Irvanto'],
            ['username' => 'klarisa',    'name' => 'Klarisa Judhika Nathaniece Nababan'],
            ['username' => 'prisicilla', 'name' => 'Prisicilla Frederica Br Purba'],
            ['username' => 'aksha',      'name' => 'Aksha Mahdar Alwi'],
            ['username' => 'ghulam',     'name' => 'Ghulam Aly'],
        ];

        foreach ($pegawai as $data) {
            User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'name'                    => trim($data['name']),
                    'password'                => Hash::make($data['username'] . '123'),
                    'role'                    => 'pegawai',
                    'status'                  => 'aktif',
                    'jatah_cuti_tahunan'      => 12,
                    'cuti_tahunan_sebelumnya' => 0,
                    'tahun_cuti'              => now()->year,
                ]
            );
        }

        $this->command->info('✅ Berhasil update ' . (count($admin) + count($pegawai)) . ' akun.');


        // ============================================================
        // 3. DUMMY USER — HANYA DI LOKAL
        // ============================================================
        if (app()->environment('local', 'testing')) {
            User::updateOrCreate(
                ['username' => 'pegawai'],
                [
                    'name'                    => 'Pegawai Test',
                    'password'                => Hash::make('pegawai123'),
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