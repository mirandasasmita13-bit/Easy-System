@extends('layouts.utama')

@section('title', 'Admin Control')

@section('content')

<div class="space-y-5">

    {{-- HEADER --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Admin Control Center
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Kelola seluruh data sistem dari satu halaman.
            </p>
        </div>

        <div class="inline-flex w-fit items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-2.5">
            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
            <span class="text-sm font-medium text-amber-700">Mode Admin</span>
        </div>
    </div>

    {{-- PESAN --}}
    @if(session('success'))
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tampilkan Password Baru (sekali) --}}
@if(session('new_password'))
    <div class="rounded-xl border-2 border-amber-300 bg-amber-50 px-5 py-4 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-amber-900">
                    ⚠️ Password baru untuk <strong>{{ session('new_password_user') }}</strong>:
                </p>
                <div class="mt-2 flex items-center gap-2">
                    <code class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border-2 border-amber-200 font-mono text-lg font-bold text-amber-800 select-all">
                        {{ session('new_password') }}
                    </code>
                    <button type="button"
                            onclick="navigator.clipboard.writeText('{{ session('new_password') }}'); this.innerHTML = '✅ Tersalin'; setTimeout(() => this.innerHTML = '📋 Salin', 2000)"
                            class="px-3 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition">
                        📋 Salin
                    </button>
                </div>
                <p class="mt-2 text-xs text-amber-700">
                    ⚠️ <strong>Catat sekarang!</strong> Password ini <strong>hanya muncul sekali</strong> dan tidak akan bisa dilihat lagi. Berikan ke user via WhatsApp/email, dan minta user ganti saat login pertama.
                </p>
            </div>
        </div>
    </div>
@endif

    @if(session('error'))
        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TAB NAVIGATION --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <div class="border-b border-slate-100 bg-gradient-to-r from-amber-50 to-white px-4 py-3 overflow-x-auto">
            <div class="flex items-center gap-1.5 min-w-max">

                @php
                    $tabs = [
                        'user'           => ['👤', 'Data User'],
                        'absensi'        => ['📋', 'Absensi'],
                        'lupa'           => ['⏰', 'Lupa Absen'],
                        'lembur'         => ['💼', 'Lembur'],
                        'surat'          => ['📄', 'Surat'],
                        'cuti'           => ['📅', 'Cuti'],
                        'cuti-lalu'      => ['📆', 'Cuti Lalu'],
                        'libur'          => ['🎉', 'Libur'],
                        'cuti-tambahan'  => ['📆',  'Cuti Tambahan'],
                    ];
                @endphp

                @foreach($tabs as $key => $label)
                    <a href="{{ route('admin.control', ['tab' => $key]) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition whitespace-nowrap
                              {{ $tab === $key
                                  ? 'bg-amber-500 text-white shadow-sm'
                                  : 'text-slate-600 hover:bg-slate-100' }}">
                        <span>{{ $label[0] }}</span>
                        <span>{{ $label[1] }}</span>
                    </a>
                @endforeach

            </div>
        </div>

        {{-- KONTEN TAB --}}
        <div class="p-5 sm:p-6">

            @if($tab === 'user')
                @include('admin.user-tab')

            @elseif($tab === 'absensi')
                @include('admin.absensi-tab')

            @elseif($tab === 'lupa')
                @include('admin.lupa-tab')

            @elseif($tab === 'lembur')
                @include('admin.lembur-tab')

            @elseif($tab === 'surat')
                @include('admin.surat-tab')

            @elseif($tab === 'cuti')
                @include('admin.cuti-tab')

            @elseif($tab === 'cuti-lalu')
                @include('admin.cuti-lalu-tab')

            @elseif($tab === 'libur')
                @include('admin.libur-tab')

            @elseif($tab === 'cuti-tambahan')
                @include('cuti_tambahan.admin_index')

            @endif
        </div>

    </div>

</div>

@endsection