@extends('layouts.utama')

@section('title', 'Rekap Lembur')

@section('content')

{{-- =========================================================
    HEADER
========================================================= --}}

<div class="mb-6">

    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
        Rekap Lembur
    </h2>

    <p class="mt-2 text-slate-500">
        Rekap kegiatan lembur pegawai yang tercatat dalam sistem.
    </p>

</div>

{{-- =========================================================
    RINGKASAN + FILTER
========================================================= --}}

<div class="grid grid-cols-1 lg:grid-cols-[0.7fr_1.3fr] gap-6 mb-6">

    {{-- JUMLAH LEMBUR --}}

    <div class="es-card px-7 py-7 flex flex-col justify-center">

        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
            Jumlah Lembur
        </p>

        <div class="flex items-end gap-2 mt-3">

            <span class="text-4xl font-extrabold text-purple-600">
                {{ $jumlahLembur }}
            </span>

            <span class="text-sm text-slate-400 mb-1">
                kali
            </span>

        </div>

    </div>


    {{-- TAMPILKAN REKAP --}}

    <div class="es-card px-7 py-6">

        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-4">
            Tampilkan Rekap
        </p>

        <form
            method="GET"
            action="{{ url('/rekap-lembur') }}"
        >

            <div class="flex flex-col sm:flex-row gap-3">

                {{-- BULAN --}}

                <select
                    name="bulan"
                    class="
                        w-full
                        rounded-xl
                        border border-slate-200
                        bg-white
                        px-4 py-3
                        text-sm
                        text-slate-700
                        outline-none
                        shadow-sm
                        focus:border-purple-500
                        focus:ring-2
                        focus:ring-purple-100
                    "
                >

                    @foreach(range(1, 12) as $bulanOption)

                        <option
                            value="{{ $bulanOption }}"
                            @selected($bulan == $bulanOption)
                        >
                            {{ \Carbon\Carbon::create()->month($bulanOption)->translatedFormat('F') }}
                        </option>

                    @endforeach

                </select>


                {{-- TAHUN --}}

                <select
                    name="tahun"
                    class="
                        w-full
                        sm:w-[130px]
                        rounded-xl
                        border border-slate-200
                        bg-white
                        px-4 py-3
                        text-sm
                        text-slate-700
                        outline-none
                        shadow-sm
                        focus:border-purple-500
                        focus:ring-2
                        focus:ring-purple-100
                    "
                >

                    @foreach(range(now()->year - 2, now()->year + 1) as $tahunOption)

                        <option
                            value="{{ $tahunOption }}"
                            @selected($tahun == $tahunOption)
                        >
                            {{ $tahunOption }}
                        </option>

                    @endforeach

                </select>


                {{-- TAMPILKAN --}}

                <button
                    type="submit"
                    class="
                        w-full
                        sm:w-[150px]
                        inline-flex
                        items-center
                        justify-center
                        rounded-xl
                        bg-purple-600
                        px-5 py-3
                        text-sm
                        font-bold
                        text-white
                        shadow-md
                        shadow-purple-200
                        hover:bg-purple-700
                        transition
                    "
                >
                    Tampilkan
                </button>

            </div>

        </form>

    </div>

</div>

{{-- =========================================================
    PERIODE + EXPORT
========================================================= --}}

<div class="es-card px-5 py-4 sm:px-6 sm:py-5 mb-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>

            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                Periode Rekap
            </p>

            <h3 class="mt-1 text-lg sm:text-xl font-extrabold text-slate-900">
                {{ $tanggalAwal->translatedFormat('F Y') }}
            </h3>

        </div>


        {{-- EXPORT --}}

        <div class="flex flex-wrap gap-2">

            <a
                href="{{ route('rekaplembur.export.excel', [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'user_id' => $userId
                ]) }}"
                class="
                    inline-flex
                    items-center
                    justify-center
                    rounded-xl
                    border border-emerald-200
                    bg-emerald-50
                    px-4 py-2.5
                    text-sm
                    font-bold
                    text-emerald-700
                    hover:bg-emerald-100
                    transition
                "
            >
                Export Excel
            </a>


            <a
                href="{{ route('rekaplembur.export.pdf', [
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'user_id' => $userId
                ]) }}"
                class="
                    inline-flex
                    items-center
                    justify-center
                    rounded-xl
                    border border-red-200
                    bg-red-50
                    px-4 py-2.5
                    text-sm
                    font-bold
                    text-red-700
                    hover:bg-red-100
                    transition
                "
            >
                Export PDF
            </a>

        </div>

    </div>

