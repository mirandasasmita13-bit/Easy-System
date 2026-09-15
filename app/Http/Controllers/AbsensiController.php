<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    // LOKASI KANTOR
    private float $kantorLatitude  = 4.636822941619201;
    private float $kantorLongitude = 96.84824583097509;
    private float $radiusMaksimal  = 200;
    private string $kantorAlamat   = 'Jl. Qurata Aini No.96, Nunang Antara, Kec. Bebesen, Kabupaten Aceh Tengah, Aceh 24519';

    // ==========================================
    // HALAMAN ABSENSI
    // ==========================================
    public function index()
    {
        $user = auth()->user();

        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->latest()
            ->first();

        $riwayatAbsensi = Absensi::where('user_id', $user->id)
            ->latest('tanggal')
            ->latest('jam_masuk')
            ->get();

        return view('absensi.index', [
            'absensiHariIni'  => $absensiHariIni,
            'riwayatAbsensi'  => $riwayatAbsensi,
            'kantorLatitude'  => $this->kantorLatitude,
            'kantorLongitude' => $this->kantorLongitude,
            'radiusMaksimal'  => $this->radiusMaksimal,
            'kantorAlamat'    => $this->kantorAlamat,
        ]);
    }

    // ==========================================
    // ABSEN MASUK
    // ==========================================
    public function masuk(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'shift' => 'required|in:pagi,malam',
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'foto_masuk' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // Cek sudah absen untuk shift yang sama
        $sudahAbsenShift = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->where('shift', $request->shift)
            ->exists();

        if ($sudahAbsenShift) {
            return back()->with('error',
                'Anda sudah melakukan absen masuk untuk shift ' .
                ucfirst($request->shift) . ' hari ini.'
            );
        }

        // Hitung jarak
        $jarak = $this->hitungJarak(
            $this->kantorLatitude,
            $this->kantorLongitude,
            (float) $request->latitude,
            (float) $request->longitude
        );

        // Cek radius
        if ($jarak > $this->radiusMaksimal) {
            return back()->with('error',
                'Absen ditolak. Anda berada di luar radius kantor ' .
                '(maksimal ' . $this->radiusMaksimal . ' meter). Jarak Anda sekitar ' .
                round($jarak) . ' meter.'
            );
        }

        // Simpan foto
        $foto = $request->file('foto_masuk')->store('absensi', 'public');

        // Simpan absensi
        Absensi::create([
            'user_id'         => $user->id,
            'tanggal'         => today(),
            'shift'           => $request->shift,
            'jam_masuk'       => now()->format('H:i:s'),
            'keterangan'      => 'H',
            'status_approval' => 'normal',
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'jarak'           => round($jarak, 2),
            'foto_masuk'      => $foto,
        ]);

        return back()->with('success',
            'Absen masuk berhasil. Jarak Anda dari kantor ' . round($jarak) . ' meter.'
        );
    }

    // ==========================================
    // ABSEN PULANG
    // ==========================================
    public function pulang(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'foto_pulang' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // Cari absensi yang belum pulang
        $absensi = Absensi::where('user_id', $user->id)
            ->whereNull('jam_pulang')
            ->where(function ($query) {
                $query->whereDate('tanggal', today())
                    ->orWhere(function ($query) {
                        $query->whereDate('tanggal', today()->subDay())
                              ->where('shift', 'malam');
                    });
            })
            ->latest('tanggal')
            ->latest('jam_masuk')
            ->first();

        if (!$absensi) {
            return back()->with('error',
                'Tidak ditemukan absensi yang dapat digunakan untuk absen pulang.'
            );
        }

        // Hitung jarak
        $jarak = $this->hitungJarak(
            $this->kantorLatitude,
            $this->kantorLongitude,
            (float) $request->latitude,
            (float) $request->longitude
        );

        // Cek radius
        if ($jarak > $this->radiusMaksimal) {
            return back()->with('error',
                'Absen pulang ditolak. Anda berada di luar radius kantor ' .
                '(maksimal ' . $this->radiusMaksimal . ' meter). Jarak Anda sekitar ' .
                round($jarak) . ' meter.'
            );
        }

        // Simpan foto
        $foto = $request->file('foto_pulang')->store('absensi', 'public');

        // Update absensi
        $absensi->update([
            'jam_pulang'        => now()->format('H:i:s'),
            'latitude_pulang'   => $request->latitude,
            'longitude_pulang'  => $request->longitude,
            'jarak_pulang'      => round($jarak, 2),
            'foto_pulang'       => $foto,
        ]);

        return back()->with('success',
            'Absen pulang berhasil. Jarak Anda dari kantor ' . round($jarak) . ' meter.'
        );
    }

    // ==========================================
    // HITUNG JARAK (Haversine)
    // ==========================================
    private function hitungJarak(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000;

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);

        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2 +
             cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}