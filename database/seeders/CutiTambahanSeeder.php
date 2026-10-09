<?php

namespace Database\Seeders;

use App\Models\CutiTambahan;
use App\Models\TanggalMerah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CutiTambahanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['SICT-1',  'Prisicilla Frederica Br Purba',        '2026-02-19', '2026-02-20'],
            ['SICT-2',  'Fakhri Irvanto',                       '2026-02-18', '2026-02-18'],
            ['SICT-3',  'Klarisa Judhika Nathaniece Nababan',   '2026-03-17', '2026-03-17'],
            ['SICT-4',  'Jefrizal',                             '2026-02-19', '2026-02-20'],
            ['SICT-5',  'Aksha Mahdar Alwi',                    '2026-02-19', '2026-02-21'],
            ['SICT-6',  'Klarisa Judhika Nathaniece Nababan',   '2026-02-25', '2026-02-26'],
            ['SICT-7',  'Jefrizal',                             '2026-03-17', '2026-03-17'],
            ['SICT-8',  'Fakhri Irvanto',                       '2026-03-09', '2026-03-17'],
            ['SICT-9',  'Prisicilla Frederica Br Purba',        '2026-06-18', '2026-06-19'],
            ['SICT-10', 'Jefrizal',                             '2026-06-08', '2026-06-11'],
            ['SICT-11', 'Jefrizal',                             '2026-08-03', '2026-08-06'],
            ['SICT-12', 'Rianda Imanullah',                     '2026-08-05', '2026-08-14'],
            ['SICT-13', 'Triyono',                              '2026-08-27', '2026-08-28'],
            ['SICT-14', 'Klarisa Judhika Nathaniece Nababan',   '2026-08-27', '2026-08-28'],
        ];

        foreach ($data as $item) {
            [$nomorSict, $nama, $mulai, $selesai] = $item;

            // Cari user by name (case-insensitive)
            $user = User::whereRaw('LOWER(name) = ?', [strtolower($nama)])->first();

            if (!$user) {
                $this->command->warn("User tidak ditemukan: {$nama}");
                continue;
            }

            $jumlahHari = $this->hitungHariKerja($mulai, $selesai);

            CutiTambahan::create([
                'user_id'           => $user->id,
                'nomor_sict'        => $nomorSict,
                'tanggal_pengajuan' => $mulai,
                'tanggal_mulai'     => $mulai,
                'tanggal_selesai'   => $selesai,
                'jumlah_hari'       => $jumlahHari,
                'surat'             => '-',
                'nama_surat'        => null,
                'keterangan'        => 'Data lama dari Excel',
            ]);
        }
    }

    private function hitungHariKerja(string $mulai, string $selesai): int
    {
        $jumlah  = 0;
        $tanggal = Carbon::parse($mulai);
        $akhir   = Carbon::parse($selesai);

        while ($tanggal->lte($akhir)) {
            if (!$tanggal->isWeekend() && !TanggalMerah::isMerah($tanggal->format('Y-m-d'))) {
                $jumlah++;
            }
            $tanggal->addDay();
        }

        return $jumlah;
    }
}