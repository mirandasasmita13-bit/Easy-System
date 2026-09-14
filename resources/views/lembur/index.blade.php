@extends('layouts.utama')

@section('title', 'Lembur')

@section('content')

{{-- ===================================================== --}}
{{-- HEADER --}}
{{-- ===================================================== --}}

<div class="mb-6">
    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
        Lembur
    </h2>
    <p class="mt-2 text-slate-500">
        Catat kegiatan lembur kamu di sini.
    </p>
</div>


{{-- ===================================================== --}}
{{-- PESAN --}}
{{-- ===================================================== --}}

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
        <p class="font-semibold mb-1">Data belum dapat disimpan:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{{-- ===================================================== --}}
{{-- FORM LEMBUR
-- ===================================================== --}}

<div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm mb-5">

    <div class="flex items-start gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                <circle cx="12" cy="12" r="9" />
                <path stroke-linecap="round" d="M12 7v5l3 2" />
            </svg>
        </div>
        <div>
            <h3 class="text-base font-bold text-slate-900">Catat Lembur</h3>
            <p class="text-xs text-slate-400 mt-0.5">Masukkan waktu dan kegiatan lembur.</p>
        </div>
    </div>

    <form id="formLembur" action="{{ route('lembur.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- LOKASI TERSEMBUNYI --}}
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        {{-- ================= WAKTU ================= --}}
        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 mb-4">

            <p class="text-xs font-bold text-slate-700 mb-3">Waktu Lembur</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                {{-- TANGGAL --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                </div>

                {{-- JAM MULAI --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Mulai</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                </div>

                {{-- JAM SELESAI --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jam Selesai</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}" required
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                </div>

            </div>

        </div>


        {{-- ================= KEGIATAN + FOTO (2 KOLOM) ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

            {{-- KEGIATAN --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                <p class="text-xs font-bold text-slate-700 mb-3">Kegiatan</p>

                <textarea name="kegiatan" rows="4" required
                          placeholder="Contoh: Penyelesaian laporan"
                          class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">{{ old('kegiatan') }}</textarea>
            </div>


            {{-- FOTO --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">

                <p class="text-xs font-bold text-slate-700 mb-3">Foto Bukti Lembur</p>

                <input id="fotoInput" type="file" name="foto" accept="image/*" capture="environment" class="hidden">

                {{-- SEBELUM FOTO: tombol langsung buka kamera --}}
                <div id="beforePhoto">

                    <button type="button" id="openCamera"
                            class="w-full rounded-xl border-2 border-dashed border-purple-200 bg-purple-50/60 px-4 py-6 flex flex-col items-center justify-center gap-2 hover:bg-purple-50 hover:border-purple-300 transition">

                        <div class="w-11 h-11 rounded-full bg-white text-purple-600 flex items-center justify-center shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.75 7.5h1.386a1.5 1.5 0 001.342-.83l.274-.55A1.5 1.5 0 0111.094 5h1.812a1.5 1.5 0 011.342.83l.274.55a1.5 1.5 0 001.342.83h1.386A2.25 2.25 0 0119.5 9.75v6.5a2.25 2.25 0 01-2.25 2.25h-10.5a2.25 2.25 0 01-2.25-2.25v-6.5A2.25 2.25 0 016.75 7.5z" />
                                <circle cx="12" cy="13" r="3" />
                            </svg>
                        </div>

                        <p class="text-sm font-bold text-purple-600">Ambil Foto</p>
                        <p class="text-[11px] text-slate-400">Kamera akan terbuka untuk mengambil bukti</p>

                    </button>

                    <p class="text-[11px] text-slate-400 mt-2 text-center">Maksimal ukuran foto 5 MB.</p>

                </div>


                {{-- SETELAH FOTO --}}
                <div id="afterPhoto" class="hidden">

                    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
                        <img id="photoPreview" src="" alt="Foto bukti lembur"
                             class="w-full h-32 object-cover">
                    </div>

                    <button type="button" id="retakePhoto"
                            class="w-full mt-2 rounded-xl border border-purple-200 bg-purple-50 text-purple-600 py-2.5 text-sm font-semibold hover:bg-purple-100 transition inline-flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Ambil Ulang Foto
                    </button>

                    <p class="text-[11px] text-slate-400 mt-2 text-center">Foto sudah dilengkapi watermark.</p>

                </div>

            </div>

        </div>


        {{-- ================= LOKASI + SUBMIT ================= --}}
        <div class="border-t border-slate-100 pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <p id="locationStatus" class="text-xs text-slate-400">Mengecek lokasi...</p>
            </div>

            <button type="submit" id="submitButton"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 text-white text-sm font-bold shadow-md shadow-purple-200 hover:bg-purple-700 transition">
                Simpan Lembur
            </button>

        </div>

    </form>

</div>


{{-- ===================================================== --}}
{{-- RIWAYAT LEMBUR
-- ===================================================== --}}

<div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm">

    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Catatan Lembur</h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftar kegiatan lembur kamu.</p>
        </div>
        <span class="inline-flex px-2.5 py-1 rounded-full bg-purple-50 text-purple-600 text-xs font-semibold">
            {{ $riwayatLembur->count() }} catatan
        </span>
    </div>

    <div class="space-y-2">

        @forelse($riwayatLembur as $lembur)

            @php
                $statusLembur = $lembur->status_approval ?? 'pending';

                $borderClass = match($statusLembur) {
                    'approved' => 'border-emerald-200 bg-emerald-50/40',
                    'rejected' => 'border-red-200 bg-red-50/40',
                    default    => 'border-amber-200 bg-amber-50/40',
                };
            @endphp

            <div class="rounded-xl border {{ $borderClass }} p-3 sm:p-4">

                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                    <div class="min-w-0 flex-1">

                        {{-- TANGGAL + STATUS --}}
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="text-sm font-bold text-slate-800">
                                {{ $lembur->tanggal->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-xs text-slate-400">·</span>
                            <span class="text-xs text-slate-500">
                                {{ $lembur->tanggal->translatedFormat('l') }}
                            </span>

                            @if($statusLembur === 'pending')
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-semibold">
                                    ⏳ Menunggu
                                </span>
                            @elseif($statusLembur === 'approved')
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-semibold">
                                    ✓ Disetujui
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-semibold">
                                    ✕ Ditolak
                                </span>
                            @endif
                        </div>

                        {{-- JAM + TOTAL JAM --}}
                        <p class="text-xs text-slate-600 font-medium mb-1">
                            🕐 {{ substr($lembur->jam_mulai, 0, 5) }} – {{ substr($lembur->jam_selesai, 0, 5) }}
                            @if($lembur->total_jam)
                                <span class="text-purple-600 font-semibold ml-1">
                                    ({{ $lembur->total_jam }} jam)
                                </span>
                            @endif
                        </p>

                        {{-- KEGIATAN --}}
                        <p class="text-sm text-slate-700 truncate">
                            {{ $lembur->kegiatan }}
                        </p>

                        @if($lembur->catatan_admin)
                            <p class="text-[11px] text-slate-500 mt-1.5 italic">
                                Catatan: {{ $lembur->catatan_admin }}
                            </p>
                        @endif

                    </div>

                    @if($lembur->foto)
                        <a href="{{ asset('storage/' . $lembur->foto) }}"
                           target="_blank"
                           class="shrink-0 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-purple-100 text-purple-600 text-xs font-semibold hover:bg-purple-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.8" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Lihat Foto
                        </a>
                    @endif

                </div>

            </div>

        @empty

            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 py-8 text-center">
                <p class="text-sm text-slate-500">Belum ada catatan lembur</p>
                <p class="text-xs text-slate-400 mt-1">Data lembur kamu akan muncul di sini.</p>
            </div>

        @endforelse

    </div>

</div>


{{-- ===================================================== --}}
{{-- MODAL KAMERA
-- ===================================================== --}}

<div id="cameraModal" class="hidden fixed inset-0 z-[999] bg-black/75 p-4">

    <div class="min-h-full flex items-center justify-center">

        <div class="w-full max-w-md rounded-2xl bg-white overflow-hidden shadow-2xl">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Ambil Foto Bukti</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pastikan kegiatan terlihat jelas.</p>
                </div>

                <button type="button" id="closeCamera"
                        class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200">
                    ✕
                </button>
            </div>

            {{-- VIDEO --}}
            <div class="relative bg-black">
                <video id="cameraVideo" autoplay playsinline muted
                       class="w-full aspect-[4/3] object-cover"></video>

                {{-- WATERMARK PREVIEW --}}
                <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/85 to-transparent px-3 py-2.5">
                    <p class="text-[10px] font-bold tracking-wide text-purple-300">
                        EASY SYSTEM · LEMBUR
                    </p>
                    <p id="watermarkWaktu" class="text-xs font-bold text-white leading-tight">
                        --
                    </p>
                    <p id="watermarkLokasi" class="text-[10px] text-slate-200 leading-tight mt-0.5">
                        Lokasi belum diambil
                    </p>
                </div>
            </div>

            <div id="cameraError" class="hidden px-4 pt-3">
                <div class="rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-600">
                    Kamera tidak dapat dibuka. Pastikan izin kamera sudah diberikan.
                </div>
            </div>

            {{-- BUTTON --}}
            <div class="p-4">
                <button type="button" id="takePhoto"
                        class="w-full inline-flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl text-sm font-bold shadow-md shadow-purple-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6.75 7.5h1.386a1.5 1.5 0 001.342-.83l.274-.55A1.5 1.5 0 0111.094 5h1.812a1.5 1.5 0 011.342.83l.274.55a1.5 1.5 0 001.342.83h1.386A2.25 2.25 0 0119.5 9.75v6.5a2.25 2.25 0 01-2.25 2.25h-10.5a2.25 2.25 0 01-2.25-2.25v-6.5A2.25 2.25 0 016.75 7.5z" />
                        <circle cx="12" cy="13" r="3" />
                    </svg>
                    Ambil Foto
                </button>
            </div>

        </div>

    </div>

</div>

{{-- CANVAS --}}
<canvas id="cameraCanvas" class="hidden"></canvas>


{{-- ===================================================== --}}
{{-- JAVASCRIPT
-- ===================================================== --}}

<script>

const OFFICE_LOKASI = {
    lat: 4.636822941619201,
    lng: 96.84824583097509,
    radius: 200
};

document.addEventListener('DOMContentLoaded', function () {

    const form          = document.getElementById('formLembur');
    const cameraModal   = document.getElementById('cameraModal');
    const cameraVideo   = document.getElementById('cameraVideo');
    const cameraCanvas  = document.getElementById('cameraCanvas');
    const cameraError   = document.getElementById('cameraError');
    const openCamera    = document.getElementById('openCamera');
    const closeCamera   = document.getElementById('closeCamera');
    const takePhoto     = document.getElementById('takePhoto');
    const retakePhoto   = document.getElementById('retakePhoto');
    const fotoInput     = document.getElementById('fotoInput');
    const photoPreview  = document.getElementById('photoPreview');
    const beforePhoto   = document.getElementById('beforePhoto');
    const afterPhoto    = document.getElementById('afterPhoto');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput= document.getElementById('longitude');
    const locationStatus= document.getElementById('locationStatus');

    const watermarkWaktuEl  = document.getElementById('watermarkWaktu');
    const watermarkLokasiEl = document.getElementById('watermarkLokasi');

    let cameraStream = null;

    const lokasiState = { lat: null, lng: null, jarak: null };


    /* =================================================
       FORMAT JAM (HH:MM tanpa detik)
    ================================================= */

    function formatJamTanggal() {

        const now = new Date();

        const jam = now.toLocaleTimeString('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit'
        });

        const tanggal = now.toLocaleDateString('id-ID', {
            timeZone: 'Asia/Jakarta',
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        return { jam, tanggal };
    }


    /* =================================================
       HITUNG JARAK (Haversine)
    ================================================= */

    function hitungJarakMeter(lat1, lon1, lat2, lon2) {

        const R = 6371000;
        const toRad = (deg) => (deg * Math.PI) / 180;

        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);

        const a =
            Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) *
            Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) ** 2;

        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }


    /* =================================================
       UPDATE WATERMARK PREVIEW
    ================================================= */

    function updateWatermarkPreview() {

        const { jam, tanggal } = formatJamTanggal();

        if (watermarkWaktuEl) {
            watermarkWaktuEl.textContent = tanggal + ' · ' + jam + ' WIB';
        }

        if (!watermarkLokasiEl) return;

        if (lokasiState.jarak === null) {
            watermarkLokasiEl.textContent = 'Lokasi belum diambil';
            return;
        }

        const dalamRadius = lokasiState.jarak <= OFFICE_LOKASI.radius;

        watermarkLokasiEl.textContent =
            Math.round(lokasiState.jarak) + ' m dari kantor · ' +
            (dalamRadius ? 'dalam radius' : 'di luar radius');
    }


    /* =================================================
       AMBIL LOKASI
    ================================================= */

    function getLocation() {

        locationStatus.textContent = 'Mengecek lokasi...';
        locationStatus.className = 'text-xs text-slate-400';

        if (!navigator.geolocation) {
            locationStatus.textContent = 'Browser tidak mendukung lokasi.';
            locationStatus.className = 'text-xs text-red-600';
            return;
        }

        navigator.geolocation.getCurrentPosition(

            function (position) {

                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                latitudeInput.value = lat;
                longitudeInput.value = lng;

                const jarak = hitungJarakMeter(
                    OFFICE_LOKASI.lat, OFFICE_LOKASI.lng,
                    lat, lng
                );

                lokasiState.lat = lat;
                lokasiState.lng = lng;
                lokasiState.jarak = jarak;

                const dalamRadius = jarak <= OFFICE_LOKASI.radius;

                locationStatus.textContent =
                    'Lokasi: ' + Math.round(jarak) + ' m dari kantor · ' +
                    (dalamRadius ? 'dalam radius' : 'di luar radius');

                locationStatus.className = dalamRadius
                    ? 'text-xs text-emerald-600'
                    : 'text-xs text-red-600';

                updateWatermarkPreview();
            },

            function (error) {

                console.error(error);

                latitudeInput.value = '';
                longitudeInput.value = '';

                locationStatus.textContent = 'Izinkan akses lokasi untuk melanjutkan.';
                locationStatus.className = 'text-xs text-red-600';
            },

            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }


    /* =================================================
       BUKA / TUTUP KAMERA
    ================================================= */

    async function startCamera() {

        cameraError.classList.add('hidden');

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            cameraError.classList.remove('hidden');
            return;
        }

        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' } },
                audio: false
            });

            cameraVideo.srcObject = cameraStream;
            await cameraVideo.play();

            updateWatermarkPreview();

        } catch (error) {
            console.error('Camera error:', error);
            cameraError.classList.remove('hidden');
        }
    }


    function stopCamera() {

        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }

        cameraVideo.srcObject = null;
    }


    /* =================================================
       GAMBAR WATERMARK DI FOTO
    ================================================= */

    function gambarWatermark(context, width, height) {

        const { jam, tanggal } = formatJamTanggal();

        const lokasiText = (lokasiState.lat !== null && lokasiState.lng !== null)
            ? Number(lokasiState.lat).toFixed(6) + ', ' + Number(lokasiState.lng).toFixed(6)
            : 'Lokasi tidak tersedia';

        const dalamRadius = lokasiState.jarak !== null && lokasiState.jarak <= OFFICE_LOKASI.radius;

        const jarakText = (lokasiState.jarak !== null)
            ? Math.round(lokasiState.jarak) + ' m dari kantor (' +
              (dalamRadius ? 'dalam radius' : 'di luar radius') + ')'
            : 'Jarak tidak diketahui';

        const barHeight = Math.max(90, Math.round(height * 0.24));

        const gradient = context.createLinearGradient(0, height - barHeight, 0, height);
        gradient.addColorStop(0, 'rgba(10, 10, 20, 0)');
        gradient.addColorStop(1, 'rgba(10, 10, 20, 0.85)');

        context.fillStyle = gradient;
        context.fillRect(0, height - barHeight, width, barHeight);

        const paddingX  = Math.round(width * 0.04);
        const fontLabel = Math.max(11, Math.round(width * 0.026));
        const fontMain  = Math.max(15, Math.round(width * 0.036));
        const fontSub   = Math.max(11, Math.round(width * 0.027));
        const lineGap   = Math.max(18, Math.round(width * 0.038));

        let y = height - barHeight + lineGap * 0.9;

        context.textBaseline = 'alphabetic';

        context.fillStyle = '#c4b5fd';
        context.font = '700 ' + fontLabel + 'px sans-serif';
        context.fillText('EASY SYSTEM · LEMBUR', paddingX, y);

        y += lineGap;
        context.fillStyle = '#ffffff';
        context.font = '700 ' + fontMain + 'px sans-serif';
        context.fillText(tanggal + ' · ' + jam + ' WIB', paddingX, y);

        y += lineGap * 0.9;
        context.fillStyle = '#e5e7eb';
        context.font = '500 ' + fontSub + 'px sans-serif';
        context.fillText(lokasiText, paddingX, y);

        y += lineGap * 0.9;
        context.fillStyle = dalamRadius ? '#86efac' : '#fca5a5';
        context.font = '700 ' + fontSub + 'px sans-serif';
        context.fillText(jarakText, paddingX, y);
    }


    /* =================================================
       BUKA MODAL KAMERA
    ================================================= */

    openCamera.addEventListener('click', function () {

        getLocation();

        cameraModal.classList.remove('hidden');

        startCamera();
    });


    closeCamera.addEventListener('click', function () {
        stopCamera();
        cameraModal.classList.add('hidden');
    });


    /* =================================================
       AMBIL FOTO
    ================================================= */

    takePhoto.addEventListener('click', function () {

        if (!cameraStream) return;

        const width  = cameraVideo.videoWidth;
        const height = cameraVideo.videoHeight;

        if (!width || !height) {
            alert('Kamera belum siap. Silakan tunggu sebentar.');
            return;
        }

        cameraCanvas.width  = width;
        cameraCanvas.height = height;

        const context = cameraCanvas.getContext('2d');
        context.drawImage(cameraVideo, 0, 0, width, height);

        gambarWatermark(context, width, height);

        cameraCanvas.toBlob(function (blob) {

            if (!blob) {
                alert('Foto gagal diambil.');
                return;
            }

            const file = new File(
                [blob],
                'foto-lembur-' + Date.now() + '.jpg',
                { type: 'image/jpeg' }
            );

            const dt = new DataTransfer();
            dt.items.add(file);
            fotoInput.files = dt.files;

            photoPreview.src = URL.createObjectURL(blob);

            beforePhoto.classList.add('hidden');
            afterPhoto.classList.remove('hidden');

            stopCamera();
            cameraModal.classList.add('hidden');

        }, 'image/jpeg', 0.92);
    });


    /* =================================================
       AMBIL ULANG FOTO
    ================================================= */

    retakePhoto.addEventListener('click', function () {

        fotoInput.value = '';
        photoPreview.src = '';

        afterPhoto.classList.add('hidden');
        beforePhoto.classList.remove('hidden');

        cameraModal.classList.remove('hidden');

        getLocation();
        startCamera();
    });


    /* =================================================
       SUBMIT
    ================================================= */

    form.addEventListener('submit', function (event) {

        if (!latitudeInput.value || !longitudeInput.value) {
            event.preventDefault();
            alert('Lokasi belum diperoleh. Izinkan akses lokasi terlebih dahulu.');
            getLocation();
            return;
        }

        if (!fotoInput.files || fotoInput.files.length === 0) {
            event.preventDefault();
            alert('Silakan ambil foto bukti lembur terlebih dahulu.');
            return;
        }
    });


    /* =================================================
       KLIK DI LUAR MODAL
    ================================================= */

    cameraModal.addEventListener('click', function (event) {
        if (event.target === cameraModal) {
            stopCamera();
            cameraModal.classList.add('hidden');
        }
    });


    /* =================================================
       MATIKAN KAMERA SAAT PINDAH HALAMAN
    ================================================= */

    window.addEventListener('beforeunload', function () {
        stopCamera();
    });


    /* =================================================
       CEK LOKASI SAAT HALAMAN DIBUKA
    ================================================= */

    getLocation();

});

</script>

@endsection