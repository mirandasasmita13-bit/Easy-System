@extends('layouts.auth')

@section('title', 'Register')

@section('content')

<div class="min-h-screen flex items-center justify-center py-10">
    <div class="w-full max-w-md">

        {{-- HEADER --}}
        <div class="text-center mb-8">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-purple-500 flex items-center justify-center text-white text-2xl font-bold">
                E
            </div>
            <h1 class="mt-5 text-2xl font-extrabold text-slate-900">Buat Akun</h1>
            <p class="mt-1 text-slate-500">Daftar untuk mengakses Easy System.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-7 shadow-sm">

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-100 p-4">
                    <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                {{-- NAMA --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Lengkap
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-3 rounded-xl border border-slate-200
                                  focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                           placeholder="Masukkan nama lengkap">
                </div>

                {{-- USERNAME --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        ID / Username
                    </label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                           pattern="[a-zA-Z0-9._-]+"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200
                                  focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                           placeholder="contoh: budi.santoso">
                    <p class="text-xs text-slate-400 mt-1">
                        Hanya huruf, angka, titik, garis bawah, dan tanda hubung.
                    </p>
                </div>

                {{-- PASSWORD + EYE --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Password
                    </label>

                    <div class="relative">
                        <input type="password" id="password" name="password" required minlength="8"
                               class="w-full px-4 pr-12 py-3 rounded-xl border border-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                               placeholder="Minimal 8 karakter">

                        <button type="button"
                                onclick="togglePassword('password', this)"
                                tabindex="-1"
                                aria-label="Toggle password visibility"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-purple-600 transition">
                            <svg class="w-5 h-5 eye-off" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <svg class="w-5 h-5 eye-on hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- KONFIRMASI PASSWORD + EYE --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Konfirmasi Password
                    </label>

                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                               class="w-full px-4 pr-12 py-3 rounded-xl border border-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                               placeholder="Ulangi password">

                        <button type="button"
                                onclick="togglePassword('password_confirmation', this)"
                                tabindex="-1"
                                aria-label="Toggle password visibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-purple-600 transition">
                            <svg class="w-5 h-5 eye-off" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            <svg class="w-5 h-5 eye-on hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <button type="submit"
                        class="w-full py-3.5 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">
                    Daftar
                </button>

            </form>

            {{-- LINK LOGIN --}}
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-500">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-purple-600 hover:text-purple-700">
                        Masuk di sini
                    </a>
                </p>
            </div>

        </div>

    </div>
</div>


{{-- SCRIPT EYE TOGGLE --}}
<script>
    function togglePassword(inputId, button) {
        const input  = document.getElementById(inputId);
        const eyeOff = button.querySelector('.eye-off');
        const eyeOn  = button.querySelector('.eye-on');

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