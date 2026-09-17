@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">

        {{-- HEADER --}}
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


        {{-- FORM CARD --}}
        <div class="bg-white border border-slate-200
                    rounded-2xl p-7 shadow-sm">

            {{-- ERROR --}}
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

                {{-- USERNAME --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
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

                {{-- PASSWORD + EYE TOGGLE --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Password
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full px-4 pr-12 py-3 rounded-xl
                                   border border-slate-200
                                   focus:outline-none
                                   focus:ring-2
                                   focus:ring-purple-500/20
                                   focus:border-purple-500"
                            placeholder="Masukkan password"
                        >

                        {{-- TOMBOL EYE --}}
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-purple-600 transition"
                            tabindex="-1"
                            aria-label="Toggle password visibility"
                        >
                            {{-- EYE OFF (default) --}}
                            <svg id="eye-off" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>

                            {{-- EYE ON (disembunyikan) --}}
                            <svg id="eye-on" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- SUBMIT --}}
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
                    <a href="{{ route('register') }}"
                       class="font-semibold text-purple-600 hover:text-purple-700 transition">
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


{{-- SCRIPT EYE TOGGLE --}}
<script>
    function togglePassword() {
        const input   = document.getElementById('password');
        const eyeOff  = document.getElementById('eye-off');
        const eyeOn   = document.getElementById('eye-on');

        if (input.type === 'password') {
            input.type = 'text';
            eyeOff.classList.add('hidden');
            eyeOn.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOff.classList.remove('hidden');
            eyeOn.classList.add('hidden');
        }
    }
</script>

@endsection