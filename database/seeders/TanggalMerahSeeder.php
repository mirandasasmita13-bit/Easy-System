<?php

namespace Database\Seeders;

use App\Models\TanggalMerah;
use Illuminate\Database\Seeder;

class TanggalMerahSeeder extends Seeder
{
    public function run(): void
    {
        // CONTOH tanggal merah 2026 — sesuaikan dengan SKB resmi
        $data = [
            ['tanggal' => '2026-01-01', 'keterangan' => 'Tahun Baru Masehi'],
            ['tanggal' => '2026-03-19', 'keterangan' => 'Hari Suci Nyepi'],
            ['tanggal' => '2026-05-01', 'keterangan' => 'Hari Buruh Internasional'],
            ['tanggal' => '2026-08-17', 'keterangan' => 'Hari Kemerdekaan RI'],
            ['tanggal' => '2026-12-25', 'keterangan' => 'Hari Raya Natal'],
            // tambahkan sisanya sesuai SKB resmi
        ];

        foreach ($data as $item) {
            TanggalMerah::updateOrCreate(
                ['tanggal' => $item['tanggal']],
                ['keterangan' => $item['keterangan']]
            );
        }
    }
}