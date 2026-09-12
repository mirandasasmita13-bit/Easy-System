@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')

<div class="min-h-screen flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-md">

        {{-- HEADER --}}
        <div class="text-center mb-8">

            <div class="
                mx-auto
                w-14 h-14
                rounded-2xl
                bg-purple-500
                flex items-center justify-center
                text-white
                text-2xl
                font-bold
            ">
                E
            </div>

            <h1 class="mt-5 text-2xl font-extrabold text-slate-900">
                Buat Akun
            </h1>

            <p class="mt-1 text-slate-500">
                Buat akun untuk menggunakan Easy System.
            </p>

        </div>


        {{-- CARD --}}
        <div class="
            bg-white
            border border-slate-200
            rounded-2xl
            p-7
            shadow-sm
        ">

            {{-- ERROR --}}
            @if ($errors->any())

                <div class="
                    mb-5
                    rounded-xl
                    bg-red-50
                    border border-red-100
                    p-4
                ">

                    <p class="text-sm text-red-600">
                        {{ $errors->first() }}
                    </p>

                </div>

            @endif


            {{-- FORM --}}
            <form method="POST" action="{{ route('register.store') }}">

                @csrf


                {{-- NAMA --}}
                <div class="mb-5">

                    <label class="
                        block
                        text-sm
                        font-medium
                        text-slate-700
                        mb-2
                    ">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        class="
                            w-full
                            px-4 py-3
                            rounded-xl
                            border border-slate-200
                            focus:outline-none
                            focus:ring-2
                            focus:ring-purple-500/20
                            focus:border-purple-500
                        "
                        placeholder="Masukkan nama lengkap"
                    >

                </div>


                {{-- EMAIL --}}
                <div class="mb-5">

                    <label class="
                        block
                        text-sm
                        font-medium
                        text-slate-700
                        mb-2
                    ">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="
                            w-full
                            px-4 py-3
                            rounded-xl
                            border border-slate-200
                            focus:outline-none
                            focus:ring-2
                            focus:ring-purple-500/20
                            focus:border-purple-500
                        "
                        placeholder="nama@email.com"
                    >

                </div>


                {{-- PASSWORD --}}
                <div class="mb-5">

                    <label class="
                        block
                        text-sm
                        font-medium
                        text-slate-700
                        mb-2
                    ">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        class="
                            w-full
                            px-4 py-3
                            rounded-xl
                            border border-slate-200
                            focus:outline-none
                            focus:ring-2
                            focus:ring-purple-500/20
                            focus:border-purple-500
                        "
                        placeholder="Minimal 8 karakter"
                    >

                </div>


                {{-- KONFIRMASI PASSWORD --}}
                <div class="mb-6">

                    <label class="
                        block
                        text-sm
                        font-medium
                        text-slate-700
                        mb-2
                    ">
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        class="
                            w-full
                            px-4 py-3
                            rounded-xl
                            border border-slate-200
                            focus:outline-none
                            focus:ring-2
                            focus:ring-purple-500/20
                            focus:border-purple-500
                        "
                        placeholder="Ulangi password"
                    >

                </div>


                {{-- BUTTON --}}
                <button
                    type="submit"
                    class="
                        w-full
                        py-3.5
                        rounded-xl
                        bg-purple-600
                        text-white
                        font-semibold
                        hover:bg-purple-700
                        transition
                    "
                >
                    Buat Akun
                </button>

            </form>


            {{-- LOGIN --}}
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">

                <p class="text-sm text-slate-500">

                    Sudah punya akun?

                    <a
                        href="{{ route('login') }}"
                        class="
                            font-semibold
                            text-purple-600
                            hover:text-purple-700
                            transition
                        "
                    >
                        Masuk di sini
                    </a>

                </p>

            </div>

        </div>


        {{-- FOOTER --}}
        <p class="text-center text-xs text-slate-400 mt-6">
            Easy System · Office Management
        </p>

    </div>

</div>

@endsection