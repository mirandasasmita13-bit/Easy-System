@extends('layouts.utama')

@section('title', 'Profil')

@section('content')

    {{-- HEADER --}}
    <div class="mb-8">

        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
            Profil Saya
        </h2>

        <p class="mt-2 text-slate-500">
            Informasi akun dan data kepegawaian kamu.
        </p>

    </div>


    {{-- PROFIL UTAMA --}}
    <div class="es-card p-6 sm:p-8 mb-6">

        {{-- FOTO + NAMA --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-6">

            {{-- FOTO --}}
            <div>
                <div class="
                    w-24 h-24
                    rounded-full
                    bg-purple-500
                    flex items-center justify-center
                    text-white
                    text-3xl
                    font-bold
                    overflow-hidden
                ">

                    @if($profil && $profil->foto)

                        <img
                            src="{{ asset('storage/' . $profil->foto) }}"
                            alt="Foto Profil"
                            class="w-full h-full object-cover"
                        >

                    @else

                        {{ strtoupper(substr($user->name, 0, 1)) }}

                    @endif

                </div>

            </div>


            {{-- NAMA --}}
            <div>

                <h3 class="text-2xl font-bold text-slate-900">
                    {{ $user->name }}
                </h3>

                <p class="mt-1 text-purple-600 font-medium">
                    {{ strtoupper($user->role) }}
                </p>

                <p class="mt-2 text-sm text-slate-400">
                    Informasi profil pengguna
                </p>

            </div>

        </div>


        {{-- EDIT PROFIL --}}
        <div class="mt-6 pt-5 border-t border-slate-100">

            <a
                href="{{ route('profil.edit') }}"
                class="
                    inline-flex
                    items-center
                    justify-center
                    gap-2
                    px-5 py-2.5
                    rounded-xl
                    bg-purple-600
                    text-white
                    text-sm
                    font-semibold
                    hover:bg-purple-700
                    transition
                "
            >
                ✏️ Edit Profil
            </a>

        </div>

    </div>


    {{-- INFORMASI KEPEGAWAIAN --}}
    <div class="es-card p-6 sm:p-8">

        <div class="mb-6">

            <h3 class="text-lg font-bold text-slate-900">
                Informasi Kepegawaian
            </h3>

            <p class="text-sm text-slate-400 mt-1">
                Data yang terdaftar pada sistem.
            </p>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">


            {{-- NAMA --}}
            <div>

                <p class="text-sm text-slate-400">
                    Nama Lengkap
                </p>

                <p class="mt-1 font-semibold text-slate-900">
                    {{ $user->name }}
                </p>

            </div>


            {{-- PPNPN --}}
            @if($user->role === 'ppnpn')

                {{-- NIK --}}
                <div>

                    <p class="text-sm text-slate-400">
                        NIK
                    </p>

                    <p class="mt-1 font-semibold text-slate-900">
                        {{ $profil?->nik ?? '-' }}
                    </p>

                </div>


            {{-- PEGAWAI --}}
            @elseif($user->role === 'pegawai')

                {{-- NIP --}}
                <div>

                    <p class="text-sm text-slate-400">
                        NIP
                    </p>

                    <p class="mt-1 font-semibold text-slate-900">
                        {{ $profil?->nip ?? '-' }}
                    </p>

                </div>


                {{-- PANGKAT / GOLONGAN --}}
                <div>

                    <p class="text-sm text-slate-400">
                        Pangkat / Golongan
                    </p>

                    <p class="mt-1 font-semibold text-slate-900">
                        {{ $profil?->pangkat_gol ?? '-' }}
                    </p>

                </div>


                {{-- JABATAN --}}
                <div>

                    <p class="text-sm text-slate-400">
                        Jabatan
                    </p>

                    <p class="mt-1 font-semibold text-slate-900">
                        {{ $profil?->jabatan ?? '-' }}
                    </p>

                </div>


                {{-- UNIT KERJA --}}
                <div>

                    <p class="text-sm text-slate-400">
                        Unit Kerja
                    </p>

                    <p class="mt-1 font-semibold text-slate-900">
                        {{ $profil?->unit_kerja ?? '-' }}
                    </p>

                </div>

            @endif


            {{-- EMAIL --}}
            <div>

                <p class="text-sm text-slate-400">
                    Email
                </p>

                <p class="mt-1 font-semibold text-slate-900">
                    {{ $user->email }}
                </p>

            </div>

        </div>

    </div>

@endsection