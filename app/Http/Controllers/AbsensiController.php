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

        // Absensi hari ini (apapun shift-nya)
        $absensiHariIni = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->latest()
            ->first();

        // Absensi yang BELUM pulang (untuk logic tombol di view)
        $absensiBelumPulang = Absensi::where('user_id', $user->id)
            ->whereNull('jam_pulang')
            ->whereNotNull('jam_masuk')
            ->latest('tanggal')
            ->latest('jam_masuk')
            ->first();

        $riwayatAbsensi = Absensi::where('user_id', $user->id)
            ->latest('tanggal')
            ->latest('jam_masuk')
            ->get();

        return view('absensi.index', [
            'absensiHariIni'     => $absensiHariIni,
            'absensiBelumPulang' => $absensiBelumPulang,
            'riwayatAbsensi'     => $riwayatAbsensi,
            'kantorLatitude'     => $this->kantorLatitude,
            'kantorLongitude'    => $this->kantorLongitude,
            'radiusMaksimal'     => $this->radiusMaksimal,
            'kantorAlamat'       => $this->kantorAlamat,
        ]);
    }

    // ==========================================
    // ABSEN MASUK
    // ==========================================
    public function masuk(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'shift'      => 'required|in:pagi,malam',
            'latitude'   => ['required', 'numeric', 'between:-90,90'],
            'longitude'  => ['required', 'numeric', 'between:-180,180'],
            'foto_masuk' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // =====================================================
        // 1. CEK: Ada absen yang BELUM pulang?
        // =====================================================
        $absenMenggantung = Absensi::where('user_id', $user->id)
            ->whereNull('jam_pulang')
            ->whereNotNull('jam_masuk')
            ->latest('tanggal')
            ->latest('jam_masuk')
            ->first();

        if ($absenMenggantung) {
            return back()->with(
                'error',
                'Anda masih memiliki absen masuk yang belum pulang di tanggal ' .
                $absenMenggantung->tanggal->format('d M Y') .
                '. Silakan absen pulang terlebih dahulu.'
            );
        }

        // =====================================================
        // 2. CEK: Sudah absen shift yang SAMA di hari ini?
        // =====================================================
        $sudahAbsenShift = Absensi::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->where('shift', $request->shift)
            ->exists();

        if ($sudahAbsenShift) {
            return back()->with(
                'error',
                'Anda sudah melakukan absen masuk untuk shift ' .
                ucfirst($request->shift) . ' hari ini.'
            );
        }

        // =====================================================
        // 3. HITUNG JARAK
        // =====================================================
        $jarak = $this->hitungJarak(
            $this->kantorLatitude,
            $this->kantorLongitude,
            (float) $request->latitude,
            (float) $request->longitude
        );

        if ($jarak > $this->radiusMaksimal) {
            return back()->with(
                'error',
                'Absen ditolak. Anda berada di luar radius kantor ' .
                '(maksimal ' . $this->radiusMaksimal . ' meter). Jarak Anda sekitar ' .
                round($jarak) . ' meter.'
            );
        }

        // =====================================================
        // 4. SIMPAN FOTO
        // =====================================================
        $foto = $request->file('foto_masuk')->store('absensi', 'public');

        // =====================================================
        // 5. SIMPAN ABSENSI
        // =====================================================
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

        return back()->with(
            'success',
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
            'latitude'    => ['required', 'numeric', 'between:-90,90'],
            'longitude'   => ['required', 'numeric', 'between:-180,180'],
            'foto_pulang' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // =====================================================
        // 1. CARI ABSENSI TERAKHIR YANG BELUM PULANG
        // =====================================================
        // FIX BUG: Tidak peduli tanggal — yang penting belum pulang.
        // Ini yang bikin shift malam berfungsi:
        // - Masuk 19 Sep sore → belum pulang
        // - Pulang 20 Sep pagi → update record 19 Sep
        // =====================================================
        $absensi = Absensi::where('user_id', $user->id)
            ->whereNull('jam_pulang')
            ->whereNotNull('jam_masuk')
            ->latest('tanggal')
            ->latest('jam_masuk')
            ->first();

        if (!$absensi) {
            return back()->with(
                'error',
                'Tidak ditemukan absensi yang dapat digunakan untuk absen pulang. ' .
                'Silakan absen masuk terlebih dahulu.'
            );
        }

        // =====================================================
        // 2. HITUNG JARAK
        // =====================================================
        $jarak = $this->hitungJarak(
            $this->kantorLatitude,
            $this->kantorLongitude,
            (float) $request->latitude,
            (float) $request->longitude
        );

        if ($jarak > $this->radiusMaksimal) {
            return back()->with(
                'error',
                'Absen pulang ditolak. Anda berada di luar radius kantor ' .
                '(maksimal ' . $this->radiusMaksimal . ' meter). Jarak Anda sekitar ' .
                round($jarak) . ' meter.'
            );
        }

        // =====================================================
        // 3. SIMPAN FOTO
        // =====================================================
        $foto = $request->file('foto_pulang')->store('absensi', 'public');

        // =====================================================
        // 4. UPDATE ABSENSI
        // =====================================================
        // Tambah `tanggal_pulang` untuk catat tanggal pulang
        // (kalau beda hari dengan tanggal masuk — misal shift malam)
        // =====================================================
        $absensi->update([
            'tanggal_pulang'    => now()->toDateString(),
            'jam_pulang'        => now()->format('H:i:s'),
            'latitude_pulang'   => $request->latitude,
            'longitude_pulang'  => $request->longitude,
            'jarak_pulang'      => round($jarak, 2),
            'foto_pulang'       => $foto,
        ]);

        return back()->with(
            'success',
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