<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Rekap Absensi {{ $namaBulan ?? \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}
    </title>

    <style>

        @page {
            size: A4 landscape;
            margin: 15px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin: 0 0 4px 0;
            font-size: 16px;
        }

        .identitas {
            text-align: center;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .periode {
            text-align: center;
            margin-bottom: 15px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #777;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        td.keterangan {
            text-align: left;
        }

        /* =========================================================
           STATUS
        ========================================================= */

        .hadir {
            background: #dcfce7;
            color: #166534;
            font-weight: bold;
        }

        .malam {
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: bold;
        }

        .libur {
            background: #fecaca;
            color: #b91c1c;
            font-weight: bold;
        }

        .cuti {
            background: #fef3c7;
            color: #92400e;
            font-weight: bold;
        }

        .sakit {
            background: #fee2e2;
            color: #991b1b;
            font-weight: bold;
        }

        .lupa {
            background: #f3e8ff;
            color: #7e22ce;
            font-weight: bold;
        }

        .kosong {
            color: #777;
        }

        .footer {
            margin-top: 12px;
            font-size: 8px;
            color: #666;
        }

    </style>

</head>


<body>


    {{-- =========================================================
         JUDUL
    ========================================================== --}}

    <h2>
        REKAP ABSENSI
    </h2>


    {{-- =========================================================
         IDENTITAS
    ========================================================== --}}

    <div class="identitas">
        {{ $user->name }}
    </div>


    {{-- =========================================================
         PERIODE
    ========================================================== --}}

    <div class="periode">

        Periode:

        {{ $namaBulan ?? \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F') }}

        {{ $tahun }}

    </div>


    {{-- =========================================================
         TABEL
    ========================================================== --}}

    <table>

        <thead>

            <tr>

                <th>
                    Tanggal
                </th>

                <th>
                    Hari
                </th>

                <th>
                    Shift
                </th>

                <th>
                    Status
                </th>

                <th>
                    Jam Masuk
                </th>

                <th>
                    Jam Pulang
                </th>

                <th>
                    Keterangan
                </th>

                <th>
                    Lembur
                </th>

            </tr>

        </thead>


        <tbody>

            @foreach ($tanggal as $tgl)

                @php

                    $tanggalKey = $tgl->format('Y-m-d');

                    $absensiHariIni =
                        $absensi[$tanggalKey] ?? null;

                    $cutiHariIni =
                        $cuti[$tanggalKey] ?? null;

                    $lupaHariIni =
                        $lupaAbsen[$tanggalKey] ?? null;

                    $lemburHariIni =
                        $lembur[$tanggalKey] ?? null;

                    $suratSakitHariIni =
                        $suratSakit[$tanggalKey] ?? null;


                    /*
                    |--------------------------------------------------------------------------
                    | DEFAULT
                    |--------------------------------------------------------------------------
                    */

                    $shift = '-';

                    $status = 'Belum Absen';

                    $jamMasuk = '-';

                    $jamPulang = '-';

                    $keterangan = '-';


                    /*
                    |--------------------------------------------------------------------------
                    | ABSENSI
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $absensiHariIni &&
                        $absensiHariIni->jam_masuk
                    ) {

                        $jamMasukValue =
                            \Carbon\Carbon::parse(
                                $absensiHariIni->jam_masuk
                            );


                        $jamMasuk =
                            $jamMasukValue->format('H:i');


                        $jamPulang =
                            $absensiHariIni->jam_pulang
                                ? \Carbon\Carbon::parse(
                                    $absensiHariIni->jam_pulang
                                )->format('H:i')
                                : '-';


                        /*
                        |--------------------------------------------------------------------------
                        | SHIFT
                        |--------------------------------------------------------------------------
                        |
                        | Mengikuti jam masuk seperti logika
                        | rekap sebelumnya.
                        |
                        | >= 18:00 = malam
                        | < 18:00 = pagi
                        |
                        */

                        if ($jamMasukValue->hour >= 18) {

                            $shift = 'Shift Malam';

                        } else {

                            $shift = 'Shift Pagi';
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    |
                    | ATURAN UTAMA:
                    |
                    | WEEKEND:
                    | - Ada absensi aktual = HADIR
                    | - Tidak ada absensi = LIBUR
                    |
                    | HARI KERJA:
                    | - Surat sakit = SAKIT
                    | - Cuti = CUTI
                    | - Lupa absen = LUPA ABSEN
                    | - Absensi = HADIR
                    | - Tidak ada = BELUM ABSEN
                    |
                    */

                    if ($tgl->isWeekend()) {

                        /*
                        |--------------------------------------------------------------------------
                        | WEEKEND + ABSENSI
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $absensiHariIni &&
                            $absensiHariIni->jam_masuk
                        ) {

                            $status = 'Hadir';

                            $keterangan = 'Hadir';

                        } else {

                            /*
                            |--------------------------------------------------------------------------
                            | WEEKEND TANPA ABSENSI
                            |--------------------------------------------------------------------------
                            */

                            $status = 'Libur';

                            $keterangan = 'Hari libur';
                        }


                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | HARI KERJA
                        |--------------------------------------------------------------------------
                        */


                        /*
                        |--------------------------------------------------------------------------
                        | SAKIT
                        |--------------------------------------------------------------------------
                        |
                        | Surat sakit menjadi prioritas.
                        | Jadi walaupun ada data absensi,
                        | status rekap tetap Sakit.
                        |
                        */

                        if ($suratSakitHariIni) {

                            $status = 'Sakit';

                            $keterangan = 'Surat sakit';


                        /*
                        |--------------------------------------------------------------------------
                        | CUTI
                        |--------------------------------------------------------------------------
                        */

                        } elseif ($cutiHariIni) {

                            $jenisCuti = strtolower(
                                trim(
                                    $cutiHariIni->jenis_cuti ?? ''
                                )
                            );


                            if (
                                str_contains(
                                    $jenisCuti,
                                    'tambahan'
                                )
                            ) {

                                $status = 'Cuti Tambahan';

                                $keterangan =
                                    'Cuti tambahan';


                            } elseif (
                                str_contains(
                                    $jenisCuti,
                                    'alasan'
                                )
                                ||
                                str_contains(
                                    $jenisCuti,
                                    'penting'
                                )
                            ) {

                                $status = 'Alasan Penting';

                                $keterangan =
                                    'Cuti alasan penting';


                            } else {

                                $status = 'Cuti Tahunan';

                                $keterangan =
                                    'Cuti tahunan';
                            }


                        /*
                        |--------------------------------------------------------------------------
                        | LUPA ABSEN
                        |--------------------------------------------------------------------------
                        */

                        } elseif ($lupaHariIni) {

                            $status = 'Lupa Absen';

                            $keterangan =
                                'Perbaikan absensi';


                        /*
                        |--------------------------------------------------------------------------
                        | HADIR
                        |--------------------------------------------------------------------------
                        */

                        } elseif (
                            $absensiHariIni &&
                            $absensiHariIni->jam_masuk
                        ) {

                            $status = 'Hadir';

                            $keterangan = 'Hadir';
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CLASS STATUS
                    |--------------------------------------------------------------------------
                    */

                    $statusClass = 'kosong';


                    if ($status === 'Hadir') {

                        $statusClass = 'hadir';

                    } elseif ($status === 'Sakit') {

                        $statusClass = 'sakit';

                    } elseif ($status === 'Libur') {

                        $statusClass = 'libur';

                    } elseif (
                        $status === 'Cuti Tahunan'
                        ||
                        $status === 'Alasan Penting'
                        ||
                        $status === 'Cuti Tambahan'
                    ) {

                        $statusClass = 'cuti';

                    } elseif ($status === 'Lupa Absen') {

                        $statusClass = 'lupa';
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CLASS SHIFT
                    |--------------------------------------------------------------------------
                    */

                    $shiftClass =
                        $shift === 'Shift Malam'
                            ? 'malam'
                            : '';

                @endphp


                <tr>

                    {{-- TANGGAL --}}

                    <td>
                        {{ $tgl->format('d/m/Y') }}
                    </td>


                    {{-- HARI --}}

                    <td>
                        {{ $tgl->translatedFormat('l') }}
                    </td>


                    {{-- SHIFT --}}

                    <td class="{{ $shiftClass }}">
                        {{ $shift }}
                    </td>


                    {{-- STATUS --}}

                    <td class="{{ $statusClass }}">
                        {{ $status }}
                    </td>


                    {{-- JAM MASUK --}}

                    <td>
                        {{ $jamMasuk }}
                    </td>


                    {{-- JAM PULANG --}}

                    <td>
                        {{ $jamPulang }}
                    </td>


                    {{-- KETERANGAN --}}

                    <td class="keterangan">
                        {{ $keterangan }}
                    </td>


                    {{-- LEMBUR --}}

                    <td>
                        {{ $lemburHariIni ? 'Ya' : '-' }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <div class="footer">

        <strong>Keterangan:</strong>

        Shift Pagi = absensi pagi

        &nbsp;&nbsp;

        Shift Malam = absensi malam

        &nbsp;&nbsp;

        Hadir = terdapat absensi

        &nbsp;&nbsp;

        Cuti = cuti yang tercatat pada hari kerja

        &nbsp;&nbsp;

        Sakit = surat sakit

        &nbsp;&nbsp;

        Lupa Absen = perbaikan absensi

        &nbsp;&nbsp;

        Libur = Sabtu/Minggu tanpa absensi

        <br>

        Sabtu dan Minggu otomatis dianggap hari libur
        apabila tidak terdapat absensi.

        Jika terdapat absensi aktual pada hari libur,
        maka status tetap Hadir.

    </div>


</body>

</html>