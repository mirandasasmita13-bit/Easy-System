<?php

namespace App\Http\Controllers;

use App\Models\CutiSebelumnya;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManajemenCutiController extends Controller
{
    /**
     * Halaman daftar semua PPNPN + ringkasan cuti.
     * Route: GET /manajemen-cuti
     */
    public function indexAll()
{
    $ppnpn = User::with('profil')
        ->where('role', 'ppnpn')
        ->where('status', 'aktif')
        ->orderBy('name')
        ->get();

    $perluResetCuti = User::where('role', 'ppnpn')
        ->where('status', 'aktif')
        ->where('tahun_cuti', '<', now()->year)
        ->count();

    return view('manajemen-cuti.index', compact('ppnpn', 'perluResetCuti'));
}

    /**
     * Halaman Manajemen Cuti untuk 1 PPNPN.
     * Route: GET /ppnpn/{user}/manajemen-cuti
     */
    public function index(User $user)
    {
        if ($user->role !== 'ppnpn') {
            abort(404);
        }

        $cutiSebelumnya = CutiSebelumnya::where('user_id', $user->id)
            ->orderByDesc('tanggal_mulai')
            ->get();

        $totalManualTahunan = $cutiSebelumnya->where('jenis_cuti', 'tahunan')->sum('jumlah_hari');
        $totalManualCAP     = $cutiSebelumnya->where('jenis_cuti', 'alasan_penting')->sum('jumlah_hari');

        return view('ppnpn.manajemen-cuti', compact(
            'user',
            'cutiSebelumnya',
            'totalManualTahunan',
            'totalManualCAP'
        ));
    }

    /**
     * Simpan cuti baru untuk 1 PPNPN.
     * Route: POST /ppnpn/{user}/manajemen-cuti
     */
    public function store(Request $request, User $user)
    {
        if ($user->role !== 'ppnpn') {
            abort(404);
        }

        $request->validate([
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jenis_cuti'      => ['required', 'in:tahunan,alasan_penting'],
            'alasan'          => ['nullable', 'string', 'max:500'],
        ]);

        $mulai   = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);

        // Hitung hari kerja (exclude weekend)
        $jumlah = 0;
        $cursor = $mulai->copy();
        while ($cursor->lte($selesai)) {
            if ($cursor->isWeekday()) $jumlah++;
            $cursor->addDay();
        }

        if ($jumlah <= 0) {
            return back()->withInput()->with('error',
                'Tanggal yang dipilih tidak memiliki hari kerja.'
            );
        }

        CutiSebelumnya::create([
            'user_id'         => $user->id,
            'tanggal_mulai'   => $mulai,
            'tanggal_selesai' => $selesai,
            'jumlah_hari'     => $jumlah,
            'jenis_cuti'      => $request->jenis_cuti,
            'alasan'          => $request->alasan,
            'dicatat_oleh'    => auth()->id(),
        ]);

        return redirect()
            ->route('ppnpn.manajemen-cuti', $user->id)
            ->with('success', 'Cuti berhasil ditambahkan.');
    }

    /**
     * Update cuti.
     * Route: PUT /manajemen-cuti/{cuti}
     */
    public function update(Request $request, CutiSebelumnya $cuti)
    {
        $request->validate([
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jenis_cuti'      => ['required', 'in:tahunan,alasan_penting'],
            'alasan'          => ['nullable', 'string', 'max:500'],
        ]);

        $mulai   = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);

        $jumlah = 0;
        $cursor = $mulai->copy();
        while ($cursor->lte($selesai)) {
            if ($cursor->isWeekday()) $jumlah++;
            $cursor->addDay();
        }

        if ($jumlah <= 0) {
            return back()->withInput()->with('error',
                'Tanggal yang dipilih tidak memiliki hari kerja.'
            );
        }

        $cuti->update([
            'tanggal_mulai'   => $mulai,
            'tanggal_selesai' => $selesai,
            'jumlah_hari'     => $jumlah,
            'jenis_cuti'      => $request->jenis_cuti,
            'alasan'          => $request->alasan,
        ]);

        return redirect()
            ->route('ppnpn.manajemen-cuti', $cuti->user_id)
            ->with('success', 'Cuti berhasil diperbarui.');
    }

    /**
     * Hapus cuti.
     * Route: DELETE /manajemen-cuti/{cuti}
     */
    public function destroy(CutiSebelumnya $cuti)
    {
        $userId = $cuti->user_id;
        $cuti->delete();

        return redirect()
            ->route('ppnpn.manajemen-cuti', $userId)
            ->with('success', 'Cuti berhasil dihapus.');
    }
}