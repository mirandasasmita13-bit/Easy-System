@extends('layouts.utama')

@section('title', 'Manajemen Cuti — ' . $user->name)

@section('content')

{{-- HEADER --}}
<div class="mb-6">
    <a href="{{ route('manajemen-cuti.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-purple-600 transition mb-3">
        ← Kembali ke Manajemen Cuti
    </a>

    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
        Manajemen Cuti
    </h2>
    <p class="mt-1.5 text-sm text-slate-500">
        Kelola riwayat cuti untuk <strong>{{ $user->name }}</strong>.
    </p>
</div>


{{-- PESAN --}}
@if(session('success'))
    <div class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        ⚠️ {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{{-- INFO PPNPN + TOMBOL RESET --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

    <div class="flex items-center gap-3">
        @if($user->profil?->foto)
            <img src="{{ asset('storage/' . $user->profil->foto) }}"
                 alt="{{ $user->name }}"
                 class="w-14 h-14 rounded-full object-cover border-2 border-slate-200">
        @else
            <div class="w-14 h-14 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-lg">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        <div>
            <p class="font-bold text-slate-900 text-lg">{{ $user->name }}</p>
            <p class="text-xs text-slate-400 mt-0.5">
                {{ $user->username }} · Tahun cuti {{ $user->tahun_cuti ?? now()->year }}
            </p>
        </div>
    </div>

    <form action="{{ route('ppnpn.reset-cuti', $user->id) }}" method="POST"
          onsubmit="return confirm('Reset tahun cuti {{ $user->name }} ke {{ now()->year }}? Data cuti lama tetap tersimpan.')">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold transition shadow-md shadow-amber-200 whitespace-nowrap">
            🔄 Reset Cuti ke {{ now()->year }}
        </button>
    </form>

</div>


{{-- INFO CARDS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jatah</p>
        <p class="mt-2 text-3xl font-extrabold text-purple-600">{{ $user->jatah_cuti_tahunan ?? 12 }}</p>
        <p class="text-xs text-slate-400 mt-1">hari per tahun</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Cuti Sebelumnya</p>
        <p class="mt-2 text-3xl font-extrabold text-amber-600">{{ $totalManualTahunan }}</p>
        <p class="text-xs text-slate-400 mt-1">hari</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Di Sistem</p>
        <p class="mt-2 text-3xl font-extrabold text-sky-600">{{ $user->cutiTahunanDiSistem() }}</p>
        <p class="text-xs text-slate-400 mt-1">hari</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sisa Cuti</p>
        <p class="mt-2 text-3xl font-extrabold text-emerald-600">{{ $user->sisaCutiTahunan() }}</p>
        <p class="text-xs text-slate-400 mt-1">hari</p>
    </div>

</div>


{{-- FORM TAMBAH CUTI --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
    <h3 class="text-base font-bold text-slate-900 mb-4">➕ Tambah Cuti</h3>

    <form action="{{ route('ppnpn.manajemen-cuti.store', $user->id) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-100 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-100 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Cuti</label>
                <select name="jenis_cuti" required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-100 focus:outline-none">
                    <option value="tahunan">Cuti Tahunan</option>
                    <option value="alasan_penting">Cuti Alasan Penting</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan</label>
                <input type="text" name="alasan" value="{{ old('alasan') }}"
                       placeholder="Contoh: Keluarga"
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-100 focus:outline-none">
            </div>

        </div>

        <div class="mt-5 flex justify-end">
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-purple-600 text-white text-sm font-bold hover:bg-purple-700 transition shadow-md shadow-purple-200">
                Simpan Cuti
            </button>
        </div>
    </form>
</div>


{{-- RIWAYAT CUTI --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Riwayat Cuti</h3>
            <p class="text-xs text-slate-400 mt-0.5">Cuti yang dicatat admin (sebelum sistem berjalan)</p>
        </div>
        <span class="inline-flex px-3 py-1.5 rounded-full bg-purple-50 text-purple-600 text-xs font-semibold">
            {{ $cutiSebelumnya->count() }} data
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr class="text-left text-slate-500">
                    <th class="px-6 py-4 font-semibold w-16">No</th>
                    <th class="px-6 py-4 font-semibold">Tanggal</th>
                    <th class="px-6 py-4 font-semibold text-center">Jumlah</th>
                    <th class="px-6 py-4 font-semibold">Jenis</th>
                    <th class="px-6 py-4 font-semibold">Alasan</th>
                    <th class="px-6 py-4 font-semibold text-center w-44">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($cutiSebelumnya as $index => $cuti)
                    <tr class="hover:bg-slate-50/60">

                        <td class="px-6 py-4 text-slate-500">{{ $index + 1 }}</td>

                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">
                                {{ $cuti->tanggal_mulai->translatedFormat('d M Y') }}
                                @if($cuti->tanggal_mulai->toDateString() !== $cuti->tanggal_selesai->toDateString())
                                    – {{ $cuti->tanggal_selesai->translatedFormat('d M Y') }}
                                @endif
                            </p>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-bold">
                                {{ $cuti->jumlah_hari }} hari
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            @if($cuti->jenis_cuti === 'tahunan')
                                <span class="inline-flex px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 text-xs font-semibold">Tahunan</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-lg bg-orange-50 text-orange-700 text-xs font-semibold">Alasan Penting</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-slate-600">
                            {{ $cuti->alasan ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex gap-1.5">
                                <button type="button"
                                        onclick="openEditModal(
                                            {{ $cuti->id }},
                                            '{{ $cuti->tanggal_mulai->format('Y-m-d') }}',
                                            '{{ $cuti->tanggal_selesai->format('Y-m-d') }}',
                                            '{{ $cuti->jenis_cuti }}',
                                            @js($cuti->alasan ?? '')
                                        )"
                                        class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold hover:bg-indigo-100 transition">
                                    ✏️ Edit
                                </button>

                                <form action="{{ route('manajemen-cuti.destroy', $cuti->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus cuti ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-semibold hover:bg-red-100 transition">
                                        🗑 Hapus
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center">
                            <div class="mx-auto mb-3 w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center">
                                <span class="text-2xl">📋</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-500">Belum ada cuti tercatat</p>
                            <p class="text-xs text-slate-400 mt-1">Tambah cuti di form atas untuk memulai.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


{{-- MODAL EDIT --}}
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeEditModal()"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Edit Cuti</h3>
                <button type="button" onclick="closeEditModal()"
                        class="w-9 h-9 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100">
                    ✕
                </button>
            </div>

            <form id="editForm" method="POST">
                @csrf @method('PUT')

                <div class="px-6 py-5 space-y-4">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Mulai</label>
                        <input type="date" id="editMulai" name="tanggal_mulai" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-100 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Selesai</label>
                        <input type="date" id="editSelesai" name="tanggal_selesai" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-100 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Cuti</label>
                        <select id="editJenis" name="jenis_cuti" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-100 focus:outline-none">
                            <option value="tahunan">Cuti Tahunan</option>
                            <option value="alasan_penting">Cuti Alasan Penting</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan</label>
                        <input type="text" id="editAlasan" name="alasan"
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-100 focus:outline-none">
                    </div>

                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg bg-purple-600 text-white text-sm font-bold hover:bg-purple-700">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<script>
function openEditModal(id, mulai, selesai, jenis, alasan) {
    document.getElementById('editMulai').value = mulai;
    document.getElementById('editSelesai').value = selesai;
    document.getElementById('editJenis').value = jenis;
    document.getElementById('editAlasan').value = alasan;
    document.getElementById('editForm').action = '/manajemen-cuti/' + id;

    document.getElementById('editModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEditModal();
});
</script>

@endsection