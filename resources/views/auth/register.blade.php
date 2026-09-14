@extends('layouts.auth')

@section('title', 'Register')

@section('content')

<div class="min-h-screen flex items-center justify-center py-10">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-purple-500 flex items-center justify-center text-white text-2xl font-bold">
                E
            </div>
            <h1 class="mt-5 text-2xl font-extrabold text-slate-900">Buat Akun</h1>
            <p class="mt-1 text-slate-500">Daftar untuk mengakses Easy System.</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-7 shadow-sm">

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
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                           placeholder="Masukkan nama lengkap">
                </div>

                {{-- USERNAME --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        ID / Username
                    </label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                           pattern="[a-zA-Z0-9._-]+"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                           placeholder="contoh: budi.santoso">
                    <p class="text-xs text-slate-400 mt-1">
                        Hanya huruf, angka, titik, garis bawah, dan tanda hubung.
                    </p>
                </div>

                {{-- PASSWORD --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Password
                    </label>
                    <input type="password" name="password" required minlength="8"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                           placeholder="Minimal 8 karakter">
                </div>

                {{-- KONFIRMASI --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation" required minlength="8"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                           placeholder="Ulangi password">
                </div>

                <button type="submit"
                        class="w-full py-3.5 rounded-xl bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">
                    Daftar
                </button>

            </form>

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

@endsection