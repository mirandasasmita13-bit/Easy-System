<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    // Koordinat kantor
    private $officeLatitude = 4.636822941619201;
    private $officeLongitude = 96.84824583097509;
    private $maxRadius = 200;


    // Halaman lembur
    public function index()
    {
        $user = auth()->user();

        $riwayatLembur = Lembur::where('user_id', $user->id)
            ->latest('tanggal')
            ->latest('jam_mulai')
            ->get();

        return view('lembur.index', compact('riwayatLembur'));
    }


    // Simpan lembur
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'tanggal' => ['required', 'date'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'kegiatan' => ['required', 'string', 'max:255'],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        // Cek lokasi
        $latitude = (float) $request->latitude;
        $longitude = (float) $request->longitude;

        $jarak = $this->hitungJarak(
            $latitude,
            $longitude,
            $this->officeLatitude,
            $this->officeLongitude
        );

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

        // Simpan foto
        $foto = $request->file('foto')->store('foto-lembur', 'public');

        // Hitung total jam
        $totalJam = Lembur::hitungTotalJam($request->jam_mulai, $request->jam_selesai);

        // Simpan data lembur
        Lembur::create([
            'user_id'         => $user->id,
            'tanggal'         => $request->tanggal,
            'jam_mulai'       => $request->jam_mulai,
            'jam_selesai'     => $request->jam_selesai,
            'total_jam'       => $totalJam,
            'kegiatan'        => $request->kegiatan,
            'keterangan'      => null,
            'foto'            => $foto,
            'status_approval' => 'pending',
        ]);

        return back()->with(
            'success',
            'Data lembur berhasil disimpan, menunggu persetujuan admin.'
        );
    }


    // Admin: approve lembur
    public function approve(Request $request, Lembur $lembur)
    {
        if ($lembur->status_approval !== 'pending') {
            return back()->with('error', 'Pengajuan lembur ini sudah diproses sebelumnya.');
        }

        $lembur->update([
            'status_approval' => 'approved',
            'approved_by'     => auth()->id(),
            'approved_at'     => now(),
        ]);

        return back()->with('success', 'Pengajuan lembur disetujui.');
    }


    // Admin: reject lembur
    public function reject(Request $request, Lembur $lembur)
    {
        if ($lembur->status_approval !== 'pending') {
            return back()->with('error', 'Pengajuan lembur ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $lembur->update([
            'status_approval' => 'rejected',
            'catatan_admin'   => $request->catatan_admin,
            'approved_by'     => auth()->id(),
            'approved_at'     => now(),
        ]);

        return back()->with('success', 'Pengajuan lembur ditolak.');
    }


    // Hitung jarak (Haversine)
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
            * cos($lat2Rad)
            * sin($deltaLon / 2)
            * sin($deltaLon / 2);

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $earthRadius * $c;
    }
}