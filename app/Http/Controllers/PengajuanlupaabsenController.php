<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pengajuanlupaabsen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PengajuanlupaabsenController extends Controller
{
    /**
     * =========================================================
     * INDEX — PPNPN lihat riwayat sendiri, Admin lihat semua
     * =========================================================
     */
    public function index()
    {
        $user = Auth::user();

        $query = Pengajuanlupaabsen::with(['absensi'])
            ->orderByDesc('created_at');

        // Kalau PPNPN → filter hanya miliknya
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        } else {
            // Admin lihat semua, load relasi user
            $query->with(['user', 'user.profil']);
        }

        $riwayatLupaAbsen = $query->get();

        return view('lupa_absen.index', compact('riwayatLupaAbsen'));
    }

    /**
     * =========================================================
     * STORE — Simpan pengajuan baru
     * =========================================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'     => 'required|date|before_or_equal:today',
            'jenis_absen' => 'required|in:masuk,pulang',
            'jam'         => 'required',
            'alasan'      => 'required|string|max:1000',
            'bukti'       => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ], [
            'tanggal.before_or_equal' => 'Tanggal tidak boleh melebihi hari ini.',
            'jenis_absen.in'          => 'Jenis absensi harus masuk atau pulang.',
            'bukti.max'               => 'Ukuran bukti maksimal 5 MB.',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'user_id'     => Auth::id(),
                'tanggal'     => $validated['tanggal'],
                'jenis_absen' => $validated['jenis_absen'],
                'jam'         => $validated['jam'],
                'alasan'      => $validated['alasan'],
                'status'      => 'pending',
            ];

            // Upload bukti kalau ada
            if ($request->hasFile('bukti')) {
                $data['bukti'] = $request->file('bukti')
                    ->store('bukti-lupa-absen', 'public');
            }

            Pengajuanlupaabsen::create($data);

            DB::commit();

            return redirect()->route('lupa-absen.index')
                ->with('success', 'Pengajuan perbaikan absensi berhasil dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal store lupa absen: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal mengirim pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * =========================================================
     * APPROVE — FIX UTAMA 🔥
     * Sinkronisasi: pengajuanlupaabsen → absensi
     * =========================================================
     */
    public function approve(Request $request, Pengajuanlupaabsen $lupaAbsen)
    {
        DB::beginTransaction();
        try {
            // Guard: jangan double approve
            if (in_array($lupaAbsen->status, ['approved', 'disetujui'], true)) {
                return back()->with('warning', 'Pengajuan ini sudah disetujui sebelumnya.');
            }

            // ---------------------------------------------
            // 1. Update pengajuan lupa absen
            // ---------------------------------------------
            $updateData = [
                'status' => 'approved',
            ];

            if (Schema::hasColumn('pengajuanlupaabsen', 'catatan_admin')) {
                $updateData['catatan_admin'] = $request->input('catatan_admin');
            }

            $lupaAbsen->update($updateData);

            // ---------------------------------------------
            // 2. SINKRONISASI KE TABEL ABSENSI
            // ---------------------------------------------
            $absensi = Absensi::where('user_id', $lupaAbsen->user_id)
                ->whereDate('tanggal', $lupaAbsen->tanggal)
                ->first();

            if ($absensi) {
                // Update absensi yang sudah ada
                $absensiUpdate = [
                    'status_approval' => 'approved',
                ];

                // Isi jam sesuai jenis absen
                if ($lupaAbsen->jenis_absen === 'masuk') {
                    $absensiUpdate['jam_masuk'] = $lupaAbsen->jam;
                } else {
                    $absensiUpdate['jam_pulang'] = $lupaAbsen->jam;
                }

                if (Schema::hasColumn('absensi', 'approved_at')) {
                    $absensiUpdate['approved_at'] = now();
                }
                if (Schema::hasColumn('absensi', 'approved_by')) {
                    $absensiUpdate['approved_by'] = Auth::id();
                }

                $absensi->update($absensiUpdate);

            } else {
                // Belum ada record absensi → auto-create
                $absensiCreate = [
                    'user_id'         => $lupaAbsen->user_id,
                    'tanggal'         => $lupaAbsen->tanggal,
                    'status_approval' => 'approved',
                    'shift'           => 'pagi',
                    'keterangan'      => 'Auto-create dari approval lupa absen',
                ];

                if ($lupaAbsen->jenis_absen === 'masuk') {
                    $absensiCreate['jam_masuk'] = $lupaAbsen->jam;
                } else {
                    $absensiCreate['jam_pulang'] = $lupaAbsen->jam;
                }

                if (Schema::hasColumn('absensi', 'approved_at')) {
                    $absensiCreate['approved_at'] = now();
                }
                if (Schema::hasColumn('absensi', 'approved_by')) {
                    $absensiCreate['approved_by'] = Auth::id();
                }

                Absensi::create($absensiCreate);
            }

            DB::commit();

            Log::info("Lupa absen #{$lupaAbsen->id} disetujui oleh admin #" . Auth::id());

            return back()->with('success', 'Pengajuan lupa absen berhasil disetujui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal approve lupa absen: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    /**
     * =========================================================
     * REJECT
     * =========================================================
     */
    public function reject(Request $request, Pengajuanlupaabsen $lupaAbsen)
    {
        DB::beginTransaction();
        try {
            if (in_array($lupaAbsen->status, ['rejected', 'ditolak'], true)) {
                return back()->with('warning', 'Pengajuan ini sudah ditolak sebelumnya.');
            }

            // ---------------------------------------------
            // 1. Update pengajuan
            // ---------------------------------------------
            $updateData = [
                'status' => 'rejected',
            ];

            if (Schema::hasColumn('pengajuanlupaabsen', 'catatan_admin')) {
                $updateData['catatan_admin'] = $request->input('catatan_admin');
            }

            $lupaAbsen->update($updateData);

            // ---------------------------------------------
            // 2. Sinkronisasi ke absensi (jika ada record)
            // ---------------------------------------------
            Absensi::where('user_id', $lupaAbsen->user_id)
                ->whereDate('tanggal', $lupaAbsen->tanggal)
                ->update([
                    'status_approval' => 'rejected',
                ]);

            DB::commit();

            Log::info("Lupa absen #{$lupaAbsen->id} ditolak oleh admin #" . Auth::id());

            return back()->with('success', 'Pengajuan lupa absen ditolak.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal reject lupa absen: ' . $e->getMessage());
            return back()->with('error', 'Gagal menolak: ' . $e->getMessage());
        }
    }
}