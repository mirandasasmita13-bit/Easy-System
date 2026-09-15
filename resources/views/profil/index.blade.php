@extends('layouts.utama')

@section('title', 'Profil Saya')

@section('content')

{{-- HEADER --}}
<div class="mb-6">
    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
        Profil Saya
    </h2>
    <p class="mt-1.5 text-sm text-slate-500">
        Informasi data diri dan akun Anda.
    </p>
</div>

{{-- FLASH --}}
@if(session('success'))
    <div class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        ✅ {{ session('success') }}
    </div>
@endif

@php
    $roleLabel = match($user->role) {
        'admin'   => '🛡️ Administrator',
        'pegawai' => '🧑‍💼 Pegawai',
        'ppnpn'   => '👷 PPNPN',
        default   => ucfirst($user->role),
    };
    $badgeClass = match($user->role) {
        'admin'   => 'bg-purple-100 text-purple-700',
        'pegawai' => 'bg-emerald-100 text-emerald-700',
        'ppnpn'   => 'bg-sky-100 text-sky-700',
        default   => 'bg-slate-100 text-slate-600',
    };
@endphp


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">

    {{-- =====================================================
         KIRI (1/3): CARD PROFIL
    ====================================================== --}}
    <div class="lg:col-span-1 flex">
        <div class="es-card overflow-hidden w-full flex flex-col">

            {{-- BANNER UNGU --}}
            <div class="h-24 bg-gradient-to-r from-purple-600 via-purple-500 to-purple-400 relative shrink-0">
                <div class="absolute inset-0 opacity-20"
                     style="background-image: radial-gradient(circle at 20% 30%, white 1.5px, transparent 1.5px), radial-gradient(circle at 70% 70%, white 1.5px, transparent 1.5px); background-size: 30px 30px;"></div>
            </div>

            {{-- FOTO — klik langsung buka album --}}
<div class="px-6 -mt-16 relative z-10 shrink-0 flex justify-center">

    <form id="fotoForm" action="{{ route('profil.update') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Hidden — data yang tidak boleh hilang --}}
        <input type="hidden" name="name" value="{{ $user->name }}">
        <input type="hidden" name="username" value="{{ $user->username }}">

        @if($user->role === 'ppnpn')
            <input type="hidden" name="nik" value="{{ $profil?->nik }}">
            <input type="hidden" name="jabatan" value="{{ $profil?->jabatan }}">
            <input type="hidden" name="unit_kerja" value="{{ $profil?->unit_kerja }}">
        @elseif($user->role === 'pegawai')
            <input type="hidden" name="nip" value="{{ $profil?->nip }}">
            <input type="hidden" name="pangkat_gol" value="{{ $profil?->pangkat_gol }}">
            <input type="hidden" name="jabatan" value="{{ $profil?->jabatan }}">
            <input type="hidden" name="unit_kerja" value="{{ $profil?->unit_kerja }}">
        @else
            <input type="hidden" name="jabatan" value="{{ $profil?->jabatan }}">
            <input type="hidden" name="unit_kerja" value="{{ $profil?->unit_kerja }}">
        @endif

        <label for="fotoInput" class="cursor-pointer group relative block">
            <div class="w-32 h-32 rounded-full mx-auto
                        bg-purple-500 overflow-hidden
                        flex items-center justify-center
                        text-white text-5xl font-extrabold
                        ring-4 ring-white
                        shadow-xl shadow-purple-200/60
                        transition group-hover:ring-purple-200">
                @if($profil && $profil->foto)
                    <img id="fotoPreview"
                         src="{{ asset('storage/' . $profil->foto) }}"
                         alt="Foto Profil"
                         class="w-full h-full object-cover">
                @else
                    <img id="fotoPreview" class="hidden w-full h-full object-cover" alt="">
                    <span id="fotoInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>

            {{-- HOVER OVERLAY --}}
            <div class="absolute inset-0 rounded-full bg-black/50 opacity-0
                        group-hover:opacity-100 transition
                        flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-7 h-7 text-white mx-auto" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-white text-[10px] font-bold mt-1">Ubah Foto</p>
                </div>
            </div>
        </label>

        <input type="file" id="fotoInput" name="foto" accept="image/*"
               class="hidden" onchange="autoSubmitFoto(this)">
    </form>

