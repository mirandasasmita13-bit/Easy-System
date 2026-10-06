@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<div class="auth-container">
    <div class="auth-wrapper">

        {{-- LOGO DI ATAS CARD --}}
        <a href="{{ url('/') }}" class="auth-logo">
            <img src="{{ asset('images/logoo.png') }}" alt="Logo SIKAT">
        </a>

        {{-- CARD --}}
        <div class="auth-card">

            {{-- TITLE --}}
            <h2 class="auth-welcome-title">
                Selamat datang <span>kembali</span> 👋
            </h2>

            <p class="auth-welcome-description">
                Masuk ke akun SIKAT untuk melanjutkan.
            </p>


            {{-- ALERT --}}
            @if(session('success'))
                <div class="auth-alert auth-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="auth-alert auth-alert-warning">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-alert auth-alert-error">
                    {{ $errors->first() }}
                </div>
            @endif


            {{-- FORM --}}
            <form method="POST" action="{{ url('/login') }}" class="auth-form">
                @csrf

                {{-- USERNAME --}}
                <div class="auth-field">
                    <label class="auth-label">ID / Username</label>
                    <input type="text"
                           name="username"
                           value="{{ old('username') }}"
                           required
                           autofocus
                           autocomplete="username"
                           class="auth-input"
                           placeholder="Masukkan ID / username">
                </div>

                {{-- PASSWORD --}}
                <div class="auth-field">
                    <label class="auth-label">Password</label>

                    <div class="auth-password">
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               class="auth-input"
                               placeholder="Masukkan password">

                        <button type="button"
                                onclick="togglePassword()"
                                class="auth-eye"
                                tabindex="-1"
                                aria-label="Tampilkan password">

                            <svg id="eye-off" width="20" height="20" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M10.58 10.58a2 2 0 002.83 2.83"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9.88 5.09A10.94 10.94 0 0112 5c4.97 0 9.02 3.25 10.5 7a11.67 11.67 0 01-3.01 4.42"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.61 6.61A11.66 11.66 0 001.5 12c1.48 3.75 5.53 7 10.5 7 1.6 0 3.1-.34 4.42-.94"/>
                            </svg>

                            <svg id="eye-on" class="hidden" width="20" height="20" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12z"/>
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <button type="submit" class="auth-button">
                    <span>Masuk ke SIKAT</span>
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 6l6 6-6 6"/>
                    </svg>
                </button>
            </form>


            {{-- REGISTER --}}
            <div class="auth-register">
                @if(\App\Models\User::pendaftaranDibuka())
                    <p>
                        Belum punya akun?
                        <a href="{{ route('register') }}">Daftar di sini</a>
                    </p>
                @else
                    <p>Untuk pendaftaran akun silahkan hubungi admin</p>
                @endif
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="auth-footer">
            SIKAT · Sistem Informasi Kehadiran Terintegritas
        </div>

    </div>
</div>


{{-- PASSWORD TOGGLE --}}
<script>
    function togglePassword() {
        const input  = document.getElementById('password');
        const eyeOff = document.getElementById('eye-off');
        const eyeOn  = document.getElementById('eye-on');

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