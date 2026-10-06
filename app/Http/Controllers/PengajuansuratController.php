<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuansuratController extends Controller
{
    /**
     * Halaman Surat Lainnya
     */
    public function index()
    {
        $user = auth()->user();

        $riwayatSurat = PengajuanSurat::where('user_id', $user->id)
            ->latest()
            ->get();

        return view(
            'pengajuan_surat.index',
            compact('riwayatSurat')
        );
    }

    /**
     * Simpan pengajuan surat (baru)
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // =====================================================
        // VALIDASI
        // =====================================================
        $request->validate([
            'jenis_surat' => [
                'required',
                'in:surat_keterangan,surat_tugas,surat_lainnya',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'keperluan' => [
                'required',
                'string',
                'max:1000',
            ],

            'dokumen' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ], [
            'dokumen.required' => 'Dokumen wajib diupload.',
            'dokumen.mimes'    => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'dokumen.max'      => 'Ukuran dokumen maksimal 5 MB.',
        ]);

        // =====================================================
        // CEK DUPLIKAT (cegah double submit dari backend)
        // =====================================================
        $duplikat = PengajuanSurat::where('user_id', $user->id)
            ->where('jenis_surat', $request->jenis_surat)
            ->where('tanggal', $request->tanggal)
            ->where('keperluan', $request->keperluan)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($duplikat) {
            return back()
                ->withInput()
                ->with('error', 'Surat ini baru saja kamu simpan. Mohon tunggu sebentar sebelum input lagi.');
        }

        // =====================================================
        // SIMPAN DOKUMEN
        // =====================================================
        $dokumenPath = null;
        $namaDokumen = null;

        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');

            // Simpan file ke: storage/app/public/dokumen-surat
            $dokumenPath = $file->store('dokumen-surat', 'public');

            // Simpan nama file asli
            $namaDokumen = $file->getClientOriginalName();
        }

        // =====================================================
        // SIMPAN DATA KE DATABASE
        // =====================================================
        PengajuanSurat::create([
            'user_id'      => $user->id,
            'jenis_surat'  => $request->jenis_surat,
            'tanggal'      => $request->tanggal,
            'keperluan'    => $request->keperluan,
            'dokumen'      => $dokumenPath,
            'nama_dokumen' => $namaDokumen,
        ]);

        return back()->with(
            'success',
            'Pengajuan surat berhasil disimpan.'
        );
    }


    /**
     * Update pengajuan surat (perbaikan).
     */
    public function update(Request $request, PengajuanSurat $pengajuanSurat)
    {
        // Pastikan hanya pemilik yang boleh mengedit
        if ($pengajuanSurat->user_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mengedit surat ini.');
        }

        $request->validate([
            'jenis_surat' => [
                'required',
                'in:surat_keterangan,surat_tugas,surat_lainnya',
            ],

            'tanggal' => [
                'required',
                'date',
            ],

            'keperluan' => [
                'required',
                'string',
                'max:1000',
            ],

            'dokumen' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $data = [
            'jenis_surat' => $request->jenis_surat,
            'tanggal'     => $request->tanggal,
            'keperluan'   => $request->keperluan,
        ];

        // Kalau ada dokumen baru → hapus lama, simpan baru
        if ($request->hasFile('dokumen')) {

            // Hapus file lama
            if ($pengajuanSurat->dokumen) {
                Storage::disk('public')->delete($pengajuanSurat->dokumen);
            }

            $file = $request->file('dokumen');

            $data['dokumen']      = $file->store('dokumen-surat', 'public');
            $data['nama_dokumen'] = $file->getClientOriginalName();
        }

        $pengajuanSurat->update($data);

        return back()->with(
            'success',
            'Pengajuan surat berhasil diperbarui.'
        );
    }
}