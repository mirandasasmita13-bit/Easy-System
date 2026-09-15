@extends('layouts.utama')

@section('title', 'Rekap Absensi Saya')

@section('content')

<div class="mb-7">
    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Rekap Absensi Saya</h2>
    <p class="mt-2 text-sm sm:text-base text-slate-500">Rekap kehadiran dan aktivitas Anda dalam satu periode bulan.</p>
</div>

{{-- FILTER + EXPORT --}}
<div class="bg-white px-6 py-5 rounded-2xl border border-slate-100 shadow-sm mb-5">
    <form method="GET" action="{{ route('rekap-saya') }}">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Tampilkan Rekap</p>
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_140px_130px] gap-2">
                    <select name="bulan" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        @foreach(range(1, 12) as $bulanOption)
                            <option value="{{ $bulanOption }}" @selected($bulan == $bulanOption)>{{ \Carbon\Carbon::create()->month($bulanOption)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    <select name="tahun" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        @foreach(range(now()->year - 2, now()->year + 1) as $tahunOption)
                            <option value="{{ $tahunOption }}" @selected($tahun == $tahunOption)>{{ $tahunOption }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-purple-200 transition hover:bg-purple-700">Tampilkan</button>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 shrink-0">
                <div class="flex gap-2">
                    <a href="{{ route('rekap-saya.export.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-600 transition hover:bg-emerald-100">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 20h14a2 2 0 002-2V8.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0014.5 2H5a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        Excel
                    </a>
                    <a href="{{ route('rekap-saya.export.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-100">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l4 4v14H7a2 2 0 01-2-2V5a2 2 0 012-2z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6M9 17h6" /></svg>
                        PDF
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- RINGKASAN --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-5">
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Hadir</p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-emerald-600">{{ $jumlahHadir ?? 0 }}</span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Cuti</p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-amber-600">{{ ($jumlahCutiTahunan ?? 0) + ($jumlahCutiAlasanPenting ?? 0) }}</span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Surat Sakit</p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-rose-600">{{ $jumlahSakit ?? 0 }}</span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Lembur</p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-indigo-600">{{ $jumlahLembur ?? 0 }}</span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>
    <div class="bg-white px-5 py-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pending</p>
        <div class="mt-2 flex items-end gap-2">
            <span class="text-3xl font-extrabold text-orange-600">{{ $jumlahPending ?? 0 }}</span>
            <span class="text-xs text-slate-400 mb-1">hari</span>
        </div>
    </div>
</div>

{{-- LEGEND --}}
<div class="bg-white px-5 py-4 rounded-2xl border border-slate-100 shadow-sm mb-5">
    <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
        <span class="text-xs font-bold text-slate-500 mr-1">Keterangan:</span>
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200 flex items-center justify-center text-[11px] font-extrabold">H</span>
            <span class="text-xs text-slate-500">Shift Pagi</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-violet-100 text-violet-800 ring-1 ring-violet-200 flex items-center justify-center text-[10px] font-extrabold">M</span>
            <span class="text-xs text-slate-500">Shift Malam</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 ring-1 ring-amber-200 flex items-center justify-center text-[10px] font-extrabold">C</span>
            <span class="text-xs text-slate-500">Cuti Tahunan</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-orange-100 text-orange-800 ring-1 ring-orange-200 flex items-center justify-center text-[9px] font-extrabold">CAP</span>
            <span class="text-xs text-slate-500">Cuti Alasan Penting</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-sky-100 text-sky-800 ring-1 ring-sky-200 flex items-center justify-center text-[9px] font-extrabold">LA</span>
            <span class="text-xs text-slate-500">Lupa Absen</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-800 ring-1 ring-rose-200 flex items-center justify-center text-[11px] font-extrabold">S</span>
            <span class="text-xs text-slate-500">Surat Sakit</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-orange-200 text-orange-900 ring-1 ring-orange-300 flex items-center justify-center text-[10px] font-extrabold">P</span>
            <span class="text-xs text-slate-500">Pending</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-red-100 text-red-800 ring-1 ring-red-200 flex items-center justify-center text-[8px] font-extrabold">LIB</span>
            <span class="text-xs text-slate-500">Hari Libur</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-800 ring-1 ring-indigo-200 flex items-center justify-center text-[10px] font-extrabold">L</span>
            <span class="text-xs text-slate-500">Lembur</span>
        </div>
    </div>
</div>

{{-- MATRIX PERSONAL --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

    <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
        <h3 class="text-lg font-bold text-slate-900">Rekap Kehadiran Saya</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-max w-full border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    @foreach($tanggal as $hari)
                        @php $isWeekend = $hari->isWeekend(); @endphp
                        <th class="px-2 py-3 text-center border-r border-slate-100 min-w-[80px] {{ $isWeekend ? 'bg-rose-50' : 'bg-slate-50' }}">
                            <div class="text-xs font-extrabold {{ $isWeekend ? 'text-rose-500' : 'text-slate-600' }}">{{ $hari->format('d') }}</div>
                            <div class="text-[10px] font-medium mt-0.5 {{ $isWeekend ? 'text-rose-300' : 'text-slate-400' }}">{{ $hari->translatedFormat('D') }}</div>
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

                            $lupaApproved = false;
                            if ($lupaHariIni) {
                                $approvedVals = ['approved', 'disetujui', 'diterima', 'setuju', 'accept', 'accepted', 'terima'];
                                foreach (['status', 'status_approval', 'status_pengajuan', 'approval_status'] as $f) {
                                    if (isset($lupaHariIni->$f) && in_array(strtolower(trim((string) $lupaHariIni->$f)), $approvedVals, true)) {
                                        $lupaApproved = true; break;
                                    }
                                }
                            }

                            $absensiValid = $absensiHariIni && $absensiHariIni->jam_masuk
                                && ($absensiHariIni->status_approval !== 'pending' || $lupaApproved);

                            $absensiPending = $absensiHariIni
                                && $absensiHariIni->status_approval === 'pending'
                                && !$lupaApproved;

                            $kode = '-';
                            $warna = 'bg-slate-50 text-slate-300 ring-1 ring-slate-100';
                            $keterangan = 'Belum ada data';
                            $jamMasukText = null; $jamPulangText = null; $isPending = false;
                        @endphp

                        @if($absensiValid)
                            @php
                                $jamMasuk = \Carbon\Carbon::parse($absensiHariIni->jam_masuk);
                                if ($absensiHariIni->shift === 'malam' || $jamMasuk->hour >= 18) {
                                    $kode = 'M'; $warna = 'bg-violet-100 text-violet-800 ring-1 ring-violet-200';
                                    $keterangan = 'Hadir shift malam';
                                } else {
                                    $kode = 'H'; $warna = 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200';
                                    $keterangan = 'Hadir shift pagi';
                                }
                                $jamMasukText = $jamMasuk->format('H:i');
                                $jamPulangText = $absensiHariIni->jam_pulang ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') : null;
                            @endphp
                        @elseif($absensiPending)
                            @php
                                $kode = 'P'; $warna = 'bg-orange-200 text-orange-900 ring-1 ring-orange-300';
                                $keterangan = 'Menunggu approval admin';
                                $isPending = true;
                                $jamMasukText = $absensiHariIni->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                            @endphp
                        @elseif($hari->isWeekend())
                            @php $kode = 'LIB'; $warna = 'bg-red-100 text-red-800 ring-1 ring-red-200'; $keterangan = 'Hari libur'; @endphp
                        @elseif($cutiHariIni)
                            @if($cutiHariIni->jenis_cuti === 'tahunan')
                                @php $kode = 'C'; $warna = 'bg-amber-100 text-amber-800 ring-1 ring-amber-200'; $keterangan = 'Cuti tahunan'; @endphp
                            @elseif($cutiHariIni->jenis_cuti === 'alasan_penting')
                                @php $kode = 'CAP'; $warna = 'bg-orange-100 text-orange-800 ring-1 ring-orange-200'; $keterangan = 'Cuti alasan penting'; @endphp
                            @endif
                        @elseif($suratSakitHariIni)
                            @php $kode = 'S'; $warna = 'bg-rose-100 text-rose-800 ring-1 ring-rose-200'; $keterangan = 'Surat izin sakit'; @endphp
                        @elseif($lupaApproved)
                            @php $kode = 'H'; $warna = 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200'; $keterangan = 'Lupa absen disetujui'; @endphp
                        @elseif($lupaHariIni)
                            @php $kode = 'LA'; $warna = 'bg-sky-100 text-sky-800 ring-1 ring-sky-200'; $keterangan = 'Lupa absen'; @endphp
                        @endif

                        <td class="px-1 py-5 text-center border-r border-slate-100 min-w-[80px]
                            {{ $kode === 'LIB' ? 'bg-red-50/40' : '' }}
                            {{ $isPending ? 'bg-orange-50/40' : '' }}">
                            <div class="relative inline-flex flex-col items-center">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-[9px] font-extrabold {{ $warna }}" title="{{ $keterangan }}">{{ $kode }}</span>
                                @if($jamMasukText || $jamPulangText)
                                    <div class="mt-1 text-[9px] leading-tight {{ $isPending ? 'text-orange-700 font-semibold' : 'text-slate-500' }}">
                                        @if($jamMasukText)<div class="whitespace-nowrap">↓ {{ $jamMasukText }}</div>@endif
                                        @if($jamPulangText)<div class="whitespace-nowrap">↑ {{ $jamPulangText }}</div>@endif
                                    </div>
                                @endif
                                @if($lemburHariIni)
                                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[8px] font-extrabold ring-2 ring-white" title="Lembur">L</span>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    {{-- DETAIL --}}
    <div class="border-t border-slate-100">
        <div class="px-5 sm:px-6 py-5">
            <h3 class="text-base font-bold text-slate-900">Detail Kehadiran</h3>
            <p class="text-sm text-slate-400 mt-1">Waktu masuk dan pulang berdasarkan data absensi.</p>
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

                            $lupaApproved = false;
                            if ($lupaHariIni) {
                                $approvedVals = ['approved', 'disetujui', 'diterima', 'setuju', 'accept', 'accepted', 'terima'];
                                foreach (['status', 'status_approval', 'status_pengajuan', 'approval_status'] as $f) {
                                    if (isset($lupaHariIni->$f) && in_array(strtolower(trim((string) $lupaHariIni->$f)), $approvedVals, true)) {
                                        $lupaApproved = true; break;
                                    }
                                }
                            }

                            $absensiValid = $absensiHariIni && $absensiHariIni->jam_masuk
                                && ($absensiHariIni->status_approval !== 'pending' || $lupaApproved);
                        @endphp

                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold text-slate-800">{{ $hari->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $hari->translatedFormat('l') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                @if($absensiHariIni && $absensiHariIni->status_approval === 'pending' && !$lupaApproved)
                                    <span class="inline-flex rounded-lg bg-orange-200 px-2.5 py-1 text-[10px] font-bold text-orange-900 ring-1 ring-orange-300">⏳ Pending</span>
                                @elseif($absensiHariIni && $absensiHariIni->jam_masuk)
                                    @php $jamMasuk = \Carbon\Carbon::parse($absensiHariIni->jam_masuk); @endphp
                                    @if($absensiHariIni->shift === 'malam' || $jamMasuk->hour >= 18)
                                        <span class="inline-flex rounded-lg bg-violet-100 px-2.5 py-1 text-[10px] font-bold text-violet-800 ring-1 ring-violet-200">M — Shift Malam</span>
                                    @else
                                        <span class="inline-flex rounded-lg bg-emerald-100 px-2.5 py-1 text-[10px] font-bold text-emerald-800 ring-1 ring-emerald-200">H — Shift Pagi</span>
                                    @endif
                                @elseif($hari->isWeekend())
                                    <span class="inline-flex rounded-lg bg-red-100 px-2.5 py-1 text-[10px] font-bold text-red-800 ring-1 ring-red-200">LIB — Hari Libur</span>
                                @elseif($cutiHariIni)
                                    @if($cutiHariIni->jenis_cuti === 'tahunan')
                                        <span class="inline-flex rounded-lg bg-amber-100 px-2.5 py-1 text-[10px] font-bold text-amber-800 ring-1 ring-amber-200">C — Cuti Tahunan</span>
                                    @elseif($cutiHariIni->jenis_cuti === 'alasan_penting')
                                        <span class="inline-flex rounded-lg bg-orange-100 px-2.5 py-1 text-[10px] font-bold text-orange-800 ring-1 ring-orange-200">CAP — Alasan Penting</span>
                                    @else
                                        <span class="inline-flex rounded-lg bg-amber-100 px-2.5 py-1 text-[10px] font-bold text-amber-800 ring-1 ring-amber-200">C — Cuti</span>
                                    @endif
                                @elseif($suratSakitHariIni)
                                    <span class="inline-flex rounded-lg bg-rose-100 px-2.5 py-1 text-[10px] font-bold text-rose-800 ring-1 ring-rose-200">S — Surat Sakit</span>
                                @elseif($lupaApproved)
                                    <span class="inline-flex rounded-lg bg-emerald-100 px-2.5 py-1 text-[10px] font-bold text-emerald-800 ring-1 ring-emerald-200">H — Lupa Absen Disetujui</span>
                                @elseif($lupaHariIni)
                                    <span class="inline-flex rounded-lg bg-sky-100 px-2.5 py-1 text-[10px] font-bold text-sky-800 ring-1 ring-sky-200">LA — Lupa Absen</span>
                                @else
                                    <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-400 ring-1 ring-slate-200">Belum Absen</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($absensiHariIni && $absensiHariIni->jam_masuk)
                                    <span class="text-sm font-semibold {{ ($absensiHariIni->status_approval === 'pending' && !$lupaApproved) ? 'text-orange-600' : 'text-slate-700' }}">
                                        {{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-sm text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($absensiHariIni && $absensiHariIni->jam_pulang)
                                    <span class="text-sm font-semibold text-slate-700">{{ \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') }}</span>
                                @else
                                    <span class="text-sm text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($lemburHariIni)
                                        <span class="inline-flex rounded-lg bg-indigo-100 px-2 py-1 text-[10px] font-bold text-indigo-800 ring-1 ring-indigo-200">Lembur</span>
                                    @endif
                                    @if($absensiHariIni && $absensiHariIni->status_approval === 'pending' && !$lupaApproved)
                                        <span class="text-xs text-orange-600 font-semibold">Menunggu persetujuan admin</span>
                                    @elseif($suratSakitHariIni)
                                        <span class="text-xs text-slate-500">{{ $suratSakitHariIni->keperluan }}</span>
                                    @elseif($cutiHariIni)
                                        <span class="text-xs text-slate-500">{{ $cutiHariIni->keterangan ?? 'Cuti tercatat' }}</span>
                                    @elseif($lupaApproved)
                                        <span class="text-xs text-emerald-600 font-semibold">Lupa absen disetujui</span>
                                    @elseif($lupaHariIni)
                                        <span class="text-xs text-slate-500">Lupa/perbaikan absensi</span>
                                    @elseif($absensiHariIni && $absensiHariIni->jam_masuk)
                                        @php $jamMasuk = \Carbon\Carbon::parse($absensiHariIni->jam_masuk); @endphp
                                        <span class="text-xs text-slate-500">{{ ($absensiHariIni->shift === 'malam' || $jamMasuk->hour >= 18) ? 'Shift malam' : 'Shift pagi' }}</span>
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

<div class="mt-4 px-5 sm:px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50/70">
    <p class="text-xs leading-relaxed text-slate-400">
        Sabtu dan Minggu otomatis ditandai sebagai hari libur.
        Absensi dengan status <strong>pending</strong> tidak dihitung sebagai kehadiran sampai admin menyetujui.
        Lupa absen yang <strong>sudah disetujui</strong> otomatis dihitung sebagai kehadiran.
        <strong>↓ = jam masuk · ↑ = jam pulang.</strong>
    </p>
</div>

@endsection