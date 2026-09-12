@extends('layouts.utama')
@section('title', 'Dashboard PPNPN')
@section('content')

{{-- ================= HEADER ================= --}}
<div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
    <div>
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
            Halo, {{ auth()->user()->name }} 👋
        </h2>
        <p class="text-sm text-slate-500 mt-1">
            Berikut adalah ringkasan aktivitas dan administrasi Anda hari ini.
        </p>
    </div>
    
    <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-slate-200 text-sm text-slate-600 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Sistem Online
        </div>
    </div>
</div>

{{-- ================= HERO CLOCK & QUICK ACTION ================= --}}
<div class="dashboard-hero mb-6 overflow-hidden relative rounded-3xl bg-gradient-to-r from-purple-700 to-purple-500 shadow-lg">
    {{-- Dekorasi Background --}}
    <div class="absolute -right-20 -top-28 w-80 h-80 rounded-full bg-white/10 blur-2xl"></div>
    <div class="absolute -left-24 -bottom-32 w-80 h-80 rounded-full bg-white/5 blur-2xl"></div>

    <div class="relative z-10 p-6 sm:p-8 lg:p-10">
        <div class="grid lg:grid-cols-[1fr_auto] items-center gap-8">
            
            {{-- WAKTU --}}
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <p id="tanggal" class="text-purple-100 text-sm font-medium tracking-wide"></p>
                </div>
                <div id="jam" class="text-5xl sm:text-6xl font-extrabold text-white tracking-tight">
                    00:00:00
                </div>
                <p class="mt-2 text-purple-200 text-sm">
                    Waktu Indonesia Barat (WIB)
                </p>
            </div>

            {{-- QUICK ACTION (ABSEN) --}}
            <div class="flex flex-col sm:flex-row lg:flex-col gap-3 lg:items-end">
                {{-- Kondisional: Jika belum absen masuk, tampilkan tombol ini --}}
                <a href="{{ url('/absensi/masuk') }}" class="inline-flex items-center justify-center gap-2 bg-white text-purple-700 px-6 py-3.5 rounded-xl font-bold shadow-md hover:-translate-y-0.5 hover:bg-purple-50 transition text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    Absen Masuk
                </a>
                {{-- Kondisional: Jika sudah absen masuk tapi belum pulang --}}
                <!-- <a href="{{ url('/absensi/pulang') }}" class="inline-flex items-center justify-center gap-2 bg-purple-800/80 backdrop-blur-sm border border-purple-400/30 text-white px-6 py-3.5 rounded-xl font-bold shadow-md hover:bg-purple-800 transition text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                    Absen Pulang
                </a> -->
            </div>

        </div>
    </div>
</div>

