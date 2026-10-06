@extends('layouts.utama')

@section('title', 'Data Pengguna')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900"> 
            Data Pengguna 
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Kelola data PPNPN & Magang/PKL yang terdaftar di sistem.
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


    {{-- STATISTIK (3 CARD) --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- AKTIF --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">
                        {{ $role === 'magang' ? 'Magang Aktif' : 'PPNPN Aktif' }}
                    </p>
                    <p class="mt-1 text-3xl font-bold text-emerald-600">{{ $countAktif }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl font-bold">
                    ✓
                </div>
            </div>
        </div>

        {{-- NONAKTIF --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">
                        {{ $role === 'magang' ? 'Magang Nonaktif' : 'PPNPN Nonaktif' }}
                    </p>
                    <p class="mt-1 text-3xl font-bold text-slate-500">{{ $countNonaktif }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 text-xl font-bold">
                    —
                </div>
            </div>
        </div>

        {{-- TOTAL --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">
                        Total {{ $role === 'magang' ? 'Magang' : 'PPNPN' }}
                    </p>
                    <p class="mt-1 text-3xl font-bold text-purple-600">{{ $countSemua }}</p>
                    <p class="text-xs text-gray-400 mt-1">Aktif + Nonaktif</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 text-sm font-bold">
                    Σ
                </div>
            </div>
        </div>

    </div>


    {{-- DAFTAR PENGGUNA --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- HEADER TABEL + TAB ROLE --}}
        <div class="px-6 py-5 border-b border-gray-100">

            {{-- BARIS 1: Judul + Tab Role --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

                {{-- KIRI: Judul --}}
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Daftar {{ $role === 'magang' ? 'Magang / PKL' : 'PPNPN' }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Data pengguna dengan kategori {{ $role === 'magang' ? 'Magang / PKL' : 'PPNPN' }}.
                    </p>
                </div>

                {{-- KANAN: Tab Role --}}
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('ppnpn.index', ['role' => 'ppnpn', 'filter' => $filter]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition
                              {{ $role === 'ppnpn'
                                  ? 'bg-purple-600 text-white shadow-md shadow-purple-200'
                                  : 'bg-white border border-slate-200 text-slate-500 hover:border-purple-200 hover:text-purple-600' }}">
                        👷 PPNPN
                        <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-[11px] font-bold
                            {{ $role === 'ppnpn' ? 'bg-purple-500 text-white' : 'bg-slate-100 text-slate-500' }}">
                            {{ $countPpnpnSemua }}
                        </span>
                    </a>

                    <a href="{{ route('ppnpn.index', ['role' => 'magang', 'filter' => $filter]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold whitespace-nowrap transition
                              {{ $role === 'magang'
                                  ? 'bg-purple-600 text-white shadow-md shadow-purple-200'
                                  : 'bg-white border border-slate-200 text-slate-500 hover:border-purple-200 hover:text-purple-600' }}">
                        🎓 Magang / PKL
                        <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full text-[11px] font-bold
                            {{ $role === 'magang' ? 'bg-purple-500 text-white' : 'bg-slate-100 text-slate-500' }}">
                            {{ $countMagangSemua }}
                        </span>
                    </a>
                </div>

            </div>

            {{-- BARIS 2: Filter Status + Search (SEJAJAR) --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                {{-- KIRI: Filter Status --}}
                <div class="flex flex-nowrap gap-2">
                    <a href="{{ route('ppnpn.index', ['role' => $role, 'filter' => 'semua']) }}"
                       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-semibold transition
                              {{ $filter === 'semua'
                                  ? 'bg-purple-600 text-white shadow-md shadow-purple-200'
                                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Semua ({{ $countSemua }})
                    </a>
                    <a href="{{ route('ppnpn.index', ['role' => $role, 'filter' => 'aktif']) }}"
                       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-semibold transition
                              {{ $filter === 'aktif'
                                  ? 'bg-purple-600 text-white shadow-md shadow-purple-200'
                                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Aktif ({{ $countAktif }})
                    </a>
                    <a href="{{ route('ppnpn.index', ['role' => $role, 'filter' => 'nonaktif']) }}"
                       class="whitespace-nowrap px-4 py-2 rounded-lg text-sm font-semibold transition
                              {{ $filter === 'nonaktif'
                                  ? 'bg-purple-600 text-white shadow-md shadow-purple-200'
                                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Nonaktif ({{ $countNonaktif }})
                    </a>
                </div>

                {{-- KANAN: Search (pendek) --}}
                <div class="relative shrink-0" style="width: 100%; max-width: 260px;">
                    <input type="text" id="searchPpnpn"
                        placeholder="Cari nama, username..."
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 pl-10 text-sm text-gray-700 focus:border-purple-400 focus:ring-2 focus:ring-purple-100 focus:outline-none">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1010.5 3a7.5 7.5 0 006.15 13.65z" />
                    </svg>
                </div>

            </div>

        </div>


        {{-- TABEL PENGGUNA --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-left text-gray-500">
                        <th class="px-6 py-4 font-semibold w-16">No</th>
                        <th class="px-6 py-4 font-semibold">
                            {{ $role === 'magang' ? 'Magang / PKL' : 'PPNPN' }}
                        </th>
                        <th class="px-6 py-4 font-semibold">NIK</th>
                        <th class="px-6 py-4 font-semibold">Username</th>
                        <th class="px-6 py-4 font-semibold text-center w-28">Status</th>
                        <th class="px-6 py-4 font-semibold text-center w-44">Aksi</th>
                    </tr>
                </thead>

                <tbody id="ppnpnTable" class="divide-y divide-gray-100">
                    @forelse($ppnpn as $index => $item)
                        <tr class="ppnpn-row hover:bg-gray-50/70 {{ $item->status === 'nonaktif' ? 'opacity-60' : '' }}"
                            data-search="{{ strtolower(
                                $item->name . ' ' .
                                ($item->username ?? '') . ' ' .
                                ($item->profil?->nik ?? '')
                            ) }}">

                            <td class="px-6 py-5 text-gray-500">{{ $index + 1 }}</td>

                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    @if($item->profil?->foto)
                                        <img src="{{ asset('storage/' . $item->profil->foto) }}"
                                             alt="Foto {{ $item->name }}"
                                             class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    @else
                                        <div class="w-10 h-10 rounded-full
                                                    {{ $item->role === 'magang' ? 'bg-amber-100 text-amber-700' : 'bg-purple-100 text-purple-700' }}
                                                    flex items-center justify-center font-semibold">
                                            {{ strtoupper(substr($item->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $item->role === 'magang' ? 'Magang / PKL' : 'PPNPN' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5 text-gray-600">
                                {{ $item->profil?->nik ?? '-' }}
                            </td>

                            <td class="px-6 py-5 text-gray-600">
                                {{ $item->username ?? '-' }}
                            </td>

                            <td class="px-6 py-5 text-center">
                                @if($item->status === 'aktif')
                                    <span class="inline-flex px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-semibold">
                                        Nonaktif
                                    </span>
                                    @if($item->tanggal_nonaktif)
                                        <p class="text-[10px] text-gray-400 mt-1">
                                            sejak {{ $item->tanggal_nonaktif->format('d/m/Y') }}
                                        </p>
                                    @endif
                                @endif
                            </td>

                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex gap-2">
                                    {{-- LIHAT --}}
                                    <button type="button"
                                            onclick="openPpnpnModal(
                                                @js($item->name),
                                                @js($item->username ?? '-'),
                                                @js($item->profil?->nik ?? '-'),
                                                @js($item->profil?->foto ? asset('storage/' . $item->profil->foto) : null),
                                                @js($item->role)
                                            )"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-purple-50 text-purple-700 text-xs font-medium hover:bg-purple-100 transition">
                                        Lihat
                                    </button>

                                    {{-- NONAKTIFKAN / AKTIFKAN --}}
                                    @if($item->status === 'aktif')
                                        <form action="{{ route('ppnpn.nonaktifkan', $item->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Nonaktifkan {{ $item->name }}?')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 transition">
                                                Nonaktifkan
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('ppnpn.aktifkan', $item->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Aktifkan kembali {{ $item->name }}?')">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-medium hover:bg-emerald-100 transition">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <div class="text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 10-6 0" />
                                    </svg>
                                    <p class="font-medium text-gray-500">
                                        Belum ada data {{ $role === 'magang' ? 'Magang / PKL' : 'PPNPN' }}
                                    </p>
                                    <p class="text-sm mt-1">
                                        {{ $role === 'magang' ? 'Magang' : 'PPNPN' }} yang melakukan registrasi akan muncul di sini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>


{{-- =====================================================
     MODAL DETAIL PENGGUNA
===================================================== --}}
<div id="ppnpnModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closePpnpnModal()"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Detail Pengguna</h3>
                    <p class="mt-1 text-sm text-gray-500">Informasi pengguna</p>
                </div>
                <button type="button" onclick="closePpnpnModal()"
                        class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-6">
                <div class="flex justify-center">
                    <img id="modalPhoto" src="" alt="Foto Pengguna"
                         class="hidden w-24 h-24 rounded-full object-cover border border-gray-200">
                    <div id="modalInitial"
                         class="w-24 h-24 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-3xl font-semibold"></div>
                </div>
                <div class="text-center mt-4">
                    <h4 id="modalName" class="text-xl font-bold text-gray-800">-</h4>
                    <span id="modalRole"
                          class="inline-flex mt-2 px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold">
                        PPNPN
                    </span>
                </div>
                <div class="mt-6 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">NIK</p>
                        <p id="modalNik" class="mt-1 text-sm font-medium text-gray-800">-</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Username</p>
                        <p id="modalEmail" class="mt-1 text-sm font-medium text-gray-800 break-all">-</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="button" onclick="closePpnpnModal()"
                        class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-100 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>


{{-- =====================================================
     JAVASCRIPT
===================================================== --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchPpnpn');
    const rows = document.querySelectorAll('.ppnpn-row');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            rows.forEach(row => {
                const searchText = row.dataset.search || '';
                row.classList.toggle('hidden', !searchText.includes(keyword));
            });
        });
    }

});


/* MODAL DETAIL PENGGUNA */
function openPpnpnModal(name, username, nik, foto, role) {
    const modal = document.getElementById('ppnpnModal');
    document.getElementById('modalName').textContent = name;
    document.getElementById('modalEmail').textContent = username;
    document.getElementById('modalNik').textContent = nik;

    const modalRole = document.getElementById('modalRole');
    if (role === 'magang') {
        modalRole.textContent = 'Magang / PKL';
        modalRole.className = 'inline-flex mt-2 px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-semibold';
    } else {
        modalRole.textContent = 'PPNPN';
        modalRole.className = 'inline-flex mt-2 px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold';
    }

    const modalPhoto = document.getElementById('modalPhoto');
    const modalInitial = document.getElementById('modalInitial');

    if (foto) {
        modalPhoto.src = foto;
        modalPhoto.classList.remove('hidden');
        modalInitial.classList.add('hidden');
    } else {
        modalInitial.textContent = name.trim().charAt(0).toUpperCase();
        modalInitial.classList.remove('hidden');
        modalPhoto.classList.add('hidden');
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closePpnpnModal() {
    document.getElementById('ppnpnModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}


/* ESC */
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closePpnpnModal();
});

</script>

@endsection