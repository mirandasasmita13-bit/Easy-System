<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PpnpnController extends Controller
{
    /**
     * Daftar PPNPN.
     * Filter: semua | aktif | nonaktif | cuti
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'semua');

        $query = User::with('profil')
            ->where('role', 'ppnpn');

        if ($filter === 'aktif') {
            $query->where('status', 'aktif');
        } elseif ($filter === 'nonaktif') {
            $query->where('status', 'nonaktif');
        } elseif ($filter === 'cuti') {
            // Tab Cuti: hanya PPNPN aktif
            $query->where('status', 'aktif');
        }

        $ppnpn = $query
            ->orderByRaw("FIELD(status, 'aktif', 'nonaktif')")
            ->orderBy('name')
            ->get();

        // Counter
        $countAktif    = User::where('role', 'ppnpn')->where('status', 'aktif')->count();
        $countNonaktif = User::where('role', 'ppnpn')->where('status', 'nonaktif')->count();
        $countSemua    = $countAktif + $countNonaktif;

        // Cek PPNPN yang perlu reset cuti
        $perluResetCuti = User::where('role', 'ppnpn')
            ->where('status', 'aktif')
            ->where('tahun_cuti', '<', now()->year)
            ->count();

        return view('ppnpn.index', compact(
            'ppnpn', 'filter',
            'countAktif', 'countNonaktif', 'countSemua',
            'perluResetCuti'
        ));
    }

    /**
     * Nonaktifkan PPNPN.
     */
    public function nonaktifkan(Request $request, User $user)
    {
        if ($user->role !== 'ppnpn') {
            abort(404);
        }

        if ($user->status === 'nonaktif') {
            return back()->with('error', 'PPNPN ini sudah nonaktif.');
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
     * Aktifkan kembali PPNPN.
     */
    public function aktifkan(Request $request, User $user)
    {
        if ($user->role !== 'ppnpn') {
            abort(404);
        }

        if ($user->status === 'aktif') {
            return back()->with('error', 'PPNPN ini sudah aktif.');
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