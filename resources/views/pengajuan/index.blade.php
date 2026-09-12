@extends('layouts.utama')

@section('title', 'Pengajuan')

@section('content')

{{-- =====================================================
    HEADER
===================================================== --}}
<div class="mb-7">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

        <div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Pengajuan
            </h2>

            <p class="mt-2 text-sm sm:text-base text-slate-500">
                Kelola dan periksa pengajuan administrasi PPNPN.
            </p>
        </div>

        {{-- JUMLAH PENDING --}}
        <div class="inline-flex items-center gap-2
                    px-4 py-2.5
                    rounded-xl
                    bg-red-50
                    border border-red-100
                    text-red-600
                    text-sm font-semibold">

            <span class="w-2 h-2 rounded-full bg-red-500"></span>

            3 pengajuan menunggu
        </div>

    </div>

</div>


{{-- =====================================================
    RINGKASAN
===================================================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-7">

    {{-- SEMUA --}}
    <div class="stat-card">

        <div class="flex items-start justify-between">

            <div>
                <p class="text-sm font-medium text-slate-500">
                    Semua Pengajuan
                </p>

                <p class="text-3xl font-extrabold text-slate-900 mt-3">
                    5
                </p>
            </div>

            <div class="stat-icon bg-purple-50 text-purple-600">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.8"
                     stroke="currentColor"
                     class="w-5 h-5">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M9 5h6
                             M9 3h6
                             a1 1 0 011 1v1
                             h1.5A1.5 1.5 0 0119 6.5v13
                             A1.5 1.5 0 0117.5 21h-11
                             A1.5 1.5 0 015 19.5v-13
                             A1.5 1.5 0 016.5 5H8V4
                             a1 1 0 011-1z" />

                </svg>

            </div>

        </div>

        <p class="text-xs text-slate-400 mt-4">
            Seluruh pengajuan
        </p>

    </div>


    {{-- LUPA ABSEN --}}
    <div class="stat-card">

        <div class="flex items-start justify-between">

            <div>
                <p class="text-sm font-medium text-slate-500">
                    Lupa Absen
                </p>

                <p class="text-3xl font-extrabold text-red-500 mt-3">
                    2
                </p>
            </div>

            <div class="stat-icon bg-red-50 text-red-500">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.8"
                     stroke="currentColor"
                     class="w-5 h-5">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v4
                             m0 4h.01
                             M10.29 3.86L2.82 17
                             a2 2 0 001.74 3h14.88
                             a2 2 0 001.74-3L13.71 3.86
                             a2 2 0 00-3.42 0z" />

                </svg>

            </div>

        </div>

        <p class="text-xs text-slate-400 mt-4">
            Menunggu pemeriksaan
        </p>

    </div>


    {{-- LEMBUR --}}
    <div class="stat-card">

        <div class="flex items-start justify-between">

            <div>
                <p class="text-sm font-medium text-slate-500">
                    Lembur
                </p>

                <p class="text-3xl font-extrabold text-amber-500 mt-3">
                    1
                </p>
            </div>

            <div class="stat-icon bg-amber-50 text-amber-500">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.8"
                     stroke="currentColor"
                     class="w-5 h-5">

                    <circle cx="12"
                            cy="12"
                            r="8.5" />

                    <path stroke-linecap="round"
                          d="M12 7v5l3 2" />

                </svg>

            </div>

        </div>

        <p class="text-xs text-slate-400 mt-4">
            Menunggu pemeriksaan
        </p>

    </div>


    {{-- SURAT --}}
    <div class="stat-card">

        <div class="flex items-start justify-between">

            <div>
                <p class="text-sm font-medium text-slate-500">
                    Surat Lainnya
                </p>

                <p class="text-3xl font-extrabold text-emerald-500 mt-3">
                    0
                </p>
            </div>

            <div class="stat-icon bg-emerald-50 text-emerald-500">

                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.8"
                     stroke="currentColor"
                     class="w-5 h-5">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M4 5.5A2.5 2.5 0 016.5 3h11A2.5 2.5 0 0120 5.5v13
                             a2.5 2.5 0 01-2.5 2.5h-11A2.5 2.5 0 014 18.5v-13z" />

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M8 8h8
                             M8 12h8
                             M8 16h5" />

                </svg>

            </div>

        </div>

        <p class="text-xs text-slate-400 mt-4">
            Menunggu pemeriksaan
        </p>

    </div>

</div>


{{-- =====================================================
    DAFTAR PENGAJUAN
===================================================== --}}
<div class="es-card p-6 sm:p-7">

    {{-- HEADER --}}
    <div class="flex flex-col lg:flex-row
                lg:items-center
                lg:justify-between
                gap-4
                mb-6">

        <div>

            <h3 class="font-bold text-lg text-slate-900">
                Daftar Pengajuan
            </h3>

            <p class="text-sm text-slate-400 mt-1">
                Periksa pengajuan yang masuk dari PPNPN.
            </p>

        </div>


        {{-- FILTER --}}
        <div class="flex flex-col sm:flex-row gap-3">

            <select
                class="px-4 py-2.5
                       rounded-xl
                       border border-slate-200
                       bg-white
                       text-sm text-slate-600
                       focus:outline-none
                       focus:ring-2
                       focus:ring-purple-200">

                <option>Semua Status</option>
                <option>Menunggu Approval</option>
                <option>Disetujui</option>
                <option>Ditolak</option>

            </select>

        </div>

    </div>


    {{-- =================================================
        TAB
    ================================================= --}}
    <div class="flex items-center gap-2
                border-b border-slate-100
                mb-5
                overflow-x-auto">

        <button
            type="button"
            onclick="showTab('semua', this)"
            class="tab-button active-tab
                   px-4 py-3
                   text-sm font-semibold
                   whitespace-nowrap
                   border-b-2 border-purple-600
                   text-purple-600">

            Semua

        </button>


        <button
            type="button"
            onclick="showTab('lupa', this)"
            class="tab-button
                   px-4 py-3
                   text-sm font-semibold
                   whitespace-nowrap
                   border-b-2 border-transparent
                   text-slate-400
                   hover:text-purple-600">

            Lupa Absen

            <span class="ml-1.5
                         px-2 py-0.5
                         rounded-full
                         bg-red-50
                         text-red-500
                         text-xs">

                2

            </span>

        </button>


        <button
            type="button"
            onclick="showTab('lembur', this)"
            class="tab-button
                   px-4 py-3
                   text-sm font-semibold
                   whitespace-nowrap
                   border-b-2 border-transparent
                   text-slate-400
                   hover:text-purple-600">

            Lembur

            <span class="ml-1.5
                         px-2 py-0.5
                         rounded-full
                         bg-amber-50
                         text-amber-500
                         text-xs">

                1

            </span>

        </button>


        <button
            type="button"
            onclick="showTab('surat', this)"
            class="tab-button
                   px-4 py-3
                   text-sm font-semibold
                   whitespace-nowrap
                   border-b-2 border-transparent
                   text-slate-400
                   hover:text-purple-600">

            Surat Lainnya

        </button>

    </div>


    {{-- =================================================
        DAFTAR
    ================================================= --}}
    <div class="space-y-3">


        {{-- ================= LUPA ABSEN ================= --}}
        <div class="pengajuan-item lupa rounded-2xl
                    border border-slate-100
                    bg-slate-50/50
                    p-4 sm:p-5">

            <div class="flex flex-col lg:flex-row
                        lg:items-center
                        gap-4">

                {{-- ICON --}}
                <div class="w-11 h-11
                            rounded-xl
                            bg-red-50
                            text-red-500
                            flex items-center justify-center
                            flex-shrink-0">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.8"
                         stroke="currentColor"
                         class="w-5 h-5">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 9v4
                                 m0 4h.01
                                 M10.29 3.86L2.82 17
                                 a2 2 0 001.74 3h14.88
                                 a2 2 0 001.74-3L13.71 3.86
                                 a2 2 0 00-3.42 0z" />

                    </svg>

                </div>


                {{-- INFORMASI --}}
                <div class="flex-1 min-w-0">

                    <div class="flex flex-wrap items-center gap-2">

                        <h4 class="font-bold text-slate-800">
                            Lupa Absen
                        </h4>

                        <span class="px-2.5 py-1
                                     rounded-full
                                     bg-red-50
                                     text-red-500
                                     text-xs font-semibold">

                            Menunggu Approval

                        </span>

                    </div>

                    <p class="text-sm text-slate-500 mt-1">
                        PPNPN Testing
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Perbaikan absensi · 10 September 2026
                    </p>

                </div>


                {{-- WAKTU --}}
                <div class="lg:text-right">

                    <p class="text-xs text-slate-400">
                        Jam perbaikan
                    </p>

                    <p class="font-bold text-slate-700 mt-1">
                        07:30
                    </p>

                </div>


                {{-- BUTTON --}}
                <div>

                    <button
                        type="button"
                        class="w-full lg:w-auto
                               px-4 py-2.5
                               rounded-xl
                               bg-purple-600
                               text-white
                               text-sm
                               font-semibold
                               hover:bg-purple-700
                               transition">

                        Periksa

                    </button>

                </div>

            </div>

        </div>


        {{-- ================= LUPA ABSEN 2 ================= --}}
        <div class="pengajuan-item lupa rounded-2xl
                    border border-slate-100
                    bg-slate-50/50
                    p-4 sm:p-5">

            <div class="flex flex-col lg:flex-row
                        lg:items-center
                        gap-4">

                <div class="w-11 h-11
                            rounded-xl
                            bg-red-50
                            text-red-500
                            flex items-center justify-center
                            flex-shrink-0">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.8"
                         stroke="currentColor"
                         class="w-5 h-5">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 9v4
                                 m0 4h.01
                                 M10.29 3.86L2.82 17
                                 a2 2 0 001.74 3h14.88
                                 a2 2 0 001.74-3L13.71 3.86
                                 a2 2 0 00-3.42 0z" />

                    </svg>

                </div>


                <div class="flex-1 min-w-0">

                    <div class="flex flex-wrap items-center gap-2">

                        <h4 class="font-bold text-slate-800">
                            Lupa Absen
                        </h4>

                        <span class="px-2.5 py-1
                                     rounded-full
                                     bg-red-50
                                     text-red-500
                                     text-xs font-semibold">

                            Menunggu Approval

                        </span>

                    </div>

                    <p class="text-sm text-slate-500 mt-1">
                        PPNPN Testing
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Perbaikan absensi · 9 September 2026
                    </p>

                </div>


                <div class="lg:text-right">

                    <p class="text-xs text-slate-400">
                        Jam perbaikan
                    </p>

                    <p class="font-bold text-slate-700 mt-1">
                        08:00
                    </p>

                </div>


                <div>

                    <button
                        type="button"
                        class="w-full lg:w-auto
                               px-4 py-2.5
                               rounded-xl
                               bg-purple-600
                               text-white
                               text-sm
                               font-semibold
                               hover:bg-purple-700
                               transition">

                        Periksa

                    </button>

                </div>

            </div>

        </div>


        {{-- ================= LEMBUR ================= --}}
        <div class="pengajuan-item lembur rounded-2xl
                    border border-slate-100
                    bg-slate-50/50
                    p-4 sm:p-5">

            <div class="flex flex-col lg:flex-row
                        lg:items-center
                        gap-4">

                <div class="w-11 h-11
                            rounded-xl
                            bg-amber-50
                            text-amber-500
                            flex items-center justify-center
                            flex-shrink-0">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.8"
                         stroke="currentColor"
                         class="w-5 h-5">

                        <circle cx="12"
                                cy="12"
                                r="8.5" />

                        <path stroke-linecap="round"
                              d="M12 7v5l3 2" />

                    </svg>

                </div>


                <div class="flex-1 min-w-0">

                    <div class="flex flex-wrap items-center gap-2">

                        <h4 class="font-bold text-slate-800">
                            Lembur
                        </h4>

                        <span class="px-2.5 py-1
                                     rounded-full
                                     bg-amber-50
                                     text-amber-600
                                     text-xs font-semibold">

                            Menunggu Approval

                        </span>

                    </div>

                    <p class="text-sm text-slate-500 mt-1">
                        PPNPN Testing
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Pengajuan lembur · 9 September 2026
                    </p>

                </div>


                <div class="lg:text-right">

                    <p class="text-xs text-slate-400">
                        Waktu lembur
                    </p>

                    <p class="font-bold text-slate-700 mt-1">
                        17:00 - 20:00
                    </p>

                </div>


                <div>

                    <button
                        type="button"
                        class="w-full lg:w-auto
                               px-4 py-2.5
                               rounded-xl
                               bg-purple-600
                               text-white
                               text-sm
                               font-semibold
                               hover:bg-purple-700
                               transition">

                        Periksa

                    </button>

                </div>

            </div>

        </div>


        {{-- ================= SURAT ================= --}}
        <div class="pengajuan-item surat rounded-2xl
                    border border-slate-100
                    bg-slate-50/50
                    p-4 sm:p-5">

            <div class="flex flex-col lg:flex-row
                        lg:items-center
                        gap-4">

                <div class="w-11 h-11
                            rounded-xl
                            bg-purple-50
                            text-purple-600
                            flex items-center justify-center
                            flex-shrink-0">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.8"
                         stroke="currentColor"
                         class="w-5 h-5">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M6 3.75h8.25L19.5 9v11.25
                                 A1.75 1.75 0 0117.75 22h-11
                                 A1.75 1.75 0 015 20.25V5.5
                                 A1.75 1.75 0 016.75 3.75z" />

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M14 3.75V9h5.25" />

                    </svg>

                </div>


                <div class="flex-1 min-w-0">

                    <div class="flex flex-wrap items-center gap-2">

                        <h4 class="font-bold text-slate-800">
                            Surat Lainnya
                        </h4>

                        <span class="px-2.5 py-1
                                     rounded-full
                                     bg-emerald-50
                                     text-emerald-600
                                     text-xs font-semibold">

                            Disetujui

                        </span>

                    </div>

                    <p class="text-sm text-slate-500 mt-1">
                        PPNPN Testing
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Pengajuan surat · 8 September 2026
                    </p>

                </div>


                <div class="lg:text-right">

                    <p class="text-xs text-slate-400">
                        Jenis surat
                    </p>

                    <p class="font-bold text-slate-700 mt-1">
                        Surat Keterangan
                    </p>

                </div>


                <div>

                    <button
                        type="button"
                        class="w-full lg:w-auto
                               px-4 py-2.5
                               rounded-xl
                               border border-slate-200
                               bg-white
                               text-slate-600
                               text-sm
                               font-semibold
                               hover:bg-slate-50
                               transition">

                        Lihat

                    </button>

                </div>

            </div>

        </div>


        {{-- ================= EMPTY ================= --}}
        <div id="emptyState"
             class="hidden
                    rounded-2xl
                    bg-slate-50
                    border border-dashed border-slate-200
                    py-10
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
                Belum ada pengajuan
            </p>

            <p class="text-xs text-slate-400 mt-1">
                Pengajuan akan muncul di sini.
            </p>

        </div>

    </div>

</div>


{{-- =====================================================
    TAB JAVASCRIPT
===================================================== --}}
<script>

    function showTab(category, button) {

        const items = document.querySelectorAll('.pengajuan-item');
        const buttons = document.querySelectorAll('.tab-button');

        let visibleCount = 0;


        // Reset tombol
        buttons.forEach(btn => {

            btn.classList.remove(
                'border-purple-600',
                'text-purple-600'
            );

            btn.classList.add(
                'border-transparent',
                'text-slate-400'
            );

        });


        // Aktifkan tombol
        button.classList.remove(
            'border-transparent',
            'text-slate-400'
        );

        button.classList.add(
            'border-purple-600',
            'text-purple-600'
        );


        // Filter pengajuan
        items.forEach(item => {

            if (category === 'semua' || item.classList.contains(category)) {

                item.classList.remove('hidden');

                visibleCount++;

            } else {

                item.classList.add('hidden');

            }

        });


        // Empty state
        const emptyState = document.getElementById('emptyState');

        if (visibleCount === 0) {

            emptyState.classList.remove('hidden');

        } else {

            emptyState.classList.add('hidden');

        }

    }

</script>

@endsection