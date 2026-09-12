@extends('layouts.utama')

@section('title', 'Data PPNPN')

@section('content')

<div class="space-y-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Data PPNPN
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Daftar PPNPN yang terdaftar dalam sistem Easy System.
        </p>
    </div>


    {{-- =========================================================
        TOTAL PPNPN
    ========================================================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-sm text-gray-500">
                    Total PPNPN
                </p>

                <p class="mt-1 text-3xl font-bold text-gray-800">
                    {{ $ppnpn->count() }}
                </p>
            </div>

            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">

                <svg
                    class="w-6 h-6 text-purple-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 10-6 0"
                    />
                </svg>

            </div>

        </div>

    </div>


    {{-- =========================================================
        DAFTAR PPNPN
    ========================================================== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- HEADER TABEL --}}
        <div class="px-6 py-5 border-b border-gray-100">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Daftar PPNPN
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Data pengguna dengan kategori PPNPN.
                    </p>
                </div>


                {{-- SEARCH --}}
                <div class="relative w-full lg:w-72">

                    <input
                        type="text"
                        id="searchPpnpn"
                        placeholder="Cari nama, email, atau NIK..."
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 pl-10 text-sm text-gray-700 focus:border-purple-400 focus:ring-2 focus:ring-purple-100 focus:outline-none"
                    >

                    <svg
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1010.5 3a7.5 7.5 0 006.15 13.65z"
                        />
                    </svg>

                </div>

            </div>

        </div>


        {{-- =====================================================
            TABLE
        ====================================================== --}}
        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b border-gray-100">

                    <tr class="text-left text-gray-500">

                        <th class="px-6 py-4 font-semibold w-16">
                            No
                        </th>

                        <th class="px-6 py-4 font-semibold">
                            PPNPN
                        </th>

                        <th class="px-6 py-4 font-semibold">
                            NIK
                        </th>

                        <th class="px-6 py-4 font-semibold">
                            Email
                        </th>

                        <th class="px-6 py-4 font-semibold text-center w-28">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody
                    id="ppnpnTable"
                    class="divide-y divide-gray-100"
                >

                    @forelse($ppnpn as $index => $item)

                        <tr
                            class="ppnpn-row hover:bg-gray-50/70"
                            data-search="{{ strtolower(
                                $item->name . ' ' .
                                $item->email . ' ' .
                                ($item->profil?->nik ?? '')
                            ) }}"
                        >

                            {{-- NO --}}
                            <td class="px-6 py-5 text-gray-500">
                                {{ $index + 1 }}
                            </td>


                            {{-- NAMA + FOTO --}}
                            <td class="px-6 py-5">

                                <div class="flex items-center gap-3">

                                    @if($item->profil?->foto)

                                        <img
                                            src="{{ asset('storage/' . $item->profil->foto) }}"
                                            alt="Foto {{ $item->name }}"
                                            class="w-10 h-10 rounded-full object-cover border border-gray-200"
                                        >

                                    @else

                                        <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-semibold">
                                            {{ strtoupper(substr($item->name, 0, 1)) }}
                                        </div>

                                    @endif

                                    <div>

                                        <p class="font-semibold text-gray-800">
                                            {{ $item->name }}
                                        </p>

                                        <p class="text-xs text-gray-400 mt-0.5">
                                            PPNPN
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- NIK --}}
                            <td class="px-6 py-5 text-gray-600">
                                {{ $item->profil?->nik ?? '-' }}
                            </td>


                            {{-- EMAIL --}}
                            <td class="px-6 py-5 text-gray-600">
                                {{ $item->email }}
                            </td>


                            {{-- AKSI --}}
                            <td class="px-6 py-5 text-center">

                                <button
                                    type="button"
                                    onclick="openPpnpnModal(
                                        @js($item->name),
                                        @js($item->email),
                                        @js($item->profil?->nik ?? '-'),
                                        @js($item->profil?->foto ? asset('storage/' . $item->profil->foto) : null)
                                    )"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-purple-50 text-purple-700 text-sm font-medium hover:bg-purple-100 transition"
                                >
                                    Lihat
                                </button>

                            </td>

                        </tr>

                    @empty

                        {{-- EMPTY STATE --}}
                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-14 text-center"
                            >

                                <div class="text-gray-400">

                                    <svg
                                        class="w-12 h-12 mx-auto mb-3"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 10-6 0"
                                        />
                                    </svg>

                                    <p class="font-medium text-gray-500">
                                        Belum ada data PPNPN
                                    </p>

                                    <p class="text-sm mt-1">
                                        PPNPN yang melakukan registrasi akan muncul di sini.
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



