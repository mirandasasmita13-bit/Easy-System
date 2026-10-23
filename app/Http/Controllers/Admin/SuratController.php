<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuansurat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    /**
     * Daftar pengajuan surat
     */
    public function index(Request $request)
    {
        $jenisSurat = $request->get('jenis_surat');
        $userId     = $request->get('user_id');
        $tanggal    = $request->get('tanggal');

        $query = Pengajuansurat::with(['user', 'user.profil'])
            ->orderBy('created_at', 'desc');

        if ($jenisSurat) {
            $query->where('jenis_surat', $jenisSurat);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        $data['surats']         = $query->limit(100)->get();
        $data['users']          = User::whereIn('role', ['ppnpn', 'magang'])->orderBy('name')->get();
        $data['filterJenis']    = $jenisSurat;
        $data['filterUser']     = $userId;
        $data['filterTanggal']  = $tanggal;

        // Daftar jenis surat unik (untuk dropdown filter)
        $data['jenisSuratList'] = Pengajuansurat::select('jenis_surat')
            ->distinct()
            ->orderBy('jenis_surat')
            ->pluck('jenis_surat');

        // Total
        $data['total'] = Pengajuansurat::count();

        return view('admin.control', [
            'tab'  => 'surat',
            'data' => $data,
        ]);
    }


    /**
     * Hapus pengajuan surat
     */
    public function destroy($id)
    {
        $surat = Pengajuansurat::findOrFail($id);

        // Hapus file dokumen kalau ada
        if ($surat->dokumen && Storage::disk('public')->exists($surat->dokumen)) {
            Storage::disk('public')->delete($surat->dokumen);
        }

        $surat->delete();

        return back()->with('success', 'Pengajuan surat berhasil dihapus.');
    }
}