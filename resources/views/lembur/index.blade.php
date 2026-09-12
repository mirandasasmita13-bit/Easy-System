@extends('layouts.utama')

@section('title', 'Lembur')

@section('content')

    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div class="mb-7">

        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
            Lembur
        </h2>

        <p class="mt-2 text-slate-500">
            Catat kegiatan lembur kamu di sini.
        </p>

    </div>


    {{-- =====================================================
        PESAN SUKSES
    ====================================================== --}}

    @if(session('success'))

        <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">
            {{ session('success') }}
        </div>

    @endif


    {{-- =====================================================
        PESAN ERROR
    ====================================================== --}}

    @if(session('error'))

        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
            {{ session('error') }}
        </div>

    @endif


    {{-- =====================================================
        ERROR VALIDASI
    ====================================================== --}}

    @if($errors->any())

        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">

            <p class="font-semibold mb-2">
                Data belum dapat disimpan:
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


    {{-- =====================================================
        FORM LEMBUR
    ====================================================== --}}

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

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.8"
                    stroke="currentColor"
                    class="w-6 h-6"
                >

                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                    />

                    <path
                        stroke-linecap="round"
                        d="M12 7v5l3 2"
                    />

                </svg>

            </div>


            <div>

                <h3 class="text-lg font-bold text-slate-900">
                    Catat Lembur
                </h3>

                <p class="text-sm text-slate-400 mt-1">
                    Masukkan waktu dan kegiatan lembur kamu.
                </p>

            </div>

        </div>


        {{-- =================================================
            FORM
        ================================================== --}}

        <form
            id="formLembur"
            action="{{ route('lembur.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf


            {{-- LOKASI TERSEMBUNYI --}}

            <input
                type="hidden"
                name="latitude"
                id="latitude"
            >

            <input
                type="hidden"
                name="longitude"
                id="longitude"
            >


            {{-- =================================================
                WAKTU
            ================================================== --}}

            <div class="
                rounded-2xl
                border border-slate-200
                bg-slate-50/50
                p-5
                mb-5
            ">

                <div class="mb-5">

                    <p class="text-sm font-bold text-slate-800">
                        Waktu Lembur
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Tentukan tanggal dan waktu lembur.
                    </p>

                </div>


                <div class="
                    grid
                    grid-cols-1
                    md:grid-cols-3
                    gap-5
                ">

                    {{-- TANGGAL --}}

                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            value="{{ old('tanggal') }}"
                            required
                            class="
                                w-full
                                rounded-xl
                                border border-slate-200
                                bg-white
                                px-4 py-3
                                text-sm
                                text-slate-700
                                outline-none
                                focus:border-purple-500
                                focus:ring-2
                                focus:ring-purple-100
                                transition
                            "
                        >

                    </div>


                    {{-- JAM MULAI --}}

                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Jam Mulai
                        </label>

                        <input
                            type="time"
                            name="jam_mulai"
                            value="{{ old('jam_mulai') }}"
                            required
                            class="
                                w-full
                                rounded-xl
                                border border-slate-200
                                bg-white
                                px-4 py-3
                                text-sm
                                text-slate-700
                                outline-none
                                focus:border-purple-500
                                focus:ring-2
                                focus:ring-purple-100
                                transition
                            "
                        >

                    </div>


                    {{-- JAM SELESAI --}}

                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Jam Selesai
                        </label>

                        <input
                            type="time"
                            name="jam_selesai"
                            value="{{ old('jam_selesai') }}"
                            required
                            class="
                                w-full
                                rounded-xl
                                border border-slate-200
                                bg-white
                                px-4 py-3
                                text-sm
                                text-slate-700
                                outline-none
                                focus:border-purple-500
                                focus:ring-2
                                focus:ring-purple-100
                                transition
                            "
                        >

                    </div>

                </div>

            </div>


            {{-- =================================================
                KEGIATAN
            ================================================== --}}

            <div class="
                rounded-2xl
                border border-slate-200
                bg-slate-50/50
                p-5
                mb-5
            ">

                <div class="mb-4">

                    <p class="text-sm font-bold text-slate-800">
                        Kegiatan
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Jelaskan pekerjaan yang dilakukan saat lembur.
                    </p>

                </div>


                <input
                    type="text"
                    name="kegiatan"
                    value="{{ old('kegiatan') }}"
                    placeholder="Contoh: Penyelesaian laporan"
                    required
                    class="
                        w-full
                        rounded-xl
                        border border-slate-200
                        bg-white
                        px-4 py-3
                        text-sm
                        text-slate-700
                        placeholder:text-slate-400
                        outline-none
                        focus:border-purple-500
                        focus:ring-2
                        focus:ring-purple-100
                        transition
                    "
                >

            </div>


            {{-- =================================================
                FOTO BUKTI
            ================================================== --}}

            <div class="
                rounded-2xl
                border border-slate-200
                bg-slate-50/50
                p-5
                mb-5
            ">

                <div class="mb-4">

                    <p class="text-sm font-bold text-slate-800">
                        Foto Bukti Lembur
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Foto wajib diambil saat berada di area kantor.
                    </p>

                </div>


                {{-- INPUT FILE TETAP ADA DI BELAKANG --}}
                {{-- USER TIDAK AKAN MELIHAT CHOOSE FILE --}}

                <input
                    id="fotoInput"
                    type="file"
                    name="foto"
                    accept="image/*"
                    class="hidden"
                >


                {{-- =================================================
                    SEBELUM FOTO
                ================================================== --}}

                <div id="beforePhoto">

                    <button
                        type="button"
                        id="openCamera"
                        class="
                            w-full
                            rounded-2xl
                            border-2
                            border-dashed
                            border-purple-200
                            bg-purple-50/40
                            px-6
                            py-8
                            text-center
                            hover:bg-purple-50
                            hover:border-purple-300
                            transition
                        "
                    >

                        <div class="
                            mx-auto
                            w-12 h-12
                            rounded-full
                            bg-white
                            text-purple-600
                            flex
                            items-center
                            justify-center
                            shadow-sm
                            mb-3
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
                                    d="M6.75 7.5h1.386a1.5 1.5 0 001.342-.83l.274-.55A1.5 1.5 0 0111.094 5h1.812a1.5 1.5 0 011.342.83l.274.55a1.5 1.5 0 001.342.83h1.386A2.25 2.25 0 0119.5 9.75v6.5a2.25 2.25 0 01-2.25 2.25h-10.5a2.25 2.25 0 01-2.25-2.25v-6.5A2.25 2.25 0 016.75 7.5z"
                                />

                                <circle
                                    cx="12"
                                    cy="13"
                                    r="3"
                                />

                            </svg>

                        </div>


                        <p class="text-sm font-bold text-purple-600">
                            Ambil Foto
                        </p>


                        <p class="text-xs text-slate-400 mt-1">
                            Kamera akan dibuka untuk mengambil bukti
                        </p>

                    </button>

                </div>


                {{-- =================================================
                    SETELAH FOTO
                    AWALNYA TERSEMBUNYI
                ================================================== --}}

                <div
                    id="afterPhoto"
                    class="hidden"
                >

                    <div class="
                        rounded-2xl
                        border border-slate-200
                        bg-white
                        overflow-hidden
                    ">

                        {{-- FOTO HASIL --}}

                        <img
                            id="photoPreview"
                            src=""
                            alt="Foto bukti lembur"
                            class="
                                w-full
                                h-56
                                sm:h-64
                                object-cover
                            "
                        >
                        
                        {{-- TOMBOL ULANG --}}

                        <div class="p-4 flex justify-center">

                            <button
                                type="button"
                                id="retakePhoto"
                                class="
                                    inline-flex
                                    items-center
                                    justify-center
                                    gap-2
                                    px-5
                                    py-2.5
                                    rounded-xl
                                    bg-purple-50
                                    text-purple-600
                                    text-sm
                                    font-semibold
                                    hover:bg-purple-100
                                    transition
                                "
                            >

                                📷 Ambil Ulang Foto

                            </button>

                        </div>

                    </div>

                </div>


                <p class="text-xs text-slate-400 mt-3">
                    Maksimal ukuran foto 5 MB.
                </p>

            </div>


            {{-- =================================================
                LOKASI + SUBMIT
            ================================================== --}}

            <div class="
                border-t
                border-slate-100
                pt-6
                flex
                flex-col
                sm:flex-row
                sm:items-center
                sm:justify-between
                gap-4
            ">

                <div>

                    <p class="text-xs text-slate-400">
                        Lembur hanya dapat dicatat dari area kantor.
                    </p>

                    <p
                        id="locationStatus"
                        class="text-xs text-slate-400 mt-1"
                    >
                        Mengecek lokasi...
                    </p>

                </div>


                <button
                    type="submit"
                    id="submitButton"
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

                    ✓

                    Simpan Lembur

                </button>

            </div>

        </form>

    </div>


    {{-- =====================================================
        RIWAYAT LEMBUR
    ====================================================== --}}

    <div class="es-card p-6 sm:p-8">

        <div class="flex items-center justify-between mb-6">

            <div>

                <h3 class="text-lg font-bold text-slate-900">
                    Catatan Lembur
                </h3>

                <p class="text-sm text-slate-400 mt-1">
                    Daftar kegiatan lembur kamu.
                </p>

            </div>


            <span class="
                hidden
                sm:inline-flex
                px-3 py-1.5
                rounded-full
                bg-purple-50
                text-purple-600
                text-xs
                font-semibold
            ">
                Lembur
            </span>

        </div>


        @forelse($riwayatLembur as $lembur)

            <div class="
                rounded-2xl
                border border-slate-200
                bg-white
                p-4
                sm:p-5
                mb-3
            ">

                <div class="
                    flex
                    flex-col
                    sm:flex-row
                    sm:items-center
                    gap-4
                ">


                    {{-- TANGGAL --}}

                    <div class="
                        sm:w-28
                        sm:border-r
                        sm:border-slate-100
                        sm:pr-4
                    ">

                        <p class="
                            text-lg
                            font-extrabold
                            text-purple-600
                        ">
                            {{ $lembur->tanggal->format('d') }}
                        </p>

                        <p class="
                            text-xs
                            font-semibold
                            text-slate-600
                        ">
                            {{ $lembur->tanggal->translatedFormat('M Y') }}
                        </p>

                        <p class="
                            text-xs
                            text-slate-400
                            mt-0.5
                        ">
                            {{ $lembur->tanggal->translatedFormat('l') }}
                        </p>

                    </div>


                    {{-- DATA LEMBUR --}}

                    <div class="flex-1 min-w-0">

                        <div class="
                            flex
                            items-center
                            gap-2
                            mb-1
                        ">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.8"
                                stroke="currentColor"
                                class="w-4 h-4 text-purple-500 shrink-0"
                            >

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                />

                                <path
                                    stroke-linecap="round"
                                    d="M12 7v5l3 2"
                                />

                            </svg>


                            <p class="
                                text-sm
                                font-semibold
                                text-slate-700
                            ">

                                {{ substr($lembur->jam_mulai, 0, 5) }}

                                –

                                {{ substr($lembur->jam_selesai, 0, 5) }}

                            </p>

                        </div>


                        <p class="
                            text-sm
                            font-bold
                            text-slate-800
                        ">
                            {{ $lembur->kegiatan }}
                        </p>

                    </div>


                    {{-- FOTO --}}

                    @if($lembur->foto)

                        <a
                            href="{{ asset('storage/' . $lembur->foto) }}"
                            target="_blank"
                            class="
                                inline-flex
                                items-center
                                justify-center
                                gap-2
                                px-4 py-2.5
                                rounded-xl
                                bg-purple-50
                                text-purple-600
                                text-xs
                                font-semibold
                                hover:bg-purple-100
                                transition
                                shrink-0
                            "
                        >

                            📷 Lihat Foto

                        </a>

                    @endif

                </div>

            </div>

        @empty

            <div class="
                rounded-2xl
                border border-dashed
                border-slate-200
                bg-slate-50
                py-12
                text-center
            ">

                <div class="
                    mx-auto
                    mb-3
                    w-11 h-11
                    rounded-full
                    bg-white
                    border border-slate-200
                    flex
                    items-center
                    justify-center
                ">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.6"
                        stroke="currentColor"
                        class="w-5 h-5 text-slate-400"
                    >

                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                        />

                        <path
                            stroke-linecap="round"
                            d="M12 7v5l3 2"
                        />

                    </svg>

                </div>


                <p class="
                    text-sm
                    font-medium
                    text-slate-500
                ">
                    Belum ada catatan lembur
                </p>


                <p class="
                    text-xs
                    text-slate-400
                    mt-1
                ">
                    Data lembur kamu akan muncul di sini.
                </p>

            </div>

        @endforelse

    </div>


    {{-- =====================================================
        MODAL KAMERA
    ====================================================== --}}

    <div
        id="cameraModal"
        class="
            hidden
            fixed
            inset-0
            z-[999]
            bg-black/75
            p-4
        "
    >

        <div class="
            min-h-full
            flex
            items-center
            justify-center
        ">

            <div class="
                w-full
                max-w-lg
                rounded-3xl
                bg-white
                overflow-hidden
                shadow-2xl
            ">


                {{-- HEADER MODAL --}}

                <div class="
                    flex
                    items-center
                    justify-between
                    px-5 py-4
                    border-b border-slate-100
                ">

                    <div>

                        <h3 class="
                            text-base
                            font-bold
                            text-slate-900
                        ">
                            Ambil Foto Bukti
                        </h3>

                        <p class="
                            text-xs
                            text-slate-400
                            mt-0.5
                        ">
                            Pastikan kegiatan terlihat jelas.
                        </p>

                    </div>


                    <button
                        type="button"
                        id="closeCamera"
                        class="
                            w-9 h-9
                            rounded-full
                            bg-slate-100
                            text-slate-500
                            flex
                            items-center
                            justify-center
                            hover:bg-slate-200
                        "
                    >
                        ✕
                    </button>

                </div>


                {{-- VIDEO CAMERA --}}

                <div class="
                    relative
                    bg-black
                    aspect-[4/3]
                ">

                    <video
                        id="cameraVideo"
                        autoplay
                        playsinline
                        muted
                        class="
                            w-full
                            h-full
                            object-cover
                        "
                    ></video>


                    {{-- FRAME KAMERA --}}

                    <div class="
                        pointer-events-none
                        absolute
                        inset-6
                        border-2
                        border-white/50
                        rounded-2xl
                    "></div>

                </div>


                {{-- ERROR CAMERA --}}

                <div
                    id="cameraError"
                    class="hidden px-5 pt-4"
                >

                    <div class="
                        rounded-xl
                        bg-red-50
                        border border-red-200
                        px-4 py-3
                        text-xs
                        text-red-600
                    ">

                        Kamera tidak dapat dibuka.
                        Pastikan browser sudah diberi izin menggunakan kamera.

                    </div>

                </div>


                {{-- TOMBOL AMBIL --}}

                <div class="
                    px-5 py-5
                    flex
                    justify-center
                ">

                    <button
                        type="button"
                        id="takePhoto"
                        class="
                            inline-flex
                            items-center
                            justify-center
                            gap-2
                            px-7 py-3
                            rounded-xl
                            bg-purple-600
                            text-white
                            text-sm
                            font-bold
                            shadow-lg
                            shadow-purple-200
                            hover:bg-purple-700
                            transition
                        "
                    >

                        📷 Ambil Foto

                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
        CANVAS
    ====================================================== --}}

    <canvas
        id="cameraCanvas"
        class="hidden"
    ></canvas>


    {{-- =====================================================
        JAVASCRIPT
    ====================================================== --}}

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const form =
                document.getElementById('formLembur');

            const cameraModal =
                document.getElementById('cameraModal');

            const cameraVideo =
                document.getElementById('cameraVideo');

            const cameraCanvas =
                document.getElementById('cameraCanvas');

            const cameraError =
                document.getElementById('cameraError');

            const openCamera =
                document.getElementById('openCamera');

            const closeCamera =
                document.getElementById('closeCamera');

            const takePhoto =
                document.getElementById('takePhoto');

            const retakePhoto =
                document.getElementById('retakePhoto');

            const fotoInput =
                document.getElementById('fotoInput');

            const photoPreview =
                document.getElementById('photoPreview');

            const beforePhoto =
                document.getElementById('beforePhoto');

            const afterPhoto =
                document.getElementById('afterPhoto');

            const latitudeInput =
                document.getElementById('latitude');

            const longitudeInput =
                document.getElementById('longitude');

            const locationStatus =
                document.getElementById('locationStatus');


            let cameraStream = null;


            // =================================================
            // LOKASI
            // =================================================

            function getLocation() {

                locationStatus.textContent =
                    'Mengecek lokasi...';

                locationStatus.className =
                    'text-xs text-slate-400 mt-1';


                if (!navigator.geolocation) {

                    locationStatus.textContent =
                        'Browser tidak mendukung lokasi.';

                    locationStatus.className =
                        'text-xs text-red-600 mt-1';

                    return;

                }


                navigator.geolocation.getCurrentPosition(

                    function (position) {

                        latitudeInput.value =
                            position.coords.latitude;

                        longitudeInput.value =
                            position.coords.longitude;


                        locationStatus.textContent =
                            'Lokasi berhasil diperoleh.';

                        locationStatus.className =
                            'text-xs text-green-600 mt-1';

                    },

                    function (error) {

                        console.error(error);

                        latitudeInput.value = '';
                        longitudeInput.value = '';

                        locationStatus.textContent =
                            'Izinkan akses lokasi untuk melanjutkan.';

                        locationStatus.className =
                            'text-xs text-red-600 mt-1';

                    },

                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }

                );

            }


            // =================================================
            // BUKA KAMERA
            // =================================================

            async function startCamera() {

                cameraError.classList.add('hidden');


                if (
                    !navigator.mediaDevices ||
                    !navigator.mediaDevices.getUserMedia
                ) {

                    cameraError.classList.remove('hidden');

                    return;

                }


                try {

                    cameraStream =
                        await navigator.mediaDevices.getUserMedia({

                            video: {
                                facingMode: {
                                    ideal: 'environment'
                                }
                            },

                            audio: false

                        });


                    cameraVideo.srcObject =
                        cameraStream;


                    await cameraVideo.play();

                }

                catch (error) {

                    console.error(
                        'Camera error:',
                        error
                    );

                    cameraError.classList.remove('hidden');

                }

            }


            // =================================================
            // MATIKAN KAMERA
            // =================================================

            function stopCamera() {

                if (cameraStream) {

                    cameraStream
                        .getTracks()
                        .forEach(function (track) {

                            track.stop();

                        });

                    cameraStream = null;

                }


                cameraVideo.srcObject = null;

            }


            // =================================================
            // BUKA MODAL
            // =================================================

            openCamera.addEventListener(
                'click',
                function () {

                    /*
                     * Lokasi diperbarui setiap kali
                     * user mengambil foto.
                     */

                    getLocation();


                    cameraModal.classList.remove(
                        'hidden'
                    );


                    startCamera();

                }
            );


            // =================================================
            // TUTUP MODAL
            // =================================================

            closeCamera.addEventListener(
                'click',
                function () {

                    stopCamera();

                    cameraModal.classList.add(
                        'hidden'
                    );

                }
            );


            // =================================================
            // AMBIL FOTO
            // =================================================

            takePhoto.addEventListener(
                'click',
                function () {

                    if (!cameraStream) {

                        return;

                    }


                    const width =
                        cameraVideo.videoWidth;

                    const height =
                        cameraVideo.videoHeight;


                    if (!width || !height) {

                        alert(
                            'Kamera belum siap. Silakan tunggu sebentar.'
                        );

                        return;

                    }


                    cameraCanvas.width =
                        width;

                    cameraCanvas.height =
                        height;


                    const context =
                        cameraCanvas.getContext(
                            '2d'
                        );


                    context.drawImage(
                        cameraVideo,
                        0,
                        0,
                        width,
                        height
                    );


                    cameraCanvas.toBlob(

                        function (blob) {

                            if (!blob) {

                                alert(
                                    'Foto gagal diambil.'
                                );

                                return;

                            }


                            /*
                             * Buat file dari hasil kamera.
                             */

                            const file =
                                new File(
                                    [blob],
                                    'foto-lembur.jpg',
                                    {
                                        type: 'image/jpeg'
                                    }
                                );


                            /*
                             * Masukkan hasil foto
                             * ke input file.
                             */

                            const dataTransfer =
                                new DataTransfer();


                            dataTransfer.items.add(
                                file
                            );


                            fotoInput.files =
                                dataTransfer.files;


                            /*
                             * Tampilkan foto.
                             */

                            const imageUrl =
                                URL.createObjectURL(
                                    blob
                                );


                            photoPreview.src =
                                imageUrl;


                            /*
                             * PENTING:
                             *
                             * Kotak "Ambil Foto"
                             * disembunyikan.
                             */

                            beforePhoto.classList.add(
                                'hidden'
                            );


                            /*
                             * Foto hasil ditampilkan.
                             */

                            afterPhoto.classList.remove(
                                'hidden'
                            );


                            /*
                             * Kamera dimatikan.
                             */

                            stopCamera();


                            cameraModal.classList.add(
                                'hidden'
                            );

                        },

                        'image/jpeg',

                        0.90

                    );

                }
            );


            // =================================================
            // AMBIL ULANG FOTO
            // =================================================

            retakePhoto.addEventListener(
                'click',
                function () {

                    /*
                     * Hapus foto sebelumnya.
                     */

                    fotoInput.value = '';

                    photoPreview.src = '';


                    /*
                     * Sembunyikan preview.
                     */

                    afterPhoto.classList.add(
                        'hidden'
                    );


                    /*
                     * Munculkan kembali
                     * tombol Ambil Foto.
                     */

                    beforePhoto.classList.remove(
                        'hidden'
                    );


                    /*
                     * Buka kamera lagi.
                     */

                    cameraModal.classList.remove(
                        'hidden'
                    );


                    getLocation();

                    startCamera();

                }
            );


            // =================================================
            // SUBMIT
            // =================================================

            form.addEventListener(
                'submit',
                function (event) {

                    //CEK LOKASI
                     
                    if (
                        !latitudeInput.value ||
                        !longitudeInput.value
                    ) {

                        event.preventDefault();

                        alert(
                            'Lokasi belum diperoleh. Izinkan akses lokasi terlebih dahulu.'
                        );

                        getLocation();

                        return;

                    }

                    //CEK FOTO

                    if (
                        !fotoInput.files ||
                        fotoInput.files.length === 0
                    ) {

                        event.preventDefault();

                        alert(
                            'Silakan ambil foto bukti lembur terlebih dahulu.'
                        );

                        return;

                    }

                }
            );


            // =================================================
            // KLIK DI LUAR MODAL
            // =================================================

            cameraModal.addEventListener(
                'click',
                function (event) {

                    if (
                        event.target === cameraModal
                    ) {

                        stopCamera();

                        cameraModal.classList.add(
                            'hidden'
                        );

                    }

                }
            );


            // =================================================
            // MATIKAN KAMERA SAAT PINDAH HALAMAN
            // =================================================

            window.addEventListener(
                'beforeunload',
                function () {

                    stopCamera();

                }
            );


            // =================================================
            // CEK LOKASI SAAT HALAMAN DIBUKA
            // =================================================

            getLocation();

        });

    </script>

@endsection