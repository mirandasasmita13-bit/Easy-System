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

        // DASHBOARD ADMIN
        if ($user->role === 'admin') {
            $hariIni = Carbon::today();

            // PPNPN aktif & nonaktif
            $ppnpnAktif = User::where('role', 'ppnpn')
                ->where('status', 'aktif')
                ->orderBy('name')
                ->get();

            $totalPpnpnAktif = $ppnpnAktif->count();

            $totalPpnpnNonaktif = User::where('role', 'ppnpn')
                ->where('status', 'nonaktif')
                ->count();

            $totalPpnpnSemua = $totalPpnpnAktif;

            // Absensi PPNPN aktif hari ini
            $hadirPpnpn = Absensi::with('user')
                ->whereDate('tanggal', $hariIni)
                ->whereNotNull('jam_masuk')
                ->whereHas('user', function ($q) {
                    $q->where('role', 'ppnpn')->where('status', 'aktif');
                })
                ->get();

            $hadirHariIni = $hadirPpnpn->pluck('user_id')->unique()->count();

            // PPNPN belum absen
            $userSudahAbsen = $hadirPpnpn->pluck('user_id')->unique();

            $belumAbsenPpnpn = $ppnpnAktif
                ->whereNotIn('id', $userSudahAbsen)
                ->sortBy('name')
                ->values();

            $belumAbsen = $belumAbsenPpnpn->count();

            // Cuti hari ini
            $cutiHariIniData = Pengajuancuti::with('user')
                ->whereDate('tanggal_mulai', '<=', $hariIni)
                ->whereDate('tanggal_selesai', '>=', $hariIni)
                ->whereHas('user', function ($q) {
                    $q->where('role', 'ppnpn')->where('status', 'aktif');
                })
                ->get();

            $cutiHariIni = $cutiHariIniData->count();

            // Lembur hari ini
            $lemburHariIniData = Lembur::with('user')
                ->whereDate('tanggal', $hariIni)
                ->whereHas('user', function ($q) {
                    $q->where('role', 'ppnpn')->where('status', 'aktif');
                })
                ->get();

            $lemburHariIni = $lemburHariIniData->count();

            // Total pegawai (untuk cuti tambahan)
            $totalPegawaiTetap = User::where('role', 'pegawai')->count();

            // Periode bulan ini
            $awalBulan = $hariIni->copy()->startOfMonth();
            $akhirBulan = $hariIni->copy()->endOfMonth();

            // Jumlah cuti bulan ini
            $jumlahCuti = Pengajuancuti::whereHas('user', function ($q) {
                    $q->where('role', 'ppnpn');
                })
                ->whereDate('tanggal_mulai', '<=', $akhirBulan)
                ->whereDate('tanggal_selesai', '>=', $awalBulan)
                ->count();

            // Jumlah lupa absen bulan ini
            $jumlahLupaAbsen = Pengajuanlupaabsen::whereHas('user', function ($q) {
                    $q->where('role', 'ppnpn');
                })
                ->whereBetween('tanggal', [
                    $awalBulan->toDateString(),
                    $akhirBulan->toDateString(),
                ])
                ->count();

            // Jumlah surat bulan ini
            $jumlahSurat = Pengajuansurat::whereHas('user', function ($q) {
                    $q->where('role', 'ppnpn');
                })
                ->whereBetween('tanggal', [
                    $awalBulan->toDateString(),
                    $akhirBulan->toDateString(),
                ])
                ->count();

            // Jumlah lembur bulan ini
            $jumlahLembur = Lembur::whereHas('user', function ($q) {
                    $q->where('role', 'ppnpn');
                })
                ->whereBetween('tanggal', [
                    $awalBulan->toDateString(),
                    $akhirBulan->toDateString(),
                ])
                ->count();

            // Jumlah cuti alasan penting bulan ini
            $jumlahCutiAlasanPentingBulanIni = Pengajuancuti::whereHas('user', function ($q) {
                    $q->where('role', 'ppnpn');
                })
                ->where('jenis_cuti', 'alasan_penting')
                ->whereDate('tanggal_mulai', '<=', $akhirBulan)
                ->whereDate('tanggal_selesai', '>=', $awalBulan)
                ->count();

            $jumlahAktivitas = $jumlahCuti + $jumlahLupaAbsen + $jumlahSurat + $jumlahLembur;

            // Approval pending
            $pendingLembur = Lembur::where('status_approval', 'pending')->count();
            $pendingLupaAbsen = Pengajuanlupaabsen::where('status', 'pending')->count();
            $totalPendingApproval = $pendingLembur + $pendingLupaAbsen;

            // Total jam lembur disetujui bulan ini
            $totalJamLemburBulanIni = Lembur::where('status_approval', 'approved')
                ->whereBetween('tanggal', [
                    $awalBulan->toDateString(),
                    $akhirBulan->toDateString(),
                ])
                ->sum('total_jam');

            // Cek tahun cuti usang
            $perluResetCuti = User::where('role', 'ppnpn')
                ->where('status', 'aktif')
                ->where('tahun_cuti', '<', now()->year)
                ->exists();

            // Alias lama (kompatibel dengan blade)
            $ppnpn = $ppnpnAktif;
            $totalPegawai = $totalPpnpnAktif;
            $hadirPegawai = $hadirPpnpn;
            $belumAbsenPegawai = $belumAbsenPpnpn;
            $totalPpnpn = $totalPpnpnAktif;
            $jumlahCutiBulanIni = $jumlahCuti;
            $jumlahLupaAbsenBulanIni = $jumlahLupaAbsen;
            $jumlahLemburBulanIni = $jumlahLembur;

            return view('dashboardadmin.index', compact(
                'ppnpn',
                'totalPegawai', 'totalPpnpn', 'totalPegawaiTetap',
                'totalPpnpnAktif', 'totalPpnpnNonaktif', 'totalPpnpnSemua',
                'hadirHariIni', 'hadirPpnpn', 'hadirPegawai',
                'belumAbsen', 'belumAbsenPpnpn', 'belumAbsenPegawai',
                'cutiHariIni', 'cutiHariIniData',
                'lemburHariIni', 'lemburHariIniData',
                'jumlahCutiBulanIni', 'jumlahLupaAbsenBulanIni',
                'jumlahLemburBulanIni', 'jumlahCutiAlasanPentingBulanIni',
                'jumlahAktivitas',
                'pendingLembur', 'pendingLupaAbsen', 'totalPendingApproval',
                'totalJamLemburBulanIni',
                'perluResetCuti'
            ));
        }

        // =========================================================
        // DASHBOARD USER (PPNPN / PEGAWAI)
        // =========================================================
        $awalBulan = Carbon::now()->startOfMonth();
        $akhirBulan = Carbon::now()->endOfMonth();

        // Jumlah kehadiran bulan ini
        $jumlahKehadiran = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString(),
            ])
            ->whereNotNull('jam_masuk')
            ->count();

        // Jumlah cuti bulan ini
        $jumlahCuti = Pengajuancuti::where('user_id', $user->id)
            ->where(function ($q) use ($awalBulan, $akhirBulan) {
                $q->whereBetween('tanggal_mulai', [
                        $awalBulan->toDateString(),
                        $akhirBulan->toDateString(),
                    ])
                    ->orWhereBetween('tanggal_selesai', [
                        $awalBulan->toDateString(),
                        $akhirBulan->toDateString(),
                    ]);
            })
            ->count();

        // Jumlah lupa absen bulan ini
        $jumlahLupaAbsen = Pengajuanlupaabsen::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString(),
            ])
            ->count();

        // Jumlah surat bulan ini
        $jumlahSurat = Pengajuansurat::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString(),
            ])
            ->count();

        // Jumlah lembur bulan ini
        $jumlahLembur = Lembur::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString(),
            ])
            ->count();

        $jumlahAktivitas = $jumlahCuti + $jumlahLupaAbsen + $jumlahSurat + $jumlahLembur;

        // Sisa cuti
        $sisaCuti = $user->sisaCutiTahunan();
        $cutiTerpakai = $user->totalCutiTerpakai();

        // Total jam lembur disetujui bulan ini
        $totalLembur = Lembur::where('user_id', $user->id)
            ->where('status_approval', 'approved')
            ->whereBetween('tanggal', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString(),
            ])
            ->sum('total_jam');

        // Pengajuan pending milik user
        $pendingLemburSaya = Lembur::where('user_id', $user->id)
            ->where('status_approval', 'pending')
            ->count();

        $pendingLupaAbsenSaya = Pengajuanlupaabsen::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Total pengajuan pending (untuk card di dashboard)
        $totalPending = $pendingLemburSaya + $pendingLupaAbsenSaya;

        // Pengajuan disetujui hari ini
        $approvedLupaAbsenSaya = Pengajuanlupaabsen::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('approved_at', today())
            ->count();

        $approvedLemburSaya = Lembur::where('user_id', $user->id)
            ->where('status_approval', 'approved')
            ->whereDate('approved_at', today())
            ->count();

        $totalApprovedHariIni = $approvedLupaAbsenSaya + $approvedLemburSaya;

        // Dashboard pegawai (role pegawai — cuti tambahan saja)
        if ($user->role === 'pegawai') {
            return view('dashboardpegawai.index', compact(
                'sisaCuti', 'cutiTerpakai', 'jumlahKehadiran', 'jumlahAktivitas'
            ));
        }

        // Dashboard PPNPN
        return view('dashboard.index', compact(
            'sisaCuti', 'cutiTerpakai',
            'jumlahKehadiran', 'jumlahAktivitas',
            'totalLembur',
            'pendingLemburSaya', 'pendingLupaAbsenSaya',
            'totalPending', 'totalApprovedHariIni'
        ));
    }
}