<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CutiSebelumnya;
use App\Models\User;
use Illuminate\Http\Request;

class CutiLaluController extends Controller
{
    /**
     * Daftar cuti sebelumnya
     */
    public function index(Request $request)
    {
        $userId = $request->get('user_id');
        $tahun  = $request->get('tahun', now()->year);

        $query = CutiSebelumnya::with(['user', 'user.profil', 'dicatatOleh'])
            ->orderBy('tanggal_mulai', 'desc');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($tahun) {
            $query->whereYear('tanggal_mulai', $tahun);
        }

        $data['cutiLalus'] = $query->limit(100)->get();

        // Filter user — cuma PPNPN (karena Cuti Lalu khusus PPNPN)
        $data['users'] = User::where('role', 'ppnpn')
            ->orderBy('name')
            ->get();

        $data['filterUser']  = $userId;
        $data['filterTahun'] = $tahun;

        // Total
        $data['total'] = CutiSebelumnya::count();

        return view('admin.control', [
            'tab'  => 'cuti-lalu',
            'data' => $data,
        ]);
    }


    /**
     * Simpan cuti sebelumnya (baru)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'         => ['required', 'exists:users,id'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jumlah_hari'     => ['required', 'integer', 'min:1', 'max:365'],
            'jenis_cuti'      => ['required', 'in:tahunan,alasan_penting'],
            'alasan'          => ['nullable', 'string', 'max:500'],
        ]);

        // Pastikan user yang dipilih memang PPNPN
        $user = User::find($validated['user_id']);
        if (!$user || $user->role !== 'ppnpn') {
            return back()->with('error', 'Cuti Lalu hanya untuk user PPNPN.');
        }

        $validated['dicatat_oleh'] = auth()->id();

        CutiSebelumnya::create($validated);

        return redirect()
            ->route('admin.control', ['tab' => 'cuti-lalu'])
            ->with('success', 'Cuti sebelumnya berhasil ditambahkan.');
    }


    /**
     * Update cuti sebelumnya
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id'         => ['required', 'exists:users,id'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jumlah_hari'     => ['required', 'integer', 'min:1', 'max:365'],
            'jenis_cuti'      => ['required', 'in:tahunan,alasan_penting'],
            'alasan'          => ['nullable', 'string', 'max:500'],
        ]);

        $user = User::find($validated['user_id']);
        if (!$user || $user->role !== 'ppnpn') {
            return back()->with('error', 'Cuti Lalu hanya untuk user PPNPN.');
        }

        $cuti = CutiSebelumnya::findOrFail($id);
        $cuti->update($validated);

        return redirect()
            ->route('admin.control', ['tab' => 'cuti-lalu'])
            ->with('success', 'Cuti sebelumnya berhasil diperbarui.');
    }


    /**
     * Hapus cuti sebelumnya
     */
    public function destroy($id)
    {
        $cuti = CutiSebelumnya::findOrFail($id);
        $cuti->delete();

        return back()->with('success', 'Cuti sebelumnya berhasil dihapus.');
    }
}