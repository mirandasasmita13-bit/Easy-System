<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Daftar absensi
     */
    public function index(Request $request)
    {
        $tanggal    = $request->get('tanggal', now()->toDateString());
        $userId     = $request->get('user_id');
        $keterangan = $request->get('keterangan');

        $query = Absensi::with(['user', 'user.profil'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_masuk', 'desc');

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($keterangan) {
            $query->where('keterangan', $keterangan);
        }

        $data['absensis']      = $query->limit(100)->get();
        $data['users']         = User::whereIn('role', ['ppnpn', 'magang'])->orderBy('name')->get();
        $data['filterTanggal'] = $tanggal;
        $data['filterUser']    = $userId;
        $data['filterKet']     = $keterangan;

        return view('admin.control', [
            'tab'  => 'absensi',
            'data' => $data,
        ]);
    }


    /**
     * Tambah absensi manual (oleh admin)
     * - Bisa input user yang belum absen sama sekali
     * - Tanpa batas waktu
     * - Auto hitung shift berdasarkan jam masuk
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'         => ['required', 'exists:users,id'],
            'tanggal'         => ['required', 'date'],
            'shift'           => ['nullable', 'in:pagi,malam'],
            'jam_masuk'       => ['nullable', 'date_format:H:i'],
            'jam_pulang'      => ['nullable', 'date_format:H:i'],
            'tanggal_pulang'  => ['nullable', 'date'],
            'keterangan'      => ['required', 'string', 'max:10'],
            'is_jaga_pos'     => ['nullable', 'boolean'],
            'status_approval' => ['nullable', 'in:normal,pending,approved,rejected'],
        ]);

        // Cek duplikat: user + tanggal + shift udah ada?
        $sudahAda = Absensi::where('user_id', $validated['user_id'])
            ->where('tanggal', $validated['tanggal'])
            ->when($validated['shift'] ?? null, function ($q) use ($validated) {
                $q->where('shift', $validated['shift']);
            })
            ->exists();

        if ($sudahAda) {
            return back()
                ->with('error', 'Absensi untuk user ini di tanggal & shift tersebut sudah ada. Silakan edit yang ada.')
                ->withInput();
        }

        // Auto-set shift berdasarkan jam masuk kalau gak diisi manual
        $shift = $validated['shift'] ?? null;
        if (!$shift && !empty($validated['jam_masuk'])) {
            $jam = (int) substr($validated['jam_masuk'], 0, 2);
            // Shift pagi: 06:00 - 17:59
            // Shift malam: 18:00 - 05:59
            $shift = ($jam >= 6 && $jam < 18) ? 'pagi' : 'malam';
        }

        // Default tanggal_pulang = tanggal kalau jam_pulang diisi
        $tanggalPulang = $validated['tanggal_pulang'] ?? null;
        if (!empty($validated['jam_pulang']) && !$tanggalPulang) {
            $tanggalPulang = $validated['tanggal'];
        }

        Absensi::create([
            'user_id'         => $validated['user_id'],
            'tanggal'         => $validated['tanggal'],
            'shift'           => $shift,
            'jam_masuk'       => $validated['jam_masuk'] ?? null,
            'jam_pulang'      => $validated['jam_pulang'] ?? null,
            'tanggal_pulang'  => $tanggalPulang,
            'keterangan'      => $validated['keterangan'],
            'is_jaga_pos'     => $request->boolean('is_jaga_pos'),
            'status_approval' => $validated['status_approval'] ?? 'normal',
        ]);

        return redirect()
            ->route('admin.control', ['tab' => 'absensi', 'tanggal' => $validated['tanggal']])
            ->with('success', 'Absensi manual berhasil ditambahkan.');
    }


    /**
     * Update absensi
     */
    public function update(Request $request, Absensi $absensi)
    {
        $validated = $request->validate([
            'tanggal'        => ['required', 'date'],
            'shift'          => ['nullable', 'string', 'max:50'],
            'jam_masuk'      => ['nullable', 'date_format:H:i'],
            'jam_pulang'     => ['nullable', 'date_format:H:i'],
            'tanggal_pulang' => ['nullable', 'date'],
            'keterangan'     => ['required', 'string', 'max:10'],
            'is_jaga_pos'    => ['nullable', 'boolean'],
        ]);

        $validated['is_jaga_pos'] = $request->boolean('is_jaga_pos');
        $absensi->update($validated);

        return redirect()
            ->route('admin.control', ['tab' => 'absensi'])
            ->with('success', 'Absensi berhasil diperbarui.');
    }


    /**
     * Hapus absensi
     */
    public function destroy(Absensi $absensi)
    {
        $absensi->delete();
        return back()->with('success', 'Absensi berhasil dihapus.');
    }
}