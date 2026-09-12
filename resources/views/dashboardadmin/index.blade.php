@extends('layouts.utama')

@section('content')

<div class="space-y-7">

    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

        <div>

            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Selamat Datang, Admin Easy System
            </h1>

            <p class="mt-2 text-gray-500">
                Berikut ringkasan aktivitas sistem hari ini.
            </p>

        </div>


        <div class="inline-flex w-fit items-center gap-2 rounded-full
                    border border-gray-100 bg-white px-5 py-3 shadow-sm">

            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>

            <span class="text-sm text-gray-500">
                Easy System
            </span>

        </div>

    </div>


    {{-- =====================================================
        CLOCK HERO
    ====================================================== --}}

    <div class="relative overflow-hidden rounded-[26px]
                bg-gradient-to-r from-[#8b2cff] via-[#7924e8] to-[#a238ff]
                p-7 md:p-8 text-white shadow-lg">

        {{-- Dekorasi --}}

        <div class="absolute -right-16 -top-24 h-64 w-64
                    rounded-full bg-white/10">
        </div>

        <div class="absolute -right-5 top-12 h-40 w-40
                    rounded-full border-[25px] border-white/5">
        </div>


        <div class="relative z-10 flex flex-col gap-6
                    md:flex-row md:items-center md:justify-between">

            <div>

                <div class="flex items-center gap-2 text-sm md:text-base
                            font-medium text-white/90">

                    <span id="tanggalSekarang">
                        Memuat tanggal...
                    </span>

                </div>


                <div id="jamSekarang"
                     class="mt-3 text-5xl md:text-6xl font-bold tracking-tight">

                    00:00:00

                </div>


                <p class="mt-2 text-sm md:text-base text-white/85">
                    Waktu Indonesia Barat (WIB)
                </p>

            </div>


            <div class="flex flex-col items-start gap-4 md:items-end">

                <p class="text-sm md:text-base text-white/90">
                    Selamat bekerja dan semoga harimu berjalan lancar.
                </p>

            </div>

        </div>

    </div>


{{-- =====================================================
    AKTIVITAS & STATISTIK
====================================================== --}}

<div>

    {{-- =================================================
        HEADER
    ================================================== --}}

    <div class="flex flex-col gap-1">

        <h2 class="text-xl md:text-2xl font-bold text-[#151b32]">
            Aktivitas & Statistik
        </h2>

        <p class="text-sm text-gray-400">
            Ringkasan kehadiran dan aktivitas administrasi dalam sistem.
        </p>

    </div>



{{-- ================================================= 
    AKTIVITAS HARI INI 
================================================== --}}

<div class="mt-5">

    <div class="mb-3 flex items-center gap-2">

        <span class="h-2 w-2 rounded-full bg-[#8b2cff]"></span>

        <p class="text-sm font-semibold text-[#151b32]">
            Aktivitas Hari Ini
        </p>

    </div>


    {{-- =================================================
        4 KARTU UTAMA
    ================================================== --}}

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">


        {{-- =================================================
            HADIR
        ================================================== --}}

        <button
            type="button"
            onclick="bukaAktivitas('hadir')"
            class="group w-full rounded-2xl
                   border border-gray-100
                   border-r-2 border-b-2
                   border-r-[#9ed8bb]
                   border-b-[#9ed8bb]
                   bg-white p-5 text-left
                   shadow-sm
                   transition duration-200
                   hover:-translate-y-0.5
                   hover:shadow-md
                   focus:outline-none"
        >

            <div class="flex items-center justify-between">

                <p class="text-sm font-medium text-gray-500">
                    Hadir
                </p>

                <div
                    class="flex h-10 w-10 items-center justify-center
                           rounded-xl
                           bg-emerald-50
                           text-emerald-600
                           transition
                           group-hover:scale-105"
                >

                    <span class="text-lg font-semibold">
                        ✓
                    </span>

                </div>

            </div>


            <div class="mt-3">

                <span class="text-3xl font-bold text-emerald-600">
                    {{ $hadirHariIni }}
                </span>

            </div>


            <p class="mt-1 text-xs text-gray-400">
                Sudah absen masuk
            </p>

        </button>



        {{-- =================================================
            BELUM ABSEN
        ================================================== --}}

    <button 
    type="button" 
    onclick="bukaAktivitas('belumAbsen')" 
    class="group w-full rounded-2xl 
           border border-gray-100 
           border-r-2 border-b-2 
           border-r-[#f0a3a3] 
           border-b-[#f0a3a3] 
           bg-white p-5 text-left 
           shadow-sm 
           transition duration-200 
           hover:-translate-y-0.5 
           hover:shadow-md 
           focus:outline-none" 
>

    <div class="flex items-center justify-between">

        <p class="text-sm font-medium text-gray-500">
            Belum Absen
        </p>

        <div 
            class="flex h-10 w-10 items-center justify-center 
                   rounded-xl 
                   bg-red-50 
                   text-red-500 
                   transition 
                   group-hover:scale-105"
        >

            <span class="text-lg font-semibold">
                !
            </span>

        </div>

    </div>


    <div class="mt-3">

        <span class="text-3xl font-bold text-red-500">
            {{ $belumAbsen }}
        </span>

    </div>


    <p class="mt-1 text-xs text-gray-400">
        Belum absen masuk
    </p>

</button>


        {{-- =================================================
            CUTI
        ================================================== --}}

        <button
            type="button"
            onclick="bukaAktivitas('cuti')"
            class="group w-full rounded-2xl
                   border border-gray-100
                   border-r-2 border-b-2
                   border-r-[#c9a8ff]
                   border-b-[#c9a8ff]
                   bg-white p-5 text-left
                   shadow-sm
                   transition duration-200
                   hover:-translate-y-0.5
                   hover:shadow-md
                   focus:outline-none"
        >

            <div class="flex items-center justify-between">

                <p class="text-sm font-medium text-gray-500">
                    Cuti Tercatat
                </p>

                <div
                    class="flex h-10 w-10 items-center justify-center
                           rounded-xl
                           bg-purple-50
                           text-[#8b2cff]
                           transition
                           group-hover:scale-105"
                >

                    <span class="text-sm font-bold">
                        C
                    </span>

                </div>

            </div>


            <div class="mt-3">

                <span class="text-3xl font-bold text-[#8b2cff]">
                    {{ $cutiHariIni }}
                </span>

            </div>


            <p class="mt-1 text-xs text-gray-400">
                Catatan cuti hari ini
            </p>

        </button>



        {{-- =================================================
            LEMBUR
        ================================================== --}}

        <button
            type="button"
            onclick="bukaAktivitas('lembur')"
            class="group w-full rounded-2xl
                   border border-gray-100
                   border-r-2 border-b-2
                   border-r-[#b8c2d6]
                   border-b-[#b8c2d6]
                   bg-white p-5 text-left
                   shadow-sm
                   transition duration-200
                   hover:-translate-y-0.5
                   hover:shadow-md
                   focus:outline-none"
        >

            <div class="flex items-center justify-between">

                <p class="text-sm font-medium text-gray-500">
                    Lembur Tercatat
                </p>

                <div
                    class="flex h-10 w-10 items-center justify-center
                           rounded-xl
                           bg-slate-100
                           text-slate-600
                           transition
                           group-hover:scale-105"
                >

                    <span class="text-sm font-bold">
                        L
                    </span>

                </div>

            </div>


            <div class="mt-3">

                <span class="text-3xl font-bold text-[#151b32]">
                    {{ $lemburHariIni }}
                </span>

            </div>


            <p class="mt-1 text-xs text-gray-400">
                Catatan lembur hari ini
            </p>

        </button>


    </div>

</div>


    {{-- =================================================
        PEMBATAS HALUS
    ================================================== --}}

    <div class="my-6 border-t border-gray-100"></div>



  {{-- =================================================
    4 STATISTIK PERIODE BERJALAN
================================================= --}}

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">


    {{-- =================================================
        CUTI
    ================================================== --}}

    <div
        class="group w-full rounded-2xl
               border border-gray-100
               border-r-2 border-b-2
               border-r-[#c9a8ff]
               border-b-[#c9a8ff]
               bg-white p-5
               shadow-sm
               transition duration-200
               hover:-translate-y-0.5
               hover:shadow-md"
    >

        <div class="flex items-center justify-between">

            <p class="text-sm font-medium text-gray-500">
                Cuti
            </p>

            <div
                class="flex h-10 w-10 items-center justify-center
                       rounded-xl
                       bg-purple-50
                       text-[#8b2cff]
                       transition duration-200
                       group-hover:scale-105"
            >

                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <rect
                        x="3"
                        y="4"
                        width="18"
                        height="18"
                        rx="2"
                    />

                    <path d="M16 2v4M8 2v4M3 10h18" />

                </svg>

            </div>

        </div>


        <div class="mt-3">

            <span class="text-3xl font-bold text-[#8b2cff]">
                {{ $jumlahCutiBulanIni ?? 0 }}
            </span>

        </div>


        <p class="mt-1 text-xs text-gray-400">
            Catatan cuti bulan ini
        </p>

    </div>



    {{-- =================================================
        LUPA ABSEN
    ================================================== --}}

    <div
        class="group w-full rounded-2xl
               border border-gray-100
               border-r-2 border-b-2
               border-r-[#f5c58a]
               border-b-[#f5c58a]
               bg-white p-5
               shadow-sm
               transition duration-200
               hover:-translate-y-0.5
               hover:shadow-md"
    >

        <div class="flex items-center justify-between">

            <p class="text-sm font-medium text-gray-500">
                Lupa Absen
            </p>

            <div
                class="flex h-10 w-10 items-center justify-center
                       rounded-xl
                       bg-orange-50
                       text-orange-500
                       transition duration-200
                       group-hover:scale-105"
            >

                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <circle
                        cx="12"
                        cy="8"
                        r="3.5"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 20v-1a7 7 0 0114 0v1"
                    />

                </svg>

            </div>

        </div>


        <div class="mt-3">

            <span class="text-3xl font-bold text-orange-500">
                {{ $jumlahLupaAbsenBulanIni ?? 0 }}
            </span>

        </div>


        <p class="mt-1 text-xs text-gray-400">
            Catatan periode ini
        </p>

    </div>



    {{-- =================================================
        LEMBUR
    ================================================== --}}

    <div
        class="group w-full rounded-2xl
               border border-gray-100
               border-r-2 border-b-2
               border-r-[#bfc4d4]
               border-b-[#bfc4d4]
               bg-white p-5
               shadow-sm
               transition duration-200
               hover:-translate-y-0.5
               hover:shadow-md"
    >

        <div class="flex items-center justify-between">

            <p class="text-sm font-medium text-gray-500">
                Lembur
            </p>

            <div
                class="flex h-10 w-10 items-center justify-center
                       rounded-xl
                       bg-gray-100
                       text-[#151b32]
                       transition duration-200
                       group-hover:scale-105"
            >

                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 7v5l3 2"
                    />

                </svg>

            </div>

        </div>


        <div class="mt-3">

            <span class="text-3xl font-bold text-[#151b32]">
                {{ $jumlahLemburBulanIni ?? 0 }}
            </span>

        </div>


        <p class="mt-1 text-xs text-gray-400">
            Catatan lembur bulan ini
        </p>

    </div>



    {{-- =================================================
        CUTI ALASAN PENTING
    ================================================== --}}

    <div
        class="group w-full rounded-2xl
               border border-gray-100
               border-r-2 border-b-2
               border-r-[#9fc4e8]
               border-b-[#9fc4e8]
               bg-white p-5
               shadow-sm
               transition duration-200
               hover:-translate-y-0.5
               hover:shadow-md"
    >

        <div class="flex items-center justify-between">

            <p class="text-sm font-medium text-gray-500">
                Cuti Alasan Penting
            </p>

            <div
                class="flex h-10 w-10 items-center justify-center
                       rounded-xl
                       bg-blue-50
                       text-blue-600
                       transition duration-200
                       group-hover:scale-105"
            >

                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M10.3 3.8L2.9 17a2 2 0 001.75 3h14.7a2 2 0 001.75-3L13.7 3.8a2 2 0 00-3.4 0z"
                    />

                </svg>

            </div>

        </div>


        <div class="mt-3">

            <span class="text-3xl font-bold text-blue-600">
                {{ $jumlahCutiAlasanPentingBulanIni ?? 0 }}
            </span>

        </div>


        <p class="mt-1 text-xs text-gray-400">
            Catatan periode ini
        </p>

    </div>

</div>

        {{-- =================================================
            INFORMASI PERIODE
        ================================================== --}}

        <div class="mt-5 flex flex-col gap-3 rounded-xl
                    border border-gray-100 bg-gray-50/70
                    px-4 py-3 sm:flex-row sm:items-center
                    sm:justify-between">

            <div>

                <p class="text-sm font-semibold text-[#151b32]">
                    Periode Aktivitas
                </p>

                <p class="mt-0.5 text-xs text-gray-400">
                    Statistik administrasi yang ditampilkan pada dashboard.
                </p>

            </div>


            <div class="inline-flex w-fit items-center gap-2
                        rounded-lg border border-gray-100
                        bg-white px-3 py-2">

                <span class="h-2 w-2 rounded-full bg-[#8b2cff]"></span>

                <span class="text-xs font-medium text-gray-600">
                    Bulan Berjalan
                </span>

            </div>

        </div>

    </div>


<!-- DATA TERSEMBUNYI UNTUK MODAL -->
<div class="hidden">


    {{-- DATA HADIR --}}

    <div id="dataHadir">

        @forelse($hadirPegawai as $item)

            <div class="flex items-center justify-between
                        border-b border-gray-100 py-3">

                <div>

                    <p class="text-sm font-semibold text-[#151b32]">
                        {{ $item->user?->name ?? '-' }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Sudah absen masuk
                    </p>

                </div>


                <span class="text-sm font-medium text-emerald-600">

                    {{ $item->jam_masuk
                        ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i')
                        : '-'
                    }}

                </span>

            </div>

        @empty

            <div class="py-8 text-center text-sm text-gray-400">
                Belum ada pegawai yang absen hari ini.
            </div>

        @endforelse

    </div>



    {{-- DATA BELUM ABSEN --}}

    <div id="dataBelumAbsen">

        @forelse($belumAbsenPegawai as $index => $item)

            <div class="flex items-center gap-3
                        border-b border-gray-100 py-3">

                <div class="flex h-8 w-8 shrink-0
                            items-center justify-center
                            rounded-lg bg-orange-50
                            text-sm font-semibold text-orange-500">

                    {{ $index + 1 }}

                </div>


                <p class="text-sm font-semibold text-[#151b32]">
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

            <div class="border-b border-gray-100 py-3">

                <p class="text-sm font-semibold text-[#151b32]">
                    {{ $item->user?->name ?? '-' }}
                </p>


                <p class="mt-1 text-xs text-[#8b2cff]">

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


                <p class="mt-1 text-xs text-gray-400">

                    {{ $item->tanggal_mulai
                        ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y')
                        : '-'
                    }}

                    -

                    {{ $item->tanggal_selesai
                        ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y')
                        : '-'
                    }}

                </p>

            </div>

        @empty

            <div class="py-8 text-center text-sm text-gray-400">
                Tidak ada catatan cuti hari ini.
            </div>

        @endforelse

    </div>



    {{-- DATA LEMBUR --}}

    <div id="dataLembur">

        @forelse($lemburHariIniData as $item)

            <div class="border-b border-gray-100 py-3">

                <p class="text-sm font-semibold text-[#151b32]">
                    {{ $item->user?->name ?? '-' }}
                </p>


                <p class="mt-1 text-xs text-gray-500">

                    {{ $item->jam_mulai
                        ? \Carbon\Carbon::parse($item->jam_mulai)->format('H:i')
                        : '-'
                    }}

                    -

                    {{ $item->jam_selesai
                        ? \Carbon\Carbon::parse($item->jam_selesai)->format('H:i')
                        : '-'
                    }}

                </p>


                <p class="mt-1 text-xs text-gray-400">
                    {{ $item->kegiatan ?? '-' }}
                </p>

            </div>

        @empty

            <div class="py-8 text-center text-sm text-gray-400">
                Tidak ada catatan lembur hari ini.
            </div>

        @endforelse

    </div>

</div>



{{-- =====================================================
    MODAL AKTIVITAS HARI INI
====================================================== --}}

<div
    id="modalAktivitas"
    class="fixed inset-0 z-50 hidden items-center justify-center
           bg-black/40 px-4"
    onclick="tutupAktivitas()"
>

    <div
        class="w-full max-w-lg rounded-2xl bg-white shadow-xl"
        onclick="event.stopPropagation()"
    >

        {{-- HEADER MODAL --}}

        <div class="flex items-center justify-between
                    border-b border-gray-100 px-6 py-4">

            <div>

                <h3
                    id="modalJudul"
                    class="text-lg font-bold text-[#151b32]"
                >
                    Aktivitas
                </h3>


                <p class="mt-1 text-xs text-gray-400">
                    Data aktivitas hari ini
                </p>

            </div>


            <button
                type="button"
                onclick="tutupAktivitas()"
                class="flex h-8 w-8 items-center justify-center
                       rounded-lg text-gray-400
                       focus:outline-none"
            >
                ✕
            </button>

        </div>



        {{-- ISI MODAL --}}

        <div
            id="modalIsi"
            class="max-h-[60vh] overflow-y-auto px-6 py-5"
        >
        </div>



        {{-- FOOTER MODAL --}}

        <div class="border-t border-gray-100 px-6 py-4 text-right">

            <button
                type="button"
                onclick="tutupAktivitas()"
                class="rounded-xl bg-gray-100 px-4 py-2
                       text-sm font-medium text-gray-600"
            >
                Tutup
            </button>

        </div>

    </div>

</div>



{{-- =====================================================
    SCRIPT DASHBOARD
====================================================== --}}

<script>


    /* =====================================================
       MODAL AKTIVITAS HARI INI
    ====================================================== */

    function bukaAktivitas(jenis) {

        const modal = document.getElementById('modalAktivitas');
        const judul = document.getElementById('modalJudul');
        const isi = document.getElementById('modalIsi');

        let sumber = null;


        if (jenis === 'hadir') {

            judul.textContent = 'Pegawai yang Sudah Absen';

            sumber = document.getElementById('dataHadir');

        }


        if (jenis === 'belumAbsen') {

            judul.textContent = 'Pegawai yang Belum Absen';

            sumber = document.getElementById('dataBelumAbsen');

        }


        if (jenis === 'cuti') {

            judul.textContent = 'Cuti Hari Ini';

            sumber = document.getElementById('dataCuti');

        }


        if (jenis === 'lembur') {

            judul.textContent = 'Lembur Hari Ini';

            sumber = document.getElementById('dataLembur');

        }


        if (sumber) {

            isi.innerHTML = sumber.innerHTML;

        } else {

            isi.innerHTML = `
                <div class="py-8 text-center text-sm text-gray-400">
                    Data tidak ditemukan.
                </div>
            `;

        }


        modal.classList.remove('hidden');

        modal.classList.add('flex');

    }



    function tutupAktivitas() {

        const modal = document.getElementById('modalAktivitas');

        modal.classList.add('hidden');

        modal.classList.remove('flex');

    }



    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {

            tutupAktivitas();

        }

    });



    /* =====================================================
       CLOCK
    ====================================================== */

    function updateClock() {

        const sekarang = new Date();


        const opsiTanggal = {

            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            timeZone: 'Asia/Jakarta'

        };


        const tanggal = sekarang.toLocaleDateString(
            'id-ID',
            opsiTanggal
        );


        const waktu = sekarang.toLocaleTimeString(
            'id-ID',
            {

                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta'

            }
        );


        document.getElementById('tanggalSekarang').textContent = tanggal;

        document.getElementById('jamSekarang').textContent = waktu;

    }


    updateClock();

    setInterval(updateClock, 1000);

</script>

@endsection