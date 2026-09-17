<?php

namespace App\Http\Controllers;

use App\Exports\RekapabsensiExport;
use App\Exports\RekapSayaExport;
use App\Models\Absensi;
use App\Models\Lembur;
use App\Models\Pengajuancuti;
use App\Models\Pengajuanlupaabsen;
use App\Models\Pengajuansurat;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RekapabsensiController extends Controller
{
    /* =========================================================
       HELPER
       ========================================================= */

    /**
     * Susun data absensi per-user / per-tanggal.
     * TIDAK dipakai untuk hitung statistik — hanya untuk tampilan matrix.
     */
    private function susunAbsensi($absensiData, bool $perUser = true): array
    {
        $absensi      = [];
        $absensiHadir = [];

        foreach ($absensiData as $item) {
            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            if ($perUser) {
                $absensi[$item->user_id][$tanggalKey] = $item;
                if ($item->status_approval !== 'pending') {
                    $absensiHadir[$item->user_id][$tanggalKey] = $item;
                }
            } else {
                $absensi[$tanggalKey] = $item;
                if ($item->status_approval !== 'pending') {
                    $absensiHadir[$tanggalKey] = $item;
                }
            }
        }

        return [$absensi, $absensiHadir];
    }

    /**
     * Cek apakah pengajuan lupa absen sudah disetujui.
     * Support berbagai variasi field & value.
     */
    private function isLupaApproved($lupa): bool
    {
        if (!$lupa) return false;

        $approved = ['approved', 'disetujui', 'diterima', 'setuju', 'accept', 'accepted', 'terima'];

        foreach (['status', 'status_approval', 'status_pengajuan', 'approval_status', 'status_verifikasi'] as $field) {
            if (!isset($lupa->$field)) continue;
            $val = strtolower(trim((string) $lupa->$field));
            if (in_array($val, $approved, true)) return true;
        }
        return false;
    }

    /**
     * RULE UTAMA — tentukan kode & hitung per tanggal.
     * Dipakai SEMUA method (view, PDF, Excel) supaya konsisten.
     */
    private function evaluasiHari($tgl, $absensiHariIni, $cutiHariIni, $lupaHariIni, $sakitHariIni): array
    {
        $lupaApproved = $this->isLupaApproved($lupaHariIni);

        $absensiValid = $absensiHariIni
            && $absensiHariIni->jam_masuk
            && ($absensiHariIni->status_approval !== 'pending' || $lupaApproved);

        $absensiPending = $absensiHariIni
            && $absensiHariIni->status_approval === 'pending'
            && !$lupaApproved;

        $kode      = '-';
        $kategori  = 'kosong';
        $jamMasuk  = null;
        $jamPulang = null;

        if ($tgl->isWeekend()) {
            if ($absensiValid) {
                if ($absensiHariIni->shift === 'malam') {
                    $kode = 'M'; $kategori = 'malam';
                } else {
                    $kode = 'H'; $kategori = 'hadir';
                }
                $jamMasuk  = $absensiHariIni->jam_masuk;
                $jamPulang = $absensiHariIni->jam_pulang;
            } elseif ($absensiPending) {
                $kode = 'P'; $kategori = 'pending';
                $jamMasuk = $absensiHariIni->jam_masuk;
            } else {
                $kode = 'LIB'; $kategori = 'libur';
            }
        } else {
            if ($sakitHariIni) {
                $kode = 'S'; $kategori = 'sakit';
            } elseif ($cutiHariIni) {
                if ($cutiHariIni->jenis_cuti === 'alasan_penting') {
                    $kode = 'CAP'; $kategori = 'cap';
                } else {
                    $kode = 'C'; $kategori = 'cuti';
                }
            } elseif ($absensiValid) {
                if ($absensiHariIni->shift === 'malam') {
                    $kode = 'M'; $kategori = 'malam';
                } else {
                    $kode = 'H'; $kategori = 'hadir';
                }
                $jamMasuk  = $absensiHariIni->jam_masuk;
                $jamPulang = $absensiHariIni->jam_pulang;
            } elseif ($lupaApproved) {
                $kode = 'H'; $kategori = 'hadir';
            } elseif ($lupaHariIni && !$absensiPending) {
                $kode = 'LA'; $kategori = 'lupa';
            } elseif ($absensiPending) {
                $kode = 'P'; $kategori = 'pending';
                $jamMasuk = $absensiHariIni->jam_masuk;
            }
        }

        return [
            'kode'        => $kode,
            'kategori'    => $kategori,
            'jam_masuk'   => $jamMasuk,
            'jam_pulang'  => $jamPulang,
            'is_pending'  => $absensiPending,
            'lupa_approved' => $lupaApproved,
        ];
    }

    /**
     * Hitung statistik dari hasil evaluasi.
     */
    private function hitungStatistik($tanggal, $absensi, $cuti, $lupaAbsen, $suratSakit, $perUser = false, $userId = null): array
    {
        $stat = [
            'hadir'             => 0,
            'cuti_tahunan'      => 0,
            'cuti_alasan'       => 0,
            'sakit'             => 0,
            'lupa'              => 0,
            'pending'           => 0,
            'libur'             => 0,
        ];

        foreach ($tanggal as $tgl) {
            $tanggalKey = $tgl->format('Y-m-d');

            if ($perUser) {
                $absensiHariIni = $absensi[$userId][$tanggalKey] ?? null;
                $cutiHariIni    = $cuti[$userId][$tanggalKey] ?? null;
                $lupaHariIni    = $lupaAbsen[$userId][$tanggalKey] ?? null;
                $sakitHariIni   = $suratSakit[$userId][$tanggalKey] ?? null;
            } else {
                $absensiHariIni = $absensi[$tanggalKey] ?? null;
                $cutiHariIni    = $cuti[$tanggalKey] ?? null;
                $lupaHariIni    = $lupaAbsen[$tanggalKey] ?? null;
                $sakitHariIni   = $suratSakit[$tanggalKey] ?? null;
            }

            $hasil = $this->evaluasiHari($tgl, $absensiHariIni, $cutiHariIni, $lupaHariIni, $sakitHariIni);

            switch ($hasil['kategori']) {
                case 'hadir':
                case 'malam':
                    $stat['hadir']++;
                    break;
                case 'cuti':
                    $stat['cuti_tahunan']++;
                    break;
                case 'cap':
                    $stat['cuti_alasan']++;
                    break;
                case 'sakit':
                    $stat['sakit']++;
                    break;
                case 'lupa':
                    $stat['lupa']++;
                    break;
                case 'pending':
                    $stat['pending']++;
                    break;
                case 'libur':
                    $stat['libur']++;
                    break;
            }
        }

        return $stat;
    }


    /* =========================================================
       REKAP ABSENSI ADMIN
       ========================================================= */
    public function index(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $tanggalAwal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $tanggal = [];
        $hari = $tanggalAwal->copy();
        while ($hari->lte($tanggalAkhir)) {
            $tanggal[] = $hari->copy();
            $hari->addDay();
        }

        $ppnpn = User::where('role', 'ppnpn')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $absensiData = Absensi::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn')->where('status', 'aktif');
            })
            ->get();

        [$absensi, $absensiHadir] = $this->susunAbsensi($absensiData, true);

        // CUTI
        $cutiData = Pengajuancuti::whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn')->where('status', 'aktif');
            })
            ->get();

        $cuti = [];
        foreach ($cutiData as $item) {
            $mulai   = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);
            while ($mulai->lte($selesai)) {
                if (!$mulai->isWeekend() && $mulai->between($tanggalAwal, $tanggalAkhir)) {
                    $cuti[$item->user_id][$mulai->format('Y-m-d')] = $item;
                }
                $mulai->addDay();
            }
        }

        // LUPA ABSEN
        $lupaData = Pengajuanlupaabsen::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn')->where('status', 'aktif');
            })
            ->get();

        $lupaAbsen = [];
        foreach ($lupaData as $item) {
            $lupaAbsen[$item->user_id][Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // SURAT SAKIT
        $sakitData = Pengajuansurat::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn')->where('status', 'aktif');
            })
            ->where(function ($q) {
                $q->whereRaw('LOWER(COALESCE(jenis_surat, "")) LIKE ?', ['%sakit%'])
                  ->orWhereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%sakit%'])
                  ->orWhere(function ($sub) {
                      $sub->whereRaw('LOWER(COALESCE(jenis_surat, "")) = ?', ['surat_keterangan'])
                          ->whereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%berobat%']);
                  });
            })
            ->get();

        $suratSakit = [];
        foreach ($sakitData as $item) {
            $suratSakit[$item->user_id][Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        return view('rekapabsensi.index', compact(
            'bulan', 'tahun', 'tanggalAwal', 'tanggalAkhir', 'tanggal',
            'ppnpn',
            'absensi', 'absensiHadir',
            'cuti', 'lupaAbsen', 'suratSakit'
        ));
    }


    /* =========================================================
       EXPORT EXCEL ADMIN
       ========================================================= */
    public function exportExcel(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        return Excel::download(
            new RekapabsensiExport($bulan, $tahun),
            'rekap-absensi-' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx'
        );
    }


    /* =========================================================
       EXPORT PDF ADMIN
       ========================================================= */
    public function exportPdf(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $tanggalAwal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $tanggal = [];
        $hari = $tanggalAwal->copy();
        while ($hari->lte($tanggalAkhir)) {
            $tanggal[] = $hari->copy();
            $hari->addDay();
        }

        $ppnpn = User::where('role', 'ppnpn')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        $absensiData = Absensi::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn')->where('status', 'aktif');
            })
            ->get();

        [$absensi, $absensiHadir] = $this->susunAbsensi($absensiData, true);

        // CUTI
        $cutiData = Pengajuancuti::whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn')->where('status', 'aktif');
            })
            ->get();

        $cuti = [];
        foreach ($cutiData as $item) {
            $mulai = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);
            while ($mulai->lte($selesai)) {
                if (!$mulai->isWeekend() && $mulai->between($tanggalAwal, $tanggalAkhir)) {
                    $cuti[$item->user_id][$mulai->format('Y-m-d')] = $item;
                }
                $mulai->addDay();
            }
        }

        // LUPA ABSEN
        $lupaData = Pengajuanlupaabsen::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn')->where('status', 'aktif');
            })
            ->get();

        $lupaAbsen = [];
        foreach ($lupaData as $item) {
            $lupaAbsen[$item->user_id][Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // SURAT SAKIT
        $sakitData = Pengajuansurat::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn')->where('status', 'aktif');
            })
            ->where(function ($q) {
                $q->whereRaw('LOWER(COALESCE(jenis_surat, "")) LIKE ?', ['%sakit%'])
                  ->orWhereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%sakit%'])
                  ->orWhere(function ($sub) {
                      $sub->whereRaw('LOWER(COALESCE(jenis_surat, "")) = ?', ['surat_keterangan'])
                          ->whereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%berobat%']);
                  });
            })
            ->get();

        $suratSakit = [];
        foreach ($sakitData as $item) {
            $suratSakit[$item->user_id][Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        $pdf = Pdf::loadView('rekapabsensi.pdf', compact(
            'bulan', 'tahun', 'tanggalAwal', 'tanggalAkhir', 'tanggal',
            'ppnpn',
            'absensi', 'absensiHadir',
            'cuti', 'lupaAbsen', 'suratSakit'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'rekap-absensi-' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.pdf'
        );
    }


    /* =========================================================
       REKAP SAYA (individu)
       ========================================================= */
    public function rekapSaya(Request $request)
    {
        $user  = auth()->user();
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $tanggalAwal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $tanggal = [];
        $hari = $tanggalAwal->copy();
        while ($hari->lte($tanggalAkhir)) {
            $tanggal[] = $hari->copy();
            $hari->addDay();
        }

        // ABSENSI
        $absensiData = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        [$absensi, $absensiHadir] = $this->susunAbsensi($absensiData, false);

        // CUTI
        $cutiData = Pengajuancuti::where('user_id', $user->id)
            ->whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->get();

        $cuti = [];
        foreach ($cutiData as $item) {
            $mulai = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);
            while ($mulai->lte($selesai)) {
                if (!$mulai->isWeekend() && $mulai->between($tanggalAwal, $tanggalAkhir)) {
                    $cuti[$mulai->format('Y-m-d')] = $item;
                }
                $mulai->addDay();
            }
        }

        // LUPA ABSEN
        $lupaData = Pengajuanlupaabsen::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lupaAbsen = [];
        foreach ($lupaData as $item) {
            $lupaAbsen[Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // SURAT SAKIT
        $suratSakitData = Pengajuansurat::where('user_id', $user->id)
            ->whereDate('tanggal', '>=', $tanggalAwal->format('Y-m-d'))
            ->whereDate('tanggal', '<=', $tanggalAkhir->format('Y-m-d'))
            ->where(function ($query) {
                $query->whereRaw('LOWER(COALESCE(jenis_surat, "")) LIKE ?', ['%sakit%'])
                    ->orWhereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%sakit%'])
                    ->orWhere(function ($q) {
                        $q->whereRaw('LOWER(COALESCE(jenis_surat, "")) = ?', ['surat_keterangan'])
                          ->whereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%berobat%']);
                    });
            })
            ->get();

        $suratSakit = [];
        foreach ($suratSakitData as $item) {
            $suratSakit[Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // LEMBUR
        $lemburData = Lembur::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->where('status_approval', 'approved')
            ->get();

        $lembur = [];
        foreach ($lemburData as $item) {
            $lembur[Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // =====================================================
        // HITUNG STATISTIK — pakai helper yang sama
        // =====================================================
        $stat = $this->hitungStatistik(
            $tanggal,
            $absensi,
            $cuti,
            $lupaAbsen,
            $suratSakit,
            false  // perUser = false
        );

        $jumlahHadir             = $stat['hadir'];
        $jumlahCutiTahunan       = $stat['cuti_tahunan'];
        $jumlahCutiAlasanPenting = $stat['cuti_alasan'];
        $jumlahSakit             = $stat['sakit'];
        $jumlahLupa              = $stat['lupa'];
        $jumlahPending           = $stat['pending'];
        $jumlahLembur            = $lemburData->count();

        // Total hari kerja
        $totalHariKerja = 0;
        foreach ($tanggal as $hari) {
            if (!$hari->isWeekend()) {
                $totalHariKerja++;
            }
        }

        return view('rekap_saya.index', compact(
            'user', 'bulan', 'tahun', 'tanggalAwal', 'tanggalAkhir', 'tanggal',
            'absensi', 'absensiHadir',
            'cuti', 'lupaAbsen', 'suratSakit', 'lembur',
            'jumlahHadir', 'jumlahCutiTahunan', 'jumlahCutiAlasanPenting',
            'jumlahSakit', 'jumlahLupa', 'jumlahPending', 'jumlahLembur',
            'totalHariKerja'
        ));
    }


    /* =========================================================
       EXPORT EXCEL REKAP SAYA
       ========================================================= */
    public function exportExcelSaya(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        return Excel::download(
            new RekapSayaExport($bulan, $tahun),
            'rekap-saya-' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx'
        );
    }


    /* =========================================================
       EXPORT PDF REKAP SAYA
       ========================================================= */
    public function exportPdfSaya(Request $request)
    {
        $user  = auth()->user();
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $tanggalAwal  = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $tanggal = [];
        $hari = $tanggalAwal->copy();
        while ($hari->lte($tanggalAkhir)) {
            $tanggal[] = $hari->copy();
            $hari->addDay();
        }

        // ABSENSI
        $absensiData = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        [$absensi, $absensiHadir] = $this->susunAbsensi($absensiData, false);

        // CUTI
        $cutiData = Pengajuancuti::where('user_id', $user->id)
            ->whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->get();

        $cuti = [];
        foreach ($cutiData as $item) {
            $mulai   = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);
            while ($mulai->lte($selesai)) {
                if (!$mulai->isWeekend() && $mulai->between($tanggalAwal, $tanggalAkhir)) {
                    $cuti[$mulai->format('Y-m-d')] = $item;
                }
                $mulai->addDay();
            }
        }

        // LUPA ABSEN
        $lupaData = Pengajuanlupaabsen::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lupaAbsen = [];
        foreach ($lupaData as $item) {
            $lupaAbsen[Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // SURAT SAKIT
        $suratSakitData = Pengajuansurat::where('user_id', $user->id)
            ->whereDate('tanggal', '>=', $tanggalAwal->format('Y-m-d'))
            ->whereDate('tanggal', '<=', $tanggalAkhir->format('Y-m-d'))
            ->where(function ($query) {
                $query->whereRaw('LOWER(COALESCE(jenis_surat, "")) LIKE ?', ['%sakit%'])
                    ->orWhereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%sakit%'])
                    ->orWhere(function ($q) {
                        $q->whereRaw('LOWER(COALESCE(jenis_surat, "")) = ?', ['surat_keterangan'])
                          ->whereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%berobat%']);
                    });
            })
            ->get();

        $suratSakit = [];
        foreach ($suratSakitData as $item) {
            $suratSakit[Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // =====================================================
        // HITUNG STATISTIK — sama persis dengan rekapSaya
        // =====================================================
        $stat = $this->hitungStatistik(
            $tanggal,
            $absensi,
            $cuti,
            $lupaAbsen,
            $suratSakit,
            false
        );

        $jumlahHadir             = $stat['hadir'];
        $jumlahCutiTahunan       = $stat['cuti_tahunan'];
        $jumlahCutiAlasanPenting = $stat['cuti_alasan'];
        $jumlahSakit             = $stat['sakit'];
        $jumlahLupa              = $stat['lupa'];
        $jumlahPending           = $stat['pending'];

        // LEMBUR
        $jumlahLembur = Lembur::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->where('status_approval', 'approved')
            ->count();

        $pdf = Pdf::loadView('rekap_saya.pdf', compact(
            'user', 'bulan', 'tahun', 'tanggalAwal', 'tanggalAkhir', 'tanggal',
            'absensi', 'absensiHadir',
            'cuti', 'lupaAbsen', 'suratSakit',
            'jumlahHadir',
            'jumlahCutiTahunan',
            'jumlahCutiAlasanPenting',
            'jumlahSakit',
            'jumlahLupa',
            'jumlahLembur',
            'jumlahPending'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(
            'rekap-saya-' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.pdf'
        );
    }
}