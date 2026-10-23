<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Pengajuanlupaabsen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LupaAbsenController extends Controller
{
    /**
     * Daftar pengajuan lupa absen
     */
    public function index(Request $request)
    {
        $status  = $request->get('status', 'pending'); // default tampil pending
        $tanggal = $request->get('tanggal');
        $userId  = $request->get('user_id');

        $query = Pengajuanlupaabsen::with(['user', 'absensi'])
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $data['pengajuans']    = $query->limit(100)->get();
        $data['users']         = User::whereIn('role', ['ppnpn', 'magang'])->orderBy('name')->get();
        $data['filterStatus']  = $status;
        $data['filterTanggal'] = $tanggal;
        $data['filterUser']    = $userId;

        // Hitung jumlah per status (badge)
        $data['counts'] = [
            'semua'    => Pengajuanlupaabsen::count(),
            'pending'  => Pengajuanlupaabsen::where('status', 'pending')->count(),
            'approved' => Pengajuanlupaabsen::where('status', 'approved')->count(),
            'rejected' => Pengajuanlupaabsen::where('status', 'rejected')->count(),
        ];

        return view('admin.control', [
            'tab'  => 'lupa',
            'data' => $data,
        ]);
    }


    /**
     * Approve pengajuan lupa absen
     * - Update kolom absensis (jam_masuk / jam_pulang)
     * - Set status pengajuan = approved
     * - Pakai lockForUpdate biar aman dari race condition
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($request, $id) {

                $pengajuan = Pengajuanlupaabsen::lockForUpdate()->findOrFail($id);

                // Cek dulu — jangan sampai dua admin approve bareng
                if ($pengajuan->status !== 'pending') {
                    throw new \Exception('Pengajuan ini sudah diproses oleh admin lain.');
                }

                // Cari / buat data absensi di tabel absensis
                $absensi = Absensi::where('user_id', $pengajuan->user_id)
                    ->where('tanggal', $pengajuan->tanggal)
                    ->first();

                if (!$absensi) {
                    // Kalau belum ada, bikin baru
                    $absensi = new Absensi();
                    $absensi->user_id    = $pengajuan->user_id;
                    $absensi->tanggal    = $pengajuan->tanggal;
                    $absensi->keterangan = 'H';
                }

                // Update jam sesuai jenis absen
                if ($pengajuan->jenis_absen === 'masuk') {
                    $absensi->jam_masuk = $pengajuan->jam;
                } elseif ($pengajuan->jenis_absen === 'pulang') {
                    $absensi->jam_pulang = $pengajuan->jam;
                }

                $absensi->save();

                // Kalau absensi_id di pengajuan kosong, isi sekarang
                if (!$pengajuan->absensi_id) {
                    $pengajuan->absensi_id = $absensi->id;
                }

                // Update status pengajuan
                $pengajuan->status         = 'approved';
                $pengajuan->approved_by    = auth()->id();
                $pengajuan->approved_at    = now();
                $pengajuan->catatan_admin  = $request->catatan_admin;
                $pengajuan->save();
            });

            return redirect()
                ->route('admin.control', ['tab' => 'lupa', 'status' => 'pending'])
                ->with('success', 'Pengajuan berhasil di-approve. Data absensi sudah diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Reject pengajuan
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => ['required', 'string', 'max:500'],
        ], [
            'catatan_admin.required' => 'Catatan wajib diisi saat menolak pengajuan.',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {

                $pengajuan = Pengajuanlupaabsen::lockForUpdate()->findOrFail($id);

                if ($pengajuan->status !== 'pending') {
                    throw new \Exception('Pengajuan ini sudah diproses oleh admin lain.');
                }

                $pengajuan->status        = 'rejected';
                $pengajuan->approved_by   = auth()->id();
                $pengajuan->approved_at   = now();
                $pengajuan->catatan_admin = $request->catatan_admin;
                $pengajuan->save();
            });

            return redirect()
                ->route('admin.control', ['tab' => 'lupa', 'status' => 'pending'])
                ->with('success', 'Pengajuan berhasil ditolak.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

        /**
     * Tambah lupa absen manual (oleh admin)
     * Tanpa batas 3 hari — bisa input tanggal mundur bebas
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => ['required', 'exists:users,id'],
            'tanggal'     => ['required', 'date'],
            'jenis_absen' => ['required', 'in:masuk,pulang'],
            'jam'         => ['required', 'date_format:H:i'],
            'alasan'      => ['required', 'string', 'max:1000'],
            'bukti'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'status'      => ['required', 'in:pending,approved,rejected'],
        ]);

        try {
            DB::transaction(function () use ($request, $validated) {

                $data = [
                    'user_id'     => $validated['user_id'],
                    'tanggal'     => $validated['tanggal'],
                    'jenis_absen' => $validated['jenis_absen'],
                    'jam'         => $validated['jam'],
                    'alasan'      => $validated['alasan'],
                    'status'      => $validated['status'],
                ];

                // Upload bukti kalau ada
                if ($request->hasFile('bukti')) {
                    $data['bukti'] = $request->file('bukti')->store('bukti-lupa-absen', 'public');
                }

                // Kalau status langsung approved → langsung update absensi + set approver
                if ($validated['status'] === 'approved') {
                    $data['approved_by'] = auth()->id();
                    $data['approved_at'] = now();
                }

                $pengajuan = Pengajuanlupaabsen::create($data);

                // Kalau langsung approved → sinkron ke tabel absensi
                if ($validated['status'] === 'approved') {
                    $absensi = Absensi::where('user_id', $pengajuan->user_id)
                        ->where('tanggal', $pengajuan->tanggal)
                        ->first();

                    if (!$absensi) {
                        $absensi = new Absensi();
                        $absensi->user_id    = $pengajuan->user_id;
                        $absensi->tanggal    = $pengajuan->tanggal;
                        $absensi->keterangan = 'H';
                    }

                    if ($pengajuan->jenis_absen === 'masuk') {
                        $absensi->jam_masuk = $pengajuan->jam;
                    } else {
                        $absensi->jam_pulang = $pengajuan->jam;
                    }

                    $absensi->save();

                    $pengajuan->absensi_id = $absensi->id;
                    $pengajuan->save();
                }
            });

            return redirect()
                ->route('admin.control', ['tab' => 'lupa'])
                ->with('success', 'Pengajuan lupa absen berhasil ditambahkan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Update lupa absen
     * Kalau status approved → sinkron ulang ke absensi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal'     => ['required', 'date'],
            'jenis_absen' => ['required', 'in:masuk,pulang'],
            'jam'         => ['required', 'date_format:H:i'],
            'alasan'      => ['required', 'string', 'max:1000'],
            'status'      => ['required', 'in:pending,approved,rejected'],
            'bukti'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $id) {

                $pengajuan = Pengajuanlupaabsen::lockForUpdate()->findOrFail($id);

                $data = [
                    'tanggal'     => $validated['tanggal'],
                    'jenis_absen' => $validated['jenis_absen'],
                    'jam'         => $validated['jam'],
                    'alasan'      => $validated['alasan'],
                    'status'      => $validated['status'],
                ];

                // Update approver sesuai status
                if ($validated['status'] === 'approved' && $pengajuan->status !== 'approved') {
                    $data['approved_by'] = auth()->id();
                    $data['approved_at'] = now();
                } elseif ($validated['status'] === 'pending') {
                    $data['approved_by'] = null;
                    $data['approved_at'] = null;
                }

                // Upload bukti baru
                if ($request->hasFile('bukti')) {
                    if ($pengajuan->bukti && Storage::disk('public')->exists($pengajuan->bukti)) {
                        Storage::disk('public')->delete($pengajuan->bukti);
                    }
                    $data['bukti'] = $request->file('bukti')->store('bukti-lupa-absen', 'public');
                }

                $pengajuan->update($data);

                // Kalau status approved → sinkron ke absensi
                if ($validated['status'] === 'approved') {
                    $absensi = Absensi::where('user_id', $pengajuan->user_id)
                        ->where('tanggal', $pengajuan->tanggal)
                        ->first();

                    if (!$absensi) {
                        $absensi = new Absensi();
                        $absensi->user_id    = $pengajuan->user_id;
                        $absensi->tanggal    = $pengajuan->tanggal;
                        $absensi->keterangan = 'H';
                    }

                    if ($pengajuan->jenis_absen === 'masuk') {
                        $absensi->jam_masuk = $pengajuan->jam;
                    } else {
                        $absensi->jam_pulang = $pengajuan->jam;
                    }

                    $absensi->save();
                }
            });

            return redirect()
                ->route('admin.control', ['tab' => 'lupa'])
                ->with('success', 'Pengajuan berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Hapus lupa absen
     */
    public function destroy($id)
    {
        try {
            $pengajuan = Pengajuanlupaabsen::findOrFail($id);

            // Hapus file bukti
            if ($pengajuan->bukti && Storage::disk('public')->exists($pengajuan->bukti)) {
                Storage::disk('public')->delete($pengajuan->bukti);
            }

            $pengajuan->delete();

            return back()->with('success', 'Pengajuan berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}