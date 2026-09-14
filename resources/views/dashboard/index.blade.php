@extends('layouts.utama')

@section('title', 'Dashboard')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="mb-6">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Halo, {{ auth()->user()->name }} 👋
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Berikut ringkasan aktivitas Anda hari ini.
            </p>
        </div>

        <div class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 text-sm text-slate-600 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Sistem Online
        </div>

    </div>

</div>


{{-- ================= HERO CLOCK + ABSEN ================= --}}
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-purple-700 to-purple-500 shadow-lg mb-6">

    <div class="absolute -right-20 -top-28 w-80 h-80 rounded-full bg-white/10 blur-2xl"></div>
    <div class="absolute -left-24 -bottom-32 w-80 h-80 rounded-full bg-white/5 blur-2xl"></div>

    <div class="relative z-10 p-6 sm:p-8 lg:p-10">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

            {{-- WAKTU --}}
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <p id="tanggal" class="text-purple-100 text-sm font-medium tracking-wide"></p>
                </div>

                <div id="jam" class="text-5xl sm:text-6xl font-extrabold text-white tracking-tight">
                    00:00:00
                </div>

                <p class="mt-2 text-purple-200 text-sm">
                    Waktu Indonesia Barat (WIB)
                </p>
            </div>

            {{-- TOMBOL ABSEN --}}
            <div class="shrink-0">

                @php
                    $absensiHariIni = \App\Models\Absensi::where('user_id', auth()->id())
                        ->whereDate('tanggal', today())
                        ->latest()
                        ->first();
                @endphp

                @if(!$absensiHariIni)

                    <a href="{{ url('/absensi') }}"
                       class="inline-flex items-center justify-center gap-2 bg-white text-purple-700 px-6 py-4 rounded-2xl font-extrabold text-base shadow-xl hover:bg-purple-50 hover:-translate-y-0.5 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                        </svg>
                        Absen Masuk
                    </a>

                @elseif(!$absensiHariIni->jam_pulang)

                    <a href="{{ url('/absensi') }}"
                       class="inline-flex items-center justify-center gap-2 bg-white text-purple-700 px-6 py-4 rounded-2xl font-extrabold text-base shadow-xl hover:bg-purple-50 hover:-translate-y-0.5 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h9"/>
                        </svg>
                        Absen Pulang
                    </a>

                @else

                    <div class="inline-flex items-center justify-center gap-2 bg-emerald-500/20 border border-emerald-300/40 text-white px-6 py-4 rounded-2xl font-bold text-base backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        Absen Lengkap
                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- ================= STATISTIK (AKSEN WARNA) ================= --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    {{-- KEHADIRAN BULAN INI --}}
    <div class="group relative bg-white rounded-2xl border border-slate-100 shadow-sm p-5 overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-400"></div>

        <div class="flex items-start justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Kehadiran
                </p>
                <div class="flex items-baseline gap-1.5 mt-3">
                    <span class="text-3xl font-extrabold text-emerald-600">
                        {{ $jumlahKehadiran ?? 0 }}
                    </span>
                    <span class="text-xs text-slate-400">hari</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    Bulan ini
                </p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>


    {{-- SISA CUTI --}}
    <div class="group relative bg-white rounded-2xl border border-slate-100 shadow-sm p-5 overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-purple-400"></div>

        <div class="flex items-start justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Sisa Cuti
                </p>
                <div class="flex items-baseline gap-1.5 mt-3">
                    <span class="text-3xl font-extrabold text-purple-600">
                        {{ $sisaCuti ?? 0 }}
                    </span>
                    <span class="text-xs text-slate-400">hari</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    Tahun berjalan
                </p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 3v2.25M17.25 3v2.25M3.75 9h16.5M5.25 5.25h13.5A1.5 1.5 0 0120.25 6.75v11.5a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V6.75a1.5 1.5 0 011.5-1.5z"/>
                </svg>
            </div>
        </div>
    </div>


    {{-- LEMBUR BULAN INI --}}
    <div class="group relative bg-white rounded-2xl border border-slate-100 shadow-sm p-5 overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-400"></div>

        <div class="flex items-start justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Lembur
                </p>
                <div class="flex items-baseline gap-1.5 mt-3">
                    <span class="text-3xl font-extrabold text-amber-600">
                        {{ $totalLembur ?? 0 }}
                    </span>
                    <span class="text-xs text-slate-400">jam</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    Bulan ini
                </p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>


    {{-- STATUS HARI INI --}}
    <div class="group relative bg-white rounded-2xl border border-slate-100 shadow-sm p-5 overflow-hidden hover:-translate-y-0.5 hover:shadow-md transition">
        <div class="absolute left-0 top-0 bottom-0 w-1
                    {{ !$absensiHariIni ? 'bg-red-400' : (!$absensiHariIni->jam_pulang ? 'bg-amber-400' : 'bg-emerald-400') }}"></div>

        <div class="flex items-start justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    Hari Ini
                </p>
                <div class="mt-3">
                    <span class="text-lg sm:text-xl font-extrabold
                                 {{ !$absensiHariIni ? 'text-red-500' : (!$absensiHariIni->jam_pulang ? 'text-amber-600' : 'text-emerald-600') }}">
                        @if(!$absensiHariIni)
                            Belum Absen
                        @elseif(!$absensiHariIni->jam_pulang)
                            Sudah Masuk
                        @else
                            Lengkap
                        @endif
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    @if(!$absensiHariIni)
                        Silakan absen
                    @elseif(!$absensiHariIni->jam_pulang)
                        Belum absen pulang
                    @else
                        Absen selesai
                    @endif
                </p>
            </div>
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition
                        {{ !$absensiHariIni ? 'bg-red-50 text-red-500' : (!$absensiHariIni->jam_pulang ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3.75m0 3h.008v.008H12v-.008zM10.29 3.86l-7.5 13A1.5 1.5 0 004.09 19h15.82a1.5 1.5 0 001.3-2.25l-7.5-13a1.5 1.5 0 00-2.6 0z"/>
                </svg>
            </div>
        </div>
    </div>

</div>


{{-- ================= CARD PENGAJUAN HARI INI ================= --}}
@if(($totalPending ?? 0) > 0 || ($totalApprovedHariIni ?? 0) > 0)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-slate-900 text-sm">Status Pengajuan Saya</p>
                <p class="text-xs text-slate-400 mt-0.5">Informasi pengajuan hari ini.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

            {{-- PENDING --}}
            @if(($totalPending ?? 0) > 0)
                <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 6v6l3 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-amber-800">
                            {{ $totalPending }} menunggu approval
                        </p>
                        <p class="text-xs text-amber-600 mt-0.5">
                            Sedang diproses admin
                        </p>
                    </div>
                </div>
            @endif


            {{-- APPROVED HARI INI --}}
            @if(($totalApprovedHariIni ?? 0) > 0)
                <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-emerald-800">
                            {{ $totalApprovedHariIni }} disetujui hari ini
                        </p>
                        <p class="text-xs text-emerald-600 mt-0.5">
                            Sudah diverifikasi admin
                        </p>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endif


{{-- ================= INFORMASI PENTING ================= --}}
<div class="rounded-2xl bg-purple-50 border border-purple-100 p-5 flex items-start gap-3">

    <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
        </svg>
    </div>

    <div>
        <p class="font-bold text-purple-800 text-sm">Informasi Penting</p>
        <p class="text-xs text-purple-700 mt-1 leading-relaxed">
            Jangan lupa absen pulang sebelum meninggalkan kantor.
            Jika lupa absen, silakan ajukan perbaikan di menu <strong>Lupa Absen</strong>.
        </p>
    </div>

</div>


{{-- ================= JAM REALTIME ================= --}}
<script>

    function updateClock() {

        const now = new Date();

        const jam = now.toLocaleTimeString('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const tanggal = now.toLocaleDateString('id-ID', {
            timeZone: 'Asia/Jakarta',
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        document.getElementById('jam').textContent = jam;
        document.getElementById('tanggal').textContent = tanggal;
    }

    updateClock();
    setInterval(updateClock, 1000);

</script>

@endsection