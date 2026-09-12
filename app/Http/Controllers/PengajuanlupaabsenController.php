<?php

namespace App\Http\Controllers;

use App\Models\Pengajuanlupaabsen;
use Illuminate\Http\Request;

class PengajuanlupaabsenController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $riwayatLupaAbsen = Pengajuanlupaabsen::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('lupa_absen.index', compact('riwayatLupaAbsen'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'tanggal' => [
                'required',
                'date',
            ],

            'jenis_absen' => [
                'required',
                'in:masuk,pulang',
            ],

            'jam' => [
                'required',
                'date_format:H:i',
            ],

            'bukti' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],

            'alasan' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        $bukti = null;

        if ($request->hasFile('bukti')) {
            $bukti = $request
                ->file('bukti')
                ->store('bukti-lupa-absen', 'public');
        }

        Pengajuanlupaabsen::create([
            'user_id' => $user->id,
            'tanggal' => $request->tanggal,
            'jenis_absen' => $request->jenis_absen,
            'jam' => $request->jam,
            'bukti' => $bukti,
            'alasan' => $request->alasan,
        ]);

        return back()->with(
            'success',
            'Perbaikan absensi berhasil disimpan.'
        );
    }
}