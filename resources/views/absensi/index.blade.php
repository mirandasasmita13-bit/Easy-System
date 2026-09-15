@extends('layouts.utama')

@section('title', 'Absensi')

@section('content')

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">Absensi</h2>
        <p class="mt-2 text-slate-500">Catat kehadiran kamu untuk hari ini.</p>
    </div>

    {{-- PESAN --}}
    @if(session('success'))
        <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-sm text-red-700">
            <p class="font-semibold mb-2">Absensi gagal:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- HERO CLOCK --}}
    <div class="dashboard-hero mb-7 overflow-hidden">
        <div class="absolute -right-16 -top-20 w-56 h-56 rounded-full bg-white/10"></div>
        <div class="absolute -left-20 -bottom-24 w-56 h-56 rounded-full bg-white/5"></div>

        <div class="relative z-10 p-5 sm:p-6">
            <div class="grid lg:grid-cols-[1fr_auto] items-center gap-5">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                        <p id="tanggal" class="text-purple-100 text-sm sm:text-base font-medium"></p>
                    </div>
                    <div id="jam" class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight">
                        00:00:00
                    </div>
                </div>

                <div class="lg:text-right">
                    <p class="text-purple-100 text-xs sm:text-sm mb-2">Status kehadiran</p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/10 text-white text-sm font-semibold">
                        @if(!$absensiHariIni)
                            <span class="w-2 h-2 rounded-full bg-amber-300"></span>
                            Belum Absen
                        @elseif(!$absensiHariIni->jam_pulang)
                            <span class="w-2 h-2 rounded-full bg-purple-300"></span>
                            Sudah Absen Masuk
                        @else
                            <span class="w-2 h-2 rounded-full bg-emerald-300"></span>
                            Sudah Absen Lengkap
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- AREA ABSEN --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-7">

        {{-- ABSEN MASUK --}}
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H3"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-slate-900">Absen Masuk</h3>
                    <p class="text-sm text-slate-400 mt-1">Catat waktu kedatangan kamu.</p>
                </div>
            </div>

            @if(!$absensiHariIni)
                <form id="formAbsenMasuk" action="{{ url('/absensi/masuk') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mt-6">
                        <label for="shift" class="block text-sm font-semibold text-slate-700 mb-2">Shift Kerja</label>
                        <select id="shift" name="shift" required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                            <option value="">Pilih shift</option>
                            <option value="pagi">Shift Pagi</option>
                            <option value="malam">Shift Malam</option>
                        </select>
                    </div>

                    <div class="mt-5 rounded-xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-xs uppercase tracking-wider font-semibold text-slate-400">Lokasi Kantor</p>
                        <p class="text-sm text-slate-600 leading-relaxed mt-2">{{ $kantorAlamat }}</p>

                        <div id="statusLokasiMasuk" class="mt-3 flex items-start gap-2 text-sm text-slate-400">
                            <span>📍</span>
                            <span>Lokasi belum diambil</span>
                        </div>

                        <button type="button" id="btnLokasiMasuk"
                                class="w-full mt-3 rounded-xl border border-purple-100 bg-purple-50 text-purple-600 py-2.5 text-sm font-semibold transition hover:bg-purple-100">
                            📍 Ambil Lokasi
                        </button>
                    </div>

                    <input type="hidden" name="latitude" id="latitudeMasuk">
                    <input type="hidden" name="longitude" id="longitudeMasuk">

                    <div class="mt-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Foto Absensi</label>

                        <div id="previewFotoMasuk"
                             class="hidden mb-3 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 cursor-pointer relative group"
                             onclick="bukaModalFoto('gambarMasuk')">
                            <img id="gambarMasuk" src="" alt="Preview" class="w-full max-h-64 object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="text-white text-sm font-bold">🔍 Lihat Foto</span>
                            </div>
                        </div>

                        <input type="file" id="foto_masuk" name="foto_masuk" accept="image/*" class="hidden">

                        <button type="button" id="btnFotoMasuk" disabled
                                class="w-full rounded-xl border border-purple-100 bg-purple-50 text-purple-600 py-3 text-sm font-semibold transition hover:bg-purple-100 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-purple-50">
                            📷 Ambil Foto
                        </button>

                        <p id="keteranganFotoMasuk" class="text-xs text-slate-400 mt-2">
                            Ambil lokasi terlebih dahulu sebelum mengambil foto.
                        </p>

                        <button type="button" id="btnFotoUlangMasuk"
                                class="hidden w-full mt-2 text-sm text-purple-600 font-semibold hover:text-purple-700">
                            ↻ Ambil Foto Ulang
                        </button>
                    </div>

                    <button type="submit" id="btnAbsenMasuk" disabled
                            class="w-full mt-5 bg-purple-600 hover:bg-purple-700 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white py-3.5 rounded-xl font-semibold transition shadow-lg shadow-purple-600/10">
                        ✓ &nbsp; Absen Masuk
                    </button>
                </form>
            @else
                <button type="button" disabled
                        class="w-full mt-6 bg-slate-100 text-slate-400 py-3.5 rounded-xl font-semibold cursor-not-allowed">
                    ✓ &nbsp; Sudah Absen Masuk
                </button>
            @endif
        </div>


        {{-- ABSEN PULANG --}}
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0116.5 21h-6a2.25 2.25 0 01-2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h9"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-slate-900">Absen Pulang</h3>
                    <p class="text-sm text-slate-400 mt-1">Catat waktu kepulangan kamu.</p>
                </div>
            </div>

            @if($absensiHariIni && !$absensiHariIni->jam_pulang)
                <form id="formAbsenPulang" action="{{ url('/absensi/pulang') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mt-6 rounded-xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-xs uppercase tracking-wider font-semibold text-slate-400">Lokasi Kantor</p>
                        <p class="text-sm text-slate-600 leading-relaxed mt-2">{{ $kantorAlamat }}</p>

                        <div id="statusLokasiPulang" class="mt-3 flex items-start gap-2 text-sm text-slate-400">
                            <span>📍</span>
                            <span>Lokasi belum diambil</span>
                        </div>

                        <button type="button" id="btnLokasiPulang"
                                class="w-full mt-3 rounded-xl border border-purple-100 bg-purple-50 text-purple-600 py-2.5 text-sm font-semibold transition hover:bg-purple-100">
                            📍 Ambil Lokasi
                        </button>
                    </div>

                    <input type="hidden" name="latitude" id="latitudePulang">
                    <input type="hidden" name="longitude" id="longitudePulang">

                    <div class="mt-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Foto Absensi</label>

                        <div id="previewFotoPulang"
                             class="hidden mb-3 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 cursor-pointer relative group"
                             onclick="bukaModalFoto('gambarPulang')">
                            <img id="gambarPulang" src="" alt="Preview" class="w-full max-h-64 object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="text-white text-sm font-bold">🔍 Lihat Foto</span>
                            </div>
                        </div>

                        <input type="file" id="foto_pulang" name="foto_pulang" accept="image/*" class="hidden">

                        <button type="button" id="btnFotoPulang" disabled
                                class="w-full rounded-xl border border-purple-100 bg-purple-50 text-purple-600 py-3 text-sm font-semibold transition hover:bg-purple-100 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-purple-50">
                            📷 Ambil Foto
                        </button>

                        <p id="keteranganFotoPulang" class="text-xs text-slate-400 mt-2">
                            Ambil lokasi terlebih dahulu sebelum mengambil foto.
                        </p>

                        <button type="button" id="btnFotoUlangPulang"
                                class="hidden w-full mt-2 text-sm text-purple-600 font-semibold hover:text-purple-700">
                            ↻ Ambil Foto Ulang
                        </button>
                    </div>

                    <button type="submit" id="btnAbsenPulang" disabled
                            class="w-full mt-5 bg-slate-800 hover:bg-slate-900 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white py-3.5 rounded-xl font-semibold transition">
                        Absen Pulang
                    </button>
                </form>
            @elseif(!$absensiHariIni)
                <div class="mt-6 rounded-xl bg-slate-50 border border-slate-100 p-5 text-center">
                    <p class="text-sm text-slate-400">Silakan lakukan absen masuk terlebih dahulu.</p>
                </div>
                <button type="button" disabled
                        class="w-full mt-4 bg-slate-100 text-slate-400 py-3.5 rounded-xl font-semibold cursor-not-allowed">
                    Absen Pulang
                </button>
            @else
                <button type="button" disabled
                        class="w-full mt-6 bg-emerald-50 text-emerald-600 py-3.5 rounded-xl font-semibold cursor-not-allowed">
                    ✓ &nbsp; Sudah Absen Pulang
                </button>
            @endif
        </div>
    </div>


    {{-- INFORMASI HARI INI --}}
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm mb-7">
        <div class="mb-5">
            <h3 class="font-bold text-lg text-slate-900">Informasi Kehadiran</h3>
            <p class="text-sm text-slate-400 mt-1">Ringkasan absensi kamu hari ini.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Jam Masuk</p>
                <p class="text-2xl font-bold text-slate-900 mt-2">
                    {{ $absensiHariIni?->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : '--:--' }}
                </p>
                @if($absensiHariIni && $absensiHariIni->jarak !== null)
                    <p class="text-xs text-slate-500 mt-2">📍 {{ number_format($absensiHariIni->jarak, 0) }} m dari kantor</p>
                @endif
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Jam Pulang</p>
                <p class="text-2xl font-bold text-slate-900 mt-2">
                    {{ $absensiHariIni?->jam_pulang ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') : '--:--' }}
                </p>
                @if($absensiHariIni && $absensiHariIni->jarak_pulang !== null)
                    <p class="text-xs text-slate-500 mt-2">📍 {{ number_format($absensiHariIni->jarak_pulang, 0) }} m dari kantor</p>
                @endif
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Shift</p>
                <p class="text-lg font-bold text-purple-600 mt-3">
                    @if($absensiHariIni)
                        @if($absensiHariIni->shift === 'malam') Shift Malam
                        @elseif($absensiHariIni->shift === 'pagi') Shift Pagi
                        @else Belum dipilih @endif
                    @else Belum dipilih @endif
                </p>
            </div>
        </div>
    </div>


    {{-- CATATAN --}}
    <div class="rounded-2xl bg-purple-50 border border-purple-100 p-5 sm:p-6">
        <div class="flex gap-3">
            <div class="text-purple-600 mt-0.5">ⓘ</div>
            <div>
                <p class="font-semibold text-purple-900">Informasi</p>
                <p class="text-sm text-purple-700 mt-1 leading-relaxed">
                    Pastikan melakukan absensi sesuai shift dan berada di dalam radius kantor maksimal {{ $radiusMaksimal }} meter.
                </p>
            </div>
        </div>
    </div>


    {{-- MODAL KAMERA --}}
    <div id="modalKamera" class="hidden fixed inset-0 z-[999] bg-black/70 items-center justify-center p-4">
        <div class="w-full max-w-[320px] sm:max-w-sm bg-white rounded-2xl overflow-hidden shadow-2xl max-h-[92vh] flex flex-col">

            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 shrink-0">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Ambil Foto</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pastikan wajah terlihat jelas.</p>
                </div>
                <button type="button" id="btnTutupKamera"
                        class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-base shrink-0">
                    ×
                </button>
            </div>

            {{-- VIDEO + OVERLAY WATERMARK LIVE --}}
            <div class="relative bg-black shrink-0">
                <video id="videoKamera" autoplay playsinline muted
                       class="w-full aspect-[3/4] max-h-[55vh] object-cover"></video>
                <canvas id="canvasKamera" class="hidden"></canvas>

                {{-- PREVIEW WATERMARK LIVE (mengikuti layout canvas) --}}
                <div class="pointer-events-none absolute inset-x-0 bottom-0 pt-16 px-4 pb-3"
                     style="background: linear-gradient(to top, rgba(10,10,20,0.92) 0%, rgba(10,10,20,0.55) 45%, rgba(10,10,20,0) 100%);">

                    <p class="text-[11px] font-bold tracking-wide text-purple-300 leading-tight">
                        EASY SYSTEM <span class="text-purple-400/70">|</span> <span id="wmJenisLabel">ABSEN MASUK</span>
                    </p>

                    <p id="wmTanggalJam" class="mt-1 text-sm font-bold text-white leading-tight">
                        -- | --
                    </p>

                    <p id="wmAlamat" class="mt-1 text-[10px] font-medium text-slate-100 leading-snug">
                        {{ $kantorAlamat }}
                    </p>

                    <p id="wmJarak" class="mt-1 text-[11px] font-bold text-slate-100 leading-tight">
                        Jarak: --
                    </p>

                    <p id="wmGps" class="text-[10px] font-medium text-slate-300 leading-tight">
                        GPS: --
                    </p>

                </div>
            </div>

            <div class="p-3 shrink-0">
                <button type="button" id="btnAmbilKamera"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-xl text-sm font-semibold">
                    📷 Ambil Foto
                </button>
            </div>
        </div>
    </div>


    {{-- MODAL LIHAT FOTO FULLSCREEN --}}
    <div id="modalFoto" class="hidden fixed inset-0 z-[999] bg-black/90 items-center justify-center p-4"
         onclick="tutupModalFoto()">
        <div class="relative max-w-2xl w-full max-h-[90vh] flex flex-col"
             onclick="event.stopPropagation()">

            <button type="button" onclick="tutupModalFoto()"
                    class="absolute -top-12 right-0 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-2xl font-light transition">
                ×
            </button>

            <div class="rounded-2xl overflow-hidden bg-black shadow-2xl">
                <img id="modalFotoImg" src="" alt="Foto absensi" class="w-full max-h-[80vh] object-contain">
            </div>

            <div class="mt-4 flex justify-center">
                <a id="modalFotoDownload" href="" download="absensi.jpg"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold transition"
                   onclick="event.stopPropagation();">
                    ⬇ Unduh Foto
                </a>
            </div>
        </div>
    </div>


    {{-- JAVASCRIPT --}}
    <script>

        const OFFICE_LOKASI = {
            lat: {{ $kantorLatitude }},
            lng: {{ $kantorLongitude }},
            radius: {{ $radiusMaksimal }},
            alamat: {!! json_encode($kantorAlamat) !!}
        };

        document.addEventListener('DOMContentLoaded', function () {

            /* FORMAT JAM & TANGGAL */
            function formatJamTanggal(denganDetik) {
                const now = new Date();
                const options = { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit' };
                if (denganDetik) options.second = '2-digit';

                const jam = now.toLocaleTimeString('id-ID', options);
                const tanggal = now.toLocaleDateString('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
                });
                return { jam, tanggal };
            }

            function updateClock() {
                const { jam: jamHero, tanggal } = formatJamTanggal(true);
                const jamElement = document.getElementById('jam');
                const tanggalElement = document.getElementById('tanggal');
                if (jamElement) jamElement.textContent = jamHero;
                if (tanggalElement) tanggalElement.textContent = tanggal;

                // Update watermark preview kamera (kalau modal terbuka)
                if (modalKamera && !modalKamera.classList.contains('hidden')) {
                    updatePreviewWatermark();
                }
            }


            /* HITUNG JARAK */
            function hitungJarakMeter(lat1, lon1, lat2, lon2) {
                const R = 6371000;
                const toRad = (deg) => (deg * Math.PI) / 180;
                const dLat = toRad(lat2 - lat1);
                const dLon = toRad(lon2 - lon1);
                const a = Math.sin(dLat / 2) ** 2 +
                          Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                          Math.sin(dLon / 2) ** 2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }


            /* STATE LOKASI */
            const lokasiState = {
                masuk:  { lat: null, lng: null, jarak: null },
                pulang: { lat: null, lng: null, jarak: null }
            };


            /* VARIABEL KAMERA */
            const modalKamera    = document.getElementById('modalKamera');
            const videoKamera    = document.getElementById('videoKamera');
            const canvasKamera   = document.getElementById('canvasKamera');
            const btnAmbilKamera = document.getElementById('btnAmbilKamera');
            const btnTutupKamera = document.getElementById('btnTutupKamera');

            let streamKamera = null;
            let targetFoto = null;
            let jenisAktif = null;


            /* UPDATE PREVIEW WATERMARK (LIVE) */
            function updatePreviewWatermark() {
                if (!jenisAktif) return;

                const data = lokasiState[jenisAktif] || {};
                const { jam, tanggal } = formatJamTanggal(false);

                const wmJenisLabel = document.getElementById('wmJenisLabel');
                const wmTanggalJam = document.getElementById('wmTanggalJam');
                const wmAlamat     = document.getElementById('wmAlamat');
                const wmJarak      = document.getElementById('wmJarak');
                const wmGps        = document.getElementById('wmGps');

                if (wmJenisLabel) {
                    wmJenisLabel.textContent = jenisAktif === 'pulang' ? 'ABSEN PULANG' : 'ABSEN MASUK';
                }

                if (wmTanggalJam) {
                    wmTanggalJam.textContent = tanggal + ' | ' + jam;
                }

                if (wmAlamat) {
                    wmAlamat.textContent = OFFICE_LOKASI.alamat;
                }

                if (wmJarak) {
                    if (data.jarak !== null && data.jarak !== undefined) {
                        const dalam = data.jarak <= OFFICE_LOKASI.radius;
                        wmJarak.textContent = 'Jarak: ' + Math.round(data.jarak) + ' m dari kantor' +
                            (dalam ? ' (dalam radius)' : ' (di luar radius)');
                        wmJarak.style.color = dalam ? '#86efac' : '#fca5a5';
                    } else {
                        wmJarak.textContent = 'Jarak: --';
                        wmJarak.style.color = '#e5e7eb';
                    }
                }

                if (wmGps) {
                    if (data.lat !== null && data.lng !== null) {
                        wmGps.textContent = 'GPS: ' + Number(data.lat).toFixed(6) + ', ' + Number(data.lng).toFixed(6);
                    } else {
                        wmGps.textContent = 'GPS: --';
                    }
                }
            }


            /* BUKA / TUTUP KAMERA */
            async function bukaKamera(target, jenis) {
                targetFoto = target;
                jenisAktif = jenis;

                updatePreviewWatermark();

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Kamera tidak didukung oleh browser ini.');
                    return;
                }

                try {
                    streamKamera = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: { ideal: 720 }, height: { ideal: 960 } },
                        audio: false
                    });
                    videoKamera.srcObject = streamKamera;
                    modalKamera.classList.remove('hidden');
                    modalKamera.classList.add('flex');
                    updateClock();
                } catch (error) {
                    console.error(error);
                    alert('Kamera tidak dapat dibuka. Pastikan izin kamera sudah diberikan.');
                }
            }

            function tutupKamera() {
                if (streamKamera) {
                    streamKamera.getTracks().forEach((track) => track.stop());
                    streamKamera = null;
                }
                videoKamera.srcObject = null;
                modalKamera.classList.add('hidden');
                modalKamera.classList.remove('flex');
            }

            if (btnTutupKamera) btnTutupKamera.addEventListener('click', tutupKamera);


            /* MODAL LIHAT FOTO */
            window.bukaModalFoto = function(imageId) {
                const imgElement = document.getElementById(imageId);
                if (!imgElement || !imgElement.src) return;

                const modalImg = document.getElementById('modalFotoImg');
                const modalDl  = document.getElementById('modalFotoDownload');
                const modal    = document.getElementById('modalFoto');

                modalImg.src = imgElement.src;
                modalDl.href = imgElement.src;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            };

            window.tutupModalFoto = function() {
                const modal = document.getElementById('modalFoto');
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            };

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') window.tutupModalFoto();
            });


            /* =====================================================
               GAMBAR WATERMARK DI ATAS FOTO (FINAL)
               Format seperti contoh "SMART PPNPN"
            ===================================================== */
            function gambarWatermark(context, width, height, jenis) {
                const data = lokasiState[jenis] || {};
                const { jam, tanggal } = formatJamTanggal(false);

                const dalamRadius = data.jarak !== null && data.jarak <= OFFICE_LOKASI.radius;

                const gpsText = (data.lat !== null && data.lng !== null)
                    ? Number(data.lat).toFixed(6) + ', ' + Number(data.lng).toFixed(6)
                    : '--';

                const jarakText = (data.jarak !== null)
                    ? 'Jarak: ' + Math.round(data.jarak) + ' m dari kantor' + (dalamRadius ? ' (dalam radius)' : ' (di luar radius)')
                    : 'Jarak: --';

                const label = jenis === 'pulang' ? 'ABSEN PULANG' : 'ABSEN MASUK';

                // Ukuran area watermark (bawah)
                const barHeight = Math.max(180, Math.round(height * 0.35));
                const paddingX  = Math.round(width * 0.05);

                // Ukuran font
                const fontHeader = Math.max(13, Math.round(width * 0.032));
                const fontMain   = Math.max(17, Math.round(width * 0.042));
                const fontSub    = Math.max(12, Math.round(width * 0.028));
                const fontSmall  = Math.max(11, Math.round(width * 0.026));
                const lineGap    = Math.max(20, Math.round(width * 0.042));

                // Gradient gelap
                const gradient = context.createLinearGradient(0, height - barHeight, 0, height);
                gradient.addColorStop(0,    'rgba(10, 10, 20, 0)');
                gradient.addColorStop(0.30, 'rgba(10, 10, 20, 0.60)');
                gradient.addColorStop(1,    'rgba(10, 10, 20, 0.92)');

                context.fillStyle = gradient;
                context.fillRect(0, height - barHeight, width, barHeight);

                const maxWidth = width - (paddingX * 2);

                function fitText(text, font, maxW) {
                    context.font = font;
                    if (context.measureText(text).width <= maxW) return text;
                    let out = text;
                    while (out.length > 0 && context.measureText(out + '...').width > maxW) {
                        out = out.slice(0, -1);
                    }
                    return out + '...';
                }

                // ---- Mulai render ----
                let y = height - barHeight + lineGap * 1.0;
                context.textBaseline = 'alphabetic';

                // 1) HEADER: EASY SYSTEM | ABSEN MASUK
                context.fillStyle = '#c4b5fd';
                context.font = '700 ' + fontHeader + 'px Arial, sans-serif';
                context.fillText('EASY SYSTEM  |  ' + label, paddingX, y);

                // 2) TANGGAL | JAM
                y += lineGap;
                context.fillStyle = '#ffffff';
                context.font = '700 ' + fontMain + 'px Arial, sans-serif';
                context.fillText(
                    fitText(tanggal + ' | ' + jam, '700 ' + fontMain + 'px Arial, sans-serif', maxWidth),
                    paddingX, y
                );

                // 3) ALAMAT KANTOR
                y += lineGap * 0.95;
                context.fillStyle = '#f1f5f9';
                context.font = '500 ' + fontSmall + 'px Arial, sans-serif';
                context.fillText(
                    fitText(OFFICE_LOKASI.alamat, '500 ' + fontSmall + 'px Arial, sans-serif', maxWidth),
                    paddingX, y
                );

                // 4) JARAK (warna dinamis)
                y += lineGap * 0.9;
                context.fillStyle = dalamRadius ? '#86efac' : '#fca5a5';
                context.font = '700 ' + fontSub + 'px Arial, sans-serif';
                context.fillText(
                    fitText(jarakText, '700 ' + fontSub + 'px Arial, sans-serif', maxWidth),
                    paddingX, y
                );

                // 5) GPS
                y += lineGap * 0.85;
                context.fillStyle = '#cbd5e1';
                context.font = '500 ' + fontSmall + 'px Arial, sans-serif';
                context.fillText(
                    fitText('GPS: ' + gpsText, '500 ' + fontSmall + 'px Arial, sans-serif', maxWidth),
                    paddingX, y
                );
            }


            /* AMBIL FOTO DARI KAMERA */
            if (btnAmbilKamera) {
                btnAmbilKamera.addEventListener('click', function () {
                    if (!targetFoto) return;

                    const videoW = videoKamera.videoWidth;
                    const videoH = videoKamera.videoHeight;

                    if (!videoW || !videoH) {
                        alert('Kamera belum siap. Tunggu sebentar, lalu coba lagi.');
                        return;
                    }

                    const targetRatio = 3 / 4;
                    let cropW, cropH, cropX, cropY;
                    const videoRatio = videoW / videoH;

                    if (videoRatio > targetRatio) {
                        cropH = videoH;
                        cropW = Math.round(videoH * targetRatio);
                        cropX = Math.round((videoW - cropW) / 2);
                        cropY = 0;
                    } else {
                        cropW = videoW;
                        cropH = Math.round(videoW / targetRatio);
                        cropX = 0;
                        cropY = Math.round((videoH - cropH) / 2);
                    }

                    canvasKamera.width  = cropW;
                    canvasKamera.height = cropH;

                    const context = canvasKamera.getContext('2d');
                    context.drawImage(videoKamera, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);

                    gambarWatermark(context, cropW, cropH, jenisAktif);

                    canvasKamera.toBlob(function (blob) {
                        if (!blob) { alert('Foto gagal diambil.'); return; }

                        const file = new File([blob], 'absensi-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        targetFoto.files = dataTransfer.files;

                        const preview    = targetFoto.dataset.preview;
                        const image      = targetFoto.dataset.image;
                        const ulang      = targetFoto.dataset.ulang;
                        const keterangan = targetFoto.dataset.keterangan;

                        if (preview && image) {
                            document.getElementById(image).src = URL.createObjectURL(file);
                            document.getElementById(preview).classList.remove('hidden');
                        }
                        if (ulang) document.getElementById(ulang).classList.remove('hidden');
                        if (keterangan) document.getElementById(keterangan).textContent =
                            'Foto berhasil diambil (watermark: waktu, lokasi & jarak).';

                        tutupKamera();

                        if (jenisAktif === 'masuk')  cekFormMasuk();
                        if (jenisAktif === 'pulang') cekFormPulang();
                    }, 'image/jpeg', 0.92);
                });
            }


            /* SETUP FOTO MASUK & PULANG */
            const fotoMasuk         = document.getElementById('foto_masuk');
            const btnFotoMasuk      = document.getElementById('btnFotoMasuk');
            const btnFotoUlangMasuk = document.getElementById('btnFotoUlangMasuk');
            const fotoPulang         = document.getElementById('foto_pulang');
            const btnFotoPulang      = document.getElementById('btnFotoPulang');
            const btnFotoUlangPulang = document.getElementById('btnFotoUlangPulang');

            if (fotoMasuk) {
                fotoMasuk.dataset.preview    = 'previewFotoMasuk';
                fotoMasuk.dataset.image      = 'gambarMasuk';
                fotoMasuk.dataset.ulang      = 'btnFotoUlangMasuk';
                fotoMasuk.dataset.keterangan = 'keteranganFotoMasuk';
            }
            if (fotoPulang) {
                fotoPulang.dataset.preview    = 'previewFotoPulang';
                fotoPulang.dataset.image      = 'gambarPulang';
                fotoPulang.dataset.ulang      = 'btnFotoUlangPulang';
                fotoPulang.dataset.keterangan = 'keteranganFotoPulang';
            }

            if (btnFotoMasuk) btnFotoMasuk.addEventListener('click', () => bukaKamera(fotoMasuk, 'masuk'));
            if (btnFotoUlangMasuk) btnFotoUlangMasuk.addEventListener('click', () => bukaKamera(fotoMasuk, 'masuk'));
            if (btnFotoPulang) btnFotoPulang.addEventListener('click', () => bukaKamera(fotoPulang, 'pulang'));
            if (btnFotoUlangPulang) btnFotoUlangPulang.addEventListener('click', () => bukaKamera(fotoPulang, 'pulang'));


            /* AMBIL LOKASI */
            function ambilLokasi(latitudeInput, longitudeInput, statusElement, button, jenis, btnFoto) {
                return new Promise(function (resolve, reject) {
                    if (!navigator.geolocation) { alert('Browser kamu tidak mendukung GPS.'); reject(); return; }

                    button.disabled = true;
                    const textAsli = button.innerHTML;
                    button.innerHTML = '📍 Mengambil lokasi...';

                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;

                            document.getElementById(latitudeInput).value = latitude;
                            document.getElementById(longitudeInput).value = longitude;

                            const jarak = hitungJarakMeter(OFFICE_LOKASI.lat, OFFICE_LOKASI.lng, latitude, longitude);
                            lokasiState[jenis] = { lat: latitude, lng: longitude, jarak: jarak };

                            const dalamRadius = jarak <= OFFICE_LOKASI.radius;

                            statusElement.innerHTML =
                                '<span>' + (dalamRadius ? '🟢' : '🔴') + '</span>' +
                                '<span>Jarak dari kantor: ~' + Math.round(jarak) + ' meter<br>' +
                                '<span class="text-xs ' + (dalamRadius ? 'text-emerald-500' : 'text-red-500') + '">' +
                                (dalamRadius ? 'Dalam radius kantor (maksimal ' + OFFICE_LOKASI.radius + ' m)'
                                             : 'Di luar radius kantor (maksimal ' + OFFICE_LOKASI.radius + ' m)') +
                                '</span></span>';

                            button.disabled = false;
                            button.innerHTML = '↻ Ambil Ulang Lokasi';
                            if (btnFoto) btnFoto.disabled = false;

                            // Update preview watermark kalau modal terbuka
                            if (jenisAktif === jenis) updatePreviewWatermark();

                            resolve(jarak);
                        },
                        function (error) {
                            button.disabled = false;
                            button.innerHTML = textAsli;
                            if (error.code === 1) alert('Akses lokasi ditolak. Silakan izinkan lokasi pada browser.');
                            else if (error.code === 2) alert('Lokasi tidak dapat ditemukan. Pastikan GPS/lokasi perangkat aktif.');
                            else if (error.code === 3) alert('Waktu pengambilan lokasi habis. Silakan coba lagi.');
                            else alert('Gagal mengambil lokasi.');
                            reject();
                        },
                        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                    );
                });
            }

            const btnLokasiMasuk = document.getElementById('btnLokasiMasuk');
            if (btnLokasiMasuk) {
                btnLokasiMasuk.addEventListener('click', function () {
                    ambilLokasi('latitudeMasuk', 'longitudeMasuk',
                                document.getElementById('statusLokasiMasuk'),
                                btnLokasiMasuk, 'masuk', btnFotoMasuk)
                        .then(function () { cekFormMasuk(); });
                });
            }

            const btnLokasiPulang = document.getElementById('btnLokasiPulang');
            if (btnLokasiPulang) {
                btnLokasiPulang.addEventListener('click', function () {
                    ambilLokasi('latitudePulang', 'longitudePulang',
                                document.getElementById('statusLokasiPulang'),
                                btnLokasiPulang, 'pulang', btnFotoPulang)
                        .then(function () { cekFormPulang(); });
                });
            }


            /* CEK FORM */
            function cekFormMasuk() {
                const shift     = document.getElementById('shift')?.value;
                const latitude  = document.getElementById('latitudeMasuk')?.value;
                const longitude = document.getElementById('longitudeMasuk')?.value;
                const foto      = document.getElementById('foto_masuk')?.files.length;
                const button    = document.getElementById('btnAbsenMasuk');
                if (button) button.disabled = !(shift && latitude && longitude && foto);
            }

            function cekFormPulang() {
                const latitude  = document.getElementById('latitudePulang')?.value;
                const longitude = document.getElementById('longitudePulang')?.value;
                const foto      = document.getElementById('foto_pulang')?.files.length;
                const button    = document.getElementById('btnAbsenPulang');
                if (button) button.disabled = !(latitude && longitude && foto);
            }

            const shift = document.getElementById('shift');
            if (shift) shift.addEventListener('change', cekFormMasuk);
            if (fotoMasuk)  fotoMasuk.addEventListener('change', cekFormMasuk);
            if (fotoPulang) fotoPulang.addEventListener('change', cekFormPulang);


            /* SUBMIT MASUK */
            const formMasuk = document.getElementById('formAbsenMasuk');
            if (formMasuk) {
                formMasuk.addEventListener('submit', function (event) {
                    const latitude  = document.getElementById('latitudeMasuk').value;
                    const longitude = document.getElementById('longitudeMasuk').value;
                    const foto      = document.getElementById('foto_masuk').files.length;

                    if (!latitude || !longitude) { event.preventDefault(); alert('Silakan ambil lokasi terlebih dahulu.'); return; }
                    if (!foto) { event.preventDefault(); alert('Silakan ambil foto terlebih dahulu.'); return; }

                    const button = document.getElementById('btnAbsenMasuk');
                    button.disabled = true;
                    button.innerHTML = '⏳ Menyimpan absensi...';
                });
            }


            /* SUBMIT PULANG */
            const formPulang = document.getElementById('formAbsenPulang');
            if (formPulang) {
                formPulang.addEventListener('submit', function (event) {
                    const latitude  = document.getElementById('latitudePulang').value;
                    const longitude = document.getElementById('longitudePulang').value;
                    const foto      = document.getElementById('foto_pulang').files.length;

                    if (!latitude || !longitude) { event.preventDefault(); alert('Silakan ambil lokasi terlebih dahulu.'); return; }
                    if (!foto) { event.preventDefault(); alert('Silakan ambil foto terlebih dahulu.'); return; }

                    const button = document.getElementById('btnAbsenPulang');
                    button.disabled = true;
                    button.innerHTML = '⏳ Menyimpan absensi...';
                });
            }


            updateClock();
            setInterval(updateClock, 1000);

        });

    </script>

@endsection