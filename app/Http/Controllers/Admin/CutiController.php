<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\pengajuancuti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CutiController extends Controller
{
    /**
     * Daftar pengajuan cuti
     */
    public function index(Request $request)
    {
        $jenisCuti = $request->get('jenis_cuti');
        $userId    = $request->get('user_id');
        $tahun     = $request->get('tahun', now()->year);

        $query = pengajuancuti::with(['user', 'user.profil'])
            ->orderBy('tanggal_mulai', 'desc');

        if ($jenisCuti) {
            $query->where('jenis_cuti', $jenisCuti);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($tahun) {
            $query->whereYear('tanggal_mulai', $tahun);
        }

        $data['cutis']        = $query->limit(100)->get();
        $data['users']        = User::whereIn('role', ['ppnpn', 'magang'])->orderBy('name')->get();
        $data['filterJenis']  = $jenisCuti;
        $data['filterUser']   = $userId;
        $data['filterTahun']  = $tahun;

        // Ringkasan per user (untuk laporan sisa cuti)
        $data['sisaCutiPerUser'] = User::whereIn('role', ['ppnpn'])
            ->with('profil')
            ->orderBy('name')
            ->get()
            ->map(function ($u) use ($tahun) {
                $terpakai = pengajuancuti::where('user_id', $u->id)
                    ->where('jenis_cuti', 'tahunan')
                    ->whereYear('tanggal_mulai', $tahun)
                    ->sum('jumlah_hari');

                $jatah = $u->jatah_cuti_tahunan ?? 12;
                $sebelumnya = $u->cuti_tahunan_sebelumnya ?? 0;

                return [
                    'user'       => $u,
                    'jatah'      => $jatah,
                    'sebelumnya' => $sebelumnya,
                    'terpakai'   => $terpakai,
                    'sisa'       => max(0, $jatah - $sebelumnya - $terpakai),
                ];
            });

        return view('admin.control', [
            'tab'  => 'cuti',
            'data' => $data,
        ]);
    }


    /**
     * Update pengajuan cuti (koreksi admin)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis_cuti'        => ['required', 'in:tahunan,alasan_penting'],
            'tanggal_mulai'     => ['required', 'date'],
            'tanggal_selesai'   => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jumlah_hari'       => ['required', 'integer', 'min:1', 'max:365'],
            'keterangan'        => ['nullable', 'string', 'max:1000'],
        ]);

        $cuti = pengajuancuti::findOrFail($id);
        $cuti->update($validated);

        return redirect()
            ->route('admin.control', ['tab' => 'cuti'])
            ->with('success', 'Pengajuan cuti berhasil diperbarui.');
    }


    /**
     * Hapus pengajuan cuti
     */
    public function destroy($id)
    {
        $cuti = pengajuancuti::findOrFail($id);

        // Hapus file surat kalau ada
        if ($cuti->surat && Storage::disk('public')->exists($cuti->surat)) {
            Storage::disk('public')->delete($cuti->surat);
        }

        $cuti->delete();

        return back()->with('success', 'Pengajuan cuti berhasil dihapus.');
    }
}