@extends('layouts.utama')

@section('title', 'Lupa Absen')

@section('content')

{{-- ========================================================= --}}
{{-- HEADER
-- ========================================================= --}}

<div class="mb-7">
    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
        Lupa Absen
    </h2>
    <p class="mt-2 text-slate-500">
        Perbaiki catatan absensi apabila kamu lupa melakukan absen.
    </p>
</div>


{{-- ========================================================= --}}
{{-- PESAN
-- ========================================================= --}}

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
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{{-- ========================================================= --}}
{{-- LAYOUT 2 KOLOM
     - Desktop: KIRI (form + riwayat) · KANAN (alur, sticky)
     - Mobile : FORM → RIWAYAT → ALUR (paling bawah)
-- ========================================================= --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    {{-- =====================================================
         KOLOM KIRI (2/3): FORM + RIWAYAT
    ====================================================== --}}
    <div class="lg:col-span-2 space-y-5 order-1">


        {{-- =================================================
             FORM PERBAIKAN ABSEN
        ================================================== --}}
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm">

            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                        <circle cx="12" cy="12" r="9"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Perbaiki Absensi</h3>
                    <p class="text-sm text-slate-400 mt-1">Isi informasi absensi yang terlewat dengan benar.</p>
                </div>
            </div>

            <form action="{{ route('lupa-absen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- TANGGAL --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal') }}"
                               max="{{ now()->toDateString() }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    </div>

                    {{-- JENIS ABSEN --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Absensi</label>
                        <select name="jenis_absen" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                            <option value="">Pilih jenis absensi</option>
                            <option value="masuk"  {{ old('jenis_absen') === 'masuk'  ? 'selected' : '' }}>Absen Masuk</option>
                            <option value="pulang" {{ old('jenis_absen') === 'pulang' ? 'selected' : '' }}>Absen Pulang</option>
                        </select>
                    </div>

                    {{-- JAM YANG SEHARUSNYA --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Jam yang Seharusnya
                            <span class="text-xs text-slate-400 font-normal">(jam yang benar)</span>
                        </label>
                        <input type="time" name="jam" value="{{ old('jam') }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    </div>

                    {{-- BUKTI --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Bukti
                            <span class="text-xs text-slate-400 font-normal"> </span>
                        </label>
                        <input type="file" name="bukti" accept=".pdf,.jpg,.jpeg,.png,.webp"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-purple-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-purple-600 hover:file:bg-purple-100">
                    </div>

                    {{-- ALASAN --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan</label>
                        <textarea name="alasan" rows="4" required
                                  placeholder="Contoh: Lupa absen masuk karena langsung mengikuti kegiatan..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">{{ old('alasan') }}</textarea>
                    </div>

                </div>

                <div class="mt-7 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-xs text-slate-400">
                        Pastikan tanggal dan jam absensi sudah sesuai.
                    </p>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-purple-600 text-white text-sm font-bold shadow-lg shadow-purple-200 hover:bg-purple-700 hover:-translate-y-0.5 transition">
                        <span>✓</span>
                        Simpan Perbaikan
                    </button>
                </div>
            </form>

        </div>


        {{-- =================================================
             RIWAYAT PERBAIKAN
        ================================================== --}}
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Riwayat Perbaikan</h3>
                    <p class="text-sm text-slate-400 mt-1">Daftar perbaikan absensi yang pernah diajukan.</p>
                </div>
                <span class="inline-flex w-fit px-3 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">
                    {{ $riwayatLupaAbsen->count() }} pengajuan
                </span>
            </div>

            @forelse($riwayatLupaAbsen as $item)

                <div class="rounded-2xl border p-5 mb-3
                            {{ $item->status === 'pending'
                                ? 'border-amber-200 bg-amber-50/50'
                                : ($item->status === 'approved'
                                    ? 'border-emerald-100 bg-emerald-50/30'
                                    : 'border-red-100 bg-red-50/30') }}">

                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                        <div class="flex-1">

                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-slate-800">
                                    {{ $item->tanggal->translatedFormat('d F Y') }}
                                </p>

                                @if($item->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-[11px] font-semibold">
                                        ⏳ Menunggu Approval
                                    </span>
                                @elseif($item->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-semibold">
                                        ✓ Disetujui
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-[11px] font-semibold">
                                        ✕ Ditolak
                                    </span>
                                @endif
                            </div>

                            {{-- PERBANDINGAN JAM --}}
                            <div class="mt-3 rounded-xl bg-white border border-slate-100 p-3 text-sm">
                                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">
                                    {{ ucfirst($item->jenis_absen) }}
                                </span>

                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                    @php
                                        $jamAsli = $item->jenis_absen === 'masuk'
                                            ? optional($item->absensi)->jam_masuk
                                            : optional($item->absensi)->jam_pulang;
                                    @endphp

                                    <span class="text-slate-400">Tercatat:</span>
                                    <span class="font-mono text-slate-600">
                                        {{ $jamAsli ? substr($jamAsli, 0, 5) : '—' }}
                                    </span>

                                    <span class="text-slate-400 mx-1">→</span>

                                    <span class="text-slate-400">Diminta:</span>
                                    <span class="font-mono font-bold text-purple-600">
                                        {{ substr($item->jam, 0, 5) }}
                                    </span>
                                </div>
                            </div>

                            {{-- ALASAN --}}
                            <div class="mt-3">
                                <p class="text-xs font-medium text-slate-400 mb-1">Alasan</p>
                                <p class="text-sm text-slate-600">{{ $item->alasan }}</p>
                            </div>

                            {{-- CATATAN ADMIN --}}
                            @if($item->catatan_admin)
                                <div class="mt-3 rounded-xl bg-white border border-slate-100 p-3">
                                    <p class="text-xs font-medium text-slate-400 mb-1">Catatan Admin</p>
                                    <p class="text-sm text-slate-600">{{ $item->catatan_admin }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- BUKTI --}}
                        @if($item->bukti)
                            <a href="{{ asset('storage/' . $item->bukti) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-purple-50 text-purple-600 text-xs font-semibold hover:bg-purple-100 transition shrink-0">
                                👁️ Lihat Bukti
                            </a>
                        @endif

                    </div>

                </div>

            @empty

                <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-12 text-center">
                    <p class="text-sm font-semibold text-slate-500">Belum ada pengajuan perbaikan</p>
                    <p class="text-xs text-slate-400 mt-1">Perbaikan absensi kamu akan muncul di sini.</p>
                </div>

            @endforelse

        </div>

    </div>


    {{-- =====================================================
         KOLOM KANAN (1/3): ALUR PERBAIKAN ABSEN
         Mobile: order-2 → tampil paling bawah
    ====================================================== --}}
    <div class="lg:col-span-1 order-2">
        <div class="lg:sticky lg:top-6 rounded-2xl border border-purple-100 bg-purple-50/70 p-5 sm:p-6">

            <div class="flex items-start gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-purple-900 text-sm">Alur Perbaikan Absen</p>
                    <p class="text-xs text-purple-700 mt-0.5">Mohon dibaca sebelum mengajukan.</p>
                </div>
            </div>

            <ol class="space-y-3 text-sm text-purple-800">
                <li class="flex gap-3">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">1</span>
                    <span>Lakukan absen masuk/pulang seperti biasa (walaupun jamnya salah).</span>
                </li>
                <li class="flex gap-3">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">2</span>
                    <span>Ajukan perbaikan di form ini dengan jam yang seharusnya.</span>
                </li>
                <li class="flex gap-3">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">3</span>
                    <span>Admin akan verifikasi. Selama menunggu, absensi ditandai <span class="font-semibold text-amber-700">pending</span>.</span>
                </li>
                <li class="flex gap-3">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">4</span>
                    <span>Setelah disetujui, jam di rekap otomatis berubah.</span>
                </li>
            </ol>

            {{-- Placeholder SOP tambahan --}}
            <div class="mt-4 pt-4 border-t border-purple-200/60">
                <p class="text-xs text-purple-600 italic">
                    * SOP tambahan sesuai kebijakan kantor dapat ditambahkan di sini.
                </p>
            </div>

        </div>
    </div>

</div>

@endsection