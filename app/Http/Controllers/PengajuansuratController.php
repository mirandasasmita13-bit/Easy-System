<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use Illuminate\Http\Request;

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
     * Simpan pengajuan surat
     */
    public function store(Request $request)
    {
        $user = auth()->user();

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

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DOKUMEN
        |--------------------------------------------------------------------------
        */

        $dokumenPath = null;
        $namaDokumen = null;

        if ($request->hasFile('dokumen')) {

            $file = $request->file('dokumen');

            // Simpan file ke:
            // storage/app/public/dokumen-surat
            $dokumenPath = $file->store(
                'dokumen-surat',
                'public'
            );

            // Simpan nama file asli
            $namaDokumen = $file->getClientOriginalName();
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA KE DATABASE
        |--------------------------------------------------------------------------
        */

        PengajuanSurat::create([
            'user_id' => $user->id,
            'jenis_surat' => $request->jenis_surat,
            'tanggal' => $request->tanggal,
            'keperluan' => $request->keperluan,
            'dokumen' => $dokumenPath,
            'nama_dokumen' => $namaDokumen,
        ]);

        /*
        |--------------------------------------------------------------------------
        | KEMBALI KE HALAMAN
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Pengajuan surat berhasil disimpan.'
        );
    }
}