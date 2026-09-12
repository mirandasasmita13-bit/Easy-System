@extends('layouts.utama')

@section('title', 'Cuti')

@section('content')

{{-- ========================================================= --}}
{{-- HEADER --}}
{{-- ========================================================= --}}

<div class="mb-7">

    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
        Cuti
    </h2>

    <p class="mt-2 text-slate-500">
        Kelola pencatatan cuti kamu.
    </p>

</div>


{{-- ========================================================= --}}
{{-- PESAN SUCCESS / ERROR --}}
{{-- ========================================================= --}}

@if(session('success'))

    <div class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>

@endif


@if(session('error'))

    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ session('error') }}
    </div>

@endif


@if($errors->any())

    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">

        <p class="font-semibold mb-1">
            Terdapat kesalahan:
        </p>

        <ul class="list-disc list-inside space-y-1">

            @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif


{{-- ========================================================= --}}
{{-- RINGKASAN CUTI --}}
{{-- ========================================================= --}}

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-7">

    {{-- SISA CUTI --}}
    <div class="stat-card group">

        <div class="flex items-start justify-between">

            <div>

                <p class="text-sm font-medium text-slate-500">
                    Sisa Cuti Tahunan
                </p>

                <div class="flex items-baseline gap-2 mt-3">

                    <span class="text-3xl font-extrabold text-purple-600">
                        {{ $sisaCuti }}
                    </span>

                    <span class="text-sm text-slate-400">
                        hari
                    </span>

                </div>

            </div>


            <div class="stat-icon bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-5 h-5"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6.75 3v2.25
                           M17.25 3v2.25
                           M3.75 9h16.5
                           M5.25 5.25h13.5A1.5 1.5 0 0120.25 6.75v11.5
                           a1.5 1.5 0 01-1.5 1.5H5.25
                           a1.5 1.5 0 01-1.5-1.5V6.75
                           a1.5 1.5 0 011.5-1.5z"
                    />

                </svg>

            </div>

        </div>

        <p class="text-xs text-slate-400 mt-4">
            Jatah cuti tahun berjalan
        </p>

    </div>


    {{-- CUTI DIGUNAKAN --}}
    <div class="stat-card group">

        <div class="flex items-start justify-between">

            <div>

                <p class="text-sm font-medium text-slate-500">
                    Cuti Digunakan
                </p>

                <div class="flex items-baseline gap-2 mt-3">

                    <span class="text-3xl font-extrabold text-slate-900">
                        {{ $cutiTahunanTerpakai }}
                    </span>

                    <span class="text-sm text-slate-400">
                        hari
                    </span>

                </div>

            </div>


            <div class="stat-icon bg-pink-50 text-pink-600 group-hover:bg-pink-500 group-hover:text-white transition">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-5 h-5"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6v6l4 2
                           M21 12a9 9 0 11-18 0
                           a9 9 0 0118 0z"
                    />

                </svg>

            </div>

        </div>

        <p class="text-xs text-slate-400 mt-4">
            Total cuti tahunan yang telah digunakan
        </p>

    </div>

</div>


{{-- ========================================================= --}}
{{-- TOMBOL CATAT CUTI --}}
{{-- ========================================================= --}}

<div class="flex justify-end mb-5">

    <button
        type="button"
        onclick="toggleCutiForm()"
        class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-5 py-3 rounded-xl font-semibold shadow-sm transition"
    >

        <span class="text-lg leading-none">
            +
        </span>

        Catat Cuti

    </button>

</div>


{{-- ========================================================= --}}
{{-- FORM CUTI --}}
{{-- ========================================================= --}}

<div
    id="cutiForm"
    class="es-card p-6 sm:p-7 mb-7 hidden"