{{-- ================= STATISTIK PRIBADI ================= --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    
    {{-- KEHADIRAN BULAN INI --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Kehadiran Bulan Ini</p>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-emerald-600">{{ $jumlahKehadiran ?? '18' }}</span>
                    <span class="text-sm text-slate-400">hari</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-3">Tepat waktu & terlambat</p>
    </div>

    {{-- SISA CUTI --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Sisa Cuti</p>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-purple-600">{{ $sisaCuti ?? '6' }}</span>
                    <span class="text-sm text-slate-400">hari</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 9h16.5M5.25 5.25h13.5A1.5 1.5 0 0120.25 6.75v11.5a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V6.75a1.5 1.5 0 011.5-1.5z" /></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-3">Jatah cuti tahun berjalan</p>
    </div>

    {{-- LEMBUR BULAN INI --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Lembur Bulan Ini</p>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-amber-500">{{ $totalLembur ?? '12' }}</span>
                    <span class="text-sm text-slate-400">jam</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-3">Total jam lembur disetujui</p>
    </div>

    {{-- STATUS HARI INI --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Status Hari Ini</p>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-xl font-extrabold text-slate-800">Belum Absen</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3h.008v.008H12v-.008zM10.29 3.86l-7.5 13A1.5 1.5 0 004.09 19h15.82a1.5 1.5 0 001.3-2.25l-7.5-13a1.5 1.5 0 00-2.6 0z" /></svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-3">Silakan lakukan absensi</p>
    </div>
</div>

{{-- ================= KONTEN UTAMA (2 KOLOM) ================= --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- KOLOM KIRI (2/3): RIWAYAT KEHADIRAN --}}
    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800">Riwayat Kehadiran Terbaru</h3>
                    <p class="text-xs text-slate-400 mt-1">5 aktivitas absensi terakhir Anda.</p>
                </div>
                <a href="#" class="text-sm font-semibold text-purple-600 hover:text-purple-700">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 font-semibold">Jam Masuk</th>
                            <th class="px-5 py-3 font-semibold">Jam Keluar</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        {{-- Contoh Data Statis (Ganti dengan Looping Laravel) --}}
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 font-medium text-slate-800">11 Sep 2026</td>
                            <td class="px-5 py-3 text-slate-600">07:55</td>
                            <td class="px-5 py-3 text-slate-600">17:05</td>
                            <td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold">Tepat Waktu</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 font-medium text-slate-800">10 Sep 2026</td>
                            <td class="px-5 py-3 text-slate-600">08:15</td>
                            <td class="px-5 py-3 text-slate-600">17:00</td>
                            <td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-semibold">Terlambat</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 font-medium text-slate-800">09 Sep 2026</td>
                            <td class="px-5 py-3 text-slate-600">07:45</td>
                            <td class="px-5 py-3 text-slate-600">17:00</td>
                            <td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold">Tepat Waktu</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 font-medium text-slate-800">08 Sep 2026</td>
                            <td class="px-5 py-3 text-slate-600">-</td>
                            <td class="px-5 py-3 text-slate-600">-</td>
                            <td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">Cuti</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 font-medium text-slate-800">07 Sep 2026</td>
                            <td class="px-5 py-3 text-slate-600">07:50</td>
                            <td class="px-5 py-3 text-slate-600">17:02</td>
                            <td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold">Tepat Waktu</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- BANNER INFORMASI --}}
        <div class="bg-gradient-to-r from-purple-50 to-white rounded-2xl border border-purple-100 p-5 flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
            </div>
            <div>
                <h4 class="font-bold text-purple-800 text-sm mb-1">Informasi Penting</h4>
                <p class="text-xs text-purple-600 leading-relaxed">
                    Jangan lupa untuk melakukan absen pulang sebelum pukul 17:00 WIB. Jika Anda lupa melakukan absen, silakan ajukan formulir "Lupa Absen" di menu Pengajuan.
                </p>
            </div>
        </div>

    </div>

    {{-- KOLOM KANAN (1/3): PENGAJUAN SAYA --}}
    <div class="space-y-6">
        
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800">Pengajuan Saya</h3>
                <a href="#" class="text-xs font-semibold text-purple-600 hover:text-purple-700 bg-purple-50 px-2.5 py-1 rounded-md">+ Buat Baru</a>
            </div>

            <div class="space-y-3">
                {{-- ITEM PENGAJUAN 1 --}}
                <div class="p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition cursor-pointer group">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 shrink-0 rounded-lg bg-red-50 text-red-500 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3h.008v.008H12v-.008zM10.29 3.86l-7.5 13A1.5 1.5 0 004.09 19h15.82a1.5 1.5 0 001.3-2.25l-7.5-13a1.5 1.5 0 00-2.6 0z" /></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-800">Lupa Absen</p>
                            <p class="text-xs text-slate-500 mt-0.5">10 Sep 2026 · Perbaikan Absensi</p>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded-full bg-red-50 text-red-600 text-[10px] font-bold">Menunggu Approval</span>
                        </div>
                    </div>
                </div>

                {{-- ITEM PENGAJUAN 2 --}}
                <div class="p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition cursor-pointer group">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 shrink-0 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-800">Lembur</p>
                            <p class="text-xs text-slate-500 mt-0.5">9 Sep 2026 · 2 Jam</p>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold">Menunggu Approval</span>
                        </div>
                    </div>
                </div>

                {{-- ITEM PENGAJUAN 3 --}}
                <div class="p-3 rounded-xl border border-slate-100 hover:border-purple-200 hover:bg-purple-50/50 transition cursor-pointer group">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 shrink-0 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-500 group-hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-7.5a2.25 2.25 0 00-2.25-2.25h-10.5A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25h6.75M16.5 19.5l2.25 2.25L22.5 18" /></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-800">Surat Lainnya</p>
                            <p class="text-xs text-slate-500 mt-0.5">8 Sep 2026 · Surat Keterangan</p>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold">Disetujui</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="#" class="block text-center text-sm font-semibold text-purple-600 hover:text-purple-700 mt-4 pt-4 border-t border-slate-100">
                Lihat Semua Pengajuan
            </a>
        </div>

    </div>
</div>

{{-- ================= JAM REALTIME ================= --}}
<script>
    function updateClock() {
        const now = new Date();
        const jam = now.toLocaleTimeString('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        }).replace(/\./g, ':'); // Memastikan format jam menggunakan titik dua

        const tanggal = now.toLocaleDateString('id-ID', {
            timeZone: 'Asia/Jakarta',
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        document.getElementById('jam').textContent = jam;
        document.getElementById('tanggal').textContent = tanggal;
    }

    updateClock();
    setInterval(updateClock, 1000);
</script>

@endsection