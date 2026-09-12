<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Rekap Absensi
        {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F') }}
        {{ $tahun }}
    </title>


    <style>

        @page {
            size: A3 landscape;
            margin: 15px;
        }


        body {

            font-family: DejaVu Sans, sans-serif;

            font-size: 8px;

            color: #222;

        }


        h2 {

            text-align: center;

            margin: 0 0 5px 0;

            font-size: 16px;

        }


        .periode {

            text-align: center;

            margin-bottom: 15px;

            font-size: 10px;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            table-layout: fixed;

        }


        th,
        td {

            border: 1px solid #777;

            padding: 3px;

            text-align: center;

            vertical-align: middle;

        }


        th {

            font-weight: bold;

            background: #f3f4f6;

        }


        th.no {

            width: 30px;

        }


        th.nama {

            width: 150px;

        }


        th.jabatan {

            width: 100px;

        }


        td.nama {

            text-align: left;

            white-space: nowrap;

            overflow: hidden;

        }


        td.jabatan {

            text-align: left;

            white-space: nowrap;

            overflow: hidden;

        }


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

            background: #ffffff;

            color: #222;

            font-weight: bold;

        }


        .cuti-tambahan {

            background: #ffffff;

            color: #222;

            font-weight: bold;

        }


        .lupa {

            background: #ffffff;

            color: #222;

            font-weight: bold;

        }


        .kosong {

            background: #ffffff;

            color: #777;

        }


        .weekend-header {

            background: #fecaca;

            color: #b91c1c;

        }


        .ringkasan {

            font-weight: bold;

            background: #f9fafb;

        }


        .footer {

            margin-top: 10px;

            font-size: 8px;

            color: #666;

        }

    </style>

</head>


