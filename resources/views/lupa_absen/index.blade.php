@extends('layouts.utama')

@section('title', 'Lupa Absen')

@section('content')

    {{-- ================= HEADER ================= --}}
    <div class="mb-7">

        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
            Lupa Absen
        </h2>

        <p class="mt-2 text-slate-500">
            Perbaiki catatan absensi apabila kamu lupa melakukan absen.
        </p>

    </div>

    {{-- ================= FORM PERBAIKAN ================= --}}
    <div class="es-card p-6 sm:p-8 mb-7">

        {{-- HEADER CARD --}}
        <div class="flex items-start gap-4 mb-7">

            <div class="
                w-12 h-12
                rounded-2xl
                bg-purple-50
                text-purple-600
                flex items-center justify-center
                shrink-0
            ">
                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-6 h-6">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6v6l4 2" />

                    <circle cx="12"
                        cy="12"
                        r="9" />

                </svg>

            </div>


            <div>

                <h3 class="text-lg font-bold text-slate-900">
                    Perbaiki Absensi
                </h3>

                <p class="text-sm text-slate-400 mt-1">
                    Isi informasi absensi yang terlewat dengan benar.
                </p>

            </div>

        </div>


        {{-- ================= FORM ================= --}}
        <form action="#" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                {{-- TANGGAL --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        class="
                            w-full
                            rounded-xl
                            border border-slate-200
                            bg-slate-50
                            px-4 py-3
                            text-sm text-slate-700
                            outline-none
                            focus:border-purple-500
                            focus:ring-2
                            focus:ring-purple-100
                            transition
                        "
                    >

                </div>


                {{-- JENIS ABSENSI --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Jenis Absensi
                    </label>

                    <select
                        name="jenis_absen"
                        class="
                            w-full
                            rounded-xl
                            border border-slate-200
                            bg-slate-50
                            px-4 py-3
                            text-sm text-slate-700
                            outline-none
                            focus:border-purple-500
                            focus:ring-2
                            focus:ring-purple-100
                            transition
                        "
                    >

                        <option value="">
                            Pilih jenis absensi
                        </option>

                        <option value="masuk">
                            Absen Masuk
                        </option>

                        <option value="pulang">
                            Absen Pulang
                        </option>

                    </select>

                </div>


                {{-- JAM --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Jam Absensi
                    </label>

                    <input
                        type="time"
                        name="jam"
                        class="
                            w-full
                            rounded-xl
                            border border-slate-200
                            bg-slate-50
                            px-4 py-3
                            text-sm text-slate-700
                            outline-none
                            focus:border-purple-500
                            focus:ring-2
                            focus:ring-purple-100
                            transition
                        "
                    >

                </div>


                {{-- BUKTI --}}
                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Bukti / Surat
                    </label>

                    <input
                        type="file"
                        name="bukti"
                        class="
                            w-full
                            rounded-xl
                            border border-slate-200
                            bg-slate-50
                            px-4 py-2.5
                            text-sm text-slate-500

                            file:mr-4
                            file:rounded-lg
                            file:border-0
                            file:bg-purple-50
                            file:px-3
                            file:py-2
                            file:text-sm
                            file:font-medium
                            file:text-purple-600

                            hover:file:bg-purple-100
                        "
                    >
                </div>

                {{-- ALASAN --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Alasan
                    </label>

                    <textarea
                        name="alasan"
                        rows="4"
                        placeholder="Contoh: Lupa melakukan absensi masuk karena langsung mengikuti kegiatan..."
                        class="
                            w-full
                            rounded-xl
                            border border-slate-200
                            bg-slate-50
                            px-4 py-3
                            text-sm text-slate-700
                            placeholder:text-slate-400
                            outline-none
                            resize-none

                            focus:border-purple-500
                            focus:ring-2
                            focus:ring-purple-100

                            transition
                        "
                    ></textarea>

                </div>

            </div>


            {{-- ================= BUTTON ================= --}}
            <div class="
                mt-7
                pt-6
                border-t border-slate-100

                flex flex-col
                sm:flex-row
                sm:items-center
                sm:justify-between

                gap-4
            ">

                <p class="text-xs text-slate-400">
                    Pastikan tanggal dan jam absensi sudah sesuai.
                </p>


                <button
                    type="submit"
                    class="
                        inline-flex
                        items-center
                        justify-center
                        gap-2

                        px-6 py-3
                        rounded-xl

                        bg-purple-600
                        text-white

                        text-sm
                        font-bold

                        shadow-lg
                        shadow-purple-200

                        hover:bg-purple-700
                        hover:-translate-y-0.5

                        transition
                    "
                >

                    <span>
                        ✓
                    </span>

                    Simpan Perbaikan

                </button>

            </div>

        </form>

    </div>


    {{-- ================= CATATAN ABSENSI ================= --}}
    <div class="es-card p-6 sm:p-8">

        <div class="
            flex flex-col
            sm:flex-row
            sm:items-center
            sm:justify-between
            gap-3
            mb-6
        ">

            <div>

                <h3 class="text-lg font-bold text-slate-900">
                    Catatan Absensi
                </h3>

                <p class="text-sm text-slate-400 mt-1">
                    Daftar perbaikan absensi yang pernah dilakukan.
                </p>

            </div>


            <span class="
                inline-flex
                w-fit
                px-3 py-1.5
                rounded-full

                bg-slate-100
                text-slate-500

                text-xs
                font-semibold
            ">

                Riwayat

            </span>

        </div>

{{-- ================= RIWAYAT LUPA ABSEN ================= --}}
    @forelse($riwayatLupaAbsen as $item)

            <div class="
                rounded-2xl
                bg-white
                border border-slate-200
                p-5
                mb-3
            ">

        <div class="flex items-start justify-between gap-4">

            <div>

                <p class="text-sm font-semibold text-slate-800">
                    {{ $item->tanggal->translatedFormat('d F Y') }}
                </p>

                <p class="text-xs text-slate-500 mt-1">
                    {{ ucfirst($item->jenis_absen) }}
                    · {{ $item->jam }}
                </p>

            </div>

        </div>


        {{-- ALASAN --}}
        <div class="mt-4">

            <p class="text-xs font-medium text-slate-400 mb-1">
                Alasan
            </p>

            <p class="text-sm text-slate-600">
                {{ $item->alasan }}
            </p>

        </div>


        {{-- BUKTI --}}
        @if($item->bukti)

            <a href="{{ asset('storage/' . $item->bukti) }}"
               target="_blank"
               class="
                    inline-flex
                    items-center
                    mt-4
                    px-4 py-2
                    rounded-xl
                    bg-purple-50
                    text-purple-600
                    text-xs
                    font-semibold
                    hover:bg-purple-100
                    transition
               ">
                Lihat Bukti
            </a>

        @endif

    </div>
@empty

    {{-- ================= EMPTY STATE ================= --}}
    <div class="
        rounded-2xl
        bg-slate-50
        border border-dashed border-slate-200
        py-12
        text-center
    ">

        <div class="
            mx-auto
            mb-4
            w-12 h-12
            rounded-full
            bg-white
            border border-slate-200
            flex
            items-center
            justify-center
        ">

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

        <p class="text-sm font-semibold text-slate-500">
            Belum ada catatan absensi
        </p>

        <p class="text-xs text-slate-400 mt-1">
            Perbaikan absensi kamu akan muncul di sini.
        </p>

    </div>

@endforelse
@endsection