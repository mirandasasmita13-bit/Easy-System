@extends('layouts.utama')

@section('title', 'Rekap Absensi')

@section('content')

{{-- =====================================================
    HEADER
===================================================== --}}

<div class="mb-6">

    <div class="flex flex-col gap-6">

        {{-- JUDUL --}}
        <div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Rekap Absensi
            </h2>

            <p class="mt-2 text-slate-500">
                Rekap kehadiran pegawai dalam satu periode bulan.
            </p>
        </div>

        {{-- RINGKASAN + FILTER --}}
        <div class="grid grid-cols-1 lg:grid-cols-[0.7fr_1.3fr] gap-6">

        {{-- JUMLAH PPNPN --}}
        <div class="es-card px-7 py-7 flex flex-col justify-center">

            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Jumlah PPNPN
            </p>

            <div class="flex items-end gap-2 mt-3">

                <span class="text-4xl font-extrabold text-purple-600">
                    {{ count($ppnpn) }}
                </span>

                <span class="text-sm text-slate-400 mb-1">
                    orang
                </span>
            </div>
        </div>


            {{-- TAMPILKAN REKAP --}}
            <div class="es-card px-7 py-6">

                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-4">
                    Tampilkan Rekap
                </p>

                <form
                    method="GET"
                    action="{{ url('/rekap') }}"
                >

                    <div class="flex flex-col sm:flex-row gap-3">

                        {{-- BULAN --}}
                        <select
                            name="bulan"
                            class="
                                w-full
                                rounded-xl
                                border border-slate-200
                                bg-white
                                px-4 py-3
                                text-sm
                                text-slate-700
                                outline-none
                                shadow-sm
                                focus:border-purple-500
                                focus:ring-2
                                focus:ring-purple-100
                            "
                        >

                            @foreach(range(1, 12) as $bulanOption)

                                <option
                                    value="{{ $bulanOption }}"
                                    @selected($bulan == $bulanOption)
                                >
                                    {{ \Carbon\Carbon::create()->month($bulanOption)->translatedFormat('F') }}
                                </option>

                            @endforeach

                        </select>

                        {{-- TAHUN --}}
                        <select
                            name="tahun"
                            class="
                                w-full
                                sm:w-[130px]
                                rounded-xl
                                border border-slate-200
                                bg-white
                                px-4 py-3
                                text-sm
                                text-slate-700
                                outline-none
                                shadow-sm
                                focus:border-purple-500
                                focus:ring-2
                                focus:ring-purple-100
                            "
                        >

                            @foreach(range(now()->year - 2, now()->year + 1) as $tahunOption)

                                <option
                                    value="{{ $tahunOption }}"
                                    @selected($tahun == $tahunOption)
                                >
                                    {{ $tahunOption }}
                                </option>
                            @endforeach
                        </select>

                        {{-- TAMPILKAN --}}
                        <button
                            type="submit"
                            class="
                                w-full
                                sm:w-[150px]
                                inline-flex
                                items-center
                                justify-center
                                rounded-xl
                                bg-purple-600
                                px-5 py-3
                                text-sm
                                font-bold
                                text-white
                                shadow-md
                                shadow-purple-200
                                hover:bg-purple-700
                                transition
                            "
                        >
                            Tampilkan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

{{-- =====================================================
    PERIODE + EXPORT
===================================================== --}}

<div class="es-card px-5 py-4 sm:px-6 sm:py-5 mb-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

        {{-- PERIODE --}}
        <div>

            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Periode Rekap
            </p>

            <h3 class="mt-1 text-lg sm:text-xl font-extrabold text-slate-900">
                {{ $tanggalAwal->translatedFormat('F Y') }}
            </h3>

        </div>


        {{-- EXPORT --}}
        <div class="flex flex-wrap items-center gap-3">

            {{-- EXCEL --}}
            <a
                href="{{ route('rekapabsensi.export.excel', [
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]) }}"
                class="
                    inline-flex
                    items-center
                    justify-center
                    gap-2
                    rounded-xl
                    border
                    border-emerald-200
                    bg-emerald-50
                    px-5 py-3
                    text-sm
                    font-bold
                    text-emerald-600
                    hover:bg-emerald-100
                    transition
                "
            >
                Export Excel
            </a>


            {{-- PDF --}}
            <a
                href="{{ route('rekapabsensi.export.pdf', [
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]) }}"
                class="
                    inline-flex
                    items-center
                    justify-center
                    gap-2
                    rounded-xl
                    border
                    border-red-200
                    bg-red-50
                    px-5 py-3
                    text-sm
                    font-bold
                    text-red-600
                    hover:bg-red-100
                    transition
                "
            >
                Export PDF
            </a>

        </div>

    </div>