</div>


{{-- =========================================================
    CATATAN LEMBUR
========================================================= --}}

<div class="es-card overflow-hidden">

    <div class="px-5 sm:px-6 py-5 border-b border-slate-100">

        <h3 class="text-lg font-bold text-slate-900">
            Catatan Lembur
        </h3>

        <p class="text-sm text-slate-400 mt-1">
            Daftar lembur yang tercatat dalam sistem.
        </p>

    </div>


    <div class="overflow-x-auto">

        <table class="min-w-full">

            <thead>

                <tr class="bg-slate-50 border-y border-slate-100">

                    <th class="px-4 py-4 text-center text-xs font-bold text-slate-500">
                        No
                    </th>

                    <th class="px-4 py-4 text-left text-xs font-bold text-slate-500">
                        Nama
                    </th>

                    <th class="px-4 py-4 text-left text-xs font-bold text-slate-500">
                        Tanggal
                    </th>

                    <th class="px-4 py-4 text-left text-xs font-bold text-slate-500">
                        Jam
                    </th>

                    <th class="px-4 py-4 text-left text-xs font-bold text-slate-500">
                        Kegiatan
                    </th>

                    <th class="px-4 py-4 text-center text-xs font-bold text-slate-500">
                        Bukti
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-slate-100">

                @forelse($dataLembur as $index => $lembur)

                    <tr class="hover:bg-slate-50/60 transition">

                        {{-- NO --}}

                        <td class="px-4 py-5 text-center">

                            <span class="text-sm font-semibold text-slate-500">
                                {{ $index + 1 }}
                            </span>

                        </td>


                        {{-- NAMA --}}

                        <td class="px-4 py-5">

                            <div class="flex items-center gap-3">

                                <div class="
                                    w-9 h-9
                                    rounded-xl
                                    bg-indigo-50
                                    text-indigo-600
                                    flex
                                    items-center
                                    justify-center
                                    text-xs
                                    font-extrabold
                                ">

                                    {{ strtoupper(
                                        substr(
                                            $lembur->user?->name ?? '?',
                                            0,
                                            2
                                        )
                                    ) }}

                                </div>


                                <div>

                                    <p class="
                                        text-sm
                                        font-bold
                                        text-slate-800
                                    ">

                                        {{ $lembur->user?->name ?? 'Tidak diketahui' }}

                                    </p>

                                    <p class="
                                        text-[11px]
                                        text-slate-400
                                    ">

                                        {{ $lembur->user?->profil?->jabatan ?? 'PPNPN' }}

                                    </p>

                                </div>

                            </div>

                        </td>


                        {{-- TANGGAL --}}

                        <td class="px-4 py-5 whitespace-nowrap">

                            <p class="
                                text-sm
                                font-bold
                                text-slate-800
                            ">

                                {{ \Carbon\Carbon::parse($lembur->tanggal)->translatedFormat('d F Y') }}

                            </p>

                            <p class="
                                text-xs
                                text-slate-400
                                mt-0.5
                            ">

                                {{ \Carbon\Carbon::parse($lembur->tanggal)->translatedFormat('l') }}

                            </p>

                        </td>


                        {{-- JAM --}}

                        <td class="px-4 py-5 whitespace-nowrap">

                            <span class="
                                text-sm
                                font-semibold
                                text-slate-700
                            ">

                                {{ $lembur->jam_mulai ?? '--:--' }}
                                –
                                {{ $lembur->jam_selesai ?? '--:--' }}

                            </span>

                        </td>


                        {{-- KEGIATAN --}}

                        <td class="px-4 py-5 min-w-[220px]">

                            <p class="
                                text-sm
                                text-slate-600
                            ">

                                {{ $lembur->kegiatan ?? '-' }}

                            </p>

                            @if($lembur->keterangan)

                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $lembur->keterangan }}
                                </p>

                            @endif

                        </td>


                        {{-- BUKTI --}}

                        <td class="px-4 py-5 text-center">

                            @if($lembur->foto)

                                <button
                                    type="button"
                                    onclick="openBuktiModal('{{ asset('storage/' . $lembur->foto) }}')"
                                    class="
                                        inline-flex
                                        items-center
                                        justify-center
                                        rounded-lg
                                        bg-purple-600
                                        px-3 py-1.5
                                        text-xs
                                        font-semibold
                                        text-white
                                        hover:bg-purple-700
                                        transition
                                    "
                                >
                                    Lihat
                                </button>

                            @else

                                <span class="text-xs text-slate-300">
                                    —
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="px-6 py-16 text-center"
                        >

                            <p class="
                                text-sm
                                font-semibold
                                text-slate-500
                            ">
                                Belum ada data lembur
                            </p>

                            <p class="
                                text-xs
                                text-slate-400
                                mt-1
                            ">
                                Data lembur akan muncul setelah tercatat.
                            </p>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- FOOTER --}}

    <div class="
        px-5 sm:px-6
        py-4
        border-t
        border-slate-100
        bg-slate-50/50
    ">

        <p class="text-xs text-slate-400">

            Rekap lembur ditampilkan berdasarkan periode dan PPNPN
            yang dipilih oleh admin.

        </p>

    </div>