</div>

            {{-- INFO PROFIL --}}
            <div class="px-6 pb-6 pt-4 text-center shrink-0">
                <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $user->name }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-mono">{{ '@' . $user->username }}</p>

                <span class="mt-3 inline-flex items-center gap-1 rounded-full px-3 py-1 text-[11px] font-bold {{ $badgeClass }}">
                    {{ $roleLabel }}
                </span>
            </div>

            {{-- DIVIDER --}}
            <div class="mx-6 border-t border-slate-100 shrink-0"></div>

            {{-- DETAIL INFO --}}
            <div class="px-6 py-5 space-y-4 flex-1">

                @if($user->role === 'ppnpn' && $profil?->nik)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.418.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NIK</p>
                            <p class="mt-0.5 text-xs font-semibold text-slate-700 font-mono">{{ $profil->nik }}</p>
                        </div>
                    </div>
                @endif

                @if($user->role === 'pegawai' && $profil?->nip)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.418.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">NIP</p>
                            <p class="mt-0.5 text-xs font-semibold text-slate-700 font-mono">{{ $profil->nip }}</p>
                        </div>
                    </div>
                @endif

                @if($profil?->jabatan)
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jabatan</p>
                            <p class="mt-0.5 text-xs font-semibold text-slate-700">{{ $profil->jabatan }}</p>
                        </div>
                    </div>
                @endif

                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Username</p>
                        <p class="mt-0.5 text-xs font-semibold text-slate-700 font-mono">{{ $user->username }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Terdaftar Sejak</p>
                        <p class="mt-0.5 text-xs font-semibold text-slate-700">
                            {{ $user->created_at?->translatedFormat('d F Y') ?? '—' }}
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- =====================================================
         KANAN (2/3): INFORMASI PROFIL + TOMBOL EDIT
    ====================================================== --}}
    <div class="lg:col-span-2 flex">
        <div class="es-card w-full flex flex-col">

            {{-- HEADER + TOMBOL EDIT --}}
            <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between gap-4 shrink-0">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Informasi Pribadi</h3>
                    <p class="text-xs text-slate-400 mt-1">Data akun dan kepegawaian Anda.</p>
                </div>

                <a href="{{ route('profil.edit') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                          bg-purple-600 text-white text-sm font-bold
                          shadow-md shadow-purple-200
                          hover:bg-purple-700 hover:-translate-y-0.5 transition shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Profil
                </a>
            </div>

            {{-- KONTEN VIEW --}}
            <div class="p-6 sm:p-8 space-y-6 flex-1">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nama Lengkap</p>
                        <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Username</p>
                        <p class="mt-1.5 text-sm font-semibold text-slate-900 font-mono">{{ $user->username }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                @if($user->role === 'ppnpn')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">NIK</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-900 font-mono">{{ $profil?->nik ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jabatan</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $profil?->jabatan ?? '—' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Unit Kerja</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $profil?->unit_kerja ?? '—' }}</p>
                        </div>
                    </div>

                @elseif($user->role === 'pegawai')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">NIP</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-900 font-mono">{{ $profil?->nip ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pangkat / Golongan</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $profil?->pangkat_gol ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jabatan</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $profil?->jabatan ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Unit Kerja</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $profil?->unit_kerja ?? '—' }}</p>
                        </div>
                    </div>

                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jabatan</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $profil?->jabatan ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Unit Kerja</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $profil?->unit_kerja ?? '—' }}</p>
                        </div>
                    </div>
                @endif

                <div class="border-t border-slate-100"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Role Sistem</p>
                        <p class="mt-1.5 text-sm font-semibold text-slate-900">{{ $roleLabel }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Status Akun</p>
                        <p class="mt-1.5 text-sm font-semibold {{ $user->status === 'aktif' ? 'text-emerald-600' : 'text-red-500' }}">
                            ● {{ ucfirst($user->status) }}
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

@endsection


@push('scripts')
<script>
    // ---------------------------------------------
    // AUTO-SUBMIT FOTO SAAT DIPILIH
    // ---------------------------------------------
    function autoSubmitFoto(input) {
        if (!input.files || !input.files[0]) return;

        // Preview dulu (biar user lihat perubahan)
        const reader = new FileReader();
        reader.onload = function(e) {
            let img = document.getElementById('fotoPreview');
            const initial = document.getElementById('fotoInitial');

            if (initial) initial.remove();

            if (!img) {
                img = document.createElement('img');
                img.id = 'fotoPreview';
                img.className = 'w-full h-full object-cover';
                document.querySelector('.rounded-full').appendChild(img);
            }

            img.classList.remove('hidden');
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);

        // Submit form setelah 400ms (biar user lihat preview dulu)
        setTimeout(() => {
            document.getElementById('fotoForm').submit();
        }, 400);
    }
</script>
@endpush