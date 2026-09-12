<?php

namespace App\Http\Controllers;

use App\Models\Pengajuancuti;
use Illuminate\Http\Request;

class CutiTambahanController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $riwayatCuti = Pengajuancuti::where('user_id', $user->id)
            ->where('jenis_cuti', 'tambahan')
            ->latest('tanggal_mulai')
            ->get();

        return view('cuti_tambahan.index', compact('riwayatCuti'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'tanggal_mulai' => [
                'required',
                'date',
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'surat' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($request->tanggal_selesai);

        $jumlahHari = 0;

        $tanggal = $tanggalMulai->copy();

        while ($tanggal->lte($tanggalSelesai)) {

            if ($tanggal->isWeekday()) {
                $jumlahHari++;
            }

            $tanggal->addDay();
        }

        if ($jumlahHari <= 0) {
            return back()
                ->withErrors([
                    'tanggal_mulai' => 'Rentang tanggal cuti tidak memiliki hari kerja.',
                ])
                ->withInput();
        }

        $surat = $request
            ->file('surat')
            ->store('surat-cuti', 'public');

        Pengajuancuti::create([
            'user_id' => $user->id,
            'jenis_cuti' => 'tambahan',
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jumlah_hari' => $jumlahHari,
            'surat' => $surat,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with(
            'success',
            'Cuti tambahan berhasil disimpan.'
        );
    }
}