>

    <div class="mb-6">

        <h3 class="font-bold text-lg text-slate-900">
            Catat Cuti
        </h3>

        <p class="text-sm text-slate-400 mt-1">
            Isi data cuti dan unggah surat yang telah disetujui.
        </p>

    </div>


    {{-- FORM --}}
    <form
        action="{{ route('pengajuan_cuti.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">


            {{-- ================================================= --}}
            {{-- JENIS CUTI --}}
            {{-- ================================================= --}}

            <div class="sm:col-span-2">

                <label
                    for="jenis_cuti"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Jenis Cuti
                </label>

                <select
                    id="jenis_cuti"
                    name="jenis_cuti"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100"
                >

                    <option value="">
                        Pilih jenis cuti
                    </option>

                    <option
                        value="tahunan"
                        {{ old('jenis_cuti') === 'tahunan' ? 'selected' : '' }}
                    >
                        Cuti Tahunan
                    </option>

                    <option
                        value="alasan_penting"
                        {{ old('jenis_cuti') === 'alasan_penting' ? 'selected' : '' }}
                    >
                        Cuti Alasan Penting
                    </option>

                </select>

            </div>


            {{-- ================================================= --}}
            {{-- TANGGAL MULAI --}}
            {{-- ================================================= --}}

            <div>

                <label
                    for="tanggal_mulai"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Tanggal Mulai
                </label>

                <input
                    id="tanggal_mulai"
                    type="date"
                    name="tanggal_mulai"
                    value="{{ old('tanggal_mulai') }}"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100"
                >

            </div>


            {{-- ================================================= --}}
            {{-- TANGGAL SELESAI --}}
            {{-- ================================================= --}}

            <div>

                <label
                    for="tanggal_selesai"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Tanggal Selesai
                </label>

                <input
                    id="tanggal_selesai"
                    type="date"
                    name="tanggal_selesai"
                    value="{{ old('tanggal_selesai') }}"
                    required
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100"
                >

            </div>


            {{-- ================================================= --}}
            {{-- KETERANGAN --}}
            {{-- ================================================= --}}

            <div class="sm:col-span-2">

                <label
                    for="keterangan"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Keterangan
                </label>

                <textarea
                    id="keterangan"
                    name="keterangan"
                    rows="4"
                    placeholder="Tuliskan keterangan cuti..."
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100"
                >{{ old('keterangan') }}</textarea>

            </div>


            {{-- ================================================= --}}
            {{-- SURAT CUTI --}}
            {{-- ================================================= --}}

            <div class="sm:col-span-2">

                <label
                    for="surat"
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >
                    Surat Cuti
                </label>

                <label
                    for="surat"
                    class="flex flex-col items-center justify-center w-full min-h-32 rounded-xl border-2 border-dashed border-purple-200 bg-purple-50/40 cursor-pointer hover:bg-purple-50 transition"
                >

                    <div class="text-center">

                        <div class="text-2xl mb-2">
                            📄
                        </div>

                        <p class="text-sm font-semibold text-purple-600">
                            Pilih surat
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            PDF, JPG, atau PNG
                        </p>

                        <p class="text-xs text-slate-400">
                            Maksimal ukuran file 5 MB
                        </p>

                    </div>


                    <input
                        id="surat"
                        type="file"
                        name="surat"
                        required
                        class="hidden"
                        accept=".pdf,.jpg,.jpeg,.png"
                    >

                </label>


                {{-- NAMA FILE YANG DIPILIH --}}
                <p
                    id="namaFileSurat"
                    class="text-sm text-slate-500 mt-2 hidden"
                ></p>

            </div>


            {{-- ================================================= --}}
            {{-- BUTTON --}}
            {{-- ================================================= --}}

            <div class="flex justify-end gap-3 mt-6 sm:col-span-2">

                <button
                    type="button"
                    onclick="toggleCutiForm()"
                    class="px-5 py-3 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-5 py-3 rounded-xl text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 transition"
                >
                    Simpan Cuti
                </button>

            </div>

        </div>

    </form>

</div>


{{-- ========================================================= --}}
{{-- RIWAYAT CUTI --}}
{{-- ========================================================= --}}

<div class="es-card p-6 sm:p-7">

    <div class="mb-5">

        <h3 class="font-bold text-lg text-slate-900">
            Riwayat Cuti
        </h3>

        <p class="text-sm text-slate-400 mt-1">
            Riwayat pencatatan cuti kamu.
        </p>

    </div>


    {{-- DATA DARI DATABASE --}}
    <div class="space-y-3">

        @forelse($riwayatCuti as $cuti)

            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 sm:p-5">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">


                    {{-- ================================================= --}}
                    {{-- INFORMASI CUTI --}}
                    {{-- ================================================= --}}

                    <div>

                        <div class="flex flex-wrap items-center gap-2">

                            <span class="font-semibold text-slate-900">

                                {{ $cuti->tanggal_mulai->translatedFormat('d F Y') }}

                                @if(
                                    $cuti->tanggal_mulai->toDateString()
                                    !==
                                    $cuti->tanggal_selesai->toDateString()
                                )

                                    –

                                    {{ $cuti->tanggal_selesai->translatedFormat('d F Y') }}

                                @endif

                            </span>


                            {{-- JENIS CUTI --}}

                            @if($cuti->jenis_cuti === 'tahunan')

                                <span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-600 text-[11px] font-semibold">
                                    Cuti Tahunan
                                </span>

                            @elseif($cuti->jenis_cuti === 'alasan_penting')

                                <span class="px-2.5 py-1 rounded-full bg-pink-50 text-pink-600 text-[11px] font-semibold">
                                    Cuti Alasan Penting
                                </span>

                            @endif

                        </div>


                        {{-- KETERANGAN --}}

                        @if($cuti->keterangan)

                            <p class="text-sm text-slate-500 mt-2">
                                {{ $cuti->keterangan }}
                            </p>

                        @endif

                    </div>


                    {{-- ================================================= --}}
                    {{-- DETAIL --}}
                    {{-- ================================================= --}}

                    <div class="flex flex-wrap items-center gap-2 text-sm text-slate-400 shrink-0">

                        <span>
                            {{ $cuti->jumlah_hari }}
                            hari
                        </span>


                        {{-- ================================================= --}}
                        {{-- SURAT CUTI --}}
                        {{-- ================================================= --}}

                        @if($cuti->surat)

                            <span>
                                •
                            </span>


                            {{-- NAMA FILE --}}

                            <span
                                class="text-slate-500 max-w-[180px] truncate"
                                title="{{ $cuti->nama_surat ?? 'Surat' }}"
                            >
                                📄 {{ $cuti->nama_surat ?? 'Surat' }}
                            </span>


                            {{-- PREVIEW --}}

                            <button
                                type="button"
                                onclick='openPreview(
                                    @js(asset("storage/" . $cuti->surat)),
                                    @js($cuti->nama_surat ?? "Surat Cuti")
                                )'
                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-purple-600 text-white text-xs font-semibold hover:bg-purple-700 transition"
                            >
                                Preview
                            </button>


                            {{-- DOWNLOAD --}}

                            <a
                                href="{{ asset('storage/' . $cuti->surat) }}"
                                download="{{ $cuti->nama_surat ?? 'surat-cuti' }}"
                                class="inline-flex items-center px-3 py-1.5 rounded-lg border border-purple-200 bg-white text-purple-600 text-xs font-semibold hover:bg-purple-50 transition"
                            >
                                Download
                            </a>

                        @endif

                    </div>

                </div>

            </div>

        @empty

            {{-- ================================================= --}}
            {{-- EMPTY STATE --}}
            {{-- ================================================= --}}

            <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-10 text-center">

                <div class="mx-auto mb-3 w-11 h-11 rounded-full bg-white border border-slate-200 flex items-center justify-center">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.6"
                        stroke="currentColor"
                        class="w-5 h-5 text-slate-400"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />

                    </svg>

                </div>


                <p class="text-sm font-medium text-slate-500">
                    Belum ada riwayat cuti
                </p>

                <p class="text-xs text-slate-400 mt-1">
                    Data cuti yang kamu catat akan muncul di sini.
                </p>

            </div>

        @endforelse

    </div>

