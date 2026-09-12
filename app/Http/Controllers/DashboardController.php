<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pengajuancuti;
use App\Models\Pengajuanlupaabsen;
use App\Models\Pengajuansurat;
use App\Models\Lembur;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | ADMIN DASHBOARD
        |--------------------------------------------------------------------------
        |
        | Sistem utama Easy System adalah untuk PPNPN.
        |
        | Role:
        | - admin   : mengelola sistem
        | - ppnpn   : pengguna utama sistem
        | - pegawai : hanya digunakan untuk fitur Cuti Tambahan
        |
        */

        if ($user->role === 'admin') {

            $hariIni = Carbon::today();

            /*
            |--------------------------------------------------------------------------
            | DATA PPNPN
            |--------------------------------------------------------------------------
            |
            | Semua aktivitas utama dashboard admin menggunakan data PPNPN.
            |
            */

            $ppnpn = User::where('role', 'ppnpn')
                ->orderBy('name')
                ->get();


            /*
            |--------------------------------------------------------------------------
            | TOTAL PPNPN
            |--------------------------------------------------------------------------
            */

            $totalPpnpn = $ppnpn->count();


            /*
            |--------------------------------------------------------------------------
            | ABSENSI PPNPN HARI INI
            |--------------------------------------------------------------------------
            */

            $hadirPpnpn = Absensi::with('user')
                ->whereDate('tanggal', $hariIni)
                ->whereNotNull('jam_masuk')
                ->whereHas('user', function ($query) {
                    $query->where('role', 'ppnpn');
                })
                ->get();


            /*
            |--------------------------------------------------------------------------
            | JUMLAH PPNPN YANG HADIR HARI INI
            |--------------------------------------------------------------------------
            */

            $hadirHariIni = $hadirPpnpn
                ->pluck('user_id')
                ->unique()
                ->count();


            /*
            |--------------------------------------------------------------------------
            | PPNPN YANG BELUM ABSEN
            |--------------------------------------------------------------------------
            */

            $userSudahAbsen = $hadirPpnpn
                ->pluck('user_id')
                ->unique();

            $belumAbsenPpnpn = $ppnpn
                ->whereNotIn('id', $userSudahAbsen)
                ->sortBy('name')
                ->values();


            /*
            |--------------------------------------------------------------------------
            | JUMLAH PPNPN YANG BELUM ABSEN
            |--------------------------------------------------------------------------
            */

            $belumAbsen = $belumAbsenPpnpn->count();


            /*
            |--------------------------------------------------------------------------
            | CUTI PPNPN HARI INI
            |--------------------------------------------------------------------------
            */

            $cutiHariIniData = Pengajuancuti::with('user')
                ->whereDate(
                    'tanggal_mulai',
                    '<=',
                    $hariIni
                )
                ->whereDate(
                    'tanggal_selesai',
                    '>=',
                    $hariIni
                )
                ->whereHas('user', function ($query) {
                    $query->where('role', 'ppnpn');
                })
                ->get();

            $cutiHariIni = $cutiHariIniData->count();


            /*
            |--------------------------------------------------------------------------
            | LEMBUR PPNPN HARI INI
            |--------------------------------------------------------------------------
            */

            $lemburHariIniData = Lembur::with('user')
                ->whereDate(
                    'tanggal',
                    $hariIni
                )
                ->whereHas('user', function ($query) {
                    $query->where('role', 'ppnpn');
                })
                ->get();

            $lemburHariIni = $lemburHariIniData->count();


            /*
            |--------------------------------------------------------------------------
            | TOTAL PEGAWAI
            |--------------------------------------------------------------------------
            |
            | Pegawai tetap tetap dihitung terpisah.
            | Role pegawai hanya digunakan untuk fitur Cuti Tambahan.
            |
            */

            $totalPegawaiTetap = User::where(
                'role',
                'pegawai'
            )->count();


            /*
            |--------------------------------------------------------------------------
            | PERIODE BULAN BERJALAN
            |--------------------------------------------------------------------------
            */

            $awalBulan = $hariIni->copy()->startOfMonth();
            $akhirBulan = $hariIni->copy()->endOfMonth();


            /*
            |--------------------------------------------------------------------------
            | CUTI PPNPN BULAN INI
            |--------------------------------------------------------------------------
            */

            $jumlahCuti = Pengajuancuti::whereHas('user', function ($query) {
                    $query->where('role', 'ppnpn');
                })
                ->whereDate(
                    'tanggal_mulai',
                    '<=',
                    $akhirBulan
                )
                ->whereDate(
                    'tanggal_selesai',
                    '>=',
                    $awalBulan
                )
                ->count();


            /*
            |--------------------------------------------------------------------------
            | LUPA ABSEN PPNPN BULAN INI
            |--------------------------------------------------------------------------
            */

            $jumlahLupaAbsen = Pengajuanlupaabsen::whereHas('user', function ($query) {
                    $query->where('role', 'ppnpn');
                })
                ->whereBetween(
                    'tanggal',
                    [
                        $awalBulan->toDateString(),
                        $akhirBulan->toDateString(),
                    ]
                )
                ->count();


            /*
            |--------------------------------------------------------------------------
            | SURAT PPNPN BULAN INI
            |--------------------------------------------------------------------------
            */

            $jumlahSurat = Pengajuansurat::whereHas('user', function ($query) {
                    $query->where('role', 'ppnpn');
                })
                ->whereBetween(
                    'tanggal',
                    [
                        $awalBulan->toDateString(),
                        $akhirBulan->toDateString(),
                    ]
                )
                ->count();


            /*
            |--------------------------------------------------------------------------
            | LEMBUR PPNPN BULAN INI
            |--------------------------------------------------------------------------
            */

            $jumlahLembur = Lembur::whereHas('user', function ($query) {
                    $query->where('role', 'ppnpn');
                })
                ->whereBetween(
                    'tanggal',
                    [
                        $awalBulan->toDateString(),
                        $akhirBulan->toDateString(),
                    ]
                )
                ->count();


            /*
            |--------------------------------------------------------------------------
            | CUTI ALASAN PENTING PPNPN BULAN INI
            |--------------------------------------------------------------------------
            */

            $jumlahCutiAlasanPentingBulanIni = Pengajuancuti::whereHas('user', function ($query) {
                    $query->where('role', 'ppnpn');
                })
                ->where(
                    'jenis_cuti',
                    'alasan_penting'
                )
                ->whereDate(
                    'tanggal_mulai',
                    '<=',
                    $akhirBulan
                )
                ->whereDate(
                    'tanggal_selesai',
                    '>=',
                    $awalBulan
                )
                ->count();


            /*
            |--------------------------------------------------------------------------
            | NAMA VARIABEL UNTUK STATISTIK DASHBOARD
            |--------------------------------------------------------------------------
            |
            | Nama ini mengikuti Blade dashboard yang sekarang.
            |
            */

            $jumlahCutiBulanIni = $jumlahCuti;

            $jumlahLupaAbsenBulanIni = $jumlahLupaAbsen;

            $jumlahLemburBulanIni = $jumlahLembur;


            /*
            |--------------------------------------------------------------------------
            | TOTAL AKTIVITAS PPNPN BULAN INI
            |--------------------------------------------------------------------------
            */

            $jumlahAktivitas =
                $jumlahCuti +
                $jumlahLupaAbsen +
                $jumlahSurat +
                $jumlahLembur;


            /*
            |--------------------------------------------------------------------------
            | ALIAS VARIABEL LAMA
            |--------------------------------------------------------------------------
            |
            | Ini sengaja dipertahankan supaya Blade lama yang masih
            | menggunakan nama "pegawai" tidak langsung error.
            |
            | Isinya tetap DATA PPNPN.
            |
            */

            $totalPegawai = $totalPpnpn;

            $hadirPegawai = $hadirPpnpn;

            $belumAbsenPegawai = $belumAbsenPpnpn;


            /*
            |--------------------------------------------------------------------------
            | DASHBOARD ADMIN
            |--------------------------------------------------------------------------
            */

            return view(
                'dashboardadmin.index',
                compact(
                    'ppnpn',

                    'totalPegawai',
                    'totalPpnpn',
                    'totalPegawaiTetap',

                    'hadirHariIni',
                    'hadirPpnpn',
                    'hadirPegawai',

                    'belumAbsen',
                    'belumAbsenPpnpn',
                    'belumAbsenPegawai',

                    'cutiHariIni',
                    'cutiHariIniData',

                    'lemburHariIni',
                    'lemburHariIniData',

                    'jumlahCutiBulanIni',
                    'jumlahLupaAbsenBulanIni',
                    'jumlahLemburBulanIni',
                    'jumlahCutiAlasanPentingBulanIni',

                    'jumlahAktivitas'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD USER / PPNPN / PEGAWAI
        |--------------------------------------------------------------------------
        |
        | Bagian ini tetap menggunakan user yang sedang login.
        |
        */


        /*
        |--------------------------------------------------------------------------
        | PERIODE BULAN INI
        |--------------------------------------------------------------------------
        */

        $awalBulan = Carbon::now()->startOfMonth();
        $akhirBulan = Carbon::now()->endOfMonth();


        /*
        |--------------------------------------------------------------------------
        | KEHADIRAN
        |--------------------------------------------------------------------------
        */

        $jumlahKehadiran = Absensi::where(
                'user_id',
                $user->id
            )
            ->whereBetween(
                'tanggal',
                [
                    $awalBulan->toDateString(),
                    $akhirBulan->toDateString(),
                ]
            )
            ->whereNotNull('jam_masuk')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | CUTI
        |--------------------------------------------------------------------------
        */

        $jumlahCuti = Pengajuancuti::where(
                'user_id',
                $user->id
            )
            ->where(function ($query) use (
                $awalBulan,
                $akhirBulan
            ) {
                $query
                    ->whereBetween(
                        'tanggal_mulai',
                        [
                            $awalBulan->toDateString(),
                            $akhirBulan->toDateString(),
                        ]
                    )
                    ->orWhereBetween(
                        'tanggal_selesai',
                        [
                            $awalBulan->toDateString(),
                            $akhirBulan->toDateString(),
                        ]
                    );
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | LUPA ABSEN
        |--------------------------------------------------------------------------
        */

        $jumlahLupaAbsen = Pengajuanlupaabsen::where(
                'user_id',
                $user->id
            )
            ->whereBetween(
                'tanggal',
                [
                    $awalBulan->toDateString(),
                    $akhirBulan->toDateString(),
                ]
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | SURAT
        |--------------------------------------------------------------------------
        */

        $jumlahSurat = Pengajuansurat::where(
                'user_id',
                $user->id
            )
            ->whereBetween(
                'tanggal',
                [
                    $awalBulan->toDateString(),
                    $akhirBulan->toDateString(),
                ]
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | LEMBUR
        |--------------------------------------------------------------------------
        */

        $jumlahLembur = Lembur::where(
                'user_id',
                $user->id
            )
            ->whereBetween(
                'tanggal',
                [
                    $awalBulan->toDateString(),
                    $akhirBulan->toDateString(),
                ]
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | TOTAL AKTIVITAS
        |--------------------------------------------------------------------------
        */

        $jumlahAktivitas =
            $jumlahCuti +
            $jumlahLupaAbsen +
            $jumlahSurat +
            $jumlahLembur;


        /*
        |--------------------------------------------------------------------------
        | SISA CUTI
        |--------------------------------------------------------------------------
        |
        | Hanya cuti tahunan yang mengurangi kuota 12 hari.
        |
        */

        $cutiTerpakai = Pengajuancuti::where(
                'user_id',
                $user->id
            )
            ->where(
                'jenis_cuti',
                'tahunan'
            )
            ->whereYear(
                'tanggal_mulai',
                now()->year
            )
            ->sum('jumlah_hari');


        $sisaCuti = max(
            0,
            12 - $cutiTerpakai
        );


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD PEGAWAI
        |--------------------------------------------------------------------------
        |
        | Pegawai hanya memiliki fitur khusus seperti Cuti Tambahan.
        |
        */

        if ($user->role === 'pegawai') {

            return view(
                'dashboardpegawai.index',
                compact(
                    'sisaCuti',
                    'jumlahKehadiran',
                    'jumlahAktivitas'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD PPNPN
        |--------------------------------------------------------------------------
        */

        return view(
            'dashboard.index',
            compact(
                'sisaCuti',
                'jumlahKehadiran',
                'jumlahAktivitas'
            )
        );
    }
}