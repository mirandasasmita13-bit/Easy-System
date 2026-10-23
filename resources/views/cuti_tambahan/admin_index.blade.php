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

    {{-- SUCCESS / ERROR --}}
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FILTER --}}
    <div class="rounded-2xl border border-purple-200 bg-gradient-to-r from-purple-50 to-white shadow-sm p-4 mb-5">
        <form method="GET" class="flex flex-col sm:flex-row gap-2">

            <input type="hidden" name="tab" value="cuti-tambahan">

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nomor SICT / nama..."
                   class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

            <select name="user_id"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                <option value="">Semua Pegawai</option>
                @foreach($daftarPegawai as $p)
                    <option value="{{ $p->id }}" @selected(request('user_id') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>

            <select name="status"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="dibatalkan" @selected(request('status') === 'dibatalkan')>Dibatalkan</option>
            </select>

            <button type="submit"
                    class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700 transition whitespace-nowrap shadow-sm">
                Filter
            </button>

            <a href="{{ route('admin.control', ['tab' => 'cuti-tambahan']) }}"
               class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition whitespace-nowrap text-center">
                Reset
            </a>

        </form>
    </div>

    {{-- 2 KOLOM: DAFTAR SICT + REKAP KUOTA --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KIRI: DAFTAR SICT --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-purple-200 bg-white shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-purple-100 bg-gradient-to-r from-purple-50 to-white">
                    <h2 class="font-bold text-purple-800 flex items-center gap-2">
                        <span>📋</span>
                        <span>Daftar SICT</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Semua pengajuan cuti tambahan pegawai.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-purple-100 text-purple-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold">Nomor</th>
                                <th class="px-4 py-3 text-left font-bold">Nama Pemohon</th>
                                <th class="px-4 py-3 text-left font-bold">Tanggal</th>
                                <th class="px-4 py-3 text-left font-bold">Hari</th>
                                <th class="px-4 py-3 text-left font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($daftarSict as $sict)
                                <tr class="hover:bg-purple-50/50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-purple-100 text-purple-800 text-xs font-bold">
                                            {{ $sict->nomor_sict }}
                                        </span>
                                        @if($sict->status === 'dibatalkan')
                                            <span class="ml-1 px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-bold">BATAL</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 font-medium">
                                        {{ $sict->user->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                        {{ $sict->tanggal_mulai->translatedFormat('d M Y') }}
                                        @if($sict->tanggal_selesai->ne($sict->tanggal_mulai))
                                            <span class="text-slate-400">–</span>
                                            {{ $sict->tanggal_selesai->translatedFormat('d M Y') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                                            {{ $sict->jumlah_hari }} hari
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-3 items-center">
                                            @if($sict->surat && $sict->surat !== '-')
                                                <a href="{{ route('cuti_tambahan.admin.download', $sict->id) }}"
                                                   class="inline-flex items-center gap-1 text-purple-600 hover:text-purple-800 font-semibold text-xs transition">
                                                    <span>⬇</span>
                                                    <span>Download</span>
                                                </a>
                                            @endif
                                            @if($sict->status === 'aktif')
                                                <form action="{{ route('cuti_tambahan.admin.cancel', $sict->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Batalkan SICT {{ $sict->nomor_sict }}?');">
                                                    @csrf
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 font-semibold text-xs transition">
                                                        <span>✕</span>
                                                        <span>Batalkan</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
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

        {{-- KANAN: REKAP KUOTA --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl border border-purple-200 bg-white shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-purple-100 bg-gradient-to-r from-purple-50 to-white">
                    <h2 class="font-bold text-purple-800 flex items-center gap-2">
                        <span>📊</span>
                        <span>Rekap Kuota</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Cuti tambahan pegawai {{ date('Y') }}.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-purple-100 text-purple-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold">Nama</th>
                                <th class="px-4 py-3 text-center font-bold">Pakai</th>
                                <th class="px-4 py-3 text-center font-bold">Sisa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rekapKuota as $r)
                                @php
                                    $warnaPakai = $r['terpakai'] == 0 ? 'text-slate-400' : 'text-slate-700 font-semibold';
                                    $warnaSisa = $r['sisa'] <= 0 ? 'bg-red-100 text-red-700'
                                              : ($r['sisa'] <= 3 ? 'bg-amber-100 text-amber-700'
                                              : 'bg-green-100 text-green-700');
                                @endphp
                                <tr class="hover:bg-purple-50/50 transition">
                                    <td class="px-4 py-2.5 text-slate-700 font-medium">
                                        {{ $r['nama'] }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center {{ $warnaPakai }}">
                                        {{ $r['terpakai'] }}
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] px-2.5 py-0.5 rounded-full text-xs font-bold {{ $warnaSisa }}">
                                            {{ $r['sisa'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-slate-400">
                                        Belum ada pegawai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER: TOTAL --}}
                <div class="px-4 py-3 border-t border-purple-100 bg-purple-50">
                    <div class="flex justify-between text-xs text-purple-800 font-semibold">
                        <span>👥 Total: {{ count($rekapKuota) }} pegawai</span>
                        <span>🎯 Kuota: 12/pegawai</span>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

@endsection