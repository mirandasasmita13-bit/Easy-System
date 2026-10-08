@extends('layouts.utama')

@section('title', 'Cuti Tambahan')

@section('content')

<div class="mb-6">
    <div class="flex flex-col gap-6">

        {{-- JUDUL --}}
        <div>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                Cuti Tambahan
            </h2>

            <p class="mt-2 text-slate-500">
                Kelola dan lihat riwayat cuti tambahan Anda.
            </p>
        </div>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- ERROR --}}
        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold mb-1">Ada data yang perlu diperbaiki:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- CARD: NOMOR SICT BERIKUTNYA --}}
        <div class="rounded-2xl border border-purple-200 bg-gradient-to-br from-purple-50 to-white p-6 shadow-sm">
            <p class="text-sm font-semibold text-purple-600 uppercase tracking-wide">
                Nomor SICT Berikutnya
            </p>

            <p class="mt-2 text-4xl font-extrabold text-purple-700">
                {{ $nomorBerikutnya }}
            </p>

            <p class="mt-2 text-sm text-slate-500">
                Nomor ini otomatis diberikan saat Anda menyimpan cuti tambahan.
            </p>
        </div>


        {{-- FORM --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="font-bold text-slate-800">Tambah Cuti Tambahan</h2>
            </div>

            <form action="{{ route('cuti_tambahan.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="p-6 space-y-6">

                @csrf

                {{-- TANGGAL --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Mulai
                        </label>
                        <input type="date"
                               name="tanggal_mulai"
                               value="{{ old('tanggal_mulai') }}"
                               required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Selesai
                        </label>
                        <input type="date"
                               name="tanggal_selesai"
                               value="{{ old('tanggal_selesai') }}"
                               required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    </div>
                </div>

                <p class="text-xs text-slate-400 -mt-2">
                    Sabtu, Minggu, dan tanggal merah tidak dihitung sebagai hari cuti.
                </p>

                {{-- KETERANGAN --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Keterangan
                    </label>
                    <textarea name="keterangan"
                              rows="4"
                              placeholder="Tambahkan keterangan jika diperlukan..."
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none resize-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">{{ old('keterangan') }}</textarea>
                </div>

                {{-- SURAT --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Surat Cuti
                    </label>
                    <input type="file"
                           name="surat"
                           accept=".pdf,.jpg,.jpeg,.png"
                           required
                           class="block w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-purple-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-purple-700">
                    <p class="mt-2 text-xs text-slate-400">
                        Format PDF, JPG, JPEG, atau PNG. Maksimal 5 MB.
                    </p>
                </div>

                {{-- BUTTON --}}
                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-purple-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-purple-700">
                        Simpan Cuti Tambahan
                    </button>
                </div>

            </form>
        </div>


        {{-- DAFTAR SICT TAHUN INI (mirip Excel) --}}
        <div>
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-800">
                    Daftar SICT Tahun 2026
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Agenda penomoran cuti tambahan tahun ini.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Nomor SICT</th>
                                <th class="px-4 py-3 text-left font-semibold">Nama Pemohon</th>
                                <th class="px-4 py-3 text-left font-semibold">Tanggal Cuti</th>
                                <th class="px-4 py-3 text-left font-semibold">Jumlah Hari</th>
                                <th class="px-4 py-3 text-left font-semibold">Surat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($daftarSict as $sict)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-semibold text-purple-700">
                                        {{ $sict->nomor_sict }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">
                                        {{ $sict->user->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $sict->tanggal_mulai->translatedFormat('d M Y') }}
                                        @if($sict->tanggal_selesai->ne($sict->tanggal_mulai))
                                            - {{ $sict->tanggal_selesai->translatedFormat('d M Y') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $sict->jumlah_hari }} hari
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($sict->surat)
                                            <a href="{{ asset('storage/' . $sict->surat) }}"
                                               target="_blank"
                                               class="text-purple-600 hover:underline font-semibold">
                                                Lihat
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                        Belum ada data SICT.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        {{-- RIWAYAT PRIBADI --}}
        <div>
            <div class="mb-4">
                <h2 class="text-lg font-bold text-slate-800">
                    Riwayat Cuti Tambahan Saya
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Daftar cuti tambahan yang pernah Anda ajukan.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                @forelse($riwayatCuti as $cuti)
                    <div class="p-5 border-b border-slate-100 last:border-b-0">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <p class="font-semibold text-purple-700">
                                    {{ $cuti->nomor_sict ?? '-' }}
                                </p>

                                <p class="font-semibold text-slate-800 mt-1">
                                    {{ $cuti->tanggal_mulai->translatedFormat('d F Y') }}
                                    @if($cuti->tanggal_selesai->format('Y-m-d') !== $cuti->tanggal_mulai->format('Y-m-d'))
                                        - {{ $cuti->tanggal_selesai->translatedFormat('d F Y') }}
                                    @endif
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $cuti->jumlah_hari }} hari
                                </p>

                                @if($cuti->keterangan)
                                    <p class="mt-2 text-sm text-slate-600">
                                        {{ $cuti->keterangan }}
                                    </p>
                                @endif
                            </div>

                            @if($cuti->surat)
                                <a href="{{ asset('storage/' . $cuti->surat) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center rounded-lg border border-purple-200 px-4 py-2 text-sm font-semibold text-purple-600 hover:bg-purple-50 transition">
                                    Lihat Surat
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="mt-4 font-semibold text-slate-700">
                            Belum ada cuti tambahan
                        </p>
                        <p class="mt-1 text-sm text-slate-400">
                            Riwayat cuti tambahan Anda akan muncul di sini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection