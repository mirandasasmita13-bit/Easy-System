<?php

namespace App\Http\Controllers;

use App\Models\CutiTambahan;
use App\Models\TanggalMerah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CutiTambahanController extends Controller
{
    const KUOTA_TAHUNAN = 12;

    // =========================================================
    // PEGAWAI
    // =========================================================

    public function index()
    {
        $user = auth()->user();

        $riwayatCuti = CutiTambahan::where('user_id', $user->id)
            ->latest('tanggal_mulai')
            ->get();

        $nomorBerikutnya = $this->generateNomorSict();
        $kuota = $this->hitungSisaKuota($user->id);

        $daftarSict = CutiTambahan::with('user')
            ->orderByRaw('CAST(SUBSTRING(nomor_sict, 6) AS UNSIGNED) ASC')
            ->get();

        return view('cuti_tambahan.index', compact(
            'riwayatCuti',
            'nomorBerikutnya',
            'daftarSict',
            'kuota'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'surat'           => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'keterangan'      => ['nullable', 'string', 'max:1000'],
        ]);

        $tanggalMulai   = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);

        // 1. Hitung hari kerja
        $jumlahHari = $this->hitungHariKerja($tanggalMulai, $tanggalSelesai);

        if ($jumlahHari <= 0) {
            return back()
                ->withErrors([
                    'tanggal_mulai' => 'Rentang tanggal cuti tidak memiliki hari kerja (semua weekend/tanggal merah).',
                ])
                ->withInput();
        }

        // 2. Cek kuota SEBELUM simpan
        $kuota = $this->hitungSisaKuota($user->id);

        if ($jumlahHari > $kuota['sisa']) {
            return back()
                ->withErrors([
                    'tanggal_mulai' => "Kuota cuti tambahan tidak cukup. Sisa kuota: {$kuota['sisa']} hari, yang diajukan: {$jumlahHari} hari.",
                ])
                ->withInput();
        }

        // 3. Simpan surat
        $surat     = $request->file('surat')->store('surat-cuti', 'public');
        $namaSurat = $request->file('surat')->getClientOriginalName();

        // 4. Simpan ke DB
        DB::transaction(function () use ($user, $request, $jumlahHari, $surat, $namaSurat) {
            $nomorSict = $this->generateNomorSict();

            CutiTambahan::create([
                'user_id'           => $user->id,
                'nomor_sict'        => $nomorSict,
                'tanggal_pengajuan' => now()->format('Y-m-d'),
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,
                'jumlah_hari'       => $jumlahHari,
                'status'            => 'aktif',
                'surat'             => $surat,
                'nama_surat'        => $namaSurat,
                'keterangan'        => $request->keterangan,
            ]);
        });

        return back()->with('success', 'Cuti tambahan berhasil disimpan.');
    }

    /**
     * Pegawai batalkan cuti tambahannya sendiri.
     * Nomor SICT akan RELEASE (bisa dipakai orang lain).
     */
    public function cancel(Request $request, CutiTambahan $cutiTambahan)
    {
        $user = auth()->user();

        // Pastikan ini milik user yang login
        if ($cutiTambahan->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak membatalkan cuti ini.');
        }

        // Pastikan belum dibatalkan
        if ($cutiTambahan->status === 'dibatalkan') {
            return back()->withErrors(['msg' => 'Cuti ini sudah dibatalkan.']);
        }

        $cutiTambahan->update([
            'status'          => 'dibatalkan',
            'dibatalkan_at'   => now(),
            'dibatalkan_oleh' => $user->id,
            'alasan_batal'    => $request->alasan_batal,
        ]);

        return back()->with('success', "Cuti {$cutiTambahan->nomor_sict} berhasil dibatalkan. Nomor SICT kembali tersedia.");
    }

    // =========================================================
    // ADMIN — MONITORING
    // =========================================================

        public function adminIndex(Request $request)
            {
                $query = CutiTambahan::with('user');

                // Filter by pegawai
                if ($request->filled('user_id')) {
                    $query->where('user_id', $request->user_id);
                }

                // Filter by status
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

                // Search
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('nomor_sict', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                    });
                }

                $daftarSict = $query
                    ->orderByRaw('CAST(SUBSTRING(nomor_sict, 6) AS UNSIGNED) ASC')
                    ->get();

                $daftarPegawai = User::where('role', 'pegawai')
                    ->where('status', 'aktif')
                    ->orderBy('name')
                    ->get();

                // =========================================================
                // REKAP KUOTA SEMUA PEGAWAI
                // =========================================================
                $rekapKuota = $daftarPegawai->map(function ($pegawai) {
                    $terpakai = CutiTambahan::where('user_id', $pegawai->id)
                        ->where('status', 'aktif')
                        ->whereYear('tanggal_mulai', now()->year)
                        ->sum('jumlah_hari');

                    return [
                        'id'       => $pegawai->id,
                        'nama'     => $pegawai->name,
                        'total'    => self::KUOTA_TAHUNAN,
                        'terpakai' => (int) $terpakai,
                        'sisa'     => self::KUOTA_TAHUNAN - (int) $terpakai,
                    ];
                });

                // Statistik
                $totalAktif      = CutiTambahan::aktif()->count();
                $totalDibatalkan = CutiTambahan::dibatalkan()->count();

                return view('cuti_tambahan.admin_index', compact(
                    'daftarSict',
                    'daftarPegawai',
                    'rekapKuota',
                    'totalAktif',
                    'totalDibatalkan'
                ));
            }

    /**
     * Admin edit SICT (tanggal, keterangan).
     */
    public function adminUpdate(Request $request, CutiTambahan $cutiTambahan)
    {
        $request->validate([
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan'      => ['nullable', 'string', 'max:1000'],
        ]);

        $tanggalMulai   = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);

        $jumlahHari = $this->hitungHariKerja($tanggalMulai, $tanggalSelesai);

        if ($jumlahHari <= 0) {
            return back()->withErrors([
                'tanggal_mulai' => 'Rentang tanggal cuti tidak memiliki hari kerja.',
            ])->withInput();
        }

        $cutiTambahan->update([
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jumlah_hari'     => $jumlahHari,
            'keterangan'      => $request->keterangan,
            'diedit_at'       => now(),
            'diedit_oleh'     => auth()->id(),
        ]);

        return back()->with('success', "SICT {$cutiTambahan->nomor_sict} berhasil diupdate.");
    }

    /**
     * Admin batalkan SICT.
     */
    public function adminCancel(Request $request, CutiTambahan $cutiTambahan)
    {
        if ($cutiTambahan->status === 'dibatalkan') {
            return back()->withErrors(['msg' => 'Cuti ini sudah dibatalkan.']);
        }

        $cutiTambahan->update([
            'status'          => 'dibatalkan',
            'dibatalkan_at'   => now(),
            'dibatalkan_oleh' => auth()->id(),
            'alasan_batal'    => $request->alasan_batal ?? 'Dibatalkan oleh admin',
        ]);

        return back()->with('success', "SICT {$cutiTambahan->nomor_sict} berhasil dibatalkan.");
    }

    public function downloadSurat(CutiTambahan $cutiTambahan)
    {
        if (!$cutiTambahan->surat || $cutiTambahan->surat === '-') {
            abort(404, 'Surat tidak tersedia.');
        }

        $path = storage_path('app/public/' . $cutiTambahan->surat);

        if (!file_exists($path)) {
            abort(404, 'File surat tidak ditemukan.');
        }

        $nama = $cutiTambahan->nama_surat ?: 'surat-' . $cutiTambahan->nomor_sict . '.pdf';

        return response()->download($path, $nama);
    }

    // =========================================================
    // HELPER
    // =========================================================

    private function hitungHariKerja(Carbon $mulai, Carbon $selesai): int
    {
        $jumlah  = 0;
        $tanggal = $mulai->copy();

        while ($tanggal->lte($selesai)) {
            if ($tanggal->isWeekend()) {
                $tanggal->addDay();
                continue;
            }

            if (TanggalMerah::isMerah($tanggal->format('Y-m-d'))) {
                $tanggal->addDay();
                continue;
            }

            $jumlah++;
            $tanggal->addDay();
        }

        return $jumlah;
    }

    private function generateNomorSict(): string
    {
        $terakhir = CutiTambahan::orderByRaw('CAST(SUBSTRING(nomor_sict, 6) AS UNSIGNED) DESC')
            ->value('nomor_sict');

        if (!$terakhir) {
            return 'SICT-1';
        }

        $angka = (int) substr($terakhir, 5);
        $angka++;

        return 'SICT-' . $angka;
    }

    private function hitungSisaKuota($userId): array
    {
        $terpakai = CutiTambahan::where('user_id', $userId)
            ->where('status', 'aktif')
            ->whereYear('tanggal_mulai', now()->year)
            ->sum('jumlah_hari');

        return [
            'total'    => self::KUOTA_TAHUNAN,
            'terpakai' => $terpakai,
            'sisa'     => self::KUOTA_TAHUNAN - $terpakai,
        ];
    }
}