@extends('layouts.utama')

@section('title', 'Manajemen Cuti')

@section('content')

{{-- HEADER --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Manajemen Cuti
    </h1>
    <p class="mt-1 text-sm text-gray-500">
        Kelola cuti tahunan seluruh PPNPN.
    </p>
</div>


{{-- PESAN --}}
@if(session('success'))
    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        ⚠️ {{ session('error') }}
    </div>
@endif


{{-- BANNER RESET --}}
<div class="mb-5 rounded-xl {{ $perluResetCuti > 0 ? 'border-amber-200 bg-amber-50' : 'border-purple-200 bg-purple-50' }} border px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="flex items-start gap-3">
        @if($perluResetCuti > 0)
            <span class="text-2xl">⚠️</span>
            <div>
                <p class="font-semibold text-amber-800">
                    {{ $perluResetCuti }} PPNPN masih di tahun cuti {{ now()->year - 1 }}
                </p>
                <p class="text-sm text-amber-700 mt-0.5">
                    Klik reset untuk mengembalikan semua ke tahun {{ now()->year }}.
                </p>
            </div>
        @else
            <div>
                <p class="font-semibold text-purple-800">
                    Reset Tahun Cuti
                </p>
                <p class="text-sm text-purple-700 mt-0.5">
                    Kembalikan semua cuti PPNPN aktif ke tahun {{ now()->year }}.
                </p>
            </div>
        @endif
    </div>

    <form action="{{ route('ppnpn.reset-semua-cuti') }}" method="POST"
          onsubmit="return confirm('Reset tahun cuti SEMUA PPNPN aktif ke {{ now()->year }}? Data cuti lama tetap tersimpan.')">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl {{ $perluResetCuti > 0 ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-200' : 'bg-purple-600 hover:bg-purple-700 shadow-purple-200' }} text-white text-sm font-bold transition shadow-md whitespace-nowrap">
            🔄 Reset Semua ke {{ now()->year }}
        </button>
    </form>
</div>


{{-- TABEL --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Cuti PPNPN</h2>
            <p class="text-sm text-gray-500 mt-1">Klik "Kelola" untuk atur cuti per orang.</p>
        </div>
        <span class="inline-flex px-3 py-1.5 rounded-full bg-purple-50 text-purple-600 text-xs font-semibold">
            {{ $ppnpn->count() }} PPNPN aktif
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="text-left text-gray-500">
                    <th class="px-6 py-4 font-semibold w-16">No</th>
                    <th class="px-6 py-4 font-semibold">Nama PPNPN</th>
                    <th class="px-6 py-4 font-semibold text-center w-24">Jatah</th>
                    <th class="px-6 py-4 font-semibold text-center w-28">Sebelum</th>
                    <th class="px-6 py-4 font-semibold text-center w-28">Di Sistem</th>
                    <th class="px-6 py-4 font-semibold text-center w-32">Sisa Cuti</th>
                    <th class="px-6 py-4 font-semibold text-center w-24">Tahun</th>
                    <th class="px-6 py-4 font-semibold text-center w-40">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($ppnpn as $index => $item)

                    @php
                        $sisa = $item->sisaCutiTahunan();
                        $sisaColor = $sisa <= 2
                            ? 'bg-red-50 text-red-700 border-red-200'
                            : ($sisa <= 5
                                ? 'bg-amber-50 text-amber-700 border-amber-200'
                                : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                    @endphp

                    <tr class="hover:bg-gray-50/70">

                        <td class="px-6 py-5 text-gray-500">{{ $index + 1 }}</td>

                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                @if($item->profil?->foto)
                                    <img src="{{ asset('storage/' . $item->profil->foto) }}"
                                         alt="{{ $item->name }}"
                                         class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $item->username }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-bold">
                                {{ $item->jatah_cuti_tahunan ?? 12 }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-bold">
                                {{ $item->cutiManualDiTahun() }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 text-xs font-bold">
                                {{ $item->cutiTahunanDiSistem() }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex px-3 py-1 rounded-lg border {{ $sisaColor }} text-sm font-bold">
                                {{ $sisa }} hari
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center text-gray-500 text-xs">
                            {{ $item->tahun_cuti ?? now()->year }}
                        </td>

                        <td class="px-6 py-5 text-center">
                            <a href="{{ route('ppnpn.manajemen-cuti', $item->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100 transition">
                                📋 Kelola
                            </a>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-14 text-center">
                            <p class="text-sm text-gray-500">Belum ada PPNPN aktif</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection@extends('layouts.utama')

@section('title', 'Manajemen Cuti')

@section('content')

{{-- HEADER --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Manajemen Cuti
    </h1>
    <p class="mt-1 text-sm text-gray-500">
        Kelola cuti tahunan seluruh PPNPN.
    </p>
</div>


{{-- PESAN --}}
@if(session('success'))
    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        ⚠️ {{ session('error') }}
    </div>
@endif


{{-- BANNER RESET --}}
<div class="mb-5 rounded-xl {{ $perluResetCuti > 0 ? 'border-amber-200 bg-amber-50' : 'border-purple-200 bg-purple-50' }} border px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="flex items-start gap-3">
        @if($perluResetCuti > 0)
            <span class="text-2xl">⚠️</span>
            <div>
                <p class="font-semibold text-amber-800">
                    {{ $perluResetCuti }} PPNPN masih di tahun cuti {{ now()->year - 1 }}
                </p>
                <p class="text-sm text-amber-700 mt-0.5">
                    Klik reset untuk mengembalikan semua ke tahun {{ now()->year }}.
                </p>
            </div>
        @else
            <span class="text-2xl">🎁</span>
            <div>
                <p class="font-semibold text-purple-800">
                    Reset Tahun Cuti
                </p>
                <p class="text-sm text-purple-700 mt-0.5">
                    Kembalikan semua cuti PPNPN aktif ke tahun {{ now()->year }}.
                </p>
            </div>
        @endif
    </div>

    <form action="{{ route('ppnpn.reset-semua-cuti') }}" method="POST"
          onsubmit="return confirm('Reset tahun cuti SEMUA PPNPN aktif ke {{ now()->year }}? Data cuti lama tetap tersimpan.')">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl {{ $perluResetCuti > 0 ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-200' : 'bg-purple-600 hover:bg-purple-700 shadow-purple-200' }} text-white text-sm font-bold transition shadow-md whitespace-nowrap">
            🔄 Reset Semua ke {{ now()->year }}
        </button>
    </form>
</div>


{{-- TABEL --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Daftar Cuti PPNPN</h2>
            <p class="text-sm text-gray-500 mt-1">Klik "Kelola" untuk atur cuti per orang.</p>
        </div>
        <span class="inline-flex px-3 py-1.5 rounded-full bg-purple-50 text-purple-600 text-xs font-semibold">
            {{ $ppnpn->count() }} PPNPN aktif
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="text-left text-gray-500">
                    <th class="px-6 py-4 font-semibold w-16">No</th>
                    <th class="px-6 py-4 font-semibold">Nama PPNPN</th>
                    <th class="px-6 py-4 font-semibold text-center w-24">Jatah</th>
                    <th class="px-6 py-4 font-semibold text-center w-28">Sebelum</th>
                    <th class="px-6 py-4 font-semibold text-center w-28">Di Sistem</th>
                    <th class="px-6 py-4 font-semibold text-center w-32">Sisa Cuti</th>
                    <th class="px-6 py-4 font-semibold text-center w-24">Tahun</th>
                    <th class="px-6 py-4 font-semibold text-center w-40">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($ppnpn as $index => $item)

                    @php
                        $sisa = $item->sisaCutiTahunan();
                        $sisaColor = $sisa <= 2
                            ? 'bg-red-50 text-red-700 border-red-200'
                            : ($sisa <= 5
                                ? 'bg-amber-50 text-amber-700 border-amber-200'
                                : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                    @endphp

                    <tr class="hover:bg-gray-50/70">

                        <td class="px-6 py-5 text-gray-500">{{ $index + 1 }}</td>

                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                @if($item->profil?->foto)
                                    <img src="{{ asset('storage/' . $item->profil->foto) }}"
                                         alt="{{ $item->name }}"
                                         class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $item->username }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-bold">
                                {{ $item->jatah_cuti_tahunan ?? 12 }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-bold">
                                {{ $item->cutiManualDiTahun() }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 text-xs font-bold">
                                {{ $item->cutiTahunanDiSistem() }}
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center">
                            <span class="inline-flex px-3 py-1 rounded-lg border {{ $sisaColor }} text-sm font-bold">
                                {{ $sisa }} hari
                            </span>
                        </td>

                        <td class="px-6 py-5 text-center text-gray-500 text-xs">
                            {{ $item->tahun_cuti ?? now()->year }}
                        </td>

                        <td class="px-6 py-5 text-center">
                            <a href="{{ route('ppnpn.manajemen-cuti', $item->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100 transition">
                                📋 Kelola
                            </a>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-14 text-center">
                            <p class="text-sm text-gray-500">Belum ada PPNPN aktif</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection