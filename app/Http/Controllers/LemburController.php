<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | KOORDINAT KANTOR
    |--------------------------------------------------------------------------
    */

    private $officeLatitude = 4.636822941619201;
    private $officeLongitude = 96.84824583097509;
    private $maxRadius = 500;


    /*
    |--------------------------------------------------------------------------
    | HALAMAN LEMBUR
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        $riwayatLembur = Lembur::where('user_id', $user->id)
            ->latest('tanggal')
            ->latest('jam_mulai')
            ->get();

        return view('lembur.index', compact('riwayatLembur'));
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN LEMBUR
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DATA
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'tanggal' => [
                'required',
                'date',
            ],

            'jam_mulai' => [
                'required',
                'date_format:H:i',
            ],

            'jam_selesai' => [
                'required',
                'date_format:H:i',
            ],

            'kegiatan' => [
                'required',
                'string',
                'max:255',
            ],

            'foto' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'latitude' => [
                'required',
                'numeric',
            ],

            'longitude' => [
                'required',
                'numeric',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | CEK LOKASI
        |--------------------------------------------------------------------------
        */

        $latitude = (float) $request->latitude;
        $longitude = (float) $request->longitude;

        $jarak = $this->hitungJarak(
            $latitude,
            $longitude,
            $this->officeLatitude,
            $this->officeLongitude
        );


        /*
        | Kalau lebih dari 500 meter → tidak boleh lembur
        */

        if ($jarak > $this->maxRadius) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Kamu berada di luar radius kantor. Jarak kamu sekitar ' .
                    round($jarak) .
                    ' meter dari kantor.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN FOTO
        |--------------------------------------------------------------------------
        */

        $foto = $request
            ->file('foto')
            ->store('foto-lembur', 'public');


        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA LEMBUR
        |--------------------------------------------------------------------------
        */

        Lembur::create([
            'user_id' => $user->id,

            'tanggal' => $request->tanggal,

            'jam_mulai' => $request->jam_mulai,

            'jam_selesai' => $request->jam_selesai,

            'kegiatan' => $request->kegiatan,

            /*
            | Kolom keterangan tidak digunakan lagi.
            | Kalau kolomnya masih ada di database,
            | tidak masalah dibiarkan nullable.
            */

            'keterangan' => null,

            'foto' => $foto,
        ]);


        /*
        |--------------------------------------------------------------------------
        | BERHASIL
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Data lembur berhasil disimpan.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG JARAK
    |--------------------------------------------------------------------------
    |
    | Menggunakan rumus Haversine.
    | Hasil dalam meter.
    |
    */

    private function hitungJarak(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {

        $earthRadius = 6371000;

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);

        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a =
            sin($deltaLat / 2) * sin($deltaLat / 2)
            +
            cos($lat1Rad)
            *
            cos($lat2Rad)
            *
            sin($deltaLon / 2)
            *
            sin($deltaLon / 2);

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $earthRadius * $c;
    }
}