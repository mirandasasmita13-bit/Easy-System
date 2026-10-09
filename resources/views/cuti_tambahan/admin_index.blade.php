@extends('layouts.utama')

@section('title', 'Monitoring Cuti Tambahan')

@section('content')

<div class="mb-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
            Monitoring Cuti Tambahan
        </h2>
        <p class="mt-2 text-slate-500">
            Daftar semua pengajuan cuti tambahan pegawai.
        </p>
    </div>

    {{-- FILTER --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nomor SICT atau nama..."
                       class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pegawai</label>
                <select name="user_id"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    <option value="">-- Semua Pegawai --</option>
                    @foreach($daftarPegawai as $p)
                        <option value="{{ $p->id }}" @selected(request('user_id') == $p->id)>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-purple-700 transition">
                    Filter
                </button>
                <a href="{{ route('cuti_tambahan.admin.index') }}"
                   class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Reset
                </a>
            </div>

        </form>
    </div>

    {{-- TABEL --}}
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
                                @if($sict->surat && $sict->surat !== '-')
                                    <a href="{{ route('cuti_tambahan.admin.download', $sict->id) }}"
                                       class="text-purple-600 hover:underline font-semibold">
                                        Download
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                Belum ada data cuti tambahan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection