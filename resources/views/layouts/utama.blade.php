<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Easy System')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>


@php
    $user = auth()->user();
    $role = $user->role;
@endphp


<body class="bg-[#f7f7fb] text-gray-900 overflow-hidden">

<div class="flex h-screen w-screen overflow-hidden">


    {{-- =========================================================
        SIDEBAR DESKTOP
    ========================================================== --}}

    <aside class="hidden lg:flex w-[280px] min-w-[280px] h-screen shrink-0 flex-col bg-[#21152f] text-white">


        {{-- =====================================================
            LOGO
        ====================================================== --}}

        <div class="px-6 py-7 shrink-0">

            <a href="{{ url('/dashboard') }}" class="flex items-center gap-3">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-purple-500 text-xl font-bold">
                    E
                </div>

                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-white">Easy System</h1>
                    <p class="text-xs text-purple-300">Office Management</p>
                </div>

            </a>

        </div>


        {{-- GARIS --}}
        <div class="mx-6 border-t border-white/10"></div>


        {{-- =====================================================
            MENU DESKTOP
        ====================================================== --}}

        <nav class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden px-4 py-6 hide-scrollbar">


            {{-- =================================================
                ADMIN
            ================================================== --}}

            @if($role === 'admin')


                {{-- MAIN MENU --}}
                <p class="mb-3 px-4 text-[10px] uppercase tracking-[0.2em] text-purple-300">
                    Main Menu
                </p>


                {{-- DASHBOARD --}}
                <a href="{{ url('/dashboard') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('dashboard')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('dashboard') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('dashboard') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Dashboard</span>

                </a>


                {{-- PEMISAH --}}
                <div class="my-5 h-px bg-white/10"></div>


                {{-- ADMINISTRASI --}}
                <p class="mb-3 px-4 text-[10px] uppercase tracking-[0.2em] text-purple-300">
                    Administrasi
                </p>


                {{-- DATA PPNPN --}}
                <a href="{{ url('/ppnpn') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('ppnpn*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('ppnpn*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('ppnpn*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Data PPNPN</span>

                </a>


                {{-- REKAP ABSENSI --}}
                <a href="{{ url('/rekap') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('rekap') || request()->is('rekap/export*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('rekap') || request()->is('rekap/export*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('rekap') || request()->is('rekap/export*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Rekap Absensi</span>

                </a>


                {{-- REKAP LEMBUR --}}
                <a href="{{ url('/rekap-lembur') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('rekap-lembur*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('rekap-lembur*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('rekap-lembur*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Rekap Lembur</span>

                </a>


                {{-- LAPORAN --}}
                <a href="{{ url('/laporan') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('laporan*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('laporan*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('laporan*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Laporan</span>

                </a>


                {{-- PENGAJUAN (nyala juga saat di /approval/* atau /admin/cuti/*) --}}
                @php
                    $pengajuanAktif = request()->is('pengajuan*')
                        || request()->is('approval/*')
                        || request()->is('admin/cuti*');
                @endphp

                <a href="{{ url('/pengajuan') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ $pengajuanAktif
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ $pengajuanAktif ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ $pengajuanAktif ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Pengajuan</span>

                </a>



            {{-- =================================================
                PPNPN
            ================================================== --}}

            @elseif($role === 'ppnpn')


                {{-- MAIN MENU --}}
                <p class="mb-3 px-4 text-[10px] uppercase tracking-[0.2em] text-purple-300">
                    Main Menu
                </p>


                {{-- DASHBOARD --}}
                <a href="{{ url('/dashboard') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('dashboard')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('dashboard') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('dashboard') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Dashboard</span>

                </a>


                {{-- ABSENSI --}}
                <a href="{{ url('/absensi') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('absensi*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('absensi*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('absensi*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Absensi</span>

                </a>


                {{-- PEMISAH --}}
                <div class="my-5 h-px bg-white/10"></div>


                {{-- ADMINISTRASI --}}
                <p class="mb-3 px-4 text-[10px] uppercase tracking-[0.2em] text-purple-300">
                    Administrasi
                </p>


                {{-- CUTI --}}
                <a href="{{ url('/pengajuan_cuti') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('pengajuan_cuti*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('pengajuan_cuti*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('pengajuan_cuti*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Cuti</span>

                </a>


                {{-- LUPA ABSEN --}}
                <a href="{{ url('/lupa-absen') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('lupa-absen*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('lupa-absen*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('lupa-absen*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Lupa Absen</span>

                </a>


                {{-- SURAT LAINNYA --}}
                <a href="{{ url('/pengajuan_surat') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('pengajuan_surat*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('pengajuan_surat*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('pengajuan_surat*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Surat Lainnya</span>

                </a>


                {{-- LEMBUR --}}
                <a href="{{ url('/lembur') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('lembur*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('lembur*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('lembur*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Lembur</span>

                </a>


                {{-- REKAP ABSENSI --}}
                <a href="{{ url('/rekap-saya') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('rekap-saya*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('rekap-saya*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('rekap-saya*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Rekap Absensi</span>

                </a>



            {{-- =================================================
                PEGAWAI
            ================================================== --}}

            @elseif($role === 'pegawai')


                {{-- MAIN MENU --}}
                <p class="mb-3 px-4 text-[10px] uppercase tracking-[0.2em] text-purple-300">
                    Main Menu
                </p>


                {{-- DASHBOARD --}}
                <a href="{{ url('/dashboard') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('dashboard')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('dashboard') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('dashboard') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Dashboard</span>

                </a>


                {{-- PEMISAH --}}
                <div class="my-5 h-px bg-white/10"></div>


                {{-- ADMINISTRASI --}}
                <p class="mb-3 px-4 text-[10px] uppercase tracking-[0.2em] text-purple-300">
                    Administrasi
                </p>


                {{-- CUTI TAMBAHAN --}}
                <a href="{{ route('cuti_tambahan.index') }}"
                   class="flex items-center gap-4 rounded-xl px-4 py-3 transition
                          {{ request()->is('cuti-tambahan*')
                              ? 'bg-purple-500/20 text-white'
                              : 'text-purple-100 hover:bg-white/5' }}">

                    <span class="text-sm {{ request()->is('cuti-tambahan*') ? 'text-purple-300' : 'text-purple-400' }}">
                        {{ request()->is('cuti-tambahan*') ? '●' : '○' }}
                    </span>

                    <span class="text-sm font-medium">Cuti Tambahan</span>

                </a>


            @endif

        </nav>



        {{-- =========================================================
            PROFILE DESKTOP
        ========================================================== --}}

        <div class="shrink-0 p-5">

            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">


                {{-- PROFILE --}}
                <a href="{{ url('/profil') }}"
                   class="flex items-center gap-3 rounded-xl p-1 transition hover:bg-white/5">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-purple-500 text-lg font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0">

                        <p class="truncate text-sm font-semibold text-white">
                            {{ $user->name }}
                        </p>

                        <p class="mt-0.5 text-xs text-purple-300">
                            @if($role === 'admin')
                                Administrator
                            @elseif($role === 'ppnpn')
                                PPNPN
                            @else
                                Pegawai
                            @endif
                        </p>

                    </div>

                </a>


                {{-- KELUAR --}}
                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button type="submit"
                            class="mt-3 flex w-full items-center gap-2 text-left text-xs text-purple-300 transition hover:text-white">

                        <span class="text-sm">↪</span>

                        Keluar

                    </button>

                </form>

            </div>

        </div>


    </aside>



    {{-- =========================================================
        MOBILE TOP BAR
    ========================================================== --}}

    <div class="fixed left-0 right-0 top-0 z-50 flex h-16 items-center justify-between bg-[#21152f] px-5 text-white shadow-lg lg:hidden">

        <a href="{{ url('/dashboard') }}" class="flex items-center gap-3">

            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-purple-500 font-bold">
                E
            </div>

            <div>
                <p class="text-sm font-bold">Easy System</p>
                <p class="text-[10px] text-purple-300">Office Management</p>
            </div>

        </a>


        {{-- MENU BUTTON --}}
        <button type="button"
                onclick="toggleMobileMenu()"
                class="flex h-10 w-10 items-center justify-center rounded-xl text-2xl text-white transition hover:bg-white/10"
                aria-label="Buka menu">
            ⋮
        </button>

    </div>



    {{-- =========================================================
        MOBILE MENU
    ========================================================== --}}

    <div id="mobileMenu"
         class="fixed right-4 top-16 z-50 hidden w-64 rounded-2xl border border-white/10 bg-[#21152f] p-3 shadow-2xl lg:hidden">


        {{-- =====================================================
            ADMIN MOBILE
        ====================================================== --}}

        @if($role === 'admin')

            <a href="{{ url('/dashboard') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('dashboard') ? '●' : '○' }}
                </span>
                Dashboard
            </a>

            <a href="{{ url('/ppnpn') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('ppnpn*') ? '●' : '○' }}
                </span>
                Data PPNPN
            </a>

            <a href="{{ url('/rekap') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('rekap') || request()->is('rekap/export*') ? '●' : '○' }}
                </span>
                Rekap Absensi
            </a>

            <a href="{{ url('/rekap-lembur') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('rekap-lembur*') ? '●' : '○' }}
                </span>
                Rekap Lembur
            </a>

            <a href="{{ url('/laporan') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('laporan*') ? '●' : '○' }}
                </span>
                Laporan
            </a>

            {{-- PENGAJUAN (nyala juga saat di /approval/* atau /admin/cuti/*) --}}
            <a href="{{ url('/pengajuan') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('pengajuan*') || request()->is('approval/*') || request()->is('admin/cuti*') ? '●' : '○' }}
                </span>
                Pengajuan
            </a>



        {{-- =====================================================
            PPNPN MOBILE
        ====================================================== --}}

        @elseif($role === 'ppnpn')

            <a href="{{ url('/dashboard') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('dashboard') ? '●' : '○' }}
                </span>
                Dashboard
            </a>

            <a href="{{ url('/absensi') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('absensi*') ? '●' : '○' }}
                </span>
                Absensi
            </a>

            <div class="my-2 h-px bg-white/10"></div>

            <a href="{{ url('/pengajuan_cuti') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('pengajuan_cuti*') ? '●' : '○' }}
                </span>
                Cuti
            </a>

            <a href="{{ url('/lupa-absen') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('lupa-absen*') ? '●' : '○' }}
                </span>
                Lupa Absen
            </a>

            <a href="{{ url('/pengajuan_surat') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('pengajuan_surat*') ? '●' : '○' }}
                </span>
                Surat Lainnya
            </a>

            <a href="{{ url('/lembur') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('lembur*') ? '●' : '○' }}
                </span>
                Lembur
            </a>

            <a href="{{ url('/rekap-saya') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('rekap-saya*') ? '●' : '○' }}
                </span>
                Rekap Absensi
            </a>



        {{-- =====================================================
            PEGAWAI MOBILE
        ====================================================== --}}

        @elseif($role === 'pegawai')

            <a href="{{ url('/dashboard') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('dashboard') ? '●' : '○' }}
                </span>
                Dashboard
            </a>

            <div class="my-2 h-px bg-white/10"></div>

            <a href="{{ route('cuti_tambahan.index') }}" class="mobile-menu-item">
                <span class="text-purple-300">
                    {{ request()->is('cuti-tambahan*') ? '●' : '○' }}
                </span>
                Cuti Tambahan
            </a>

        @endif



        {{-- PEMISAH --}}
        <div class="my-2 h-px bg-white/10"></div>


        {{-- PROFIL --}}
        <a href="{{ url('/profil') }}" class="mobile-menu-item">
            <span class="text-purple-300">
                {{ request()->is('profil*') ? '●' : '○' }}
            </span>
            Profil
        </a>


        {{-- KELUAR --}}
        <form method="POST" action="{{ route('logout') }}">

            @csrf

            <button type="submit"
                    class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-purple-300 transition hover:bg-white/10">

                <span>↪</span>

                Keluar

            </button>

        </form>

    </div>



    {{-- =========================================================
        KONTEN UTAMA
    ========================================================== --}}

    <main class="min-w-0 flex-1 h-screen overflow-x-hidden overflow-y-auto p-5 pt-20 lg:p-8 lg:pt-8 xl:p-10 xl:pt-10">

        @yield('content')

    </main>


</div>



{{-- =========================================================
    SCRIPT MOBILE MENU
========================================================== --}}

<script>

    function toggleMobileMenu() {

        const menu = document.getElementById('mobileMenu');

        menu.classList.toggle('hidden');

    }


    document.addEventListener('click', function(event) {

        const menu = document.getElementById('mobileMenu');

        const button = event.target.closest('button[onclick="toggleMobileMenu()"]');

        if (!menu.contains(event.target) && !button) {

            menu.classList.add('hidden');

        }

    });

</script>



{{-- =========================================================
    STYLE MENU MOBILE
========================================================== --}}

<style>

    .mobile-menu-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        color: white;
        font-size: 0.875rem;
        transition: 0.2s;
    }


    .mobile-menu-item:hover {
        background: rgba(255,255,255,0.10);
    }

</style>



{{-- =========================================================
    SCRIPT DARI HALAMAN
========================================================== --}}

@stack('scripts')


</body>
</html>