<body>


    {{-- =====================================================
        JUDUL
    ====================================================== --}}

    <h2>

        REKAP ABSENSI PEGAWAI

    </h2>


    <div class="periode">

        Periode:

        {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F') }}

        {{ $tahun }}

    </div>



    {{-- =====================================================
        TABLE
    ====================================================== --}}

    <table>

        <thead>

            <tr>

                <th class="no">

                    No

                </th>


                <th class="nama">

                    Nama Pegawai

                </th>


                <th class="jabatan">

                    Jabatan

                </th>


                {{-- TANGGAL --}}

                @foreach ($tanggal as $tgl)

                    <th
                        class="{{ $tgl->isWeekend()
                            ? 'weekend-header'
                            : ''
                        }}"
                    >

                        {{ $tgl->format('d') }}

                    </th>

                @endforeach


                <th>

                    Hadir

                </th>


                <th>

                    Cuti

                </th>


                <th>

                    Cuti Tambahan

                </th>


                <th>

                    Lupa Absen

                </th>


                <th>

                    Lembur

                </th>

            </tr>

        </thead>


        <tbody>


            @forelse ($ppnpn as $index => $user)


                @php

                    $jumlahHadir = 0;

                    $jumlahCuti = 0;

                    $jumlahCutiTambahan = 0;

                    $jumlahLupaAbsen = 0;

                    $jumlahLembur = 0;

                @endphp


                <tr>


                    {{-- NO --}}

                    <td>

                        {{ $index + 1 }}

                    </td>


                    {{-- NAMA --}}

                    <td class="nama">

                        {{ $user->name }}

                    </td>


                    {{-- JABATAN --}}

                    <td class="jabatan">

                        {{ $user->profil?->jabatan ?? 'Pegawai' }}

                    </td>



                    {{-- =================================================
                        PER TANGGAL
                    ================================================== --}}

                    @foreach ($tanggal as $tgl)


                        @php

                            $tanggalKey =
                                $tgl->format('Y-m-d');


                            $absensiHariIni =
                                $absensi[$user->id][$tanggalKey]
                                ?? null;


                            $cutiHariIni =
                                $cuti[$user->id][$tanggalKey]
                                ?? null;


                            $lupaHariIni =
                                $lupaAbsen[$user->id][$tanggalKey]
                                ?? null;


                            $lemburHariIni =
                                $lembur[$user->id][$tanggalKey]
                                ?? null;


                            $kode = '-';

                            $class = 'kosong';

                        @endphp



                        {{-- =================================================
                            WEEKEND
                        ================================================== --}}

                        @if ($tgl->isWeekend())


                            {{-- ADA ABSENSI --}}

                            @if (
                                $absensiHariIni &&
                                $absensiHariIni->jam_masuk
                            )


                                @php

                                    $jamMasuk =
                                        \Carbon\Carbon::parse(
                                            $absensiHariIni->jam_masuk
                                        );


                                    if (
                                        $jamMasuk->hour >= 18
                                    ) {

                                        $kode = 'M';

                                        $class = 'malam';

                                    } else {

                                        $kode = 'H';

                                        $class = 'hadir';

                                    }


                                    $jumlahHadir++;

                                @endphp


                            {{-- TIDAK ADA ABSENSI --}}

                            @else


                                @php

                                    $kode = 'LIB';

                                    $class = 'libur';

                                @endphp


                            @endif



                        {{-- =================================================
                            CUTI
                        ================================================== --}}

                        @elseif ($cutiHariIni)


                            @if (
                                $cutiHariIni->jenis_cuti === 'tambahan'
                            )


                                @php

                                    $kode = 'CT';

                                    $class = 'cuti-tambahan';

                                    $jumlahCutiTambahan++;

                                @endphp


                            @else


                                @php

                                    $kode = 'C';

                                    $class = 'cuti';

                                    $jumlahCuti++;

                                @endphp


                            @endif



                        {{-- =================================================
                            LUPA ABSEN
                        ================================================== --}}

                        @elseif (
                            $lupaHariIni &&
                            !$absensiHariIni
                        )


                            @php

                                $kode = 'LA';

                                $class = 'lupa';

                                $jumlahLupaAbsen++;

                            @endphp



                        {{-- =================================================
                            ABSENSI
                        ================================================== --}}

                        @elseif (
                            $absensiHariIni &&
                            $absensiHariIni->jam_masuk
                        )


                            @php

                                $jamMasuk =
                                    \Carbon\Carbon::parse(
                                        $absensiHariIni->jam_masuk
                                    );


                                if (
                                    $jamMasuk->hour >= 18
                                ) {

                                    $kode = 'M';

                                    $class = 'malam';

                                } else {

                                    $kode = 'H';

                                    $class = 'hadir';

                                }


                                $jumlahHadir++;

                            @endphp


                        @endif



                        {{-- =================================================
                            LEMBUR
                        ================================================== --}}

                        @if ($lemburHariIni)

                            @php

                                $jumlahLembur++;

                            @endphp

                        @endif



                        <td class="{{ $class }}">

                            {{ $kode }}

                        </td>


                    @endforeach



                    {{-- =================================================
                        RINGKASAN
                    ================================================== --}}

                    <td class="ringkasan">

                        {{ $jumlahHadir }}

                    </td>


                    <td class="ringkasan">

                        {{ $jumlahCuti }}

                    </td>


                    <td class="ringkasan">

                        {{ $jumlahCutiTambahan }}

                    </td>


                    <td class="ringkasan">

                        {{ $jumlahLupaAbsen }}

                    </td>


                    <td class="ringkasan">

                        {{ $jumlahLembur }}

                    </td>


                </tr>


            @empty


                <tr>

                    <td
                        colspan="{{ $tanggal->count() + 8 }}"
                    >

                        Tidak ada data pegawai.

                    </td>

                </tr>


            @endforelse


        </tbody>

    </table>



    {{-- =====================================================
        FOOTER
    ====================================================== --}}

    <div class="footer">

        Keterangan:

        <strong>H</strong>
        = Hadir pagi

        &nbsp;&nbsp;

        <strong>M</strong>
        = Masuk malam

        &nbsp;&nbsp;

        <strong>LIB</strong>
        = Hari libur tanpa absensi

        &nbsp;&nbsp;

        <strong>C</strong>
        = Cuti

        &nbsp;&nbsp;

        <strong>CT</strong>
        = Cuti tambahan

        &nbsp;&nbsp;

        <strong>LA</strong>
        = Lupa absen

        <br>

        Sabtu dan Minggu otomatis dianggap hari libur,
        kecuali terdapat data absensi pada tanggal tersebut.

    </div>


</body>

</html>