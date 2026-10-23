<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\lembur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LemburController extends Controller
{
    /**
     * Daftar lembur
     */
    public function index(Request $request)
    {
        $status  = $request->get('status', 'pending');
        $tanggal = $request->get('tanggal');
        $userId  = $request->get('user_id');

        $query = lembur::with(['user', 'user.profil'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc');

        if ($status && $status !== 'semua') {
            $query->where('status_approval', $status);
        }

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $data['lemburs']       = $query->limit(100)->get();
        $data['users']         = User::whereIn('role', ['ppnpn', 'magang'])->orderBy('name')->get();
        $data['filterStatus']  = $status;
        $data['filterTanggal'] = $tanggal;
        $data['filterUser']    = $userId;

        $data['counts'] = [
            'semua'    => lembur::count(),
            'pending'  => lembur::where('status_approval', 'pending')->count(),
            'approved' => lembur::where('status_approval', 'approved')->count(),
            'rejected' => lembur::where('status_approval', 'rejected')->count(),
        ];

        return view('admin.control', [
            'tab'  => 'lembur',
            'data' => $data,
        ]);
    }


    /**
     * Approve lembur
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($request, $id) {

                $lembur = lembur::lockForUpdate()->findOrFail($id);

                if ($lembur->status_approval !== 'pending') {
                    throw new \Exception('Lembur ini sudah diproses admin lain.');
                }

                $lembur->status_approval = 'approved';
                $lembur->approved_by     = auth()->id();
                $lembur->approved_at     = now();
                $lembur->catatan_admin   = $request->catatan_admin;
                $lembur->save();
            });

            return redirect()
                ->route('admin.control', ['tab' => 'lembur', 'status' => 'pending'])
                ->with('success', 'Lembur berhasil di-approve.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Reject lembur
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => ['required', 'string', 'max:500'],
        ], [
            'catatan_admin.required' => 'Catatan wajib diisi saat menolak lembur.',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {

                $lembur = lembur::lockForUpdate()->findOrFail($id);

                if ($lembur->status_approval !== 'pending') {
                    throw new \Exception('Lembur ini sudah diproses admin lain.');
                }

                $lembur->status_approval = 'rejected';
                $lembur->approved_by     = auth()->id();
                $lembur->approved_at     = now();
                $lembur->catatan_admin   = $request->catatan_admin;
                $lembur->save();
            });

            return redirect()
                ->route('admin.control', ['tab' => 'lembur', 'status' => 'pending'])
                ->with('success', 'Lembur berhasil ditolak.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Batalkan approve/reject → balik ke pending
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'alasan_cancel' => ['required', 'string', 'max:500'],
        ], [
            'alasan_cancel.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {

                $lembur = lembur::lockForUpdate()->findOrFail($id);

                if ($lembur->status_approval === 'pending') {
                    throw new \Exception('Lembur ini masih pending, tidak perlu dibatalkan.');
                }

                $lembur->status_approval = 'pending';
                $lembur->approved_by     = null;
                $lembur->approved_at     = null;
                $lembur->catatan_admin   = 'DIBATALKAN: ' . $request->alasan_cancel;
                $lembur->save();
            });

            return redirect()
                ->route('admin.control', ['tab' => 'lembur'])
                ->with('success', 'Approval dibatalkan. Lembur kembali ke pending.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Edit lembur (pending only)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal'     => ['required', 'date'],
            'jam_mulai'   => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'total_jam'   => ['nullable', 'numeric', 'min:0.1', 'max:24'],
            'kegiatan'    => ['required', 'string', 'max:255'],
            'keterangan'  => ['nullable', 'string', 'max:1000'],
            'foto'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $id) {

                $lembur = lembur::lockForUpdate()->findOrFail($id);

                if ($lembur->status_approval !== 'pending') {
                    throw new \Exception('Hanya lembur pending yang bisa diedit.');
                }

                $data = [
                    'tanggal'     => $validated['tanggal'],
                    'jam_mulai'   => $validated['jam_mulai'],
                    'jam_selesai' => $validated['jam_selesai'],
                    'total_jam'   => $validated['total_jam'] ?? null,
                    'kegiatan'    => $validated['kegiatan'],
                    'keterangan'  => $validated['keterangan'] ?? null,
                ];

                if ($request->hasFile('foto')) {
                    if ($lembur->foto && Storage::disk('public')->exists($lembur->foto)) {
                        Storage::disk('public')->delete($lembur->foto);
                    }
                    $data['foto'] = $request->file('foto')->store('foto-lembur', 'public');
                }

                $lembur->update($data);
            });

            return redirect()
                ->route('admin.control', ['tab' => 'lembur'])
                ->with('success', 'Lembur berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Hapus lembur
     */
    public function destroy($id)
    {
        $lembur = lembur::findOrFail($id);

        if ($lembur->foto && Storage::disk('public')->exists($lembur->foto)) {
            Storage::disk('public')->delete($lembur->foto);
        }

        $lembur->delete();

        return back()->with('success', 'Data lembur berhasil dihapus.');
    }

        /**
     * Tambah lembur manual (oleh admin)
     * Tanpa batas waktu, langsung bisa set status
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'         => ['required', 'exists:users,id'],
            'tanggal'         => ['required', 'date'],
            'jam_mulai'       => ['required', 'date_format:H:i'],
            'jam_selesai'     => ['required', 'date_format:H:i'],
            'kegiatan'        => ['required', 'string', 'max:255'],
            'keterangan'      => ['nullable', 'string', 'max:1000'],
            'foto'            => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status_approval' => ['required', 'in:pending,approved,rejected'],
        ]);

        try {
            DB::transaction(function () use ($request, $validated) {

                // Hitung total jam
                $totalJam = \App\Models\Lembur::hitungTotalJam(
                    $validated['jam_mulai'],
                    $validated['jam_selesai']
                );

                $data = [
                    'user_id'         => $validated['user_id'],
                    'tanggal'         => $validated['tanggal'],
                    'jam_mulai'       => $validated['jam_mulai'],
                    'jam_selesai'     => $validated['jam_selesai'],
                    'total_jam'       => $totalJam,
                    'kegiatan'        => $validated['kegiatan'],
                    'keterangan'      => $validated['keterangan'] ?? null,
                    'status_approval' => $validated['status_approval'],
                ];

                // Kalau status approved → isi approver
                if ($validated['status_approval'] === 'approved') {
                    $data['approved_by'] = auth()->id();
                    $data['approved_at'] = now();
                }

                // Upload foto kalau ada
                if ($request->hasFile('foto')) {
                    $data['foto'] = $request->file('foto')->store('foto-lembur', 'public');
                } else {
                    // Foto wajib di DB, jadi kasih placeholder kalau gak ada
                    $data['foto'] = '-';
                }

                lembur::create($data);
            });

            return redirect()
                ->route('admin.control', ['tab' => 'lembur'])
                ->with('success', 'Data lembur berhasil ditambahkan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan lembur: ' . $e->getMessage())->withInput();
        }
    }
}