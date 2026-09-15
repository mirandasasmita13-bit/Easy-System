@extends('layouts.utama')

@section('title', 'Edit Profil')

@section('content')

{{-- HEADER + BREADCRUMB --}}
<div class="mb-6">
    <a href="{{ route('profil.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-purple-600 transition mb-3">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Profil
    </a>

    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
        Edit Profil
    </h2>
    <p class="mt-1.5 text-sm text-slate-500">
        Perbarui informasi data diri dan keamanan akun Anda.
    </p>
</div>

{{-- FLASH --}}
@if(session('success'))
    <div class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        ✅ {{ session('success') }}
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


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">

    {{-- =====================================================
         KIRI (1/3): CARD PROFIL — KLIK FOTO LANGSUNG GANTI
    ====================================================== --}}
    <div class="lg:col-span-1 flex">
        <div class="es-card overflow-hidden w-full flex flex-col">

            {{-- BANNER UNGU --}}
            <div class="h-24 bg-gradient-to-r from-purple-600 via-purple-500 to-purple-400 relative shrink-0">
                <div class="absolute inset-0 opacity-20"
                     style="background-image: radial-gradient(circle at 20% 30%, white 1.5px, transparent 1.5px), radial-gradient(circle at 70% 70%, white 1.5px, transparent 1.5px); background-size: 30px 30px;"></div>
            </div>

            {{-- FOTO — KLIK LANGSUNG BUKA ALBUM --}}
            <div class="px-6 -mt-16 relative z-10 shrink-0">

                <form id="fotoForm" action="{{ route('profil.update') }}"
                      method="POST" enctype="multipart/form-data" class="inline-block">
                    @csrf
                    @method('PUT')

                    {{-- Hidden — semua data supaya tidak null --}}
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

                    <label for="fotoInput" class="cursor-pointer group relative block mx-auto">
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

                    {{-- INPUT FILE — HIDDEN --}}
                    <input type="file" id="fotoInput" name="foto" accept="image/*"
                           class="hidden" onchange="autoSubmitFoto(this)">
                </form>

                {{-- HINT --}}
                <p class="mt-3 text-center text-[10px] text-slate-400">
                    Klik foto untuk mengubah · Maks 5 MB
                </p>
            </div>

            {{-- INFO PROFIL --}}
            <div class="px-6 pb-6 pt-4 text-center shrink-0">
                <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $user->name }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-mono">{{ '@' . $user->username }}</p>

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

                <span class="mt-3 inline-flex items-center gap-1 rounded-full px-3 py-1 text-[11px] font-bold {{ $badgeClass }}">
                    {{ $roleLabel }}
                </span>
            </div>

        </div>
    </div>


    {{-- =====================================================
         KANAN (2/3): FORM EDIT
    ====================================================== --}}
    <div class="lg:col-span-2 flex">
        <div class="es-card w-full flex flex-col">

            {{-- HEADER --}}
            <div class="p-6 sm:p-8 border-b border-slate-100 shrink-0">
                <h3 class="text-lg font-bold text-slate-900">Edit Biodata</h3>
                <p class="text-xs text-slate-400 mt-1">Perbarui informasi data diri Anda.</p>
            </div>

            {{-- FORM --}}
            <form action="{{ route('profil.update') }}" method="POST" class="p-6 sm:p-8 flex-1 flex flex-col">
                @csrf
                @method('PUT')

                <div class="space-y-5 flex-1">

                    {{-- NAMA --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama Lengkap
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-purple-400 font-bold text-sm">A</span>
                            </div>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Nama lengkap"
                                   required
                                   class="w-full rounded-xl border border-slate-200 bg-white
                                          pl-12 pr-4 py-3 text-sm text-slate-700
                                          outline-none transition
                                          focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        </div>
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- USERNAME --}}
                    <div>
                        <label for="username" class="block text-sm font-semibold text-slate-700 mb-2">
                            Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" id="username" name="username"
                                   value="{{ old('username', $user->username) }}"
                                   placeholder="username"
                                   required
                                   class="w-full rounded-xl border border-slate-200 bg-white
                                          pl-12 pr-4 py-3 text-sm text-slate-700 font-mono
                                          outline-none transition
                                          focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        </div>
                        <p class="mt-1.5 text-xs text-slate-400">
                            Hanya huruf, angka, titik, underscore, dan dash.
                        </p>
                        @error('username')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- FIELD PER ROLE --}}
                    @if($user->role === 'admin')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="jabatan" class="block text-sm font-semibold text-slate-700 mb-2">Jabatan</label>
                                <input type="text" id="jabatan" name="jabatan"
                                       value="{{ old('jabatan', $profil?->jabatan) }}"
                                       placeholder="Contoh: Administrator Sistem"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                            </div>
                            <div>
                                <label for="unit_kerja" class="block text-sm font-semibold text-slate-700 mb-2">Unit Kerja</label>
                                <input type="text" id="unit_kerja" name="unit_kerja"
                                       value="{{ old('unit_kerja', $profil?->unit_kerja) }}"
                                       placeholder="Contoh: Bagian IT"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                            </div>
                        </div>

                    @elseif($user->role === 'ppnpn')
                        <div>
                            <label for="nik" class="block text-sm font-semibold text-slate-700 mb-2">
                                NIK <span class="text-xs font-normal text-slate-400">(Nomor Induk Kependudukan)</span>
                            </label>
                            <input type="text" id="nik" name="nik"
                                   value="{{ old('nik', $profil?->nik) }}"
                                   placeholder="16 digit NIK"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-mono outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="jabatan" class="block text-sm font-semibold text-slate-700 mb-2">Jabatan</label>
                                <input type="text" id="jabatan" name="jabatan"
                                       value="{{ old('jabatan', $profil?->jabatan) }}"
                                       placeholder="Contoh: Tenaga Kebersihan"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                            </div>
                            <div>
                                <label for="unit_kerja" class="block text-sm font-semibold text-slate-700 mb-2">Unit Kerja</label>
                                <input type="text" id="unit_kerja" name="unit_kerja"
                                       value="{{ old('unit_kerja', $profil?->unit_kerja) }}"
                                       placeholder="Contoh: Sub Bagian Umum"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                            </div>
                        </div>

                    @elseif($user->role === 'pegawai')
                        <div>
                            <label for="nip" class="block text-sm font-semibold text-slate-700 mb-2">
                                NIP <span class="text-xs font-normal text-slate-400">(Nomor Induk Pegawai)</span>
                            </label>
                            <input type="text" id="nip" name="nip"
                                   value="{{ old('nip', $profil?->nip) }}"
                                   placeholder="18 digit NIP"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-mono outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="pangkat_gol" class="block text-sm font-semibold text-slate-700 mb-2">Pangkat / Golongan</label>
                                <input type="text" id="pangkat_gol" name="pangkat_gol"
                                       value="{{ old('pangkat_gol', $profil?->pangkat_gol) }}"
                                       placeholder="Contoh: Penata Muda / III-a"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                            </div>
                            <div>
                                <label for="jabatan" class="block text-sm font-semibold text-slate-700 mb-2">Jabatan</label>
                                <input type="text" id="jabatan" name="jabatan"
                                       value="{{ old('jabatan', $profil?->jabatan) }}"
                                       placeholder="Contoh: Staf Administrasi"
                                       class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                            </div>
                        </div>

                        <div>
                            <label for="unit_kerja" class="block text-sm font-semibold text-slate-700 mb-2">Unit Kerja</label>
                            <input type="text" id="unit_kerja" name="unit_kerja"
                                   value="{{ old('unit_kerja', $profil?->unit_kerja) }}"
                                   placeholder="Contoh: Sub Bagian Kepegawaian"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        </div>
                    @endif

                    {{-- INFO BOX --}}
                    <div class="rounded-xl bg-sky-50 border border-sky-100 px-4 py-3 flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-sky-200 text-sky-700 flex items-center justify-center text-[11px] font-bold shrink-0 mt-0.5">i</div>
                        <p class="text-xs text-sky-700 leading-relaxed">
                            Untuk mengubah <strong>Role</strong> dan <strong>Status Akun</strong>, silakan hubungi Administrator.
                        </p>
                    </div>

                </div>

                {{-- TOMBOL --}}
                <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 shrink-0">
                    <a href="{{ route('profil.index') }}"
                       class="inline-flex items-center justify-center px-5 py-3 rounded-xl
                              border border-slate-200 text-sm font-semibold text-slate-600
                              hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl
                                   bg-purple-600 text-white text-sm font-bold
                                   shadow-md shadow-purple-200
                                   hover:bg-purple-700 hover:-translate-y-0.5 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>

            </form>

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

        setTimeout(() => {
            document.getElementById('fotoForm').submit();
        }, 400);
    }
</script>
@endpush