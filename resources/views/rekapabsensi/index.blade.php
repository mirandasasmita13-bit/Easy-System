@extends('layouts.utama')

@section('title', 'Rekap Absensi')

@section('content')

{{-- HEADER --}}
<div class="mb-6">
    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Rekap Absensi</h2>
    <p class="mt-2 text-slate-500">Rekap kehadiran PPNPN dalam satu periode bulan.</p>
</div>

{{-- FILTER + EXPORT --}}
<div class="bg-white px-6 py-5 rounded-2xl border border-slate-100 shadow-sm mb-5">
    <form method="GET" action="{{ url('/rekap') }}">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div class="shrink-0 min-w-[160px] lg:border-r lg:border-slate-100 lg:pr-6">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">Total PPNPN</p>
                <p class="text-4xl font-extrabold text-purple-600 leading-none">{{ count($ppnpn) }}</p>
                <p class="text-xs text-slate-400 mt-1">PPNPN aktif</p>
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Tampilkan Rekap</p>
                <div class="flex flex-col sm:flex-row gap-2">
                    <select name="bulan" class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        @foreach(range(1, 12) as $bulanOption)
                            <option value="{{ $bulanOption }}" @selected($bulan == $bulanOption)>{{ \Carbon\Carbon::create()->month($bulanOption)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                    <select name="tahun" class="w-full sm:w-[120px] rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        @foreach(range(now()->year - 2, now()->year + 1) as $tahunOption)
                            <option value="{{ $tahunOption }}" @selected($tahun == $tahunOption)>{{ $tahunOption }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full sm:w-[120px] inline-flex items-center justify-center rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-purple-200 transition hover:bg-purple-700">Tampilkan</button>
                    <a href="{{ route('rekapabsensi.export.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-600 transition hover:bg-emerald-100">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3" /><path stroke-linecap="round" stroke-linejoin="round" d="M5 20h14a2 2 0 002-2V8.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0014.5 2H5a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        Excel
                    </a>
                    <a href="{{ route('rekapabsensi.export.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-100">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h8l4 4v14H7a2 2 0 01-2-2V5a2 2 0 012-2z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 3v5h5" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6M9 17h6" /></svg>
                        PDF
                    </a>
                </div>
            </div>
        </div>
    </form>
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
    </div>
</div>

{{-- MATRIX --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

    <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
        <h3 class="text-lg font-bold text-slate-900">Rekap Kehadiran PPNPN</h3>
    </div> 

    <div class="overflow-x-auto">
        <table class="min-w-max w-full border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="sticky left-0 z-30 bg-slate-50 w-[50px] min-w-[50px] px-2 py-4 text-center text-xs font-bold text-slate-500 border-r border-slate-200">No</th>
                    <th class="sticky left-[50px] z-30 bg-slate-50 min-w-[220px] px-4 py-4 text-left text-xs font-bold text-slate-500 border-r border-slate-200">Nama PPNPN</th>

                    @foreach($tanggal as $hari)
                        @php $isWeekend = $hari->isWeekend(); @endphp
                        <th class="px-1 py-3 text-center border-r border-slate-100 min-w-[72px] {{ $isWeekend ? 'bg-rose-50' : 'bg-slate-50' }}">
                            <div class="text-xs font-extrabold {{ $isWeekend ? 'text-rose-500' : 'text-slate-700' }}">{{ $hari->format('d') }}</div>
                            <div class="text-[10px] font-medium mt-0.5 {{ $isWeekend ? 'text-rose-300' : 'text-slate-400' }}">{{ $hari->translatedFormat('D') }}</div>
                        </th>
                    @endforeach

                    <th class="px-4 py-4 text-center text-xs font-bold text-slate-500 bg-slate-50 min-w-[120px] border-l-2 border-slate-200">Ringkasan</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($ppnpn as $index => $user)

                    @php
                        $jumlahHadir = 0; $jumlahCuti = 0; $jumlahCutiAlasanPenting = 0;
                        $jumlahLibur = 0; $jumlahPending = 0; $jumlahSakit = 0; $jumlahLupa = 0;
                    @endphp

                    <tr class="hover:bg-slate-50/60 transition">

                        <td class="sticky left-0 z-20 bg-white w-[50px] min-w-[50px] px-2 py-4 text-center text-xs font-semibold text-slate-500 border-r border-slate-100">
                            {{ $index + 1 }}
                        </td>

                        <td class="sticky left-[50px] z-20 bg-white min-w-[220px] px-4 py-4 border-r border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-800 truncate">{{ $user->name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ $user->profil?->jabatan ?? 'PPNPN' }}</p>
                                </div>
                            </div>
                        </td>

                        @foreach($tanggal as $hari)

                            @php
                                $tanggalKey = $hari->format('Y-m-d');
                                $absensiHariIni    = $absensi[$user->id][$tanggalKey] ?? null;
                                $cutiHariIni       = $cuti[$user->id][$tanggalKey] ?? null;
                                $lupaHariIni       = $lupaAbsen[$user->id][$tanggalKey] ?? null;
                                $sakitHariIni      = $suratSakit[$user->id][$tanggalKey] ?? null;
                                $isWeekend         = $hari->isWeekend();

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
                                $jamMasukText = null; $jamPulangText = null; $isPending = false;
                            @endphp

                            @if($isWeekend)
                                @if($absensiValid)
                                    @php
                                        if ($absensiHariIni->shift === 'malam') { $kode = 'M'; $warna = 'bg-violet-100 text-violet-800 ring-1 ring-violet-200'; }
                                        else { $kode = 'H'; $warna = 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200'; }
                                        $jumlahHadir++;
                                        $jamMasukText = \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i');
                                        $jamPulangText = $absensiHariIni->jam_pulang ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') : null;
                                    @endphp
                                @elseif($absensiPending)
                                    @php
                                        $kode = 'P'; $warna = 'bg-orange-200 text-orange-900 ring-1 ring-orange-300';
                                        $isPending = true; $jumlahPending++;
                                        $jamMasukText = $absensiHariIni->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                                    @endphp
                                @else
                                    @php $kode = 'LIB'; $warna = 'bg-red-100 text-red-800 ring-1 ring-red-200'; $jumlahLibur++; @endphp
                                @endif
                            @else
                                @if($sakitHariIni)
                                    @php $kode = 'S'; $warna = 'bg-rose-100 text-rose-800 ring-1 ring-rose-200'; $jumlahSakit++; @endphp
                                @elseif($cutiHariIni)
                                    @if($cutiHariIni->jenis_cuti === 'alasan_penting')
                                        @php $kode = 'CAP'; $warna = 'bg-orange-100 text-orange-800 ring-1 ring-orange-200'; $jumlahCutiAlasanPenting++; @endphp
                                    @else
                                        @php $kode = 'C'; $warna = 'bg-amber-100 text-amber-800 ring-1 ring-amber-200'; $jumlahCuti++; @endphp
                                    @endif
                                @elseif($absensiValid)
                                    @php
                                        if ($absensiHariIni->shift === 'malam') { $kode = 'M'; $warna = 'bg-violet-100 text-violet-800 ring-1 ring-violet-200'; }
                                        else { $kode = 'H'; $warna = 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200'; }
                                        $jumlahHadir++;
                                        $jamMasukText = \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i');
                                        $jamPulangText = $absensiHariIni->jam_pulang ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') : null;
                                    @endphp
                                @elseif($lupaApproved)
                                    @php $kode = 'H'; $warna = 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200'; $jumlahHadir++; @endphp
                                @elseif($lupaHariIni && !$absensiPending)
                                    @php $kode = 'LA'; $warna = 'bg-sky-100 text-sky-800 ring-1 ring-sky-200'; $jumlahLupa++; @endphp
                                @elseif($absensiPending)
                                    @php
                                        $kode = 'P'; $warna = 'bg-orange-200 text-orange-900 ring-1 ring-orange-300';
                                        $isPending = true; $jumlahPending++;
                                        $jamMasukText = $absensiHariIni->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                                    @endphp
                                @endif
                            @endif

                            <td class="px-1 py-2 text-center align-top border-r border-slate-100 min-w-[72px]
                                {{ $kode === 'LIB' ? 'bg-red-50/40' : '' }}
                                {{ $isPending ? 'bg-orange-50/40' : '' }}
                                {{ $kode === 'S' ? 'bg-rose-50/40' : '' }}">

                                {{-- KODE --}}
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-[10px] font-extrabold {{ $warna }}"
                                      title="{{ $isPending ? 'Menunggu approval admin' : $kode }}">
                                    {{ $kode }}
                                </span>

                                {{-- JAM MASUK --}}
                                @if($jamMasukText)
                                    <div class="mt-1 text-[9px] leading-tight whitespace-nowrap {{ $isPending ? 'text-orange-700 font-semibold' : 'text-slate-600' }}">
                                        ↓ {{ $jamMasukText }}
                                    </div>
                                @endif

                                {{-- JAM PULANG --}}
                                @if($jamPulangText)
                                    <div class="text-[9px] leading-tight whitespace-nowrap text-slate-500">
                                        ↑ {{ $jamPulangText }}
                                    </div>
                                @endif

                                {{-- ICON PENDING --}}
                                @if($isPending)
                                    <div class="mt-0.5 text-[9px] text-orange-600 font-bold">⏳</div>
                                @endif
                            </td>

                        @endforeach

                        {{-- RINGKASAN --}}
                        <td class="px-3 py-4 bg-slate-50/60 border-l-2 border-slate-200">
                            <div class="flex flex-wrap justify-center gap-1">
                                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-800 ring-1 ring-emerald-200">H {{ $jumlahHadir }}</span>
                                @if($jumlahCuti > 0)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold text-amber-800 ring-1 ring-amber-200">C {{ $jumlahCuti }}</span>
                                @endif
                                @if($jumlahCutiAlasanPenting > 0)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-orange-100 px-1.5 py-0.5 text-[10px] font-bold text-orange-800 ring-1 ring-orange-200">CAP {{ $jumlahCutiAlasanPenting }}</span>
                                @endif
                                @if($jumlahSakit > 0)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-rose-100 px-1.5 py-0.5 text-[10px] font-bold text-rose-800 ring-1 ring-rose-200">S {{ $jumlahSakit }}</span>
                                @endif
                                @if($jumlahLupa > 0)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-sky-100 px-1.5 py-0.5 text-[10px] font-bold text-sky-800 ring-1 ring-sky-200">LA {{ $jumlahLupa }}</span>
                                @endif
                                @if($jumlahPending > 0)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-orange-200 px-1.5 py-0.5 text-[10px] font-bold text-orange-900 ring-1 ring-orange-300">P {{ $jumlahPending }}</span>
                                @endif
                                @if($jumlahLibur > 0)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-bold text-red-800 ring-1 ring-red-200">LIB {{ $jumlahLibur }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="{{ count($tanggal) + 3 }}" class="px-6 py-16 text-center">
                            <p class="text-sm font-semibold text-slate-500">Belum ada data PPNPN</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection