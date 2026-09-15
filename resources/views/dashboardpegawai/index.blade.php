@extends('layouts.utama')

@section('title', 'Dashboard')

@section('content')

{{-- ================= HEADER ================= --}}
<div class="mb-7">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

        <div>

            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Selamat Datang, {{ auth()->user()->name }}
            </h2>

        </div>

        <div class="hidden lg:flex items-center gap-2
                    px-4 py-2 rounded-full
                    bg-white border border-slate-200
                    text-sm text-slate-500 shadow-sm">

            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>

            Easy System

        </div>

    </div>
</div>


{{-- ================= HERO CLOCK ================= --}}
<div class="dashboard-hero mb-7 overflow-hidden">

    {{-- dekorasi --}}
    <div class="absolute -right-20 -top-28
                w-80 h-80
                rounded-full
                bg-white/10">
    </div>

    <div class="absolute -left-24 -bottom-32
                w-80 h-80
                rounded-full
                bg-white/5">
    </div>

    <div class="relative z-10 p-6 sm:p-8 lg:p-9">

        <div class="grid lg:grid-cols-[1fr_auto]
                    items-center gap-7">

            {{-- ================= WAKTU ================= --}}
            <div>

                <div class="flex items-center gap-2 mb-3">

                    <span class="w-2 h-2 rounded-full bg-emerald-300"></span>

                    <p id="tanggal"
                       class="text-purple-100 text-sm sm:text-base font-medium">
                    </p>

                </div>

                <div id="jam"
                     class="text-5xl sm:text-6xl lg:text-7xl
                            font-extrabold
                            text-white
                            tracking-tight">

                    00:00:00

                </div>

                <p class="mt-3 text-purple-100 text-sm sm:text-base">
                    Waktu Indonesia Barat (WIB)
                </p>

            </div>


            {{-- ================= FITUR PEGAWAI ================= --}}
            <div class="lg:text-right">

                <p class="text-purple-100 text-sm mb-3">
                    Ada yang ingin kamu kelola hari ini?
                </p>

                <a href="{{ route('cuti_tambahan.index') }}"
                   class="inline-flex items-center gap-3
                          bg-white
                          text-purple-700
                          px-6 py-3.5
                          rounded-xl
                          font-bold
                          shadow-lg
                          hover:-translate-y-0.5
                          hover:bg-purple-50
                          transition">

                    <span class="text-lg">
                        ✓
                    </span>

                    Cuti Tambahan

                </a>

            </div>

        </div>

    </div>

</div>

{{-- ================= AKTIVITAS TERBARU ================= --}}
<div class="es-card p-6 sm:p-7 mb-5">

    <div class="flex items-center justify-between mb-5">

        <div>

            <h3 class="font-bold text-lg text-slate-900">
                Aktivitas Terbaru
            </h3>

            <p class="text-sm text-slate-400 mt-1">
                Riwayat aktivitas administrasi kamu.
            </p>

        </div>


        <span class="hidden sm:inline-flex
                     px-3 py-1.5
                     rounded-full
                     bg-purple-50
                     text-purple-600
                     text-xs
                     font-semibold">

            Terbaru

        </span>

    </div>


    {{-- EMPTY STATE --}}
    <div class="rounded-2xl
                bg-slate-50
                border border-dashed border-slate-200
                py-9
                text-center">

        <div class="mx-auto mb-3
                    w-11 h-11
                    rounded-full
                    bg-white
                    border border-slate-200
                    flex items-center justify-center">

            <svg xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke-width="1.6"
                 stroke="currentColor"
                 class="w-5 h-5 text-slate-400">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 6v6l4 2" />

                <circle cx="12"
                        cy="12"
                        r="9" />

            </svg>

        </div>


        <p class="text-sm font-medium text-slate-500">
            Belum ada aktivitas
        </p>

        <p class="text-xs text-slate-400 mt-1">
            Aktivitas kamu akan muncul di sini.
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