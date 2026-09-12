@extends('layouts.utama')

@section('title', 'Laporan')

@section('content')

{{-- =====================================================
     HEADER
===================================================== --}}

<div class="mb-4">
    <div>
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
            Laporan
        </h2>

        <p class="mt-2 text-slate-500">
            Pantau dan lihat detail aktivitas administrasi pegawai.
        </p>
    </div>
</div>

<form method="GET" action="{{ route('laporan') }}" class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">

    {{-- Bulan --}}
    <div>
        <label for="bulan" class="mb-2 block text-sm font-medium text-gray-700">
            Bulan
        </label>

        <select
            name="bulan"
            id="bulan"
            class="w-full rounded-xl border border-purple-100 bg-purple-50/60 px-4 py-3 text-sm text-slate-700 focus:border-purple-300 focus:ring-purple-200"
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
    </div>


    {{-- Tahun --}}
    <div>
        <label for="tahun" class="mb-2 block text-sm font-medium text-gray-700">
            Tahun
        </label>

        <select
            name="tahun"
            id="tahun"
            class="w-full rounded-xl border border-purple-100 bg-purple-50/60 px-4 py-3 text-sm text-slate-700 focus:border-purple-300 focus:ring-purple-200"
        >
            @for($i = now()->year - 2; $i <= now()->year + 1; $i++)
                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor
        </select>
    </div>


    {{-- PPNPN --}}
    <div>
        <label for="ppnpn" class="mb-2 block text-sm font-medium text-gray-700">
            PPNPN
        </label>

        <select
            name="ppnpn"
            id="ppnpn"
            class="w-full rounded-xl border border-purple-100 bg-purple-50/60 px-4 py-3 text-sm text-slate-700 focus:border-purple-300 focus:ring-purple-200"
            >
            <option value="">Semua PPNPN</option>

            @foreach($ppnpn as $p)
                <option
                    value="{{ $p->id }}"
                    {{ (string) $ppnpnId === (string) $p->id ? 'selected' : '' }}
                >
                    {{ $p->name }}
                </option>
            @endforeach
        </select>
    </div>


    {{-- Tombol --}}
    <div class="flex items-end">
        <button
            type="submit"
            class="inline-flex w-full items-center justify-center rounded-xl bg-purple-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-purple-700"
        >
            Terapkan Filter
        </button>
    </div>

</form>
    
            
{{-- =========================================================
     PERIODE LAPORAN
========================================================== --}}

