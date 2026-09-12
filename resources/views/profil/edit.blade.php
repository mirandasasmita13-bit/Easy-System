@extends('layouts.utama')

@section('title', 'Edit Profil')

@section('content')

    {{-- HEADER --}}
    <div class="mb-8">

        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
            Edit Profil
        </h2>

        <p class="mt-2 text-slate-500">
            Perbarui informasi profil kamu.
        </p>

    </div>


    {{-- FORM --}}
    <div class="es-card p-6 sm:p-8">

        <form
            action="{{ route('profil.update') }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')


            {{-- FOTO --}}
            <div class="mb-8">

                <label class="block text-sm font-semibold text-slate-700 mb-3">
                    Foto Profil
                </label>

                <div class="flex items-center gap-5">

                    <div class="
                        w-24 h-24
                        rounded-full
                        bg-purple-500
                        overflow-hidden
                        flex items-center justify-center
                        text-white
                        text-3xl
                        font-bold
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


                    <div>

                        <input
                            type="file"
                            name="foto"
                            accept="image/*"
                            class="block w-full text-sm text-slate-500
                                   file:mr-4
                                   file:py-2
                                   file:px-4
                                   file:rounded-lg
                                   file:border-0
                                   file:bg-purple-50
                                   file:text-purple-700
                                   file:font-semibold
                                   hover:file:bg-purple-100"
                        >

                        <p class="mt-2 text-xs text-slate-400">
                            JPG, PNG, atau WEBP. Maksimal 5 MB.
                        </p>

                        @error('foto')
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- DATA BERDASARKAN ROLE --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                {{-- =====================================================
                     PPNPN
                ====================================================== --}}

                @if($user->role === 'ppnpn')

                    {{-- NIK --}}
                    <div>

                        <label
                            for="nik"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            NIK
                        </label>

                        <input
                            type="text"
                            id="nik"
                            name="nik"
                            value="{{ old('nik', $profil?->nik) }}"
                            placeholder="Masukkan NIK"
                            class="
                                w-full
                                rounded-xl
                                border border-slate-200
                                px-4 py-3
                                text-sm
                                focus:outline-none
                                focus:ring-2
                                focus:ring-purple-500/20
                                focus:border-purple-500
                            "
                        >

                        @error('nik')
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                {{-- =====================================================
                     PEGAWAI
                ====================================================== --}}

                @elseif($user->role === 'pegawai')

                    {{-- NIP --}}
                    <div>

                        <label
                            for="nip"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            NIP
                        </label>

                        <input
                            type="text"
                            id="nip"
                            name="nip"
                            value="{{ old('nip', $profil?->nip) }}"
                            placeholder="Masukkan NIP"
                            class="
                                w-full
                                rounded-xl
                                border border-slate-200
                                px-4 py-3
                                text-sm
                                focus:outline-none
                                focus:ring-2
                                focus:ring-purple-500/20
                                focus:border-purple-500
                            "
                        >

                        @error('nip')
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- PANGKAT / GOLONGAN --}}
                    <div>

                        <label
                            for="pangkat_gol"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Pangkat / Golongan
                        </label>

                        <input
                            type="text"
                            id="pangkat_gol"
                            name="pangkat_gol"
                            value="{{ old('pangkat_gol', $profil?->pangkat_gol) }}"
                            placeholder="Masukkan pangkat / golongan"
                            class="
                                w-full
                                rounded-xl
                                border border-slate-200
                                px-4 py-3
                                text-sm
                                focus:outline-none
                                focus:ring-2
                                focus:ring-purple-500/20
                                focus:border-purple-500
                            "
                        >

                        @error('pangkat_gol')
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- JABATAN --}}
                    <div>

                        <label
                            for="jabatan"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Jabatan
                        </label>

                        <input
                            type="text"
                            id="jabatan"
                            name="jabatan"
                            value="{{ old('jabatan', $profil?->jabatan) }}"
                            placeholder="Masukkan jabatan"
                            class="
                                w-full
                                rounded-xl
                                border border-slate-200
                                px-4 py-3
                                text-sm
                                focus:outline-none
                                focus:ring-2
                                focus:ring-purple-500/20
                                focus:border-purple-500
                            "
                        >

                        @error('jabatan')
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- UNIT KERJA --}}
                    <div>

                        <label
                            for="unit_kerja"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Unit Kerja
                        </label>

                        <input
                            type="text"
                            id="unit_kerja"
                            name="unit_kerja"
                            value="{{ old('unit_kerja', $profil?->unit_kerja) }}"
                            placeholder="Masukkan unit kerja"
                            class="
                                w-full
                                rounded-xl
                                border border-slate-200
                                px-4 py-3
                                text-sm
                                focus:outline-none
                                focus:ring-2
                                focus:ring-purple-500/20
                                focus:border-purple-500
                            "
                        >

                        @error('unit_kerja')
                            <p class="mt-1 text-sm text-red-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                @endif

            </div>


            {{-- TOMBOL --}}
            <div class="
                mt-8
                pt-6
                border-t border-slate-100
                flex flex-col-reverse sm:flex-row
                sm:justify-end
                gap-3
            ">

                <a
                    href="{{ route('profil.index') }}"
                    class="
                        inline-flex
                        items-center
                        justify-center
                        px-5 py-3
                        rounded-xl
                        border border-slate-200
                        text-sm
                        font-semibold
                        text-slate-600
                        hover:bg-slate-50
                        transition
                    "
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="
                        inline-flex
                        items-center
                        justify-center
                        px-5 py-3
                        rounded-xl
                        bg-purple-600
                        text-white
                        text-sm
                        font-semibold
                        hover:bg-purple-700
                        transition
                    "
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

@endsection