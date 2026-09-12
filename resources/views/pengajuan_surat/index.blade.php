@extends('layouts.utama')

@section('title', 'Surat Lainnya')

@section('content')

    {{-- ===================================================== --}}
    {{-- PESAN SUCCESS / ERROR --}}
    {{-- ===================================================== --}}

    @if(session('success'))

        <div class="mb-5 rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">

            {{ session('success') }}

        </div>

    @endif


    @if(session('error'))

        <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">

            {{ session('error') }}

        </div>

    @endif


    @if($errors->any())

        <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-600">

            {{ $errors->first() }}

        </div>

    @endif



    {{-- ===================================================== --}}
    {{-- HEADER --}}
    {{-- ===================================================== --}}

    <div class="mb-7">

        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">

            Surat Lainnya

        </h2>

        <p class="mt-2 text-slate-500">

            Kelola kebutuhan administrasi surat kamu di sini.

        </p>

    </div>



    {{-- ===================================================== --}}
    {{-- FORM --}}
    {{-- ===================================================== --}}

    <div class="es-card p-6 sm:p-8 mb-7">

        <div class="flex items-start gap-4 mb-7">

            <div class="
                w-12 h-12
                rounded-2xl
                bg-purple-50
                text-purple-600
                flex items-center justify-center
                shrink-0
            ">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-6 h-6"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A2.625 2.625 0 0112 5.625v-1.5A3.375 3.375 0 009.375.75H5.25A2.25 2.25 0 003 3v18a2.25 2.25 0 002.25 2.25h9.75A4.5 4.5 0 0019.5 18.75v-4.5z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9.75h4.5M12 13.5h4.5M12 17.25h2.25"
                    />

                </svg>

            </div>


            <div>

                <h3 class="text-lg font-bold text-slate-900">

                    Buat Surat

                </h3>

                <p class="text-sm text-slate-400 mt-1">

                    Isi informasi surat yang kamu perlukan.

                </p>

            </div>

        </div>



        {{-- ================================================= --}}
        {{-- FORM SURAT --}}
        {{-- ================================================= --}}

        <form
            action="{{ route('pengajuan_surat.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf


            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                {{-- ================================================= --}}
                {{-- JENIS SURAT --}}
                {{-- ================================================= --}}

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">

                        Jenis Surat

                    </label>


                    <select
                        name="jenis_surat"
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

                            Pilih jenis surat

                        </option>


                        <option
                            value="surat_keterangan"
                            {{ old('jenis_surat') === 'surat_keterangan' ? 'selected' : '' }}
                        >

                            Surat Izin Sakit

                        </option>


                        <option
                            value="surat_lainnya"
                            {{ old('jenis_surat') === 'surat_lainnya' ? 'selected' : '' }}
                        >

                            Surat Lainnya

                        </option>

                    </select>

                </div>



                {{-- ================================================= --}}
                {{-- TANGGAL --}}
                {{-- ================================================= --}}

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">

                        Tanggal

                    </label>


                    <input
                        type="date"
                        name="tanggal"
                        value="{{ old('tanggal') }}"
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



                {{-- ================================================= --}}
                {{-- KEPERLUAN --}}
                {{-- ================================================= --}}

                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-700 mb-2">

                        Keperluan

                    </label>


                    <textarea
                        name="keperluan"
                        rows="4"
                        placeholder="Jelaskan keperluan surat..."
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
                    >{{ old('keperluan') }}</textarea>

                </div>



                {{-- ================================================= --}}
                {{-- DOKUMEN --}}
                {{-- ================================================= --}}

                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-slate-700 mb-2">

                        Dokumen Pendukung

                        <span class="font-normal text-slate-400">

                            (opsional)

                        </span>

                    </label>


                    <input
                        type="file"
                        name="dokumen"
                        id="dokumen"
                        accept=".pdf,.jpg,.jpeg,.png"
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


                    {{-- NAMA FILE YANG DIPILIH --}}

                    <p
                        id="namaFileDokumen"
                        class="hidden text-xs text-slate-400 mt-2"
                    ></p>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- BUTTON --}}
            {{-- ================================================= --}}

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

                    Pastikan informasi surat sudah benar.

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

                    <span>✓</span>

                    Simpan Surat

                </button>

            </div>

        </form>

    </div>





    {{-- ===================================================== --}}
    {{-- CATATAN SURAT --}}
    {{-- ===================================================== --}}

    <div class="es-card p-6 sm:p-8">

        <div class="mb-6">

            <h3 class="text-lg font-bold text-slate-900">

                Catatan Surat

            </h3>

            <p class="text-sm text-slate-400 mt-1">

                Daftar surat yang pernah kamu buat.

            </p>

        </div>



        {{-- ================================================= --}}
        {{-- RIWAYAT SURAT --}}
        {{-- ================================================= --}}

        @forelse($riwayatSurat as $surat)

            <div
                class="
                    rounded-2xl
                    bg-slate-50
                    border border-slate-100
                    p-5
                    mb-3
                "
            >

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">


                    {{-- ================================================= --}}
                    {{-- INFORMASI SURAT --}}
                    {{-- ================================================= --}}

                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-2">


                            {{-- TANGGAL --}}

                            <span class="text-sm font-semibold text-slate-700">

                                {{ $surat->tanggal?->format('d/m/Y') }}

                            </span>



                            {{-- JENIS SURAT --}}

                            <span
                                class="
                                    px-2.5 py-1
                                    rounded-full
                                    bg-purple-50
                                    text-purple-600
                                    text-[11px]
                                    font-semibold
                                "
                            >

                                @if($surat->jenis_surat === 'surat_keterangan')

                                    Surat Izin Sakit

                                @elseif($surat->jenis_surat === 'surat_tugas')

                                    Surat Tugas

                                @elseif($surat->jenis_surat === 'surat_lainnya')

                                    Surat Lainnya

                                @else

                                    {{ ucwords(str_replace('_', ' ', $surat->jenis_surat)) }}

                                @endif

                            </span>

                        </div>



                        {{-- KEPERLUAN --}}

                        <p class="text-sm text-slate-500 mt-2">

                            {{ $surat->keperluan }}

                        </p>



                        {{-- NAMA DOKUMEN --}}

                        @if($surat->dokumen)

                            <div class="flex items-center gap-2 mt-3">

                                <span>

                                    📄

                                </span>


                                <span
                                    class="text-xs text-slate-500 max-w-[250px] truncate"
                                    title="{{ $surat->nama_dokumen ?? 'Dokumen' }}"
                                >

                                    {{ $surat->nama_dokumen ?? 'Dokumen' }}

                                </span>

                            </div>

                        @endif

                    </div>



                    {{-- ================================================= --}}
                    {{-- TOMBOL --}}
                    {{-- ================================================= --}}

                    @if($surat->dokumen)

                        <div class="flex flex-wrap items-center gap-2 shrink-0">


                            {{-- ================================================= --}}
                            {{-- PREVIEW --}}
                            {{-- ================================================= --}}

                            <button
                                type="button"
                                onclick='openPreview(
                                    @js(asset("storage/" . $surat->dokumen)),
                                    @js($surat->nama_dokumen ?? "Dokumen Surat")
                                )'
                                class="
                                    inline-flex
                                    items-center
                                    px-3 py-1.5
                                    rounded-lg
                                    bg-purple-600
                                    text-white
                                    text-xs
                                    font-semibold
                                    hover:bg-purple-700
                                    transition
                                "
                            >

                                Preview

                            </button>



                            {{-- ================================================= --}}
                            {{-- DOWNLOAD --}}
                            {{-- ================================================= --}}

                            <a
                                href="{{ asset('storage/' . $surat->dokumen) }}"
                                download="{{ $surat->nama_dokumen ?? 'dokumen-surat' }}"
                                class="
                                    inline-flex
                                    items-center
                                    px-3 py-1.5
                                    rounded-lg
                                    border
                                    border-purple-200
                                    bg-white
                                    text-purple-600
                                    text-xs
                                    font-semibold
                                    hover:bg-purple-50
                                    transition
                                "
                            >

                                Download

                            </a>

                        </div>

                    @endif

                </div>

            </div>

        @empty

            {{-- ================================================= --}}
            {{-- TAMPILAN JIKA BELUM ADA DATA --}}
            {{-- ================================================= --}}

            <div class="
                rounded-2xl
                bg-slate-50
                border border-dashed border-slate-200
                py-12
                text-center
            ">

                <p class="text-sm font-semibold text-slate-500">

                    Belum ada catatan surat

                </p>

                <p class="text-xs text-slate-400 mt-1">

                    Surat yang kamu simpan akan muncul di sini.

                </p>

            </div>

        @endforelse

    </div>





    {{-- ===================================================== --}}
    {{-- MODAL PREVIEW --}}
    {{-- ===================================================== --}}

    <div
        id="previewModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4"
    >

        <div
            class="
                relative
                w-full
                max-w-5xl
                h-[90vh]
                bg-white
                rounded-2xl
                shadow-xl
                overflow-hidden
            "
        >

            {{-- ================================================= --}}
            {{-- HEADER MODAL --}}
            {{-- ================================================= --}}

            <div
                class="
                    flex
                    items-center
                    justify-between
                    px-5 py-4
                    border-b border-slate-100
                "
            >

                <div class="min-w-0">

                    <h3
                        id="previewTitle"
                        class="font-bold text-slate-900 truncate"
                    >

                        Preview Dokumen

                    </h3>

                </div>


                <button
                    type="button"
                    onclick="closePreview()"
                    class="
                        ml-4
                        shrink-0
                        w-9 h-9
                        rounded-lg
                        bg-slate-100
                        text-slate-500
                        hover:bg-slate-200
                        hover:text-slate-700
                        transition
                    "
                >

                    ✕

                </button>

            </div>



            {{-- ================================================= --}}
            {{-- ISI PREVIEW --}}
            {{-- ================================================= --}}

            <div
                id="previewContent"
                class="
                    w-full
                    h-[calc(90vh-73px)]
                    bg-slate-100
                "
            ></div>

        </div>

    </div>

