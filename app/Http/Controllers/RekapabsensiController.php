<?php

namespace App\Http\Controllers;

use App\Exports\RekapabsensiExport;
use App\Exports\RekapSayaExport;
use App\Models\Absensi;
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
       HELPER: Susun data absensi (dipisah valid vs pending)
       ========================================================= */
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

        // PPNPN AKTIF SAJA
        $ppnpn = User::where('role', 'ppnpn')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        // ABSENSI
        $absensiData = Absensi::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn')->where('status', 'aktif');
            })
            ->get();

        [$absensi, $absensiHadir] = $this->susunAbsensi($absensiData, true);

        // CUTI (hanya hari kerja)
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

        // HITUNG
        $jumlahHadir = 0;
        $jumlahCutiTahunan = 0;
        $jumlahCutiAlasanPenting = 0;
        $jumlahSakit = 0;
        $jumlahPending = 0;

        foreach ($tanggal as $hari) {
            $tanggalKey = $hari->format('Y-m-d');

            $adaAbsensi    = isset($absensi[$tanggalKey]);
            $adaHadirValid = isset($absensiHadir[$tanggalKey]);
            $adaCuti       = isset($cuti[$tanggalKey]);
            $adaSakit      = isset($suratSakit[$tanggalKey]);

            $absensiPending = $adaAbsensi
                && $absensi[$tanggalKey]->status_approval === 'pending';

            if ($absensiPending) {
                $jumlahPending++;
            }

            if ($hari->isWeekend()) {
                if ($adaHadirValid) {
                    $jumlahHadir++;
                }
                continue;
            }

            if ($adaHadirValid) {
                if ($adaSakit) {
                    $jumlahSakit++;
                } else {
                    $jumlahHadir++;
                }
            } elseif ($adaSakit) {
                $jumlahSakit++;
            } elseif ($adaCuti) {
                $jenisCuti = strtolower(trim($cuti[$tanggalKey]->jenis_cuti ?? ''));
                if (str_contains($jenisCuti, 'alasan') || str_contains($jenisCuti, 'penting')) {
                    $jumlahCutiAlasanPenting++;
                } else {
                    $jumlahCutiTahunan++;
                }
            }
        }

        $totalHariKerja = 0;
        foreach ($tanggal as $hari) {
            if (!$hari->isWeekend()) {
                $totalHariKerja++;
            }
        }

        return view('rekap_saya.index', compact(
            'user', 'bulan', 'tahun', 'tanggalAwal', 'tanggalAkhir', 'tanggal',
            'absensi', 'absensiHadir',
            'cuti', 'lupaAbsen', 'suratSakit',
            'jumlahHadir', 'jumlahCutiTahunan', 'jumlahCutiAlasanPenting',
            'jumlahSakit', 'jumlahPending',
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

        $pdf = Pdf::loadView('rekap_saya.pdf', compact(
            'user', 'bulan', 'tahun', 'tanggalAwal', 'tanggalAkhir', 'tanggal',
            'absensi', 'absensiHadir',
            'cuti', 'lupaAbsen', 'suratSakit'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'rekap-saya-' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.pdf'
        );
    }
}