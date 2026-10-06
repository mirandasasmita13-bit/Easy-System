@extends('layouts.utama')

@section('title', 'Cuti')

@section('content')

{{-- ========================================================= --}}
{{-- HEADER --}}
{{-- ========================================================= --}}

<div class="mb-7">
    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
        Cuti
    </h2>
    <p class="mt-2 text-slate-500">
        Kelola pencatatan cuti kamu.
    </p>
</div>


{{-- ========================================================= --}}
{{-- PESAN --}}
{{-- ========================================================= --}}

@if(session('success'))
    <div class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        <p class="font-semibold mb-1">Terdapat kesalahan:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{{-- ========================================================= --}}
{{-- RINGKASAN CUTI --}}
{{-- ========================================================= --}}

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-7">

    {{-- SISA CUTI --}}
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Sisa Cuti Tahunan</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-extrabold text-purple-600">{{ $sisaCuti }}</span>
                    <span class="text-sm text-slate-400">hari</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 3v2.25M17.25 3v2.25M3.75 9h16.5M5.25 5.25h13.5A1.5 1.5 0 0120.25 6.75v11.5a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V6.75a1.5 1.5 0 011.5-1.5z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-4">Jatah cuti tahun berjalan</p>
    </div>

    {{-- CUTI TERPAKAI --}}
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Cuti Digunakan</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $cutiTahunanTerpakai }}</span>
                    <span class="text-sm text-slate-400">hari</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-4">Total cuti tahunan yang telah digunakan</p>
    </div>

</div>