</div>


{{-- =========================================================
    MODAL BUKTI
========================================================= --}}

<div
    id="buktiModal"
    class="
        fixed
        inset-0
        z-[100]
        hidden
        items-center
        justify-center
        bg-black/60
        px-4
    "
>

    <div
        class="
            relative
            w-full
            max-w-3xl
            rounded-2xl
            bg-white
            shadow-2xl
            overflow-hidden
        "
    >

        {{-- HEADER MODAL --}}

        <div class="
            flex
            items-center
            justify-between
            px-5
            py-4
            border-b
            border-slate-100
        ">

            <div>

                <h3 class="font-bold text-slate-900">
                    Bukti Lembur
                </h3>

                <p class="text-xs text-slate-400 mt-0.5">
                    Dokumentasi kegiatan lembur
                </p>

            </div>


            <button
                type="button"
                onclick="closeBuktiModal()"
                class="
                    w-9 h-9
                    rounded-lg
                    flex
                    items-center
                    justify-center
                    text-slate-400
                    hover:bg-slate-100
                    hover:text-slate-700
                    transition
                "
            >
                ✕
            </button>

        </div>


        {{-- GAMBAR --}}

        <div class="p-5 bg-slate-50">

            <img
                id="buktiPreview"
                src=""
                alt="Bukti lembur"
                class="
                    w-full
                    max-h-[65vh]
                    object-contain
                    rounded-xl
                    bg-white
                "
            >

        </div>


        {{-- FOOTER MODAL --}}

        <div class="
            flex
            justify-end
            gap-2
            px-5
            py-4
            border-t
            border-slate-100
        ">

            <button
                type="button"
                onclick="closeBuktiModal()"
                class="
                    rounded-xl
                    border border-slate-200
                    bg-white
                    px-4 py-2.5
                    text-sm
                    font-semibold
                    text-slate-600
                    hover:bg-slate-50
                    transition
                "
            >
                Tutup
            </button>

            <a
                id="buktiDownload"
                href="#"
                download
                class="
                    rounded-xl
                    bg-purple-600
                    px-4 py-2.5
                    text-sm
                    font-bold
                    text-white
                    hover:bg-purple-700
                    transition
                "
            >
                Download
            </a>

        </div>

    </div>

</div>


@push('scripts')

<script>

    function openBuktiModal(url) {

        const modal = document.getElementById('buktiModal');
        const image = document.getElementById('buktiPreview');
        const download = document.getElementById('buktiDownload');

        image.src = url;
        download.href = url;

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    function closeBuktiModal() {

        const modal = document.getElementById('buktiModal');
        const image = document.getElementById('buktiPreview');

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        image.src = '';

    }


    document.getElementById('buktiModal')?.addEventListener('click', function(event) {

        if (event.target === this) {
            closeBuktiModal();
        }

    });

</script>

@endpush

@endsection