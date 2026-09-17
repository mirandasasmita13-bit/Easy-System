<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\Pengajuanlupaabsen;
use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        // Tab aktif (default: lembur)
        $tab = $request->get('tab', 'lembur');

        // =========================================================
        // COUNT untuk badge & summary
        // =========================================================

        $countLembur    = Lembur::where('status_approval', 'pending')->count();
        $countLupaAbsen = Pengajuanlupaabsen::where('status', 'pending')->count();
        $countSurat     = PengajuanSurat::count();

        $jumlahPending = $countLembur + $countLupaAbsen;

        // =========================================================
        // DATA per tab
        // =========================================================

        $pending       = collect();
        $riwayat       = collect();
        $ppnpn         = collect();
        $perluReset    = false;
        $tahunSekarang = now()->year;

        // TAB: LEMBURcoba jell
        if ($tab === 'lembur') {
            $pending = Lembur::with('user')
                ->where('status_approval', 'pending')
                ->latest('tanggal')
                ->get();

            $riwayat = Lembur::with('user', 'approver')
                ->whereIn('status_approval', ['approved', 'rejected'])
                ->latest('approved_at')
                ->limit(50)
                ->get();
        }

        // TAB: LUPA ABSEN
        if ($tab === 'lupa-absen') {
            $pending = Pengajuanlupaabsen::with('user', 'absensi')
                ->where('status', 'pending')
                ->latest('tanggal')
                ->get();

            $riwayat = Pengajuanlupaabsen::with('user', 'approver', 'absensi')
                ->whereIn('status', ['approved', 'rejected'])
                ->latest('approved_at')
                ->limit(50)
                ->get();
        }

        // TAB: SURAT
        if ($tab === 'surat') {
            $pending = PengajuanSurat::with('user')
                ->latest('tanggal')
                ->get();
        }

        // TAB: CUTI
        if ($tab === 'cuti') {
            $ppnpn = User::where('role', 'ppnpn')
                ->where('status', 'aktif')
                ->orderBy('name')
                ->get();

            $perluReset = User::where('role', 'ppnpn')
                ->where('status', 'aktif')
                ->where('tahun_cuti', '<', now()->year)
                ->exists();
        }

        return view('pengajuan.index', compact(
            'tab',
            'countLembur',
            'countLupaAbsen',
            'countSurat',
            'jumlahPending',
            'pending',
            'riwayat',
            'ppnpn',
            'perluReset',
            'tahunSekarang'
        ));
    }
}