{{-- ========================================================= --}}
{{-- LAYOUT 2 KOLOM --}}
{{-- ========================================================= --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    {{-- =====================================================
         KOLOM KIRI (2/3): FORM CATAT CUTI + RIWAYAT
    ====================================================== --}}
    <div class="lg:col-span-2 space-y-5 order-1">


        {{-- =================================================
             FORM CATAT CUTI
        ================================================== --}}
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm">

            <div class="mb-6">
                <h3 class="font-bold text-lg text-slate-900">Catat Cuti</h3>
                <p class="text-sm text-slate-400 mt-1">
                    Isi data cuti dan unggah surat yang telah disetujui.
                </p>
            </div>

            <form action="{{ route('pengajuan_cuti.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    {{-- JENIS CUTI --}}
                    <div class="sm:col-span-2">
                        <label for="jenis_cuti" class="block text-sm font-semibold text-slate-700 mb-2">
                            Jenis Cuti
                        </label>
                        <select id="jenis_cuti" name="jenis_cuti" required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                            <option value="">Pilih jenis cuti</option>
                            <option value="tahunan" {{ old('jenis_cuti') === 'tahunan' ? 'selected' : '' }}>
                                Cuti Tahunan
                            </option>
                            <option value="alasan_penting" {{ old('jenis_cuti') === 'alasan_penting' ? 'selected' : '' }}>
                                Cuti Alasan Penting
                            </option>
                        </select>
                    </div>

                    {{-- TANGGAL MULAI --}}
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Mulai
                        </label>
                        <input id="tanggal_mulai" type="date" name="tanggal_mulai"
                               value="{{ old('tanggal_mulai') }}" required
                               min="{{ now()->toDateString() }}"
                               data-min-tahunan="{{ now()->toDateString() }}"
                               data-min-alasan-penting="{{ now()->subDays(7)->toDateString() }}"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                        <p id="infoTanggalMulai" class="text-xs text-slate-400 mt-1.5">
                            Tidak bisa memilih tanggal yang sudah lewat.
                        </p>
                    </div>

                    {{-- TANGGAL SELESAI --}}
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Selesai
                        </label>
                        <input id="tanggal_selesai" type="date" name="tanggal_selesai"
                               value="{{ old('tanggal_selesai') }}" required
                               min="{{ now()->toDateString() }}"
                               data-min-tahunan="{{ now()->toDateString() }}"
                               data-min-alasan-penting="{{ now()->subDays(7)->toDateString() }}"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="sm:col-span-2">
                        <label for="keterangan" class="block text-sm font-semibold text-slate-700 mb-2">
                            Keterangan
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="4"
                                  placeholder="Tuliskan keterangan cuti..."
                                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">{{ old('keterangan') }}</textarea>
                    </div>

                    {{-- SURAT CUTI --}}
                    <div class="sm:col-span-2">
                        <label for="surat" class="block text-sm font-semibold text-slate-700 mb-2">
                            Surat Cuti
                        </label>

                        <label for="surat"
                               class="flex flex-col items-center justify-center w-full min-h-32 rounded-xl border-2 border-dashed border-purple-200 bg-purple-50/40 cursor-pointer hover:bg-purple-50 transition">
                            <div class="text-center">
                                <div class="text-2xl mb-2">📄</div>
                                <p class="text-sm font-semibold text-purple-600">Pilih surat</p>
                                <p class="text-xs text-slate-400 mt-1">PDF, JPG, atau PNG</p>
                                <p class="text-xs text-slate-400">Maksimal 5 MB</p>
                            </div>
                            <input id="surat" type="file" name="surat" required class="hidden"
                                   accept=".pdf,.jpg,.jpeg,.png">
                        </label>

                        <p id="namaFileSurat" class="text-sm text-slate-500 mt-2 hidden"></p>
                    </div>

                    {{-- BUTTON --}}
                    <div class="flex justify-end gap-3 mt-6 sm:col-span-2">
                        <button type="submit"
                                class="px-6 py-3 rounded-xl text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 shadow-md shadow-purple-200 transition">
                            Simpan Cuti
                        </button>
                    </div>

                </div>
            </form>

        </div>


        {{-- =================================================
             RIWAYAT CUTI
        ================================================== --}}
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm">

            <div class="mb-5">
                <h3 class="font-bold text-lg text-slate-900">Riwayat Cuti</h3>
                <p class="text-sm text-slate-400 mt-1">Riwayat pencatatan cuti kamu.</p>
            </div>

            <div class="space-y-3">

                @forelse($riwayatCuti as $cuti)

                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                            {{-- INFO --}}
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-slate-900">
                                        {{ $cuti->tanggal_mulai->translatedFormat('d F Y') }}
                                        @if($cuti->tanggal_mulai->toDateString() !== $cuti->tanggal_selesai->toDateString())
                                            – {{ $cuti->tanggal_selesai->translatedFormat('d F Y') }}
                                        @endif
                                    </span>

                                    @if($cuti->jenis_cuti === 'tahunan')
                                        <span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-600 text-[11px] font-semibold">
                                            Cuti Tahunan
                                        </span>
                                    @elseif($cuti->jenis_cuti === 'alasan_penting')
                                        <span class="px-2.5 py-1 rounded-full bg-pink-50 text-pink-600 text-[11px] font-semibold">
                                            Cuti Alasan Penting
                                        </span>
                                    @endif
                                </div>

                                @if($cuti->keterangan)
                                    <p class="text-sm text-slate-500 mt-2">{{ $cuti->keterangan }}</p>
                                @endif
                            </div>

                            {{-- DETAIL + TOMBOL --}}
                            <div class="flex flex-wrap items-center gap-2 text-sm text-slate-400 shrink-0">
                                <span>{{ $cuti->jumlah_hari }} hari</span>

                                @if($cuti->surat)
                                    <span>•</span>

                                    <span class="text-slate-500 max-w-[160px] truncate"
                                          title="{{ $cuti->nama_surat ?? 'Surat' }}">
                                        📄 {{ $cuti->nama_surat ?? 'Surat' }}
                                    </span>

                                    {{-- PREVIEW --}}
                                    <button type="button"
                                            class="btn-preview-surat inline-flex items-center px-3 py-1.5 rounded-lg bg-purple-600 text-white text-xs font-semibold hover:bg-purple-700 transition"
                                            data-url="{{ asset('storage/' . $cuti->surat) }}"
                                            data-nama="{{ $cuti->nama_surat ?? 'Surat Cuti' }}">
                                        Preview
                                    </button>

                                    {{-- DOWNLOAD --}}
                                    <a href="{{ asset('storage/' . $cuti->surat) }}"
                                       download="{{ $cuti->nama_surat ?? 'surat-cuti' }}"
                                       class="inline-flex items-center px-3 py-1.5 rounded-lg border border-purple-200 bg-white text-purple-600 text-xs font-semibold hover:bg-purple-50 transition">
                                        Download
                                    </a>
                                @endif

                                {{-- EDIT --}}
                                <button type="button"
                                        class="btn-edit-cuti inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 transition"
                                        data-id="{{ $cuti->id }}"
                                        data-jenis="{{ $cuti->jenis_cuti }}"
                                        data-tanggal-mulai="{{ $cuti->tanggal_mulai->format('Y-m-d') }}"
                                        data-tanggal-selesai="{{ $cuti->tanggal_selesai->format('Y-m-d') }}"
                                        data-keterangan="{{ $cuti->keterangan ?? '' }}"
                                        data-nama-surat="{{ $cuti->nama_surat ?? '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                    </svg>
                                    Edit
                                </button>

                            </div>

                        </div>
                    </div>

                @empty

                    <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-10 text-center">
                        <div class="mx-auto mb-3 w-11 h-11 rounded-full bg-white border border-slate-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.6" stroke="currentColor" class="w-5 h-5 text-slate-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-500">Belum ada riwayat cuti</p>
                        <p class="text-xs text-slate-400 mt-1">Data cuti yang kamu catat akan muncul di sini.</p>
                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- =====================================================
         KOLOM KANAN (1/3): KETENTUAN CUTI
    ====================================================== --}}
    <div class="lg:col-span-1 order-2">
        <div class="lg:sticky lg:top-6 rounded-2xl border border-purple-100 bg-purple-50/70 p-5 sm:p-6">

            <div class="flex items-start gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-purple-900 text-sm">Ketentuan Cuti</p>
                    <p class="text-xs text-purple-700 mt-0.5">Mohon dibaca sebelum mengajukan cuti.</p>
                </div>
            </div>

            <ul class="space-y-2.5 text-sm text-purple-800">
                <li class="flex gap-2">
                    <span class="text-purple-500 mt-0.5">•</span>
                    <span>Jatah cuti tahunan <strong>12 hari</strong> per tahun.</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-purple-500 mt-0.5">•</span>
                    <span>Cuti tahunan harus diajukan <strong>sebelum atau pada hari cuti</strong> (tidak boleh mundur).</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-purple-500 mt-0.5">•</span>
                    <span>Surat cuti yang diunggah harus sudah <strong>disetujui atasan</strong> (hard copy).</span>
                </li>
                <li class="flex gap-2">
                    <span class="text-purple-500 mt-0.5">•</span>
                    <span>Cuti alasan penting maksimal <strong>3 hari</strong> dan dapat diajukan mundur maksimal <strong>7 hari</strong>.</span>
                </li>
            </ul>

            <div class="mt-4 pt-4 border-t border-purple-200/60">
                <p class="text-xs text-purple-600 italic">
                    * SOP tambahan sesuai kebijakan kantor dapat ditambahkan di sini.
                </p>
            </div>

        </div>
    </div>