</div>


{{-- ========================================================= --}}
{{-- MODAL PREVIEW SURAT --}}
{{-- ========================================================= --}}

<div
    id="previewModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
>

    <div
        class="relative w-full max-w-5xl h-[90vh] bg-white rounded-2xl shadow-xl overflow-hidden"
    >

        {{-- HEADER MODAL --}}

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">

            <div class="min-w-0">

                <h3
                    id="previewTitle"
                    class="font-bold text-slate-900 truncate"
                >
                    Preview Surat
                </h3>

            </div>


            <button
                type="button"
                onclick="closePreview()"
                class="ml-4 shrink-0 w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition"
            >
                ✕
            </button>

        </div>


        {{-- ISI PREVIEW --}}

        <div
            id="previewContent"
            class="w-full h-[calc(90vh-73px)] bg-slate-100"
        ></div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- SCRIPT --}}
{{-- ========================================================= --}}

<script>

    function toggleCutiForm() {

        const form = document.getElementById('cutiForm');

        form.classList.toggle('hidden');

    }


    // Menampilkan nama file setelah user memilih surat

    const suratInput = document.getElementById('surat');

    if (suratInput) {

        suratInput.addEventListener('change', function () {

            const namaFile = document.getElementById('namaFileSurat');

            if (this.files.length > 0) {

                namaFile.textContent = '📄 ' + this.files[0].name;

                namaFile.classList.remove('hidden');

            } else {

                namaFile.textContent = '';

                namaFile.classList.add('hidden');

            }

        });

    }


    // Membuka preview surat

    function openPreview(url, namaFile) {

        const modal = document.getElementById('previewModal');

        const content = document.getElementById('previewContent');

        const title = document.getElementById('previewTitle');


        title.textContent = namaFile;


        const extension = url
            .split('?')[0]
            .split('.')
            .pop()
            .toLowerCase();


        // Jika PDF

        if (extension === 'pdf') {

            content.innerHTML = `
                <iframe
                    src="${url}"
                    class="w-full h-full border-0"
                    title="${namaFile}"
                ></iframe>
            `;

        }

        // Jika JPG / JPEG / PNG

        else {

            content.innerHTML = `
                <div class="w-full h-full flex items-center justify-center p-6 overflow-auto">

                    <img
                        src="${url}"
                        alt="${namaFile}"
                        class="max-w-full max-h-full object-contain rounded-lg shadow-sm"
                    >

                </div>
            `;

        }


        modal.classList.remove('hidden');

        modal.classList.add('flex');

        document.body.classList.add('overflow-hidden');

    }


    // Menutup preview

    function closePreview() {

        const modal = document.getElementById('previewModal');

        const content = document.getElementById('previewContent');


        modal.classList.add('hidden');

        modal.classList.remove('flex');

        content.innerHTML = '';

        document.body.classList.remove('overflow-hidden');

    }


    // Klik area luar modal untuk menutup

    document.getElementById('previewModal')?.addEventListener('click', function (event) {

        if (event.target === this) {

            closePreview();

        }

    });


    // Tombol ESC untuk menutup modal

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {

            closePreview();

        }

    });

</script>

@endsection