@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">

            <div class="mx-auto w-14 h-14 rounded-2xl
                        bg-purple-500
                        flex items-center justify-center
                        text-white text-2xl font-bold">
                E
            </div>

            <h1 class="mt-5 text-2xl font-extrabold text-slate-900">
                Easy System
            </h1>

            <p class="mt-1 text-slate-500">
                Silakan masuk ke akun kamu.
            </p>

        </div>


        <div class="bg-white border border-slate-200
                    rounded-2xl p-7 shadow-sm">

            @if ($errors->any())

                <div class="mb-5 rounded-xl bg-red-50
                            border border-red-100 p-4">

                    <p class="text-sm text-red-600">
                        {{ $errors->first() }}
                    </p>

                </div>

            @endif


            <form method="POST" action="{{ url('/login') }}">

                @csrf


                {{-- USERNAME / ID --}}
                <div class="mb-5">

                    <label class="block text-sm font-medium
                                text-slate-700 mb-2">
                        ID / Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        class="w-full px-4 py-3 rounded-xl
                            border border-slate-200
                            focus:outline-none
                            focus:ring-2
                            focus:ring-purple-500/20
                            focus:border-purple-500"
                        placeholder="Masukkan ID / username"
                    >

                </div>
                {{-- PASSWORD --}}
                <div class="mb-6">

                    <label class="block text-sm font-medium
                                  text-slate-700 mb-2">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full px-4 py-3 rounded-xl
                               border border-slate-200
                               focus:outline-none
                               focus:ring-2
                               focus:ring-purple-500/20
                               focus:border-purple-500"
                        placeholder="Masukkan password"
                    >

                </div>
                <button 
                    type="submit" 
                    class="w-full py-3.5 rounded-xl 
                           bg-purple-600 text-white 
                           font-semibold 
                           hover:bg-purple-700 
                           transition">

                    Masuk

                </button>

            </form>


            {{-- REGISTER --}}
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">

                <p class="text-sm text-slate-500">
                    Belum punya akun?
                    <a 
                        href="{{ route('register') }}"
                        class="font-semibold text-purple-600 hover:text-purple-700 transition"
                    >
                        Daftar di sini
                    </a>
                </p>

            </div>

        </div>


        <p class="text-center text-xs text-slate-400 mt-6">
            Easy System · Office Management
        </p>

    </div>

</div>

@endsection