</div>

{{-- =====================================================
    LEGEND
===================================================== --}}

<div class="es-card px-5 py-4 mb-5">

    <div class="flex flex-wrap items-center gap-x-5 gap-y-3">

        <span class="text-xs font-bold text-slate-500 mr-1">
            Keterangan:
        </span>


        {{-- HADIR --}}
        <div class="flex items-center gap-2">

            <span
                class="
                    w-7 h-7
                    rounded-lg
                    bg-green-50
                    text-green-600
                    flex
                    items-center
                    justify-center
                    text-[11px]
                    font-extrabold
                "
            >
                H
            </span>
            <span class="text-xs text-slate-500">
                Shift pagi
            </span>

        </div>


        {{-- CUTI TAHUNAN --}}
        <div class="flex items-center gap-2">

            <span
                class="
                    w-7 h-7
                    rounded-lg
                    bg-yellow-50
                    text-yellow-600
                    flex
                    items-center
                    justify-center
                    text-[10px]
                    font-extrabold
                "
            >
                CT
            </span>

            <span class="text-xs text-slate-500">
                Cuti Tahunan
            </span>

        </div>


        {{-- CUTI ALASAN PENTING --}}
        <div class="flex items-center gap-2">

            <span
                class="
                    w-7 h-7
                    rounded-lg
                    bg-orange-50
                    text-orange-600
                    flex
                    items-center
                    justify-center
                    text-[9px]
                    font-extrabold
                "
            >
                CAP
            </span>

            <span class="text-xs text-slate-500">
                Cuti Alasan Penting
            </span>

        </div>


        {{-- SHIFT MALAM --}}
        <div class="flex items-center gap-2">

            <span
                class="
                    w-7 h-7
                    rounded-lg
                    bg-purple-50
                    text-purple-600
                    flex
                    items-center
                    justify-center
                    text-[10px]
                    font-extrabold
                "
            >
                M
            </span>

            <span class="text-xs text-slate-500">
                Shift Malam
            </span>

        </div>


        {{-- LUPA ABSEN --}}
        <div class="flex items-center gap-2">

            <span
                class="
                    w-7 h-7
                    rounded-lg
                    bg-blue-50
                    text-blue-600
                    flex
                    items-center
                    justify-center
                    text-[9px]
                    font-extrabold
                "
            >
                LA
            </span>

            <span class="text-xs text-slate-500">
                Lupa Absen
            </span>

        </div>


        {{-- LIBUR --}}
        <div class="flex items-center gap-2">

            <span
                class="
                    w-7 h-7
                    rounded-lg
                    bg-red-50
                    text-red-500
                    flex
                    items-center
                    justify-center
                    text-[8px]
                    font-extrabold
                "
            >
                LIB
            </span>

            <span class="text-xs text-slate-500">
                Hari Libur
            </span>

        </div>

    </div>

</div>


{{-- =====================================================
    MATRIX
===================================================== --}}

