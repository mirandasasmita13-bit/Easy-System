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

        $daftarSict = CutiTambahan::with('user')
            ->orderByRaw('CAST(SUBSTRING(nomor_sict, 6) AS UNSIGNED) ASC')
            ->get();

        return view('cuti_tambahan.index', compact(
            'riwayatCuti',
            'nomorBerikutnya',
            'daftarSict'
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

        $jumlahHari = $this->hitungHariKerja($tanggalMulai, $tanggalSelesai);

        if ($jumlahHari <= 0) {
            return back()
                ->withErrors([
                    'tanggal_mulai' => 'Rentang tanggal cuti tidak memiliki hari kerja (semua weekend/tanggal merah).',
                ])
                ->withInput();
        }

        $surat     = $request->file('surat')->store('surat-cuti', 'public');
        $namaSurat = $request->file('surat')->getClientOriginalName();

        DB::transaction(function () use ($user, $request, $jumlahHari, $surat, $namaSurat) {
            $nomorSict = $this->generateNomorSict();

            CutiTambahan::create([
                'user_id'           => $user->id,
                'nomor_sict'        => $nomorSict,
                'tanggal_pengajuan' => now()->format('Y-m-d'),
                'tanggal_mulai'     => $request->tanggal_mulai,
                'tanggal_selesai'   => $request->tanggal_selesai,
                'jumlah_hari'       => $jumlahHari,
                'surat'             => $surat,
                'nama_surat'        => $namaSurat,
                'keterangan'        => $request->keterangan,
            ]);
        });

        return back()->with('success', 'Cuti tambahan berhasil disimpan.');
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

        // Search by nomor SICT atau nama
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
            ->orderBy('name')
            ->get();

        return view('cuti_tambahan.admin_index', compact('daftarSict', 'daftarPegawai'));
    }

    public function downloadSurat(CutiTambahan $cutiTambahan)
    {
        // Kalau surat kosong / placeholder, tolak
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
}