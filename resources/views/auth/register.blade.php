@extends('layouts.auth')

@section('title', 'Register')

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
                Buat akun <span>baru</span>
            </h2>

            <p class="auth-welcome-description">
                Daftarkan akun untuk mengakses SIKAT.
            </p>


            {{-- ERROR --}}
            @if ($errors->any())
                <div class="auth-alert auth-alert-error">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- FORM --}}
            <form method="POST" action="{{ route('register.store') }}" class="auth-form">
                @csrf

                {{-- NAMA --}}
                <div class="auth-field">
                    <label class="auth-label">Nama Lengkap</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           autocomplete="name"
                           class="auth-input"
                           placeholder="Masukkan nama lengkap">
                </div>

                {{-- USERNAME --}}
                <div class="auth-field">
                    <label class="auth-label">ID / Username</label>
                    <input type="text"
                           name="username"
                           value="{{ old('username') }}"
                           required
                           pattern="[a-zA-Z0-9._-]+"
                           autocomplete="username"
                           class="auth-input"
                           placeholder="contoh: budi.santoso">
                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">
                        Huruf, angka, titik, garis bawah, dan tanda hubung.
                    </p>
                </div>

                {{-- ROLE + SUB ROLE --}}
                <div class="auth-field">
                    <label class="auth-label">Daftar Sebagai</label>

                    <select name="role_sub" required class="auth-input">
                        <option value="">— Pilih status —</option>

                        <optgroup label="PPNPN">
                            <option value="ppnpn:satpam"
                                {{ old('role_sub') === 'ppnpn:satpam' ? 'selected' : '' }}>
                                PPNPN — Satpam
                            </option>
                            <option value="ppnpn:pramubakti"
                                {{ old('role_sub') === 'ppnpn:pramubakti' ? 'selected' : '' }}>
                                PPNPN — Pramubakti
                            </option>
                        </optgroup>

                        <optgroup label="Lainnya">
                            <option value="magang"
                                {{ old('role_sub') === 'magang' ? 'selected' : '' }}>
                                Magang / PKL
                            </option>
                        </optgroup>
                    </select>

                    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">
                        Pilih sesuai jabatan yang sudah ditetapkan.
                    </p>
                </div>


                {{-- PASSWORD --}}
                <div class="auth-field">
                    <label class="auth-label">Password</label>

                    <div class="auth-password">
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               minlength="8"
                               autocomplete="new-password"
                               class="auth-input"
                               placeholder="Minimal 8 karakter">

                        <button type="button"
                                onclick="togglePassword('password', this)"
                                class="auth-eye"
                                tabindex="-1"
                                aria-label="Toggle password visibility">

                            <svg class="eye-off" width="20" height="20" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M10.58 10.58a2 2 0 002.83 2.83"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9.88 5.09A10.94 10.94 0 0112 5c4.97 0 9.02 3.25 10.5 7a11.67 11.67 0 01-3.01 4.42"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.61 6.61A11.66 11.66 0 001.5 12c1.48 3.75 5.53 7 10.5 7 1.6 0 3.1-.34 4.42-.94"/>
                            </svg>

                            <svg class="eye-on hidden" width="20" height="20" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12z"/>
                                <circle cx="12" cy="12" r="2.5"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- KONFIRMASI PASSWORD --}}
                <div class="auth-field">
                    <label class="auth-label">Konfirmasi Password</label>

                    <div class="auth-password">
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               minlength="8"
                               autocomplete="new-password"
                               class="auth-input"
                               placeholder="Ulangi password">

                        <button type="button"
                                onclick="togglePassword('password_confirmation', this)"
                                class="auth-eye"
                                tabindex="-1"
                                aria-label="Toggle password visibility">

                            <svg class="eye-off" width="20" height="20" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M10.58 10.58a2 2 0 002.83 2.83"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9.88 5.09A10.94 10.94 0 0112 5c4.97 0 9.02 3.25 10.5 7a11.67 11.67 0 01-3.01 4.42"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.61 6.61A11.66 11.66 0 001.5 12c1.48 3.75 5.53 7 10.5 7 1.6 0 3.1-.34 4.42-.94"/>
                            </svg>

                            <svg class="eye-on hidden" width="20" height="20" fill="none"
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
                    <span>Buat Akun</span>
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </button>
            </form>


            {{-- LOGIN LINK --}}
            <div class="auth-register">
                <p>
                    Sudah punya akun?
                    <a href="{{ route('login') }}">Masuk di sini</a>
                </p>
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