<div class="es-card overflow-hidden">

    {{-- HEADER MATRIX --}}
    <div class="px-5 sm:px-6 py-5 border-b border-slate-100">

        <h3 class="text-lg font-bold text-slate-900">
            Rekap Kehadiran Pegawai
        </h3>

        <p class="text-sm text-slate-400 mt-1">
            Kondisi kehadiran pegawai ditampilkan berdasarkan tanggal.
        </p>

    </div>


    {{-- TABLE --}}
    <div class="overflow-x-auto">

        <table class="min-w-max w-full border-collapse">

            <thead>

                <tr class="bg-slate-50 border-b border-slate-200">

                    {{-- NO --}}
                    <th
                        class="
                            sticky left-0 z-30
                            bg-slate-50
                            w-[55px]
                            min-w-[55px]
                            px-3 py-4
                            text-center
                            text-xs
                            font-bold
                            text-slate-500
                            border-r
                            border-slate-200
                        "
                    >
                        No
                    </th>


                    {{-- NAMA --}}
                    <th
                        class="
                            sticky left-[55px] z-30
                            bg-slate-50
                            min-w-[260px]
                            px-5 py-4
                            text-left
                            text-xs
                            font-bold
                            text-slate-500
                            border-r
                            border-slate-200
                        "
                    >
                        Nama Pegawai
                    </th>


                    {{-- TANGGAL --}}
                    @foreach($tanggal as $hari)

                        @php
                            $isWeekend = $hari->isWeekend();
                        @endphp

                        <th
                            class="
                                px-2 py-3
                                text-center
                                border-r
                                border-slate-100
                                min-w-[58px]
                                {{ $isWeekend
                                    ? 'bg-red-50'
                                    : 'bg-slate-50'
                                }}
                            "
                        >

                            <div
                                class="
                                    text-xs
                                    font-extrabold
                                    {{ $isWeekend
                                        ? 'text-red-500'
                                        : 'text-slate-600'
                                    }}
                                "
                            >
                                {{ $hari->format('d') }}
                            </div>

                            <div
                                class="
                                    text-[10px]
                                    font-medium
                                    mt-0.5
                                    {{ $isWeekend
                                        ? 'text-red-300'
                                        : 'text-slate-400'
                                    }}
                                "
                            >
                                {{ $hari->translatedFormat('D') }}
                            </div>

                        </th>

                    @endforeach


                    {{-- RINGKASAN --}}
                    <th
                        class="
                            px-5 py-4
                            text-center
                            text-xs
                            font-bold
                            text-slate-500
                            bg-slate-50
                            min-w-[120px]
                        "
                    >
                        Ringkasan
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-slate-100">

                @forelse($ppnpn as $index => $user)

                    @php

                        $jumlahHadir = 0;
                        $jumlahCuti = 0;
                        $jumlahCutiAlasanPenting = 0;
                        $jumlahLibur = 0;

                    @endphp


                    <tr class="hover:bg-slate-50/60 transition">


                        {{-- NO --}}
                        <td
                            class="
                                sticky left-0 z-20
                                bg-white
                                w-[55px]
                                min-w-[55px]
                                px-3 py-4
                                text-center
                                text-xs
                                font-semibold
                                text-slate-500
                                border-r
                                border-slate-100
                            "
                        >
                            {{ $index + 1 }}
                        </td>


                        {{-- NAMA --}}
                        <td
                            class="
                                sticky left-[55px] z-20
                                bg-white
                                min-w-[260px]
                                px-5 py-4
                                border-r
                                border-slate-100
                            "
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        w-9 h-9
                                        rounded-xl
                                        bg-purple-50
                                        text-purple-600
                                        flex
                                        items-center
                                        justify-center
                                        text-xs
                                        font-extrabold
                                        shrink-0
                                    "
                                >
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>


                                <div class="min-w-0">

                                    <p
                                        class="
                                            text-sm
                                            font-bold
                                            text-slate-800
                                            truncate
                                        "
                                    >
                                        {{ $user->name }}
                                    </p>

                                    <p
                                        class="
                                            text-[11px]
                                            text-slate-400
                                            truncate
                                        "
                                    >
                                        {{ $user->profil?->jabatan ?? 'Pegawai' }}
                                    </p>

                                </div>

                            </div>

                        </td>


                        {{-- PER TANGGAL --}}
                        @foreach($tanggal as $hari)

                            @php

                                $tanggalKey = $hari->format('Y-m-d');

                                $absensiHariIni =
                                    $absensi[$user->id][$tanggalKey] ?? null;

                                $cutiHariIni =
                                    $cuti[$user->id][$tanggalKey] ?? null;

                                $lupaHariIni =
                                    $lupaAbsen[$user->id][$tanggalKey] ?? null;

                                $isWeekend = $hari->isWeekend();

                                $kode = '-';

                                $warna = 'bg-slate-50 text-slate-300';

                            @endphp


                            {{-- WEEKEND --}}
                            @if($isWeekend)

                                @if(
                                    $absensiHariIni &&
                                    $absensiHariIni->jam_masuk
                                )

                                    @php

                                        /*
                                         * SHIFT DIAMBIL LANGSUNG
                                         * DARI DATA ABSENSI
                                         */
                                        if ($absensiHariIni->shift === 'malam') {

                                            $kode = 'M';

                                            $warna =
                                                'bg-purple-50 text-purple-600';

                                        } else {

                                            $kode = 'H';

                                            $warna =
                                                'bg-green-50 text-green-600';

                                        }

                                        $jumlahHadir++;

                                    @endphp

                                @else

                                    @php

                                        $kode = 'LIB';

                                        $warna =
                                            'bg-red-50 text-red-500';

                                        $jumlahLibur++;

                                    @endphp

                                @endif


                            {{-- CUTI --}}
                            @elseif($cutiHariIni)

                                @if(
                                    $cutiHariIni->jenis_cuti === 'alasan_penting'
                                )

                                    @php

                                        $kode = 'CAP';

                                        $warna =
                                            'bg-orange-50 text-orange-600';

                                        $jumlahCutiAlasanPenting++;

                                    @endphp

                                @else

                                    @php

                                        $kode = 'C';

                                        $warna =
                                            'bg-yellow-50 text-yellow-600';

                                        $jumlahCuti++;

                                    @endphp

                                @endif


                            {{-- LUPA ABSEN --}}
                            @elseif(
                                $lupaHariIni &&
                                !$absensiHariIni
                            )

                                @php

                                    $kode = 'LA';

                                    $warna =
                                        'bg-blue-50 text-blue-600';

                                @endphp


                            {{-- ABSENSI --}}
                            @elseif(
                                $absensiHariIni &&
                                $absensiHariIni->jam_masuk
                            )

                                @php

                                    /*
                                     * SHIFT MALAM TIDAK LAGI
                                     * DITEBAK DARI JAM.
                                     *
                                     * Menggunakan kolom shift
                                     * yang memang disimpan saat absen.
                                     */

                                    if ($absensiHariIni->shift === 'malam') {

                                        $kode = 'M';

                                        $warna =
                                            'bg-purple-50 text-purple-600';

                                    } else {

                                        $kode = 'H';

                                        $warna =
                                            'bg-green-50 text-green-600';

                                    }

                                    $jumlahHadir++;

                                @endphp

                            @endif


                            {{-- CELL --}}
                            <td
                                class="
                                    px-2 py-3
                                    text-center
                                    border-r
                                    border-slate-100
                                    {{ $kode === 'LIB'
                                        ? 'bg-red-50/40'
                                        : ''
                                    }}
                                "
                            >

                                <span
                                    class="
                                        inline-flex
                                        items-center
                                        justify-center
                                        w-9 h-9
                                        rounded-xl
                                        text-[10px]
                                        font-extrabold
                                        {{ $warna }}
                                    "
                                    title="{{ $kode }}"
                                >
                                    {{ $kode }}
                                </span>

                            </td>

                        @endforeach


                        {{-- RINGKASAN --}}
                        <td class="px-4 py-4 bg-slate-50/60">

                            <div class="flex flex-wrap justify-center gap-1.5">

                                {{-- HADIR --}}
                                <span
                                    class="
                                        inline-flex
                                        items-center
                                        gap-1
                                        rounded-lg
                                        bg-green-50
                                        px-2 py-1
                                        text-[10px]
                                        font-bold
                                        text-green-600
                                    "
                                >
                                    H {{ $jumlahHadir }}
                                </span>


                                {{-- CUTI TAHUNAN --}}
                                @if($jumlahCuti > 0)

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-1
                                            rounded-lg
                                            bg-yellow-50
                                            px-2 py-1
                                            text-[10px]
                                            font-bold
                                            text-yellow-600
                                        "
                                    >
                                        CT {{ $jumlahCuti }}
                                    </span>

                                @endif


                                {{-- ALASAN PENTING --}}
                                @if($jumlahCutiAlasanPenting > 0)

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-1
                                            rounded-lg
                                            bg-orange-50
                                            px-2 py-1
                                            text-[10px]
                                            font-bold
                                            text-orange-600
                                        "
                                    >
                                        CAP {{ $jumlahCutiAlasanPenting }}
                                    </span>

                                @endif


                                {{-- LIBUR --}}
                                @if($jumlahLibur > 0)

                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-1
                                            rounded-lg
                                            bg-red-50
                                            px-2 py-1
                                            text-[10px]
                                            font-bold
                                            text-red-500
                                        "
                                    >
                                        LIB {{ $jumlahLibur }}
                                    </span>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="{{ count($tanggal) + 3 }}"
                            class="px-6 py-16 text-center"
                        >

                            <p class="text-sm font-semibold text-slate-500">
                                Belum ada data pegawai
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                Data pegawai akan muncul setelah tersedia.
                            </p>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- FOOTER --}}
    <div
        class="
            px-5 sm:px-6
            py-4
            border-t
            border-slate-100
            bg-slate-50/50
        "
    >

        <p class="text-xs text-slate-400">

            Sabtu dan Minggu otomatis ditandai sebagai hari libur.
            Jika pegawai melakukan absensi pada hari tersebut,
            absensi tetap dicatat sebagai kehadiran.

        </p>

    </div>

</div>

@endsection