@extends('layouts.utama')

@section('title', 'Pengajuan')

@section('content')

{{-- =====================================================
    HEADER
===================================================== --}}

<div class="mb-6">

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

        <div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Pengajuan
            </h2>
            <p class="mt-2 text-sm sm:text-base text-slate-500">
                Kelola dan periksa pengajuan administrasi PPNPN.
            </p>
        </div>

        @if($jumlahPending > 0)
            <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm font-semibold">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                {{ $jumlahPending }} pengajuan menunggu
            </div>
        @endif

    </div>

</div>


{{-- PESAN --}}
@if(session('success'))
    <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        {{ session('error') }}
    </div>
@endif


{{-- =====================================================
    RINGKASAN (3 CARD)
===================================================== --}}

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">

    {{-- LUPA ABSEN --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Lupa Absen</p>
                <p class="text-3xl font-extrabold text-red-500 mt-2">{{ $countLupaAbsen }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Menunggu approval</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L2.82 17a2 2 0 001.74 3h14.88a2 2 0 001.74-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- LEMBUR --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Lembur</p>
                <p class="text-3xl font-extrabold text-amber-500 mt-2">{{ $countLembur }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Menunggu approval</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                    <circle cx="12" cy="12" r="8.5"/>
                    <path stroke-linecap="round" d="M12 7v5l3 2"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- SURAT --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Surat Lainnya</p>
                <p class="text-3xl font-extrabold text-purple-600 mt-2">{{ $countSurat }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Tercatat</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h8.25L19.5 9v11.25A1.75 1.75 0 0117.75 22h-11A1.75 1.75 0 015 20.25V5.5A1.75 1.75 0 016.75 3.75z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75V9h5.25"/>
                </svg>
            </div>
        </div>
    </div>

</div>


{{-- =====================================================
    DAFTAR PENGAJUAN
===================================================== --}}

<div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h3 class="font-bold text-lg text-slate-900">Daftar Pengajuan</h3>
            <p class="text-sm text-slate-400 mt-1">Periksa pengajuan yang masuk dari PPNPN.</p>
        </div>
    </div>

    {{-- TAB NAVIGATION (URL-based) --}}
    <div class="flex items-center gap-2 border-b border-slate-100 mb-5 overflow-x-auto">

        <a href="{{ url('/pengajuan?tab=lembur') }}"
           class="px-4 py-3 text-sm font-semibold whitespace-nowrap border-b-2 transition
                  {{ $tab === 'lembur'
                      ? 'border-purple-600 text-purple-600'
                      : 'border-transparent text-slate-400 hover:text-purple-600' }}">
            Lembur
            <span class="ml-1.5 px-2 py-0.5 rounded-full bg-amber-50 text-amber-500 text-xs">{{ $countLembur }}</span>
        </a>

        <a href="{{ url('/pengajuan?tab=lupa-absen') }}"
           class="px-4 py-3 text-sm font-semibold whitespace-nowrap border-b-2 transition
                  {{ $tab === 'lupa-absen'
                      ? 'border-purple-600 text-purple-600'
                      : 'border-transparent text-slate-400 hover:text-purple-600' }}">
            Lupa Absen
            <span class="ml-1.5 px-2 py-0.5 rounded-full bg-red-50 text-red-500 text-xs">{{ $countLupaAbsen }}</span>
        </a>

        <a href="{{ url('/pengajuan?tab=surat') }}"
           class="px-4 py-3 text-sm font-semibold whitespace-nowrap border-b-2 transition
                  {{ $tab === 'surat'
                      ? 'border-purple-600 text-purple-600'
                      : 'border-transparent text-slate-400 hover:text-purple-600' }}">
            Surat Lainnya
            <span class="ml-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-xs">{{ $countSurat }}</span>
        </a>

        <a href="{{ url('/pengajuan?tab=cuti') }}"
           class="px-4 py-3 text-sm font-semibold whitespace-nowrap border-b-2 transition
                  {{ $tab === 'cuti'
                      ? 'border-purple-600 text-purple-600'
                      : 'border-transparent text-slate-400 hover:text-purple-600' }}">
            Manajemen Cuti
        </a>

    </div>


    {{-- =====================================================
        TAB: LEMBUR
    ====================================================== --}}
    @if($tab === 'lembur')

        {{-- PENDING --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-base text-slate-900">Menunggu Approval</h4>
                <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">
                    {{ $pending->count() }} pengajuan
                </span>
            </div>

            @forelse($pending as $item)
                <div class="rounded-2xl border border-amber-200 bg-amber-50/40 p-4 mb-3">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                        <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                <circle cx="12" cy="12" r="8.5"/>
                                <path stroke-linecap="round" d="M12 7v5l3 2"/>
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800">{{ $item->user->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $item->tanggal->translatedFormat('d F Y') }} ·
                                {{ substr($item->jam_mulai, 0, 5) }}–{{ substr($item->jam_selesai, 0, 5) }}
                                @if($item->total_jam)
                                    <span class="font-semibold text-purple-600">({{ $item->total_jam }} jam)</span>
                                @endif
                            </p>
                            <p class="text-sm text-slate-600 mt-2">
                                <strong>Kegiatan:</strong> {{ $item->kegiatan }}
                            </p>

                            @if($item->foto)
                                <a href="{{ asset('storage/' . $item->foto) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-purple-600 hover:text-purple-700">
                                    👁️ Lihat Foto Bukti
                                </a>
                            @endif
                        </div>

                        <div class="flex flex-col gap-2 w-full lg:w-56 shrink-0">
                            <form action="{{ route('lembur.approve', $item) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-purple-600 text-white text-sm font-semibold hover:bg-purple-700 transition">
                                    Setujui
                                </button>
                            </form>
                            <form action="{{ route('lembur.reject', $item) }}" method="POST"
                                  onsubmit="return confirm('Tolak pengajuan lembur ini?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-white border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition">
                                    Tolak
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-8 text-center">
                    <p class="text-sm text-slate-500">Tidak ada pengajuan lembur yang menunggu</p>
                </div>
            @endforelse
        </div>


        {{-- RIWAYAT --}}
        <div>
            <h4 class="font-bold text-base text-slate-900 mb-4">Riwayat Approval</h4>

            @forelse($riwayat as $item)
                <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                    <div>
                        <p class="font-semibold text-sm text-slate-900">{{ $item->user->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500">
                            {{ $item->tanggal->translatedFormat('d/m/Y') }} · {{ $item->total_jam }} jam
                        </p>
                        @if($item->catatan_admin)
                            <p class="text-xs text-slate-400 mt-1">📝 {{ $item->catatan_admin }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        @if($item->status_approval === 'approved')
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Disetujui</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold">Ditolak</span>
                        @endif
                        <p class="text-[11px] text-slate-400 mt-1">
                            oleh {{ $item->approver->name ?? '—' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-8 text-center">
                    <p class="text-sm text-slate-500">Belum ada riwayat</p>
                </div>
            @endforelse
        </div>

    @endif


    {{-- =====================================================
        TAB: LUPA ABSEN
    ====================================================== --}}
    @if($tab === 'lupa-absen')

        {{-- PENDING --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-base text-slate-900">Menunggu Approval</h4>
                <span class="px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold">
                    {{ $pending->count() }} pengajuan
                </span>
            </div>

            @forelse($pending as $item)
                <div class="rounded-2xl border border-red-200 bg-red-50/40 p-4 mb-3">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                        <div class="w-11 h-11 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L2.82 17a2 2 0 001.74 3h14.88a2 2 0 001.74-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-slate-800">{{ $item->user->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $item->tanggal->translatedFormat('d F Y') }} ·
                                Perbaikan absen <strong>{{ ucfirst($item->jenis_absen) }}</strong>
                            </p>

                            <div class="mt-3 rounded-xl bg-white border border-slate-100 p-3 text-sm">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-slate-400">Tercatat:</span>
                                    <span class="font-mono text-slate-600">
                                        {{ $item->jam_asli ? substr($item->jam_asli, 0, 5) : '—' }}
                                    </span>
                                    <span class="text-slate-400 mx-1">→</span>
                                    <span class="text-slate-400">Minta:</span>
                                    <span class="font-mono font-bold text-purple-600">
                                        {{ substr($item->jam, 0, 5) }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-sm text-slate-600 mt-2">
                                <strong>Alasan:</strong> {{ $item->alasan }}
                            </p>

                            @if($item->bukti)
                                <a href="{{ asset('storage/' . $item->bukti) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-purple-600 hover:text-purple-700">
                                    👁️ Lihat Bukti
                                </a>
                            @endif
                        </div>

                        <div class="flex flex-col gap-2 w-full lg:w-56 shrink-0">
                            <form action="{{ route('lupa-absen.approve', $item) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-purple-600 text-white text-sm font-semibold hover:bg-purple-700 transition">
                                    Setujui & Update Jam
                                </button>
                            </form>
                            <form action="{{ route('lupa-absen.reject', $item) }}" method="POST"
                                  onsubmit="return confirm('Tolak pengajuan perbaikan absen ini?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-white border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition">
                                    Tolak
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-8 text-center">
                    <p class="text-sm text-slate-500">Tidak ada pengajuan perbaikan absen yang menunggu</p>
                </div>
            @endforelse
        </div>


        {{-- RIWAYAT --}}
        <div>
            <h4 class="font-bold text-base text-slate-900 mb-4">Riwayat</h4>

            @forelse($riwayat as $item)
                <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                    <div>
                        <p class="font-semibold text-sm text-slate-900">{{ $item->user->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500">
                            {{ $item->tanggal->translatedFormat('d/m/Y') }} · {{ ucfirst($item->jenis_absen) }} ·
                            {{ substr($item->jam, 0, 5) }}
                        </p>
                    </div>
                    <div class="text-right">
                        @if($item->status === 'approved')
                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Disetujui</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold">Ditolak</span>
                        @endif
                        <p class="text-[11px] text-slate-400 mt-1">
                            oleh {{ $item->approver->name ?? '—' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-8 text-center">
                    <p class="text-sm text-slate-500">Belum ada riwayat</p>
                </div>
            @endforelse
        </div>

    @endif


    {{-- =====================================================
        TAB: SURAT
    ====================================================== --}}
    @if($tab === 'surat')

        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-base text-slate-900">Daftar Surat Lainnya</h4>
                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                    {{ $pending->count() }} surat
                </span>
            </div>

            @forelse($pending as $item)
                <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 mb-3">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4">

                        <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h8.25L19.5 9v11.25A1.75 1.75 0 0117.75 22h-11A1.75 1.75 0 015 20.25V5.5A1.75 1.75 0 016.75 3.75z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 3.75V9h5.25"/>
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-bold text-slate-800">{{ $item->user->name ?? '-' }}</p>
                                <span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-600 text-[11px] font-semibold">
                                    {{ ucwords(str_replace('_', ' ', $item->jenis_surat)) }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500 mt-1">
                                {{ $item->tanggal->translatedFormat('d F Y') }}
                            </p>

                            <p class="text-sm text-slate-600 mt-2">{{ $item->keperluan }}</p>
                        </div>

                        @if($item->dokumen)
                            <div class="shrink-0">
                                <a href="{{ asset('storage/' . $item->dokumen) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition">
                                    Lihat Dokumen
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-8 text-center">
                    <p class="text-sm text-slate-500">Belum ada surat tercatat</p>
                </div>
            @endforelse
        </div>

    @endif


    {{-- =====================================================
        TAB: CUTI
    ====================================================== --}}
    @if($tab === 'cuti')

        @if($perluReset)
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4">
                <p class="font-semibold text-amber-800">
                    ⚠️ Sebagian PPNPN memiliki tahun cuti lama
                </p>
                <p class="text-sm text-amber-700 mt-1">
                    Reset tahun cuti akan dilakukan saat fitur Manajemen Cuti selesai.
                </p>
            </div>
        @endif

        <div class="mb-6">
            <h4 class="font-bold text-base text-slate-900 mb-4">Daftar PPNPN</h4>

            <div class="space-y-2">
                @forelse($ppnpn as $p)
                    <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                        <div>
                            <p class="font-semibold text-sm text-slate-900">{{ $p->name }}</p>
                            <p class="text-xs text-slate-500">
                                Jatah: {{ $p->jatah_cuti_tahunan ?? 12 }} hari ·
                                Terpakai: {{ $p->totalCutiTerpakai() }} hari ·
                                Sisa: {{ $p->sisaCutiTahunan() }} hari
                            </p>
                        </div>
                        <p class="text-xs text-slate-400">Tahun: {{ $p->tahun_cuti ?? now()->year }}</p>
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-8 text-center">
                        <p class="text-sm text-slate-500">Belum ada PPNPN aktif</p>
                    </div>
                @endforelse
            </div>
        </div>

    @endif

</div>

@endsection