<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PpnpnController extends Controller
{
    /**
     * Daftar PPNPN.
     * Default menampilkan semua (aktif + nonaktif).
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'semua'); // semua | aktif | nonaktif

        $query = User::with('profil')
            ->where('role', 'ppnpn');

        if ($filter === 'aktif') {
            $query->where('status', 'aktif');
        } elseif ($filter === 'nonaktif') {
            $query->where('status', 'nonaktif');
        }

        $ppnpn = $query
            ->orderByRaw("FIELD(status, 'aktif', 'nonaktif')")
            ->orderBy('name')
            ->get();

        // Counter
        $countAktif    = User::where('role', 'ppnpn')->where('status', 'aktif')->count();
        $countNonaktif = User::where('role', 'ppnpn')->where('status', 'nonaktif')->count();
        $countSemua    = $countAktif + $countNonaktif;

        return view('ppnpn.index', compact(
            'ppnpn', 'filter',
            'countAktif', 'countNonaktif', 'countSemua'
        ));
    }

    /**
     * Nonaktifkan PPNPN (misal pensiun).
     * Data TIDAK dihapus, hanya status berubah.
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
}