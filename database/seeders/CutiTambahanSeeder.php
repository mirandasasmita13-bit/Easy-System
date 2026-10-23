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
        // Data SICT — pakai USERNAME (bukan nama)
        $data = [
            ['SICT-1',  'prisicilla', '2026-02-19', '2026-02-20'],
            ['SICT-2',  'fakhri',     '2026-02-18', '2026-02-18'],
            ['SICT-3',  'klarisa',    '2026-03-17', '2026-03-17'],
            ['SICT-4',  'jefrizal',   '2026-02-19', '2026-02-20'],
            ['SICT-5',  'aksha',      '2026-02-19', '2026-02-21'],
            ['SICT-6',  'klarisa',    '2026-02-25', '2026-02-26'],
            ['SICT-7',  'jefrizal',   '2026-03-17', '2026-03-17'],
            ['SICT-8',  'fakhri',     '2026-03-09', '2026-03-17'],
            ['SICT-9',  'prisicilla', '2026-06-18', '2026-06-19'],
            ['SICT-10', 'jefrizal',   '2026-06-08', '2026-06-11'],
            ['SICT-11', 'jefrizal',   '2026-08-03', '2026-08-06'],
            ['SICT-12', 'rianda',     '2026-08-05', '2026-08-14'],
            ['SICT-13', 'triyono',    '2026-08-27', '2026-08-28'],
            ['SICT-14', 'klarisa',    '2026-08-27', '2026-08-28'],
            ['SICT-15', 'fakhri',     '2026-09-16', '2026-09-18'],
            ['SICT-16', 'jefrizal',   '2026-10-06', '2026-10-07'],
            ['SICT-17', 'aksha',      '2026-10-28', '2026-10-30'],
        ];

        foreach ($data as $item) {
            [$nomorSict, $username, $mulai, $selesai] = $item;

            // MATCH BY USERNAME — dijamin unik
            $user = User::where('username', $username)
                ->where('role', 'pegawai')
                ->first();

            if (!$user) {
                $this->command->warn("User tidak ditemukan: {$username}");
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
                'status'            => 'aktif',
                'surat'             => '-',
                'nama_surat'        => null,
                'keterangan'        => 'Data dari Excel',
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