{{-- =========================================================
    MODAL DETAIL PPNPN
========================================================== --}}
<div
    id="ppnpnModal"
    class="fixed inset-0 z-50 hidden"
>

    {{-- BACKDROP --}}
    <div
        class="absolute inset-0 bg-black/40"
        onclick="closePpnpnModal()"
    ></div>


    {{-- MODAL --}}
    <div class="relative flex min-h-full items-center justify-center p-4">

        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">

                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Detail PPNPN
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Informasi pengguna
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closePpnpnModal()"
                    class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

            </div>


            {{-- CONTENT --}}
            <div class="px-6 py-6">

                {{-- FOTO --}}
                <div class="flex justify-center">

                    <img
                        id="modalPhoto"
                        src=""
                        alt="Foto PPNPN"
                        class="hidden w-24 h-24 rounded-full object-cover border border-gray-200"
                    >

                    <div
                        id="modalInitial"
                        class="w-24 h-24 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-3xl font-semibold"
                    >
                    </div>

                </div>


                {{-- NAMA --}}
                <div class="text-center mt-4">

                    <h4
                        id="modalName"
                        class="text-xl font-bold text-gray-800"
                    >
                        -
                    </h4>

                    <span class="inline-flex mt-2 px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold">
                        PPNPN
                    </span>

                </div>


                {{-- INFORMASI --}}
                <div class="mt-6 space-y-4">

                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                            NIK
                        </p>

                        <p
                            id="modalNik"
                            class="mt-1 text-sm font-medium text-gray-800"
                        >
                            -
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">
                            Email
                        </p>

                        <p
                            id="modalEmail"
                            class="mt-1 text-sm font-medium text-gray-800 break-all"
                        >
                            -
                        </p>
                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">

                <button
                    type="button"
                    onclick="closePpnpnModal()"
                    class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-100 transition"
                >
                    Tutup
                </button>

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
    JAVASCRIPT
========================================================== --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchPpnpn');
    const rows = document.querySelectorAll('.ppnpn-row');

    if (searchInput) {

        searchInput.addEventListener('input', function () {

            const keyword = this.value.toLowerCase().trim();

            rows.forEach(row => {

                const searchText = row.dataset.search || '';

                if (searchText.includes(keyword)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }

            });

        });

    }

});


/* =========================================================
   BUKA MODAL
========================================================= */

function openPpnpnModal(name, email, nik, foto) {

    const modal = document.getElementById('ppnpnModal');

    const modalName = document.getElementById('modalName');
    const modalEmail = document.getElementById('modalEmail');
    const modalNik = document.getElementById('modalNik');
    const modalPhoto = document.getElementById('modalPhoto');
    const modalInitial = document.getElementById('modalInitial');


    modalName.textContent = name;
    modalEmail.textContent = email;
    modalNik.textContent = nik;


    if (foto) {

        modalPhoto.src = foto;

        modalPhoto.classList.remove('hidden');
        modalInitial.classList.add('hidden');

    } else {

        modalInitial.textContent = name
            .trim()
            .charAt(0)
            .toUpperCase();

        modalInitial.classList.remove('hidden');
        modalPhoto.classList.add('hidden');

    }


    modal.classList.remove('hidden');

    document.body.classList.add('overflow-hidden');

}


/* =========================================================
   TUTUP MODAL
========================================================= */

function closePpnpnModal() {

    const modal = document.getElementById('ppnpnModal');

    modal.classList.add('hidden');

    document.body.classList.remove('overflow-hidden');

}


/* =========================================================
   ESCAPE
========================================================= */

document.addEventListener('keydown', function (event) {

    if (event.key === 'Escape') {
        closePpnpnModal();
    }

});

</script>

@endsection