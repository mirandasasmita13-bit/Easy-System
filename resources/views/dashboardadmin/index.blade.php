@extends('layouts.utama')

@section('content')

<div class="space-y-6">

    {{-- =====================================================
                            HEADER
    ====================================================== --}}

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Berikut ringkasan aktivitas sistem hari ini.
            </p>
        </div>

        <div class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-100 bg-white px-4 py-2.5 shadow-sm">
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
            <span class="text-sm text-slate-500">Easy System</span>
        </div>

    </div>

    {{-- =====================================================
                        CLOCK HERO
    ====================================================== --}}

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-700 to-purple-500 p-6 sm:p-8 text-white shadow-lg">

        <div class="absolute -right-16 -top-24 h-64 w-64 rounded-full bg-white/10"></div>
        <div class="absolute -right-5 top-12 h-40 w-40 rounded-full border-[25px] border-white/5"></div>

        <div class="relative z-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div>
                <p id="tanggalSekarang" class="text-sm text-white/90 font-medium">
                    Memuat tanggal...
                </p>

                <div id="jamSekarang" class="mt-2 text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight">
                    00:00:00
                </div>

                <p class="mt-2 text-sm text-white/85">
                    Waktu Indonesia Barat (WIB)
                </p>
            </div>

            <div class="md:text-right">
                <p class="text-sm text-white/90">
                    Selamat bekerja dan semoga harimu berjalan lancar.
                </p>
            </div>

        </div>

    </div>

    {{-- =====================================================
        AKTIVITAS HARI INI (5 ANGKA)
    ====================================================== --}}

    <div>

        <div class="mb-3 flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-purple-500"></span>
            <p class="text-sm font-semibold text-slate-800">Aktivitas Hari Ini</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">

            {{-- TOTAL PPNPN --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Total PPNPN
                </p>
                <p class="mt-2 text-3xl font-extrabold text-purple-600">
                    {{ $totalPpnpnSemua ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Data tersimpan</p>
            </div>

            {{-- HADIR --}}
            <button type="button" onclick="bukaAktivitas('hadir')"
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-left hover:shadow-md hover:-translate-y-0.5 transition">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Hadir
                </p>
                <p class="mt-2 text-3xl font-extrabold text-emerald-600">
                    {{ $hadirHariIni ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Sudah absen masuk</p>
            </button>

            {{-- BELUM ABSEN --}}
            <button type="button" onclick="bukaAktivitas('belumAbsen')"
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-left hover:shadow-md hover:-translate-y-0.5 transition">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Belum Absen
                </p>
                <p class="mt-2 text-3xl font-extrabold text-red-500">
                    {{ $belumAbsen ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Belum absen masuk</p>
            </button>

            {{-- CUTI --}}
            <button type="button" onclick="bukaAktivitas('cuti')"
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-left hover:shadow-md hover:-translate-y-0.5 transition">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Cuti
                </p>
                <p class="mt-2 text-3xl font-extrabold text-purple-600">
                    {{ $cutiHariIni ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Cuti hari ini</p>
            </button>

            {{-- LEMBUR APPROVAL --}}
            <a href="{{ url('/pengajuan?tab=lembur') }}"
               class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 hover:shadow-md hover:-translate-y-0.5 transition">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Lembur Appr.
                </p>
                <p class="mt-2 text-3xl font-extrabold text-amber-600">
                    {{ $pendingLembur ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Menunggu approval →</p>
            </a>

        </div>

    </div>


    {{-- =====================================================
        STATISTIK BULAN INI (5 ANGKA)
    ====================================================== --}}

    <div>

        <div class="mb-3 flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-purple-500"></span>
            <p class="text-sm font-semibold text-slate-800">Statistik Bulan Ini</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">

            {{-- CUTI --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Cuti
                </p>
                <p class="mt-2 text-3xl font-extrabold text-purple-600">
                    {{ $jumlahCutiBulanIni ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Catatan bulan ini</p>
            </div>

            {{-- LUPA ABSEN --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Lupa Absen
                </p>
                <p class="mt-2 text-3xl font-extrabold text-orange-500">
                    {{ $jumlahLupaAbsenBulanIni ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Catatan bulan ini</p>
            </div>

            {{-- LEMBUR --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Lembur
                </p>
                <p class="mt-2 text-3xl font-extrabold text-slate-800">
                    {{ $jumlahLemburBulanIni ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Catatan bulan ini</p>
            </div>

            {{-- CUTI ALASAN PENTING --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Cuti Penting
                </p>
                <p class="mt-2 text-3xl font-extrabold text-blue-600">
                    {{ $jumlahCutiAlasanPentingBulanIni ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Catatan bulan ini</p>
            </div>

            {{-- TOTAL JAM LEMBUR --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    Jam Lembur
                </p>
                <p class="mt-2 text-3xl font-extrabold text-indigo-600">
                    {{ $totalJamLemburBulanIni ?? 0 }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">Total jam disetujui</p>
            </div>

        </div>

    </div>

    {{-- =====================================================
            WIDGET: JAGA POS HARI INI (2 KOLOM)
        ====================================================== --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            {{-- HEADER WIDGET --}}
            <div class="px-5 py-4 border-b border-slate-100 bg-gradient-to-r from-purple-50 to-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shrink-0 text-xl">
                        🛡️
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Jaga Pos Hari Ini</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- 2 KOLOM: PAGI | MALAM --}}
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-100">

                {{-- KOLOM KIRI: SHIFT PAGI --}}
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">☀️</span>
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-700">
                            Shift Pagi
                        </p>
                        <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-2 rounded-full bg-amber-100 text-amber-700 text-[11px] font-bold">
                            {{ ($jagaPagiHariIni ?? collect())->count() }}
                        </span>
                    </div>

                    @if(isset($jagaPagiHariIni) && $jagaPagiHariIni->count() > 0)
                        <div class="space-y-2">
                            @foreach($jagaPagiHariIni as $item)
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="w-9 h-9 rounded-full
                                                {{ $item->user->role === 'magang' ? 'bg-amber-500 text-white' : 'bg-purple-600 text-white' }}
                                                flex items-center justify-center font-bold shrink-0 text-sm">
                                        {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">
                                            {{ $item->user->name }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">
                                            Masuk {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl bg-slate-50 border border-dashed border-slate-200 py-4 px-4 text-center">
                            <p class="text-xs text-slate-400">
                                Belum ada yang input jaga pos pagi.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- KOLOM KANAN: SHIFT MALAM --}}
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">🌙</span>
                        <p class="text-xs font-bold uppercase tracking-wider text-violet-700">
                            Shift Malam
                        </p>
                        <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-2 rounded-full bg-violet-100 text-violet-700 text-[11px] font-bold">
                            {{ ($jagaMalamHariIni ?? collect())->count() }}
                        </span>
                    </div>

                    @if(isset($jagaMalamHariIni) && $jagaMalamHariIni->count() > 0)
                        <div class="space-y-2">
                            @foreach($jagaMalamHariIni as $item)
                                @php
                                    $masukKemarin = \Carbon\Carbon::parse($item->tanggal)->isYesterday();
                                @endphp
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="w-9 h-9 rounded-full
                                                {{ $item->user->role === 'magang' ? 'bg-amber-500 text-white' : 'bg-purple-600 text-white' }}
                                                flex items-center justify-center font-bold shrink-0 text-sm">
                                        {{ strtoupper(substr($item->user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">
                                            {{ $item->user->name }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">
                                            Masuk {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}
                                            @if($masukKemarin)
                                                · {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl bg-slate-50 border border-dashed border-slate-200 py-4 px-4 text-center">
                            <p class="text-xs text-slate-400">
                                Belum ada yang input jaga pos malam.
                            </p>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    {{-- =====================================================
        INFO PENDING LAIN (kalau ada)
    ====================================================== --}}

    @if(($pendingLupaAbsen ?? 0) > 0)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-amber-800">
                            {{ $pendingLupaAbsen }} perbaikan lupa absen menunggu approval
                        </p>
                        <p class="text-xs text-amber-600 mt-0.5">
                            Segera diproses agar rekap akurat
                        </p>
                    </div>
                </div>

                <a href="{{ url('/pengajuan?tab=lupa-absen') }}"
                   class="shrink-0 inline-flex items-center gap-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 text-xs font-bold transition">
                    Proses
                </a>
            </div>
        </div>
    @endif


    {{-- =====================================================
        INFO PERIODE
    ====================================================== --}}

    <div class="flex flex-col gap-3 rounded-xl border border-slate-100 bg-slate-50/70 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <p class="text-sm font-semibold text-slate-800">Periode Aktivitas</p>
            <p class="mt-0.5 text-xs text-slate-400">
                Statistik administrasi yang ditampilkan pada dashboard.
            </p>
        </div>

        <div class="inline-flex w-fit items-center gap-2 rounded-lg border border-slate-100 bg-white px-3 py-2">
            <span class="h-2 w-2 rounded-full bg-purple-500"></span>
            <span class="text-xs font-medium text-slate-600">Bulan Berjalan</span>
        </div>

    </div>

</div>


{{-- =====================================================
    DATA TERSEMBUNYI UNTUK MODAL
====================================================== --}}

<div class="hidden">

    {{-- DATA HADIR --}}
    <div id="dataHadir">
        @forelse($hadirPpnpn as $item)
            <div class="flex items-center justify-between border-b border-slate-100 py-3 last:border-0">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">
                        {{ $item->user?->name ?? '-' }}
                    </p>
                    @if($item->shift)
                        <p class="mt-1 text-xs text-slate-400">
                            Shift {{ $item->shift === 'malam' ? 'Malam' : 'Pagi' }}
                        </p>
                    @endif
                </div>

                <div class="flex items-center gap-2 shrink-0 ml-3">
                    {{-- Jam Masuk --}}
                    <span class="text-xs font-semibold text-emerald-600">
                        {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '--:--' }}
                    </span>

                    <span class="text-slate-300">–</span>

                    {{-- Jam Pulang --}}
                    @if($item->jam_pulang)
                        <span class="text-xs font-semibold text-blue-600">
                            {{ \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') }}
                        </span>
                    @else
                        <span class="text-xs font-semibold text-amber-600">
                            --:--
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-8 text-center text-sm text-slate-400">
                Belum ada PPNPN yang absen hari ini.
            </div>
        @endforelse
    </div>

    {{-- DATA SUDAH PULANG --}}
    <div id="dataSudahPulang">
        @forelse($sudahPulangPpnpn as $item)
            <div class="flex items-center justify-between border-b border-slate-100 py-3 last:border-0">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">
                        {{ $item->user?->name ?? '-' }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Shift {{ $item->shift === 'malam' ? 'Malam' : 'Pagi' }}
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0 ml-3">
                    <span class="text-xs font-semibold text-emerald-600">
                        {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '--:--' }}
                    </span>
                    <span class="text-slate-300">–</span>
                    <span class="text-xs font-semibold text-sky-600">
                        {{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '--:--' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="py-8 text-center text-sm text-slate-400">
                Belum ada yang absen pulang hari ini.
            </div>
        @endforelse
    </div>

    {{-- DATA BELUM ABSEN --}}
    <div id="dataBelumAbsen">
        @forelse($belumAbsenPegawai as $index => $item)
            <div class="flex items-center gap-3 border-b border-slate-100 py-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-50 text-sm font-semibold text-red-500">
                    {{ $index + 1 }}
                </div>
                <p class="text-sm font-semibold text-slate-800">
                    {{ $item->name ?? '-' }}
                </p>
            </div>
        @empty
            <div class="py-8 text-center text-sm text-emerald-600">
                Semua pegawai sudah absen hari ini.
            </div>
        @endforelse
    </div>

    {{-- DATA CUTI --}}
    <div id="dataCuti">
        @forelse($cutiHariIniData as $item)
            <div class="border-b border-slate-100 py-3">
                <p class="text-sm font-semibold text-slate-800">
                    {{ $item->user?->name ?? '-' }}
                </p>
                <p class="mt-1 text-xs text-purple-600">
                    @if($item->jenis_cuti === 'tahunan')
                        Cuti Tahunan
                    @elseif($item->jenis_cuti === 'tambahan')
                        Cuti Tambahan
                    @elseif($item->jenis_cuti === 'alasan_penting')
                        Cuti Alasan Penting
                    @else
                        Cuti
                    @endif
                </p>
                <p class="mt-1 text-xs text-slate-400">
                    {{ $item->tanggal_mulai
                        ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y')
                        : '-' }}
                    -
                    {{ $item->tanggal_selesai
                        ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y')
                        : '-' }}
                </p>
            </div>
        @empty
            <div class="py-8 text-center text-sm text-slate-400">
                Tidak ada catatan cuti hari ini.
            </div>
        @endforelse
    </div>

</div>

{{-- =====================================================
    MODAL AKTIVITAS
====================================================== --}}

<div id="modalAktivitas"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4"
     onclick="tutupAktivitas()">

    <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl" onclick="event.stopPropagation()">

        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h3 id="modalJudul" class="text-lg font-bold text-slate-800">
                    Aktivitas
                </h3>
                <p class="mt-1 text-xs text-slate-400">
                    Data aktivitas hari ini
                </p>
            </div>

            <button type="button" onclick="tutupAktivitas()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">
                ✕
            </button>
        </div>

        <div id="modalIsi" class="max-h-[60vh] overflow-y-auto px-6 py-5"></div>

        <div class="border-t border-slate-100 px-6 py-4 text-right">
            <button type="button" onclick="tutupAktivitas()"
                    class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-200">
                Tutup
            </button>
        </div>

    </div>

</div>


{{-- =====================================================
    SCRIPT
====================================================== --}}

<script>

    // Modal Aktivitas
    function bukaAktivitas(jenis) {

        const modal = document.getElementById('modalAktivitas');
        const judul = document.getElementById('modalJudul');
        const isi = document.getElementById('modalIsi');

        let sumber = null;

        if (jenis === 'hadir') {
            judul.textContent = 'PPNPN yang Sudah Absen';
            sumber = document.getElementById('dataHadir');
        }

        if (jenis === 'sudahPulang') {
            judul.textContent = 'PPNPN yang Sudah Pulang';
            sumber = document.getElementById('dataSudahPulang');
        }

        if (jenis === 'belumAbsen') {
            judul.textContent = 'PPNPN yang Belum Absen';
            sumber = document.getElementById('dataBelumAbsen');
        }

        if (jenis === 'cuti') {
            judul.textContent = 'PPNPN yang Cuti Hari Ini';
            sumber = document.getElementById('dataCuti');
        }

        if (sumber) {
            isi.innerHTML = sumber.innerHTML;
        } else {
            isi.innerHTML = '<div class="py-8 text-center text-sm text-slate-400">Data tidak ditemukan.</div>';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function tutupAktivitas() {
        const modal = document.getElementById('modalAktivitas');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }


    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            tutupAktivitas();
        }
    });


    // Jam realtime
    function updateClock() {

        const sekarang = new Date();

        const tanggal = sekarang.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            timeZone: 'Asia/Jakarta'
        });

        const waktu = sekarang.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Asia/Jakarta'
        });

        document.getElementById('tanggalSekarang').textContent = tanggal;
        document.getElementById('jamSekarang').textContent = waktu;
    }

    updateClock();
    setInterval(updateClock, 1000);

</script>

@endsection