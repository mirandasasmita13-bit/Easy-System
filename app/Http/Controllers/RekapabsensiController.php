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

    // REKAP ABSENSI ADMIN
    public function index(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        // Daftar tanggal dalam bulan
        $tanggal = [];

        $hari = $tanggalAwal->copy();

        while ($hari->lte($tanggalAkhir)) {
            $tanggal[] = $hari->copy();
            $hari->addDay();
        }

        // Data Pengguna
        $ppnpn = User::whereIn('role', ['ppnpn'])
            ->orderBy('name')
            ->get();

        // Absensiloh 
        $absensiData = Absensi::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $absensi = [];

        foreach ($absensiData as $item) {
            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $absensi[$item->user_id][$tanggalKey] = $item;
        }

        // CUTI
        // PENTING:
        // Cuti hanya dimasukkan ke rekap pada HARI KERJA.
        // Jadi jika pengajuan cuti 6 hari kalender dan di dalamnya
        // terdapat Sabtu + Minggu, maka Sabtu dan Minggu TIDAK
        // akan ditandai sebagai CUTI.
        $cutiData = Pengajuancuti::whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->get();

        $cuti = [];

        foreach ($cutiData as $item) {

            $mulai = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);

            while ($mulai->lte($selesai)) {

                /*
                |--------------------------------------------------------------------------
                | Hanya hari kerja
                |--------------------------------------------------------------------------
                */

                if (!$mulai->isWeekend()) {

                    /*
                    |--------------------------------------------------------------------------
                    | Pastikan tanggal masih berada di bulan yang sedang dilihat
                    |--------------------------------------------------------------------------
                    */

                    if ($mulai->between($tanggalAwal, $tanggalAkhir)) {

                        $tanggalKey = $mulai->format('Y-m-d');

                        $cuti[$item->user_id][$tanggalKey] = $item;
                    }
                }

                $mulai->addDay();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LUPA ABSEN
        |--------------------------------------------------------------------------
        */

        $lupaData = Pengajuanlupaabsen::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lupaAbsen = [];

        foreach ($lupaData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $lupaAbsen[$item->user_id][$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | LEMBUR
        |--------------------------------------------------------------------------
        |
        | Lembur dipisahkan dari rekap absensi utama.
        |
        */

        $lemburData = \App\Models\Lembur::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lembur = [];

        foreach ($lemburData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $lembur[$item->user_id][$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | DATA UNTUK VIEW
        |--------------------------------------------------------------------------
        */

        return view('rekapabsensi.index', compact(
            'bulan',
            'tahun',
            'tanggalAwal',
            'tanggalAkhir',
            'tanggal',
            'ppnpn',
            'absensi',
            'cuti',
            'lupaAbsen',
            'lembur'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL ADMIN
    |--------------------------------------------------------------------------
    */

    public function exportExcel(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        return Excel::download(
            new RekapabsensiExport($bulan, $tahun),
            'rekap-absensi-' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF ADMIN
    |--------------------------------------------------------------------------
    */

    public function exportPdf(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Tanggal
        |--------------------------------------------------------------------------
        */

        $tanggal = [];

        $hari = $tanggalAwal->copy();

        while ($hari->lte($tanggalAkhir)) {
            $tanggal[] = $hari->copy();
            $hari->addDay();
        }

    
        // Data Pengguna
        $ppnpn = User::whereIn('role', ['ppnpn'])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Absensi
        |--------------------------------------------------------------------------
        */

        $absensiData = Absensi::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $absensi = [];

        foreach ($absensiData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $absensi[$item->user_id][$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | Cuti
        |--------------------------------------------------------------------------
        |
        | Hanya hari kerja yang masuk.
        |
        */

        $cutiData = Pengajuancuti::whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->get();

        $cuti = [];

        foreach ($cutiData as $item) {

            $mulai = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);

            while ($mulai->lte($selesai)) {

                if (
                    !$mulai->isWeekend()
                    && $mulai->between($tanggalAwal, $tanggalAkhir)
                ) {

                    $tanggalKey = $mulai->format('Y-m-d');

                    $cuti[$item->user_id][$tanggalKey] = $item;
                }

                $mulai->addDay();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Lupa Absen
        |--------------------------------------------------------------------------
        */

        $lupaData = Pengajuanlupaabsen::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lupaAbsen = [];

        foreach ($lupaData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $lupaAbsen[$item->user_id][$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | Lembur
        |--------------------------------------------------------------------------
        */

        $lemburData = \App\Models\Lembur::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lembur = [];

        foreach ($lemburData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $lembur[$item->user_id][$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView('rekapabsensi.pdf', compact(
            'bulan',
            'tahun',
            'tanggalAwal',
            'tanggalAkhir',
            'tanggal',
            'ppnpn',
            'absensi',
            'cuti',
            'lupaAbsen',
            'lembur'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'rekap-absensi-' .
            $tahun .
            '-' .
            str_pad($bulan, 2, '0', STR_PAD_LEFT) .
            '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REKAP SAYA
    |--------------------------------------------------------------------------
    */

    public function rekapSaya(Request $request)
    {
        $user = auth()->user();

        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Tanggal
        |--------------------------------------------------------------------------
        */

        $tanggal = [];

        $hari = $tanggalAwal->copy();

        while ($hari->lte($tanggalAkhir)) {
            $tanggal[] = $hari->copy();
            $hari->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | ABSENSI
        |--------------------------------------------------------------------------
        */

        $absensiData = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $absensi = [];

        foreach ($absensiData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $absensi[$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | CUTI
        |--------------------------------------------------------------------------
        |
        | PENTING:
        | Weekend tidak dimasukkan sebagai CUTI.
        |
        */

        $cutiData = Pengajuancuti::where('user_id', $user->id)
            ->whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->get();

        $cuti = [];

        foreach ($cutiData as $item) {

            $mulai = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);

            while ($mulai->lte($selesai)) {

                /*
                |--------------------------------------------------------------------------
                | Hanya hari kerja
                |--------------------------------------------------------------------------
                */

                if (
                    !$mulai->isWeekend()
                    && $mulai->between($tanggalAwal, $tanggalAkhir)
                ) {

                    $tanggalKey = $mulai->format('Y-m-d');

                    $cuti[$tanggalKey] = $item;
                }

                $mulai->addDay();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LUPA ABSEN
        |--------------------------------------------------------------------------
        */

        $lupaData = Pengajuanlupaabsen::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lupaAbsen = [];

        foreach ($lupaData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $lupaAbsen[$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | LEMBUR
        |--------------------------------------------------------------------------
        */

        $lemburData = \App\Models\Lembur::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lembur = [];

        foreach ($lemburData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $lembur[$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | SURAT SAKIT
        |--------------------------------------------------------------------------
        |
        | Mendukung:
        |
        | - jenis_surat mengandung "sakit"
        | - keperluan mengandung "sakit"
        | - jenis_surat = surat_keterangan + keperluan = berobat
        |
        */

        $suratSakitData = Pengajuansurat::where(
                'user_id',
                $user->id
            )
            ->whereDate('tanggal', '>=', $tanggalAwal->format('Y-m-d'))
            ->whereDate('tanggal', '<=', $tanggalAkhir->format('Y-m-d'))
            ->where(function ($query) {

                $query
                    ->whereRaw(
                        'LOWER(COALESCE(jenis_surat, "")) LIKE ?',
                        ['%sakit%']
                    )

                    ->orWhereRaw(
                        'LOWER(COALESCE(keperluan, "")) LIKE ?',
                        ['%sakit%']
                    )

                    ->orWhere(function ($q) {

                        $q->whereRaw(
                            'LOWER(COALESCE(jenis_surat, "")) = ?',
                            ['surat_keterangan']
                        )

                        ->whereRaw(
                            'LOWER(COALESCE(keperluan, "")) LIKE ?',
                            ['%berobat%']
                        );
                    });
            })
            ->get();

        $suratSakit = [];

        foreach ($suratSakitData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $suratSakit[$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | HITUNG JUMLAH
        |--------------------------------------------------------------------------
        */

        $jumlahHadir = 0;
        $jumlahCutiTahunan = 0;
        $jumlahCutiAlasanPenting = 0;
        $jumlahSakit = 0;
        $jumlahLembur = $lemburData->count();

        /*
        |--------------------------------------------------------------------------
        | PERHITUNGAN STATUS
        |--------------------------------------------------------------------------
        |
        | Weekend tidak dihitung sebagai cuti.
        |
        */

        foreach ($tanggal as $hari) {

            $tanggalKey = $hari->format('Y-m-d');

            $adaAbsensi = isset($absensi[$tanggalKey]);
            $adaCuti = isset($cuti[$tanggalKey]);
            $adaSakit = isset($suratSakit[$tanggalKey]);
            $adaLupa = isset($lupaAbsen[$tanggalKey]);

            /*
            |--------------------------------------------------------------------------
            | Weekend
            |--------------------------------------------------------------------------
            |
            | Kalau ada absensi aktual di weekend,
            | tetap dihitung HADIR.
            |
            */

            if ($hari->isWeekend()) {

                if ($adaAbsensi) {
                    $jumlahHadir++;
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Hari kerja
            |--------------------------------------------------------------------------
            */

            if ($adaAbsensi) {

                /*
                |--------------------------------------------------------------------------
                | Jika ada surat sakit pada hari kerja,
                | tetap gunakan status Sakit untuk rekap.
                |--------------------------------------------------------------------------
                */

                if ($adaSakit) {
                    $jumlahSakit++;
                } else {
                    $jumlahHadir++;
                }

            } elseif ($adaSakit) {

                $jumlahSakit++;

            } elseif ($adaCuti) {

                $jenisCuti = strtolower(
                    trim($cuti[$tanggalKey]->jenis_cuti ?? '')
                );

                if (
                    str_contains($jenisCuti, 'alasan')
                    || str_contains($jenisCuti, 'penting')
                ) {

                    $jumlahCutiAlasanPenting++;

                } else {

                    $jumlahCutiTahunan++;
                }

            } elseif ($adaLupa) {

                // Lupa absen tidak dihitung sebagai hadir.
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Total hari kerja
        |--------------------------------------------------------------------------
        */

        $totalHariKerja = 0;

        foreach ($tanggal as $hari) {

            if (!$hari->isWeekend()) {
                $totalHariKerja++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | KIRIM KE VIEW
        |--------------------------------------------------------------------------
        */

        return view('rekap_saya.index', compact(
            'user',
            'bulan',
            'tahun',
            'tanggalAwal',
            'tanggalAkhir',
            'tanggal',
            'absensi',
            'cuti',
            'lupaAbsen',
            'lembur',
            'suratSakit',
            'jumlahHadir',
            'jumlahCutiTahunan',
            'jumlahCutiAlasanPenting',
            'jumlahSakit',
            'jumlahLembur',
            'totalHariKerja'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL REKAP SAYA
    |--------------------------------------------------------------------------
    */

    public function exportExcelSaya(Request $request)
    {
        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        return Excel::download(
            new RekapSayaExport($bulan, $tahun),
            'rekap-saya-' .
            $tahun .
            '-' .
            str_pad($bulan, 2, '0', STR_PAD_LEFT) .
            '.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF REKAP SAYA
    |--------------------------------------------------------------------------
    */

    public function exportPdfSaya(Request $request)
    {
        $user = auth()->user();

        $bulan = (int) ($request->bulan ?? now()->month);
        $tahun = (int) ($request->tahun ?? now()->year);

        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Tanggal
        |--------------------------------------------------------------------------
        */

        $tanggal = [];

        $hari = $tanggalAwal->copy();

        while ($hari->lte($tanggalAkhir)) {
            $tanggal[] = $hari->copy();
            $hari->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Absensi
        |--------------------------------------------------------------------------
        */

        $absensiData = Absensi::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $absensi = [];

        foreach ($absensiData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $absensi[$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | Cuti
        |--------------------------------------------------------------------------
        |
        | Weekend dilewati.
        |
        */

        $cutiData = Pengajuancuti::where('user_id', $user->id)
            ->whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->get();

        $cuti = [];

        foreach ($cutiData as $item) {

            $mulai = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);

            while ($mulai->lte($selesai)) {

                if (
                    !$mulai->isWeekend()
                    && $mulai->between($tanggalAwal, $tanggalAkhir)
                ) {

                    $tanggalKey = $mulai->format('Y-m-d');

                    $cuti[$tanggalKey] = $item;
                }

                $mulai->addDay();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Lupa Absen
        |--------------------------------------------------------------------------
        */

        $lupaData = Pengajuanlupaabsen::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lupaAbsen = [];

        foreach ($lupaData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $lupaAbsen[$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | Lembur
        |--------------------------------------------------------------------------
        */

        $lemburData = \App\Models\Lembur::where('user_id', $user->id)
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->get();

        $lembur = [];

        foreach ($lemburData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $lembur[$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | Surat Sakit
        |--------------------------------------------------------------------------
        */

        $suratSakitData = Pengajuansurat::where(
                'user_id',
                $user->id
            )
            ->whereDate('tanggal', '>=', $tanggalAwal->format('Y-m-d'))
            ->whereDate('tanggal', '<=', $tanggalAkhir->format('Y-m-d'))
            ->where(function ($query) {

                $query
                    ->whereRaw(
                        'LOWER(COALESCE(jenis_surat, "")) LIKE ?',
                        ['%sakit%']
                    )

                    ->orWhereRaw(
                        'LOWER(COALESCE(keperluan, "")) LIKE ?',
                        ['%sakit%']
                    )

                    ->orWhere(function ($q) {

                        $q->whereRaw(
                            'LOWER(COALESCE(jenis_surat, "")) = ?',
                            ['surat_keterangan']
                        )

                        ->whereRaw(
                            'LOWER(COALESCE(keperluan, "")) LIKE ?',
                            ['%berobat%']
                        );
                    });
            })
            ->get();

        $suratSakit = [];

        foreach ($suratSakitData as $item) {

            $tanggalKey = Carbon::parse($item->tanggal)->format('Y-m-d');

            $suratSakit[$tanggalKey] = $item;
        }

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView('rekap_saya.pdf', compact(
            'user',
            'bulan',
            'tahun',
            'tanggalAwal',
            'tanggalAkhir',
            'tanggal',
            'absensi',
            'cuti',
            'lupaAbsen',
            'lembur',
            'suratSakit'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'rekap-saya-' .
            $tahun .
            '-' .
            str_pad($bulan, 2, '0', STR_PAD_LEFT) .
            '.pdf'
        );
    }
}