</div>


{{-- ========================================================= --}}
{{-- MODAL PREVIEW SURAT
-- ========================================================= --}}

<div id="previewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">

    <div class="relative w-full max-w-5xl h-[90vh] bg-white rounded-2xl shadow-xl overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <div class="min-w-0">
                <h3 id="previewTitle" class="font-bold text-slate-900 truncate">Preview Surat</h3>
            </div>

            <button type="button" id="btnClosePreview"
                    class="ml-4 shrink-0 w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition flex items-center justify-center">
                ✕
            </button>
        </div>

        <div id="previewContent" class="w-full h-[calc(90vh-73px)] bg-slate-100"></div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- MODAL EDIT CUTI
-- ========================================================= --}}

<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">

    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0">
            <div>
                <h3 class="font-bold text-slate-900">Edit Cuti</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbaiki data cuti yang salah.</p>
            </div>

            <button type="button" id="btnCloseEdit"
                    class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition flex items-center justify-center">
                ✕
            </button>
        </div>

        {{-- FORM --}}
        <form id="editForm" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            @csrf
            @method('PUT')

            <div class="p-5 space-y-5">

                {{-- JENIS CUTI --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Cuti</label>
                    <select name="jenis_cuti" id="edit_jenis_cuti" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                        <option value="tahunan">Cuti Tahunan</option>
                        <option value="alasan_penting">Cuti Alasan Penting</option>
                    </select>
                </div>

                {{-- TANGGAL MULAI --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                </div>

                {{-- TANGGAL SELESAI --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                </div>

                {{-- KETERANGAN --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" id="edit_keterangan" rows="4"
                              placeholder="Tuliskan keterangan cuti..."
                              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100"></textarea>
                </div>

                {{-- SURAT BARU --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Ganti Surat
                        <span class="text-xs text-slate-400 font-normal">(biarkan kosong kalau tidak ganti)</span>
                    </label>

                    <input type="file" name="surat" id="edit_surat"
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-purple-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-purple-600 hover:file:bg-purple-100">

                    <p id="edit_surat_sekarang" class="text-xs text-slate-400 mt-2"></p>
                    <p id="edit_nama_surat_baru" class="hidden text-xs text-purple-600 mt-2 font-semibold"></p>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 shrink-0 bg-slate-50">
                <button type="button" id="btnCancelEdit"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 transition">
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>


{{-- ========================================================= --}}
{{-- JAVASCRIPT
-- ========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       TANGGAL DINAMIS — BEDA ATURAN TIAP JENIS CUTI
    ====================================================== */

    const jenisCutiSelect = document.getElementById('jenis_cuti');
    const tanggalMulaiInput = document.getElementById('tanggal_mulai');
    const tanggalSelesaiInput = document.getElementById('tanggal_selesai');
    const infoTanggalMulai = document.getElementById('infoTanggalMulai');

    function updateTanggalMin() {
        if (!jenisCutiSelect || !tanggalMulaiInput || !tanggalSelesaiInput) return;

        const jenis = jenisCutiSelect.value;

        let minTanggal;

        if (jenis === 'alasan_penting') {
            minTanggal = tanggalMulaiInput.dataset.minAlasanPenting;
            if (infoTanggalMulai) infoTanggalMulai.textContent = '';
        } else {
            minTanggal = tanggalMulaiInput.dataset.minTahunan;
            if (infoTanggalMulai) infoTanggalMulai.textContent = 'Tidak bisa memilih tanggal yang sudah lewat.';
        }

        tanggalMulaiInput.min = minTanggal;
        tanggalSelesaiInput.min = minTanggal;

        if (tanggalMulaiInput.value && tanggalMulaiInput.value < minTanggal) {
            tanggalMulaiInput.value = '';
        }
        if (tanggalSelesaiInput.value && tanggalSelesaiInput.value < minTanggal) {
            tanggalSelesaiInput.value = '';
        }
    }

    updateTanggalMin();

    if (jenisCutiSelect) {
        jenisCutiSelect.addEventListener('change', updateTanggalMin);
    }


    /* =====================================================
       NAMA FILE SURAT SETELAH DIPILIH
    ====================================================== */

    const suratInput = document.getElementById('surat');
    if (suratInput) {
        suratInput.addEventListener('change', function () {
            const namaFile = document.getElementById('namaFileSurat');

            if (this.files.length > 0) {
                namaFile.textContent = '📄 ' + this.files[0].name;
                namaFile.classList.remove('hidden');
            } else {
                namaFile.textContent = '';
                namaFile.classList.add('hidden');
            }
        });
    }


    /* =====================================================
       PREVIEW SURAT (PDF / GAMBAR)
    ====================================================== */

    const modal    = document.getElementById('previewModal');
    const content  = document.getElementById('previewContent');
    const title    = document.getElementById('previewTitle');
    const btnClose = document.getElementById('btnClosePreview');


    function openPreview(url, namaFile) {

        if (!modal || !content || !title) return;

        title.textContent = namaFile || 'Preview Surat';

        const extension = url.split('?')[0].split('.').pop().toLowerCase();

        if (extension === 'pdf') {
            content.innerHTML = `
                <iframe src="${url}"
                        class="w-full h-full border-0"
                        title="${namaFile || 'Surat PDF'}"></iframe>
            `;
        } else {
            content.innerHTML = `
                <div class="w-full h-full flex items-center justify-center p-6 overflow-auto">
                    <img src="${url}"
                         alt="${namaFile || 'Surat'}"
                         class="max-w-full max-h-full object-contain rounded-lg shadow-sm">
                </div>
            `;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }


    function closePreview() {
        if (!modal || !content) return;

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        content.innerHTML = '';
        document.body.classList.remove('overflow-hidden');
    }


    document.querySelectorAll('.btn-preview-surat').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openPreview(this.dataset.url, this.dataset.nama);
        });
    });


    if (btnClose) {
        btnClose.addEventListener('click', closePreview);
    }

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closePreview();
        });
    }


    /* =====================================================
       MODAL EDIT CUTI
    ====================================================== */

    const editModal         = document.getElementById('editModal');
    const editForm          = document.getElementById('editForm');
    const btnCloseEdit      = document.getElementById('btnCloseEdit');
    const btnCancelEdit     = document.getElementById('btnCancelEdit');

    const editJenis         = document.getElementById('edit_jenis_cuti');
    const editTanggalMulai  = document.getElementById('edit_tanggal_mulai');
    const editTanggalSelesai= document.getElementById('edit_tanggal_selesai');
    const editKeterangan    = document.getElementById('edit_keterangan');
    const editSurat         = document.getElementById('edit_surat');
    const editSuratSekarang = document.getElementById('edit_surat_sekarang');
    const editNamaSuratBaru = document.getElementById('edit_nama_surat_baru');


    function openEditModal(data) {

        if (!editModal || !editForm) return;

        editForm.action = '/pengajuan_cuti/' + data.id;

        if (editJenis)          editJenis.value          = data.jenis;
        if (editTanggalMulai)   editTanggalMulai.value   = data.tanggalMulai;
        if (editTanggalSelesai) editTanggalSelesai.value = data.tanggalSelesai;
        if (editKeterangan)     editKeterangan.value     = data.keterangan;

        if (editSuratSekarang) {
            editSuratSekarang.textContent = data.namaSurat
                ? 'Surat saat ini: ' + data.namaSurat
                : 'Belum ada surat.';
        }

        if (editSurat) editSurat.value = '';
        if (editNamaSuratBaru) {
            editNamaSuratBaru.textContent = '';
            editNamaSuratBaru.classList.add('hidden');
        }

        editModal.classList.remove('hidden');
        editModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }


    function closeEditModal() {
        if (!editModal) return;

        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }


    document.querySelectorAll('.btn-edit-cuti').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openEditModal({
                id:             this.dataset.id,
                jenis:          this.dataset.jenis,
                tanggalMulai:   this.dataset.tanggalMulai,
                tanggalSelesai: this.dataset.tanggalSelesai,
                keterangan:     this.dataset.keterangan,
                namaSurat:      this.dataset.namaSurat
            });
        });
    });


    if (btnCloseEdit)  btnCloseEdit.addEventListener('click', closeEditModal);
    if (btnCancelEdit) btnCancelEdit.addEventListener('click', closeEditModal);

    if (editModal) {
        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) closeEditModal();
        });
    }


    if (editSurat && editNamaSuratBaru) {
        editSurat.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                editNamaSuratBaru.textContent = '📄 File baru: ' + this.files[0].name;
                editNamaSuratBaru.classList.remove('hidden');
            } else {
                editNamaSuratBaru.textContent = '';
                editNamaSuratBaru.classList.add('hidden');
            }
        });
    }


    /* =====================================================
       ESC UNTUK MENUTUP MODAL
    ====================================================== */

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closePreview();
            closeEditModal();
        }
    });

});

</script>

@endsection