@endsection





{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

@push('scripts')

<script>

    /*
    |--------------------------------------------------------------------------
    | MENAMPILKAN NAMA FILE SAAT DIPILIH
    |--------------------------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', function () {

        const dokumenInput =
            document.getElementById('dokumen');

        const namaFile =
            document.getElementById('namaFileDokumen');


        if (dokumenInput && namaFile) {

            dokumenInput.addEventListener('change', function () {

                if (this.files && this.files.length > 0) {

                    namaFile.textContent =
                        '📄 ' + this.files[0].name;

                    namaFile.classList.remove('hidden');

                } else {

                    namaFile.textContent = '';

                    namaFile.classList.add('hidden');

                }

            });

        }

    });



    /*
    |--------------------------------------------------------------------------
    | OPEN PREVIEW
    |--------------------------------------------------------------------------
    */

    function openPreview(url, namaFile) {

        const modal =
            document.getElementById('previewModal');

        const content =
            document.getElementById('previewContent');

        const title =
            document.getElementById('previewTitle');


        if (!modal || !content || !title) {

            return;

        }


        title.textContent =
            namaFile || 'Preview Dokumen';


        /*
        |--------------------------------------------------------------------------
        | AMBIL EXTENSION FILE
        |--------------------------------------------------------------------------
        */

        const extension =
            url
                .split('?')[0]
                .split('.')
                .pop()
                .toLowerCase();



        /*
        |--------------------------------------------------------------------------
        | PREVIEW PDF
        |--------------------------------------------------------------------------
        */

        if (extension === 'pdf') {

            content.innerHTML = `
                <iframe
                    src="${url}"
                    class="w-full h-full border-0"
                    title="${namaFile || 'Dokumen PDF'}"
                ></iframe>
            `;

        }



        /*
        |--------------------------------------------------------------------------
        | PREVIEW GAMBAR
        |--------------------------------------------------------------------------
        */

        else {

            content.innerHTML = `
                <div
                    class="
                        w-full
                        h-full
                        flex
                        items-center
                        justify-center
                        p-6
                        overflow-auto
                    "
                >

                    <img
                        src="${url}"
                        alt="${namaFile || 'Dokumen'}"
                        class="
                            max-w-full
                            max-h-full
                            object-contain
                            rounded-lg
                        "
                    >

                </div>
            `;

        }



        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN MODAL
        |--------------------------------------------------------------------------
        */

        modal.classList.remove('hidden');

        modal.classList.add('flex');

        document.body.classList.add('overflow-hidden');

    }



    /*
    |--------------------------------------------------------------------------
    | CLOSE PREVIEW
    |--------------------------------------------------------------------------
    */

    function closePreview() {

        const modal =
            document.getElementById('previewModal');

        const content =
            document.getElementById('previewContent');


        if (!modal || !content) {

            return;

        }


        modal.classList.add('hidden');

        modal.classList.remove('flex');

        content.innerHTML = '';

        document.body.classList.remove('overflow-hidden');

    }



    /*
    |--------------------------------------------------------------------------
    | KLIK DI LUAR MODAL
    |--------------------------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', function () {

        const modal =
            document.getElementById('previewModal');


        if (modal) {

            modal.addEventListener('click', function (event) {

                if (event.target === this) {

                    closePreview();

                }

            });

        }

    });



    /*
    |--------------------------------------------------------------------------
    | ESC UNTUK MENUTUP MODAL
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {

            closePreview();

        }

    });

</script>

@endpush