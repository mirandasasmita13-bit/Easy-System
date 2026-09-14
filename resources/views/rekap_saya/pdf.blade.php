@extends('layouts.utama')

@section('title', 'Rekap Absensi')

@section('content')

{{-- =====================================================
    HEADER
===================================================== --}}

<div class="mb-7">
    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
        Rekap Absensi
    </h2>
    <p class="mt-2 text-sm sm:text-base text-slate-500">
        Rekap kehadiran dan aktivitas Anda dalam satu periode bulan.
    </p>
</div>


{{-- =====================================================
    FILTER + EXPORT (1 CARD)
===================================================== --}}

<div class="bg-white px-6 py-5 rounded-2xl border border-slate-100 shadow-sm mb-5">

    <form method="GET" action="{{ route('rekap-saya') }}">

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">

            {{-- KIRI: Filter Bulan/Tahun + Tombol --}}
            <div class="flex-1 min-w-0">

                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Tampilkan Rekap
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-[1fr_140px_130px] gap-2">

                    {{-- BULAN --}}
                    <select name="bulan"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        @foreach(range(1, 12) as $bulanOption)
                            <option value="{{ $bulanOption }}" @selected($bulan == $bulanOption)>
                                {{ \Carbon\Carbon::create()->month($bulanOption)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>

                    {{-- TAHUN --}}
                    <select name="tahun"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        @foreach(range(now()->year - 2, now()->year + 1) as $tahunOption)
                            <option value="{{ $tahunOption }}" @selected($tahun == $tahunOption)>
                                {{ $tahunOption }}
                            </option>
                        @endforeach
                    </select>

                    {{-- TAMPILKAN --}}
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-purple-200 transition hover:bg-purple-700">
                        Tampilkan
                    </button>

                </div>

            </div>


            {{-- KANAN: Periode + Export --}}
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 shrink-0">

                {{-- EXPORT --}}
                <div class="flex gap-2">

                    <a href="{{ route('rekap-saya.export.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                       class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-600 transition hover:bg-emerald-100">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 20h14a2 2 0 002-2V8.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0014.5 2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Excel
                    </a>

                    <a href="{{ route('rekap-saya.export.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                       class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-100">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l4 4v14H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6M9 17h6" />
                        </svg>
                        PDF
                    </a>

                </div>

            </div>

        </div>

    </form>

</div>


{{-- =====================================================
    RINGKASAN
===================================================== --}}

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-5">

    {{-- HADIR --}}
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
            Hadir
        </p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-green-600">
                {{ $jumlahHadir }}
            </span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>


    {{-- CUTI --}}
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
            Cuti
        </p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-yellow-600">
                {{ $jumlahCutiTahunan + $jumlahCutiAlasanPenting }}
            </span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>


    {{-- SURAT SAKIT --}}
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
            Surat Sakit
        </p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-red-500">
                {{ $jumlahSakit }}
            </span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>


    {{-- LEMBUR --}}
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
            Lembur
        </p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-indigo-600">
                {{ $jumlahLembur }}
            </span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>


    {{-- PENDING --}}
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
            Pending
        </p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-red-600">
                {{ $jumlahPending ?? 0 }}
            </span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>

</div>


{{-- =====================================================
    LEGEND
===================================================== --}}

<div class="bg-white px-5 py-4 rounded-2xl border border-slate-100 shadow-sm mb-5">

    <div class="flex flex-wrap items-center gap-x-5 gap-y-3">

        <span class="text-xs font-bold text-slate-500 mr-1">
            Keterangan:
        </span>

        {{-- HADIR PAGI --}}
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-[11px] font-extrabold">H</span>
            <span class="text-xs text-slate-500">Shift Pagi</span>
        </div>

        {{-- SHIFT MALAM --}}
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-[10px] font-extrabold">M</span>
            <span class="text-xs text-slate-500">Shift Malam</span>
        </div>

        {{-- CUTI TAHUNAN --}}
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center text-[10px] font-extrabold">C</span>
            <span class="text-xs text-slate-500">Cuti Tahunan</span>
        </div>

        {{-- CUTI ALASAN PENTING --}}
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-[9px] font-extrabold">CAP</span>
            <span class="text-xs text-slate-500">Cuti Alasan Penting</span>
        </div>

        {{-- LUPA ABSEN --}}
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-[9px] font-extrabold">LA</span>
            <span class="text-xs text-slate-500">Lupa Absen</span>
        </div>

        {{-- SURAT SAKIT --}}
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-red-50 text-red-500 flex items-center justify-center text-[9px] font-extrabold">S</span>
            <span class="text-xs text-slate-500">Surat Sakit</span>
        </div>

        {{-- PENDING --}}
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-red-100 text-red-700 flex items-center justify-center text-[10px] font-extrabold">P</span>
            <span class="text-xs text-slate-500">Pending</span>
        </div>

        {{-- LIBUR --}}
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-red-50 text-red-500 flex items-center justify-center text-[8px] font-extrabold">LIB</span>
            <span class="text-xs text-slate-500">Hari Libur</span>
        </div>

        {{-- LEMBUR --}}
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-extrabold">L</span>
            <span class="text-xs text-slate-500">Lembur</span>
        </div>

    </div>

</div>


{{-- =====================================================
    MATRIX REKAP INDIVIDU
===================================================== --}}

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

    {{-- HEADER MATRIX --}}
    <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
        <h3 class="text-lg font-bold text-slate-900">
            Rekap Kehadiran Saya
        </h3>
        <p class="text-sm text-slate-400 mt-1">
            Kondisi kehadiran ditampilkan berdasarkan tanggal.
        </p>
    </div>


    {{-- MATRIX --}}
    <div class="overflow-x-auto">

        <table class="min-w-max w-full border-collapse">

            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">

                    @foreach($tanggal as $hari)
                        @php $isWeekend = $hari->isWeekend(); @endphp

                        <th class="px-2 py-3 text-center border-r border-slate-100 min-w-[70px] {{ $isWeekend ? 'bg-red-50' : 'bg-slate-50' }}">
                            <div class="text-xs font-extrabold {{ $isWeekend ? 'text-red-500' : 'text-slate-600' }}">
                                {{ $hari->format('d') }}
                            </div>
                            <div class="text-[10px] font-medium mt-0.5 {{ $isWeekend ? 'text-red-300' : 'text-slate-400' }}">
                                {{ $hari->translatedFormat('D') }}
                            </div>
                        </th>
                    @endforeach

                </tr>
            </thead>


            <tbody>
                <tr>

                    @foreach($tanggal as $hari)

                        @php
                            $tanggalKey = $hari->format('Y-m-d');

                            $absensiHariIni    = $absensi[$tanggalKey] ?? null;
                            $cutiHariIni       = $cuti[$tanggalKey] ?? null;
                            $lupaHariIni       = $lupaAbsen[$tanggalKey] ?? null;
                            $lemburHariIni     = $lembur[$tanggalKey] ?? null;
                            $suratSakitHariIni = $suratSakit[$tanggalKey] ?? null;

                            $absensiValid = $absensiHariIni
                                && $absensiHariIni->status_approval !== 'pending'
                                && $absensiHariIni->jam_masuk;

                            $absensiPending = $absensiHariIni
                                && $absensiHariIni->status_approval === 'pending';

                            $kode = '-';
                            $warna = 'bg-slate-50 text-slate-300';
                            $keterangan = 'Belum ada data';
                            $jamMasukText = null;
                            $jamPulangText = null;
                            $isPending = false;
                        @endphp

                        @if($absensiValid)
                            @php
                                $jamMasuk = \Carbon\Carbon::parse($absensiHariIni->jam_masuk);

                                if ($absensiHariIni->shift === 'malam' || $jamMasuk->hour >= 18) {
                                    $kode = 'M';
                                    $warna = 'bg-purple-50 text-purple-600';
                                    $keterangan = 'Hadir shift malam';
                                } else {
                                    $kode = 'H';
                                    $warna = 'bg-green-50 text-green-600';
                                    $keterangan = 'Hadir shift pagi';
                                }

                                $jamMasukText = $jamMasuk->format('H:i');
                                $jamPulangText = $absensiHariIni->jam_pulang
                                    ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i')
                                    : null;
                            @endphp

                        @elseif($absensiPending)
                            @php
                                $kode = 'P';
                                $warna = 'bg-red-100 text-red-700';
                                $keterangan = 'Menunggu approval admin';
                                $isPending = true;
                                $jamMasukText = $absensiHariIni->jam_masuk
                                    ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i')
                                    : null;
                            @endphp

                        @elseif($hari->isWeekend())
                            @php
                                $kode = 'LIB';
                                $warna = 'bg-red-50 text-red-500';
                                $keterangan = 'Hari libur';
                            @endphp

                        @elseif($cutiHariIni)
                            @if($cutiHariIni->jenis_cuti === 'tahunan')
                                @php
                                    $kode = 'C';
                                    $warna = 'bg-yellow-50 text-yellow-600';
                                    $keterangan = 'Cuti tahunan';
                                @endphp
                            @elseif($cutiHariIni->jenis_cuti === 'alasan_penting')
                                @php
                                    $kode = 'CAP';
                                    $warna = 'bg-orange-50 text-orange-600';
                                    $keterangan = 'Cuti alasan penting';
                                @endphp
                            @endif

                        @elseif($suratSakitHariIni)
                            @php
                                $kode = 'S';
                                $warna = 'bg-red-50 text-red-500';
                                $keterangan = 'Surat izin sakit';
                            @endphp

                        @elseif($lupaHariIni)
                            @php
                                $kode = 'LA';
                                $warna = 'bg-blue-50 text-blue-600';
                                $keterangan = 'Lupa absen';
                            @endphp
                        @endif

                        <td class="px-1 py-5 text-center border-r border-slate-100 min-w-[70px]
                            {{ $kode === 'LIB' ? 'bg-red-50/40' : '' }}
                            {{ $isPending ? 'bg-red-50/60' : '' }}">

                            <div class="relative inline-flex flex-col items-center">

                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-[9px] font-extrabold {{ $warna }}"
                                      title="{{ $keterangan }}">
                                    {{ $kode }}
                                </span>

                                @if($jamMasukText || $jamPulangText)
                                    <div class="mt-1 text-[9px] leading-tight {{ $isPending ? 'text-red-500 font-semibold' : 'text-slate-500' }}">
                                        @if($jamMasukText)<div>↓ {{ $jamMasukText }}</div>@endif
                                        @if($jamPulangText)<div>↑ {{ $jamPulangText }}</div>@endif
                                    </div>
                                @endif

                                @if($lemburHariIni)
                                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[8px] font-extrabold ring-2 ring-white"
                                          title="Lembur">L</span>
                                @endif

                            </div>

                        </td>

                    @endforeach

                </tr>
            </tbody>

        </table>

    </div>


    {{-- =================================================
        DETAIL
    ================================================== --}}

    <div class="border-t border-slate-100">

        <div class="px-5 sm:px-6 py-5">
            <h3 class="text-base font-bold text-slate-900">
                Detail Kehadiran
            </h3>
            <p class="text-sm text-slate-400 mt-1">
                Waktu masuk dan pulang berdasarkan data absensi.
            </p>
        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>
                    <tr class="bg-slate-50 border-y border-slate-100">
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">Jam Masuk</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">Jam Pulang</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500">Keterangan</th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100">

                    @foreach($tanggal as $hari)

                        @php
                            $tanggalKey = $hari->format('Y-m-d');

                            $absensiHariIni    = $absensi[$tanggalKey] ?? null;
                            $cutiHariIni       = $cuti[$tanggalKey] ?? null;
                            $lupaHariIni       = $lupaAbsen[$tanggalKey] ?? null;
                            $suratSakitHariIni = $suratSakit[$tanggalKey] ?? null;
                            $lemburHariIni     = $lembur[$tanggalKey] ?? null;
                        @endphp


                        <tr class="hover:bg-slate-50/60 transition">

                            {{-- TANGGAL --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold text-slate-800">
                                    {{ $hari->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-slate-400">
                                    {{ $hari->translatedFormat('l') }}
                                </p>
                            </td>


                            {{-- STATUS --}}
                            <td class="px-5 py-4">

                                @if($absensiHariIni && $absensiHariIni->status_approval === 'pending')
                                    <span class="inline-flex rounded-lg bg-red-100 px-2.5 py-1 text-[10px] font-bold text-red-700">
                                        ⏳ Pending — Menunggu Approval
                                    </span>
                                @elseif($absensiHariIni && $absensiHariIni->jam_masuk)
                                    @php
                                        $jamMasuk = \Carbon\Carbon::parse($absensiHariIni->jam_masuk);
                                    @endphp

                                    @if($absensiHariIni->shift === 'malam' || $jamMasuk->hour >= 18)
                                        <span class="inline-flex rounded-lg bg-purple-50 px-2.5 py-1 text-[10px] font-bold text-purple-600">
                                            M — Shift Malam
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-lg bg-green-50 px-2.5 py-1 text-[10px] font-bold text-green-600">
                                            H — Shift Pagi
                                        </span>
                                    @endif
                                @elseif($hari->isWeekend())
                                    <span class="inline-flex rounded-lg bg-red-50 px-2.5 py-1 text-[10px] font-bold text-red-500">
                                        LIB — Hari Libur
                                    </span>
                                @elseif($cutiHariIni)
                                    @if($cutiHariIni->jenis_cuti === 'tahunan')
                                        <span class="inline-flex rounded-lg bg-yellow-50 px-2.5 py-1 text-[10px] font-bold text-yellow-600">
                                            C — Cuti Tahunan
                                        </span>
                                    @elseif($cutiHariIni->jenis_cuti === 'alasan_penting')
                                        <span class="inline-flex rounded-lg bg-orange-50 px-2.5 py-1 text-[10px] font-bold text-orange-600">
                                            CAP — Alasan Penting
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-lg bg-yellow-50 px-2.5 py-1 text-[10px] font-bold text-yellow-600">
                                            C — Cuti
                                        </span>
                                    @endif
                                @elseif($suratSakitHariIni)
                                    <span class="inline-flex rounded-lg bg-red-50 px-2.5 py-1 text-[10px] font-bold text-red-500">
                                        S — Surat Sakit
                                    </span>
                                @elseif($lupaHariIni)
                                    <span class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-600">
                                        LA — Lupa Absen
                                    </span>
                                @else
                                    <span class="inline-flex rounded-lg bg-slate-50 px-2.5 py-1 text-[10px] font-bold text-slate-400">
                                        Belum Absen
                                    </span>
                                @endif

                            </td>


                            {{-- JAM MASUK --}}
                            <td class="px-5 py-4">
                                @if($absensiHariIni && $absensiHariIni->jam_masuk)
                                    <span class="text-sm font-semibold {{ $absensiHariIni->status_approval === 'pending' ? 'text-red-600' : 'text-slate-700' }}">
                                        {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-sm text-slate-300">—</span>
                                @endif
                            </td>


                            {{-- JAM PULANG --}}
                            <td class="px-5 py-4">
                                @if($absensiHariIni && $absensiHariIni->jam_pulang)
                                    <span class="text-sm font-semibold text-slate-700">
                                        {{ \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-sm text-slate-300">—</span>
                                @endif
                            </td>


                            {{-- KETERANGAN --}}
                            <td class="px-5 py-4">

                                <div class="flex flex-wrap items-center gap-2">

                                    @if($lemburHariIni)
                                        <span class="inline-flex rounded-lg bg-indigo-50 px-2 py-1 text-[10px] font-bold text-indigo-600">
                                            Lembur
                                        </span>
                                    @endif

                                    @if($absensiHariIni && $absensiHariIni->status_approval === 'pending')
                                        <span class="text-xs text-red-500 font-semibold">
                                            Menunggu persetujuan admin
                                        </span>
                                    @elseif($suratSakitHariIni)
                                        <span class="text-xs text-slate-500">
                                            {{ $suratSakitHariIni->keperluan }}
                                        </span>
                                    @elseif($cutiHariIni)
                                        <span class="text-xs text-slate-500">
                                            {{ $cutiHariIni->keterangan ?? 'Cuti tercatat' }}
                                        </span>
                                    @elseif($lupaHariIni)
                                        <span class="text-xs text-slate-500">
                                            Lupa/perbaikan absensi
                                        </span>
                                    @elseif($absensiHariIni && $absensiHariIni->jam_masuk)
                                        @php
                                            $jamMasuk = \Carbon\Carbon::parse($absensiHariIni->jam_masuk);
                                        @endphp

                                        @if($absensiHariIni->shift === 'malam' || $jamMasuk->hour >= 18)
                                            <span class="text-xs text-slate-500">Shift malam</span>
                                        @else
                                            <span class="text-xs text-slate-500">Shift pagi</span>
                                        @endif
                                    @elseif($hari->isWeekend())
                                        <span class="text-xs text-red-400">Hari libur</span>
                                    @else
                                        <span class="text-xs text-slate-300">Belum ada aktivitas</span>
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =====================================================
    FOOTER
===================================================== --}}

<div class="mt-4 px-5 sm:px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50/70">

    <p class="text-xs leading-relaxed text-slate-400">
        Sabtu dan Minggu otomatis ditandai sebagai hari libur.
        Absensi dengan status <strong>pending</strong> (menunggu approval perbaikan)
        tidak dihitung sebagai kehadiran sampai admin menyetujui.
        Jam masuk/pulang ditampilkan di bawah kode.
    </p>

</div>

@endsection