<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PpnpnController extends Controller
{
    /**
     * Daftar Pengguna (PPNPN / Magang).
     * Filter: semua | aktif | nonaktif | cuti
     * Role  : ppnpn | magang
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'semua');

        // ⬇️ TAMBAHAN: tentukan role aktif (default ppnpn)
        $role = $request->get('role', 'ppnpn');
        if (!in_array($role, ['ppnpn', 'magang'], true)) {
            $role = 'ppnpn';
        }

        $query = User::with('profil')
            ->where('role', $role);   // ⬅️ ganti dari 'ppnpn' hardcode

        if ($filter === 'aktif') {
            $query->where('status', 'aktif');
        } elseif ($filter === 'nonaktif') {
            $query->where('status', 'nonaktif');
        } elseif ($filter === 'cuti') {
            // Tab Cuti: hanya pengguna aktif (khusus PPNPN yang punya cuti)
            $query->where('status', 'aktif');
        }

        $ppnpn = $query
            ->orderByRaw("FIELD(status, 'aktif', 'nonaktif')")
            ->orderBy('name')
            ->get();

        // Counter per-role
        $countAktif    = User::where('role', $role)->where('status', 'aktif')->count();
        $countNonaktif = User::where('role', $role)->where('status', 'nonaktif')->count();
        $countSemua    = $countAktif + $countNonaktif;

        // ⬇️ TAMBAHAN: counter untuk badge di tab
        $countPpnpnAktif    = User::where('role', 'ppnpn')->where('status', 'aktif')->count();
        $countPpnpnNonaktif = User::where('role', 'ppnpn')->where('status', 'nonaktif')->count();
        $countPpnpnSemua    = $countPpnpnAktif + $countPpnpnNonaktif;

        $countMagangAktif    = User::where('role', 'magang')->where('status', 'aktif')->count();
        $countMagangNonaktif = User::where('role', 'magang')->where('status', 'nonaktif')->count();
        $countMagangSemua    = $countMagangAktif + $countMagangNonaktif;

        // Cek PPNPN yang perlu reset cuti
        $perluResetCuti = User::where('role', 'ppnpn')
            ->where('status', 'aktif')
            ->where('tahun_cuti', '<', now()->year)
            ->count();

        return view('ppnpn.index', compact(
            'ppnpn', 'filter', 'role',
            'countAktif', 'countNonaktif', 'countSemua',
            'countPpnpnAktif', 'countPpnpnNonaktif', 'countPpnpnSemua',
            'countMagangAktif', 'countMagangNonaktif', 'countMagangSemua',
            'perluResetCuti'
        ));
    }

    /**
     * Nonaktifkan pengguna (PPNPN / Magang).
     */
    public function nonaktifkan(Request $request, User $user)
    {
        // ⬇️ UBAH: izinkan ppnpn dan magang
        if (!in_array($user->role, ['ppnpn', 'magang'], true)) {
            abort(404);
        }

        if ($user->status === 'nonaktif') {
            return back()->with('error', 'Pengguna ini sudah nonaktif.');
        }

        $user->update([
            'status'           => 'nonaktif',
            'tanggal_nonaktif' => now()->toDateString(),
        ]);

        return back()->with(
            'success',
            $user->name . ' berhasil dinonaktifkan. Data tetap tersimpan.'
        );
    }

    /**
     * Aktifkan kembali pengguna (PPNPN / Magang).
     */
    public function aktifkan(Request $request, User $user)
    {
        // ⬇️ UBAH: izinkan ppnpn dan magang
        if (!in_array($user->role, ['ppnpn', 'magang'], true)) {
            abort(404);
        }

        if ($user->status === 'aktif') {
            return back()->with('error', 'Pengguna ini sudah aktif.');
        }

        $user->update([
            'status'           => 'aktif',
            'tanggal_nonaktif' => null,
        ]);

        return back()->with(
            'success',
            $user->name . ' berhasil diaktifkan kembali.'
        );
    }

    /**
     * Reset tahun cuti SEMUA PPNPN aktif.
     */
    public function resetSemuaCuti(Request $request)
    {
        $tahunBaru = now()->year;

        $count = User::where('role', 'ppnpn')
            ->where('status', 'aktif')
            ->update([
                'tahun_cuti' => $tahunBaru,
            ]);

        return redirect()
            ->route('ppnpn.index', ['filter' => 'cuti'])
            ->with('success', 'Tahun cuti ' . $count . ' PPNPN berhasil direset ke ' . $tahunBaru . '. Data cuti lama tetap tersimpan.');
    }

    /**
     * Reset tahun cuti 1 PPNPN.
     */
    public function resetCuti(Request $request, User $user)
    {
        if ($user->role !== 'ppnpn') {
            abort(404);
        }

        $tahunBaru = now()->year;

        $user->update([
            'tahun_cuti' => $tahunBaru,
        ]);

        return redirect()
            ->route('ppnpn.index', ['filter' => 'cuti'])
            ->with('success', 'Tahun cuti ' . $user->name . ' berhasil direset ke ' . $tahunBaru . '.');
    }
}