<div class="es-card mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

    <div class="flex items-center gap-4">

        {{-- ICON --}}
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-50">
            <svg
                class="h-6 w-6 text-purple-600"
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


        {{-- INFORMASI --}}
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                Periode Laporan
            </p>

            <h2 class="mt-1 text-lg font-bold text-slate-900">
                {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                {{ \Carbon\Carbon::parse($tanggalAwal)->translatedFormat('d F Y') }}
                -
                {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d F Y') }}
            </p>
        </div>

    </div>
</div>

{{-- =========================================================
     AKTIVITAS ADMINISTRASI
========================================================== --}}

<div class="es-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

    {{-- HEADER --}}
    <div class="border-b border-slate-200 px-5 pt-5">

        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-900">
                Aktivitas Administrasi
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Daftar aktivitas administrasi pegawai pada periode terpilih.
            </p>
        </div>


        {{-- TABS --}}
        <div class="mb-5 flex flex-wrap gap-2">

            {{-- SEMUA --}}
            <button
                type="button"
                data-tab="semua"
                class="laporan-tab active-tab inline-flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <rect x="3" y="3" width="7" height="7" rx="1.5" />
                    <rect x="14" y="3" width="7" height="7" rx="1.5" />
                    <rect x="3" y="14" width="7" height="7" rx="1.5" />
                    <rect x="14" y="14" width="7" height="7" rx="1.5" />
                </svg>

                Semua
            </button>


            {{-- CUTI --}}
            <button
                type="button"
                data-tab="cuti"
                class="laporan-tab inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <path d="M16 2v4M8 2v4M3 10h18" />
                </svg>

                Cuti
            </button>


            {{-- LUPA ABSEN --}}
            <button
                type="button"
                data-tab="lupa-absen"
                class="laporan-tab inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="8" r="3.5" />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 20v-1a7 7 0 0114 0v1"
                    />
                </svg>

                Lupa Absen
            </button>


            {{-- LEMBUR --}}
            <button
                type="button"
                data-tab="lembur"
                class="laporan-tab inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <circle cx="12" cy="12" r="9" />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 7v5l3 2"
                    />
                </svg>

                Lembur
            </button>

        </div>
    </div>


    {{-- =====================================================
         TABLE
    ====================================================== --}}

    <div class="px-5 pb-5">

        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">

            <div class="overflow-x-auto">

                <table class="w-full min-w-[950px]">

                    {{-- TABLE HEADER --}}
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-600 to-purple-500">

                            {{-- TANGGAL --}}
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-white">
                                <span class="inline-flex items-center gap-2">
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <rect x="3" y="4" width="18" height="18" rx="2" />
                                        <path d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>

                                    Tanggal
                                </span>
                            </th>


                            {{-- PEGAWAI --}}
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-white">
                                <span class="inline-flex items-center gap-2">
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <circle cx="12" cy="8" r="3.5" />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 20v-1a7 7 0 0114 0v1"
                                        />
                                    </svg>

                                    Pegawai
                                </span>
                            </th>


                            {{-- AKTIVITAS --}}
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-white">
                                <span class="inline-flex items-center gap-2">
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M20.59 13.41 12 22l-9-9V3h10l7.59 9.41a2 2 0 010 2.82z"
                                        />

                                        <circle cx="7.5" cy="7.5" r="1" />
                                    </svg>

                                    Aktivitas
                                </span>
                            </th>


                            {{-- KETERANGAN --}}
                            <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-white">
                                <span class="inline-flex items-center gap-2">
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <rect x="3" y="4" width="18" height="16" rx="2" />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M7 9h10M7 13h6"
                                        />
                                    </svg>

                                    Keterangan
                                </span>
                            </th>


                            {{-- BUKTI --}}
                            <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wide text-white">
                                <span class="inline-flex items-center justify-center gap-2">
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M4 5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13 3v5h5"
                                        />
                                    </svg>

                                    Bukti
                                </span>
                            </th>

                        </tr>
                    </thead>


                    {{-- TABLE BODY --}}
                    <tbody id="laporanTableBody">

                        @forelse($semuaAktivitas as $aktivitas)

                            @php

                                $jenis = strtolower(
                                    $aktivitas['jenis']
                                    ?? $aktivitas['tipe']
                                    ?? $aktivitas['aktivitas']
                                    ?? ''
                                );


                                /*
                                 * KATEGORI TAB
                                 */
                                if (str_contains($jenis, 'lupa')) {

                                    $kategori = 'lupa-absen';

                                } elseif (str_contains($jenis, 'lembur')) {

                                    $kategori = 'lembur';

                                } else {

                                    $kategori = 'cuti';
                                }


                                $namaPegawai =
                                    $aktivitas['pegawai']
                                    ?? '-';


                                $tanggalAktivitas =
                                    $aktivitas['tanggal']
                                    ?? null;


                                $keterangan =
                                    $aktivitas['keterangan']
                                    ?? '-';


                                $labelAktivitas =
                                    $aktivitas['aktivitas']
                                    ?? '-';


                                /*
                                 * DATA BUKTI
                                 *
                                 * Controller nantinya mengirim:
                                 * bukti
                                 * nama_bukti
                                 * tipe_bukti
                                 */
                                $bukti =
                                    $aktivitas['bukti']
                                    ?? null;

                                $namaBukti =
                                    $aktivitas['nama_bukti']
                                    ?? 'Bukti';


                                $tipeBukti =
                                    $aktivitas['tipe_bukti']
                                    ?? null;


                                /*
                                 * WARNA BADGE
                                 */
                                $labelLower = strtolower($labelAktivitas);

                                if (str_contains($labelLower, 'tambahan')) {

                                    $badgeWarna = 'pink';

                                } elseif ($kategori === 'cuti') {

                                    $badgeWarna = 'amber';

                                } elseif ($kategori === 'lupa-absen') {

                                    $badgeWarna = 'blue';

                                } else {

                                    $badgeWarna = 'purple';
                                }

                            @endphp


                            <tr
                                data-category="{{ $kategori }}"
                                class="laporan-row border-b border-slate-100 transition hover:bg-purple-50/40"
                            >

                                {{-- TANGGAL --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                                    <span class="inline-flex items-center gap-2">

                                        <svg
                                            class="h-4 w-4 text-slate-400"
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

                                        @if($tanggalAktivitas)

                                            {{ \Carbon\Carbon::parse($tanggalAktivitas)->translatedFormat('d M Y') }}

                                        @else

                                            -

                                        @endif

                                    </span>

                                </td>


                                {{-- PEGAWAI --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-purple-50 text-xs font-bold text-purple-600">

                                            {{ collect(explode(' ', trim($namaPegawai)))
                                                ->filter()
                                                ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                                ->take(2)
                                                ->implode('') }}

                                        </div>


                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-semibold text-slate-800">
                                                {{ $namaPegawai }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- AKTIVITAS --}}
                                <td class="px-5 py-4">

                                    @if($badgeWarna === 'amber')

                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">

                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                                <path d="M16 2v4M8 2v4M3 10h18" />
                                            </svg>

                                            {{ $labelAktivitas }}

                                        </span>

                                    @elseif($badgeWarna === 'pink')

                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-pink-50 px-3 py-1.5 text-xs font-semibold text-pink-600">

                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                                <path d="M16 2v4M8 2v4M3 10h18" />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 14v4M10 16h4"
                                                />
                                            </svg>

                                            {{ $labelAktivitas }}

                                        </span>

                                    @elseif($badgeWarna === 'blue')

                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">

                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <circle cx="12" cy="8" r="3.5" />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M5 20v-1a7 7 0 0114 0v1"
                                                />
                                            </svg>

                                            {{ $labelAktivitas }}

                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-50 px-3 py-1.5 text-xs font-semibold text-purple-700">

                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <circle cx="12" cy="12" r="9" />

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 7v5l3 2"
                                                />
                                            </svg>

                                            {{ $labelAktivitas }}

                                        </span>

                                    @endif

                                </td>


                                {{-- KETERANGAN --}}
                                <td class="max-w-[320px] px-5 py-4 text-sm text-slate-500">

                                    <p class="truncate">
                                        {{ $keterangan }}
                                    </p>

                                </td>


                                {{-- BUKTI --}}
                                <td class="px-5 py-4 text-center">

                                    @if($bukti)

                                        <button
                                            type="button"
                                            class="preview-bukti inline-flex items-center gap-2 rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-xs font-semibold text-purple-700 transition hover:bg-purple-100"
                                            data-bukti="{{ $bukti }}"
                                            data-nama="{{ $namaBukti }}"
                                            data-tipe="{{ $tipeBukti }}"
                                        >

                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6-9.75-6-9.75-6z"
                                                />

                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.5"
                                                />
                                            </svg>

                                            Preview

                                        </button>

                                    @else

                                        <span class="text-xs text-slate-400">
                                            Tidak ada
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr id="emptyRow">

                                <td
                                    colspan="5"
                                    class="px-5 py-14 text-center"
                                >

                                    <div class="mx-auto flex max-w-sm flex-col items-center">

                                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100">

                                            <svg
                                                class="h-6 w-6 text-slate-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"
                                                />
                                            </svg>

                                        </div>

                                        <p class="mt-3 text-sm font-semibold text-slate-700">
                                            Belum ada aktivitas
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            Tidak ada aktivitas administrasi pada periode ini.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>

    </div>

</div>


{{-- =========================================================
     MODAL PREVIEW BUKTI
========================================================== --}}

<div
    id="previewModal"
    class="fixed inset-0 z-[999] hidden items-center justify-center bg-slate-900/60 p-4"
>

    <div
        class="relative flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
    >

        {{-- MODAL HEADER --}}
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

            <div class="min-w-0">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Preview Bukti
                </p>

                <h3
                    id="previewTitle"
                    class="mt-1 truncate text-base font-bold text-slate-900"
                >
                    Bukti
                </h3>

            </div>


            <button
                type="button"
                id="closePreview"
                class="ml-4 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18 18 6M6 6l12 12"
                    />
                </svg>
            </button>

        </div>


        {{-- MODAL CONTENT --}}
        <div
            id="previewContent"
            class="flex min-h-[300px] flex-1 items-center justify-center overflow-auto bg-slate-50 p-4"
        >
        </div>

    </div>

</div>


{{-- =========================================================
     JAVASCRIPT
========================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | TAB FILTER
    |--------------------------------------------------------------------------
    */

    const tabs = document.querySelectorAll('.laporan-tab');
    const rows = document.querySelectorAll('.laporan-row');

    const ACTIVE_CLASSES = [
        'bg-purple-600',
        'text-white',
        'shadow-sm',
        'border-transparent'
    ];

    const INACTIVE_CLASSES = [
        'border',
        'border-slate-200',
        'bg-white',
        'text-slate-600',
        'hover:bg-slate-50'
    ];


    tabs.forEach(tab => {

        tab.addEventListener('click', function () {

            const selectedTab = this.dataset.tab;


            /*
            |--------------------------------------------------------------------------
            | RESET TAB
            |--------------------------------------------------------------------------
            */

            tabs.forEach(item => {

                item.classList.remove(
                    'active-tab',
                    ...ACTIVE_CLASSES
                );

                item.classList.add(
                    ...INACTIVE_CLASSES
                );

            });


            /*
            |--------------------------------------------------------------------------
            | ACTIVE TAB
            |--------------------------------------------------------------------------
            */

            this.classList.remove(
                ...INACTIVE_CLASSES
            );

            this.classList.add(
                'active-tab',
                ...ACTIVE_CLASSES
            );


            /*
            |--------------------------------------------------------------------------
            | FILTER TABLE
            |--------------------------------------------------------------------------
            */

            rows.forEach(row => {

                if (selectedTab === 'semua') {

                    row.style.display = '';

                    return;
                }


                const category = row.dataset.category;

                row.style.display =
                    category === selectedTab
                        ? ''
                        : 'none';

            });

        });

    });



    /*
    |--------------------------------------------------------------------------
    | MODAL PREVIEW
    |--------------------------------------------------------------------------
    */

    const modal = document.getElementById('previewModal');
    const closeButton = document.getElementById('closePreview');
    const previewContent = document.getElementById('previewContent');
    const previewTitle = document.getElementById('previewTitle');


    /*
    |--------------------------------------------------------------------------
    | BUKA PREVIEW
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.preview-bukti').forEach(button => {

        button.addEventListener('click', function () {

            const bukti = this.dataset.bukti;
            const nama = this.dataset.nama || 'Bukti';
            const tipe = (this.dataset.tipe || '').toLowerCase();


            previewTitle.textContent = nama;


            /*
            |--------------------------------------------------------------------------
            | URL FILE
            |--------------------------------------------------------------------------
            */

            let fileUrl = bukti;

            if (
                !bukti.startsWith('http://') &&
                !bukti.startsWith('https://') &&
                !bukti.startsWith('/')
            ) {
                fileUrl = '/storage/' + bukti;
            }


            /*
            |--------------------------------------------------------------------------
            | PREVIEW IMAGE
            |--------------------------------------------------------------------------
            */

            const isImage =
                tipe.includes('image') ||
                /\.(jpg|jpeg|png|gif|webp)$/i.test(bukti);


            const isPdf =
                tipe.includes('pdf') ||
                /\.pdf$/i.test(bukti);


            if (isImage) {

                previewContent.innerHTML = `
                    <div class="flex h-full w-full items-center justify-center">
                        <img
                            src="${fileUrl}"
                            alt="${nama}"
                            class="max-h-[70vh] max-w-full rounded-xl object-contain shadow-sm"
                        >
                    </div>
                `;

            }


            /*
            |--------------------------------------------------------------------------
            | PREVIEW PDF
            |--------------------------------------------------------------------------
            */

            else if (isPdf) {

                previewContent.innerHTML = `
                    <iframe
                        src="${fileUrl}"
                        class="h-[70vh] w-full rounded-xl border border-slate-200 bg-white"
                        title="${nama}"
                    ></iframe>
                `;

            }


            /*
            |--------------------------------------------------------------------------
            | FILE LAIN
            |--------------------------------------------------------------------------
            */

            else {

                previewContent.innerHTML = `
                    <div class="flex flex-col items-center justify-center text-center">

                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-purple-50">

                            <svg
                                class="h-7 w-7 text-purple-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M13 3v5h5"
                                />
                            </svg>

                        </div>

                        <p class="mt-4 text-sm font-semibold text-slate-700">
                            File tidak dapat dipreview
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Format file ini belum mendukung preview langsung.
                        </p>

                    </div>
                `;

            }


            /*
            |--------------------------------------------------------------------------
            | SHOW MODAL
            |--------------------------------------------------------------------------
            */

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.body.classList.add('overflow-hidden');
        });
    });

    // TUTUP MODAL
    function closePreviewModal() {

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        previewContent.innerHTML = '';
        document.body.classList.remove('overflow-hidden');
    }
    closeButton.addEventListener(
        'click',
        closePreviewModal
    );

    // KLIK BACKDROP
    modal.addEventListener('click', function (event) {

        if (event.target === modal) {
            closePreviewModal();
        }

    });

    // ESC
    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closePreviewModal();
        }
    });
});

</script>
@endsection