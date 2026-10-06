@extends('layouts.utama')

@section('title', 'Laporan')

@section('content')

{{-- HEADER --}}
<div class="mb-5">
    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
        Laporan
    </h2>

    <p class="mt-1 text-sm text-slate-500">
        Pantau aktivitas administrasi PPNPN.
    </p>
</div>


{{-- FILTER + PERIODE (1 CARD) --}}
<div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm mb-5">

    <form method="GET" action="{{ route('laporan') }}">

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">

            {{-- KIRI: Periode --}}
            <div class="shrink-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Periode Laporan
                </p>
                <p class="mt-1 text-lg font-extrabold text-slate-900">
                    {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    {{ \Carbon\Carbon::parse($tanggalAwal)->translatedFormat('d M') }}
                    –
                    {{ \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d M Y') }}
                </p>
            </div>


            {{-- KANAN: Filter --}}
            <div class="flex-1 min-w-0">

                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Tampilkan Laporan
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-[1fr_110px_1fr_130px] gap-2">

                    <select name="bulan"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>

                    <select name="tahun"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        @for($i = now()->year - 2; $i <= now()->year + 1; $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>

                    <select name="ppnpn"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        <option value="">Semua PPNPN</option>
                        @foreach($ppnpn as $p)
                            <option value="{{ $p->id }}" {{ (string) $ppnpnId === (string) $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-purple-200 transition hover:bg-purple-700">
                        Terapkan
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>


{{-- AKTIVITAS --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

    {{-- HEADER + TAB --}}
    <div class="px-5 pt-4">

        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-base text-slate-900">Aktivitas Administrasi</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar aktivitas PPNPN periode ini.</p>
            </div>
            <span class="text-xs font-semibold text-slate-400">
                {{ $semuaAktivitas->count() }} aktivitas
            </span>
        </div>

        {{-- TAB --}}
        <div class="flex flex-wrap gap-1.5 border-b border-slate-100 pb-3">

            <button type="button" data-tab="semua"
                    class="laporan-tab active-tab inline-flex items-center gap-1.5 rounded-lg bg-purple-600 px-3 py-2 text-xs font-semibold text-white shadow-sm transition">
                Semua
            </button>

            <button type="button" data-tab="cuti"
                    class="laporan-tab inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                Cuti
            </button>

            <button type="button" data-tab="lupa-absen"
                    class="laporan-tab inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                Lupa Absen
            </button>

            <button type="button" data-tab="lembur"
                    class="laporan-tab inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                Lembur
            </button>

            <button type="button" data-tab="surat"
                    class="laporan-tab inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                Surat
            </button>

        </div>

    </div>


    {{-- TABLE --}}
    <div class="overflow-x-auto">

        <table class="w-full min-w-[1000px]">

            {{-- HEADER UNGU SOFT ⬅️ --}}
            <thead>
                <tr class="bg-gradient-to-r from-purple-50 via-purple-50/70 to-indigo-50 border-b border-purple-100">
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-purple-700">
                        Tanggal
                    </th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-purple-700">
                        PPNPN
                    </th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-purple-700">
                        Aktivitas
                    </th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-purple-700">
                        Keterangan
                    </th>
                    <th class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-wider text-purple-700">
                        Bukti
                    </th>
                    <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-purple-700">
                        Disetujui Oleh
                    </th>
                </tr>
            </thead>


            <tbody class="divide-y divide-slate-100">

                @forelse($semuaAktivitas as $aktivitas)

                    @php
                        $tipe = strtolower($aktivitas['tipe'] ?? '');

                        $kategori = match(true) {
                            str_contains($tipe, 'lupa')   => 'lupa-absen',
                            str_contains($tipe, 'lembur') => 'lembur',
                            str_contains($tipe, 'surat')  => 'surat',
                            default                        => 'cuti',
                        };

                        $namaPpnpn        = $aktivitas['ppnpn'] ?? '-';
                        $tanggalAktivitas = $aktivitas['tanggal'] ?? null;
                        $keterangan       = $aktivitas['keterangan'] ?? '-';
                        $labelAktivitas   = $aktivitas['aktivitas'] ?? '-';
                        $bukti            = $aktivitas['bukti'] ?? null;
                        $namaBukti        = $aktivitas['nama_bukti'] ?? 'Bukti';
                        $approvedBy       = $aktivitas['approved_by'] ?? null;
                        $approvedAt       = $aktivitas['approved_at'] ?? null;
                        $status           = $aktivitas['status'] ?? null;

                        $labelLower = strtolower($labelAktivitas);
                        $badgeClass = match(true) {
                            str_contains($labelLower, 'cuti')   => 'bg-purple-50 text-purple-700',
                            str_contains($labelLower, 'lupa')   => 'bg-blue-50 text-blue-700',
                            str_contains($labelLower, 'lembur') => 'bg-amber-50 text-amber-700',
                            str_contains($labelLower, 'sakit')  => 'bg-red-50 text-red-600',
                            str_contains($labelLower, 'surat')  => 'bg-emerald-50 text-emerald-700',
                            default                              => 'bg-slate-100 text-slate-600',
                        };

                        $ext = $bukti ? strtolower(pathinfo($bukti, PATHINFO_EXTENSION)) : null;
                        $formatLabel = match($ext) {
                            'pdf'                              => 'PDF',
                            'jpg', 'jpeg', 'png', 'webp', 'gif'=> 'IMG',
                            'doc', 'docx'                      => 'DOC',
                            'xls', 'xlsx'                      => 'XLS',
                            default                            => strtoupper((string) $ext),
                        };
                    @endphp


                    <tr data-category="{{ $kategori }}"
                        class="laporan-row hover:bg-purple-50/30 transition">

                        {{-- TANGGAL --}}
                        <td class="px-5 py-3.5 text-sm text-slate-600 whitespace-nowrap">
                            {{ $tanggalAktivitas
                                ? \Carbon\Carbon::parse($tanggalAktivitas)->translatedFormat('d M Y')
                                : '—' }}
                        </td>


                        {{-- PPNPN --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-purple-100 to-indigo-100 text-[10px] font-bold text-purple-700">
                                    {{ collect(explode(' ', trim($namaPpnpn)))
                                        ->filter()
                                        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                        ->take(2)
                                        ->implode('') }}
                                </div>
                                <p class="text-sm font-semibold text-slate-700 truncate">
                                    {{ $namaPpnpn }}
                                </p>
                            </div>
                        </td>


                        {{-- AKTIVITAS --}}
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold {{ $badgeClass }}">
                                {{ $labelAktivitas }}
                            </span>
                        </td>


                        {{-- KETERANGAN --}}
                        <td class="px-5 py-3.5 text-sm text-slate-500 max-w-[250px]">
                            <p class="truncate" title="{{ $keterangan }}">
                                {{ $keterangan }}
                            </p>
                        </td>


                        {{-- BUKTI --}}
                        <td class="px-5 py-3.5">
                            @if($bukti)
                                <div class="flex items-center justify-center gap-2">

                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-bold text-slate-500 uppercase">
                                        {{ $formatLabel }}
                                    </span>

                                    <span class="text-[11px] text-slate-400 max-w-[100px] truncate hidden xl:inline" title="{{ $namaBukti }}">
                                        {{ $namaBukti }}
                                    </span>

                                    <button type="button"
                                            class="preview-bukti inline-flex items-center gap-1 rounded-md border border-purple-100 bg-purple-50 px-2 py-1 text-[11px] font-semibold text-purple-700 transition hover:bg-purple-100"
                                            data-bukti="{{ $bukti }}"
                                            data-nama="{{ $namaBukti }}">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6-9.75-6-9.75-6z" />
                                            <circle cx="12" cy="12" r="2.5" />
                                        </svg>
                                        Lihat
                                    </button>

                                </div>
                            @else
                                <p class="text-center text-xs text-slate-300">—</p>
                            @endif
                        </td>


                        {{-- DISETUJUI OLEH ⬅️ KOLOM BARU --}}
                        <td class="px-5 py-3.5">
                            @if($approvedBy)
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 text-[10px] font-bold text-emerald-700">
                                            {{ strtoupper(substr($approvedBy, 0, 1)) }}
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700 truncate">
                                            {{ $approvedBy }}
                                        </p>
                                    </div>

                                    @if($approvedAt)
                                        <p class="text-[11px] text-slate-400 pl-8">
                                            {{ \Carbon\Carbon::parse($approvedAt)->translatedFormat('d M Y · H:i') }}
                                        </p>
                                    @endif

                                    @if($status)
                                        <div class="pl-8">
                                            @if(in_array($status, ['approved', 'disetujui']))
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                                    ✓ Disetujui
                                                </span>
                                            @elseif(in_array($status, ['rejected', 'ditolak']))
                                                <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-bold text-red-600">
                                                    ✕ Ditolak
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700">
                                                    ⏳ Pending
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-xs text-slate-300 italic">—</p>
                            @endif
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="mx-auto flex max-w-sm flex-col items-center">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50">
                                    <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="mt-3 text-sm font-semibold text-slate-700">Belum ada aktivitas</p>
                                <p class="mt-1 text-xs text-slate-400">Tidak ada aktivitas pada periode ini.</p>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- MODAL PREVIEW --}}
<div id="previewModal" class="fixed inset-0 z-[999] hidden items-center justify-center bg-slate-900/60 p-4">

    <div class="relative flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-3.5">
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Preview
                </p>
                <h3 id="previewTitle" class="mt-0.5 truncate text-sm font-bold text-slate-900">
                    Bukti
                </h3>
            </div>
            <button type="button" id="closePreview"
                    class="ml-4 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="previewContent" class="flex min-h-[300px] flex-1 items-center justify-center overflow-auto bg-slate-50 p-4"></div>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    // TAB FILTER
    const tabs = document.querySelectorAll('.laporan-tab');
    const rows = document.querySelectorAll('.laporan-row');

    const ACTIVE = ['bg-purple-600', 'text-white', 'shadow-sm', 'border-transparent'];
    const INACTIVE = ['border', 'border-slate-200', 'bg-white', 'text-slate-600', 'hover:bg-slate-50'];

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const selectedTab = this.dataset.tab;

            tabs.forEach(item => {
                item.classList.remove('active-tab', ...ACTIVE);
                item.classList.add(...INACTIVE);
            });

            this.classList.remove(...INACTIVE);
            this.classList.add('active-tab', ...ACTIVE);

            rows.forEach(row => {
                if (selectedTab === 'semua') {
                    row.style.display = '';
                    return;
                }
                row.style.display = row.dataset.category === selectedTab ? '' : 'none';
            });
        });
    });


    // MODAL PREVIEW
    const modal = document.getElementById('previewModal');
    const closeButton = document.getElementById('closePreview');
    const previewContent = document.getElementById('previewContent');
    const previewTitle = document.getElementById('previewTitle');

    document.querySelectorAll('.preview-bukti').forEach(button => {
        button.addEventListener('click', function () {

            const bukti = this.dataset.bukti;
            const nama = this.dataset.nama || 'Bukti';

            previewTitle.textContent = nama;

            let fileUrl = bukti;
            if (!bukti.startsWith('http://') && !bukti.startsWith('https://') && !bukti.startsWith('/')) {
                fileUrl = '/storage/' + bukti;
            }

            const ext = bukti.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
            const isPdf = ext === 'pdf';

            if (isImage) {
                previewContent.innerHTML = `
                    <div class="flex h-full w-full items-center justify-center">
                        <img src="${fileUrl}" alt="${nama}"
                             class="max-h-[70vh] max-w-full rounded-xl object-contain shadow-sm">
                    </div>
                `;
            } else if (isPdf) {
                previewContent.innerHTML = `
                    <iframe src="${fileUrl}"
                            class="h-[70vh] w-full rounded-xl border border-slate-200 bg-white"
                            title="${nama}"></iframe>
                `;
            } else {
                previewContent.innerHTML = `
                    <div class="flex flex-col items-center justify-center text-center p-6">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-purple-50">
                            <svg class="h-7 w-7 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 3v5h5"/>
                            </svg>
                        </div>
                        <p class="mt-4 text-sm font-semibold text-slate-700">
                            Preview tidak tersedia
                        </p>
                        <p class="mt-1 text-xs text-slate-400 max-w-sm">
                            Format .${ext} tidak bisa dipreview langsung. Silakan download.
                        </p>
                        <a href="${fileUrl}" download
                           class="mt-4 inline-flex items-center gap-2 rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-purple-700 transition">
                            Download File
                        </a>
                    </div>
                `;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });

    function closePreviewModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        previewContent.innerHTML = '';
        document.body.classList.remove('overflow-hidden');
    }

    closeButton.addEventListener('click', closePreviewModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closePreviewModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePreviewModal();
    });

});

</script>

@endsection