<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengajuancuti;
use App\Models\Pengajuanlupaabsen;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // =====================================================
        // FILTER
        // =====================================================

        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);
        $ppnpnId = $request->get('ppnpn');


        // =====================================================
        // PERIODE
        // =====================================================

        $tanggalAwal = Carbon::create($tahun, $bulan, 1);
        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();


        // =====================================================
        // DATA PPNPN
        // =====================================================

        $ppnpn = User::with('profil')
            ->where('role', 'ppnpn')
            ->orderBy('name')
            ->get();


        // =====================================================
        // DATA CUTI
        // =====================================================

        $cutiQuery = Pengajuancuti::with('user.profil')
            ->whereHas('user', function ($query) {
                $query->where('role', 'ppnpn');
            })
            ->where(function ($query) use ($tanggalAwal, $tanggalAkhir) {

                $query
                    ->whereBetween('tanggal_mulai', [
                        $tanggalAwal->format('Y-m-d'),
                        $tanggalAkhir->format('Y-m-d'),
                    ])

                    ->orWhereBetween('tanggal_selesai', [
                        $tanggalAwal->format('Y-m-d'),
                        $tanggalAkhir->format('Y-m-d'),
                    ])

                    ->orWhere(function ($q) use ($tanggalAwal, $tanggalAkhir) {

                        $q->where(
                            'tanggal_mulai',
                            '<=',
                            $tanggalAwal->format('Y-m-d')
                        )

                        ->where(
                            'tanggal_selesai',
                            '>=',
                            $tanggalAkhir->format('Y-m-d')
                        );
                    });
            });


        if ($ppnpnId) {
            $cutiQuery->where('user_id', $ppnpnId);
        }


        $cuti = $cutiQuery
            ->latest('tanggal_mulai')
            ->get();


        // =====================================================
        // DATA LUPA ABSEN
        // =====================================================

        $lupaQuery = Pengajuanlupaabsen::with('user.profil')
            ->whereHas('user', function ($query) {
                $query->where('role', 'ppnpn');
            })
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ]);


        if ($ppnpnId) {
            $lupaQuery->where('user_id', $ppnpnId);
        }

        $lupaAbsen = $lupaQuery
            ->latest('tanggal')
            ->get();


        // =====================================================
        // DATA LEMBUR
        // =====================================================

        $lemburQuery = Lembur::with('user.profil')
            ->whereHas('user', function ($query) {
                $query->where('role', 'ppnpn');
            })
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ]);


        if ($ppnpnId) {
            $lemburQuery->where('user_id', $ppnpnId);
        }


        $lembur = $lemburQuery
            ->latest('tanggal')
            ->get();


        // =====================================================
        // RINGKASAN
        // =====================================================

        // CUTI TAHUNAN
        $jumlahCuti = $cuti
            ->where('jenis_cuti', 'tahunan')
            ->count();


        // CUTI ALASAN PENTING
        $jumlahCutiAlasanPenting = $cuti
            ->where('jenis_cuti', 'alasan_penting')
            ->count();


        // LUPA ABSEN
        $jumlahLupaAbsen = $lupaAbsen->count();


        // LEMBUR
        $jumlahLembur = $lembur->count();


        // =====================================================
        // AKTIVITAS SEMUA
        // =====================================================

        $semuaAktivitas = collect();


        // =====================================================
        // CUTI
        // =====================================================

        foreach ($cuti as $item) {

            $semuaAktivitas->push([
                'tanggal' => $item->tanggal_mulai,

                'ppnpn' => $item->user?->name ?? '-',

                'aktivitas' => match ($item->jenis_cuti) {

                'tambahan' => 'Cuti Tambahan',

                'alasan_penting' => 'Cuti Alasan Penting',

                    default => 'Cuti Tahunan',
                },

                'tipe' => 'cuti',

                'keterangan' => $item->keterangan ?? '-',

                // Bukti surat cuti
                'bukti' => $item->surat ?? null,

                // Nama asli file
                'nama_bukti' => $item->nama_surat
                    ?? ($item->surat
                        ? basename($item->surat)
                        : null),

                'tipe_bukti' => $item->surat
                    ? 'surat'
                    : null,
            ]);
        }


        // =====================================================
        // LUPA ABSEN
        // =====================================================

        foreach ($lupaAbsen as $item) {

            $semuaAktivitas->push([
                'tanggal' => $item->tanggal,

                'ppnpn' => $item->user?->name ?? '-',

                'aktivitas' => 'Lupa Absen',

                'tipe' => 'lupa',

                'keterangan' => $item->alasan ?? '-',

                // Bukti lupa absen
                'bukti' => $item->bukti ?? null,

                // Nama file bukti
                'nama_bukti' => $item->bukti
                    ? basename($item->bukti)
                    : null,

                'tipe_bukti' => $item->bukti
                    ? 'bukti'
                    : null,
            ]);
        }


        // =====================================================
        // LEMBUR
        // =====================================================

        foreach ($lembur as $item) {

            $semuaAktivitas->push([
                'tanggal' => $item->tanggal,

                'ppnpn' => $item->user?->name ?? '-',

                'aktivitas' => 'Lembur',

                'tipe' => 'lembur',

                'keterangan' => $item->kegiatan ?? '-',

                // Bukti foto lembur
                'bukti' => $item->foto ?? null,

                // Nama file foto
                'nama_bukti' => $item->foto
                    ? basename($item->foto)
                    : null,

                'tipe_bukti' => $item->foto
                    ? 'foto'
                    : null,
            ]);
        }


        // =====================================================
        // URUTKAN AKTIVITAS
        // =====================================================

        $semuaAktivitas = $semuaAktivitas
            ->sortByDesc(function ($item) {
                return $item['tanggal'];
            })
            ->values();


        // =====================================================
        // KIRIM KE VIEW
        // =====================================================

        return view('laporan.index', compact(
            'bulan',
            'tahun',
            'ppnpnId',
            'tanggalAwal',
            'tanggalAkhir',
            'ppnpn',
            'cuti',
            'lupaAbsen',
            'lembur',

            'jumlahCuti',
            'jumlahCutiAlasanPenting',
            'jumlahLupaAbsen',
            'jumlahLembur',
            'semuaAktivitas'
        ));
    }
}