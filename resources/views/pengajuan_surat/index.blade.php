@extends('layouts.utama')

@section('title', 'Surat Lainnya')

@section('content')

{{-- ===================================================== --}}
{{-- HEADER --}}
{{-- ===================================================== --}}

<div class="mb-7">
    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
        Surat Lainnya
    </h2>
    <p class="mt-2 text-slate-500">
        Kelola kebutuhan administrasi surat kamu di sini.
    </p>
</div>


{{-- ===================================================== --}}
{{-- PESAN --}}
{{-- ===================================================== --}}

@if(session('success'))
    <div class="mb-5 rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-600">
        {{ $errors->first() }}
    </div>
@endif


{{-- ===================================================== --}}
{{-- FORM BUAT SURAT
-- ===================================================== --}}

<div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm mb-5">

    <div class="flex items-start gap-4 mb-6">
        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A2.625 2.625 0 0112 5.625v-1.5A3.375 3.375 0 009.375.75H5.25A2.25 2.25 0 003 3v18a2.25 2.25 0 002.25 2.25h9.75A4.5 4.5 0 0019.5 18.75v-4.5z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9.75h4.5M12 13.5h4.5M12 17.25h2.25"/>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-900">Buat Surat</h3>
            <p class="text-sm text-slate-400 mt-1">Isi informasi surat yang kamu perlukan.</p>
        </div>
    </div>

    <form action="{{ route('pengajuan_surat.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- JENIS SURAT --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Surat</label>
                <select name="jenis_surat" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                    <option value="">Pilih jenis surat</option>
                    <option value="surat_keterangan" {{ old('jenis_surat') === 'surat_keterangan' ? 'selected' : '' }}>
                        Surat Izin Sakit
                    </option>
                    <option value="surat_lainnya" {{ old('jenis_surat') === 'surat_lainnya' ? 'selected' : '' }}>
                        Surat Lainnya
                    </option>
                </select>
            </div>

            {{-- TANGGAL --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
            </div>

            {{-- KEPERLUAN --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Keperluan</label>
                <textarea name="keperluan" rows="4" required
                          placeholder="Jelaskan keperluan surat..."
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">{{ old('keperluan') }}</textarea>
            </div>

            {{-- DOKUMEN --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Dokumen Pendukung
                    <span class="text-xs text-slate-400 font-normal">(opsional)</span>
                </label>

                <input type="file" name="dokumen" id="dokumen"
                       accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-purple-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-purple-600 hover:file:bg-purple-100">

                <p id="namaFileDokumen" class="hidden text-xs text-slate-400 mt-2"></p>
            </div>

        </div>

        <div class="mt-7 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-xs text-slate-400">Pastikan informasi surat sudah benar.</p>

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-purple-600 text-white text-sm font-bold shadow-lg shadow-purple-200 hover:bg-purple-700 hover:-translate-y-0.5 transition">
                <span>✓</span>
                Simpan Surat
            </button>
        </div>
    </form>

</div>


{{-- ===================================================== --}}
{{-- RIWAYAT SURAT
-- ===================================================== --}}

<div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-100 shadow-sm">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Catatan Surat</h3>
            <p class="text-sm text-slate-400 mt-1">Daftar surat yang pernah kamu buat.</p>
        </div>
        <span class="inline-flex w-fit px-3 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">
            {{ $riwayatSurat->count() }} surat
        </span>
    </div>

    <div class="space-y-3">

        @forelse($riwayatSurat as $surat)

            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-5">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                    {{-- INFO --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-semibold text-slate-700">
                                {{ $surat->tanggal?->format('d/m/Y') }}
                            </span>

                            <span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-600 text-[11px] font-semibold">
                                @if($surat->jenis_surat === 'surat_keterangan')
                                    Surat Izin Sakit
                                @elseif($surat->jenis_surat === 'surat_tugas')
                                    Surat Tugas
                                @elseif($surat->jenis_surat === 'surat_lainnya')
                                    Surat Lainnya
                                @else
                                    {{ ucwords(str_replace('_', ' ', $surat->jenis_surat)) }}
                                @endif
                            </span>
                        </div>

                        <p class="text-sm text-slate-500 mt-2">{{ $surat->keperluan }}</p>

                        @if($surat->dokumen)
                            <div class="flex items-center gap-2 mt-3">
                                <span>📄</span>
                                <span class="text-xs text-slate-500 max-w-[250px] truncate"
                                      title="{{ $surat->nama_dokumen ?? 'Dokumen' }}">
                                    {{ $surat->nama_dokumen ?? 'Dokumen' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- TOMBOL --}}
                    <div class="flex flex-wrap items-center gap-2 shrink-0">

                        {{-- PREVIEW --}}
                        @if($surat->dokumen)
                            <button type="button"
                                    class="btn-preview-surat inline-flex items-center px-3 py-1.5 rounded-lg bg-purple-600 text-white text-xs font-semibold hover:bg-purple-700 transition"
                                    data-url="{{ asset('storage/' . $surat->dokumen) }}"
                                    data-nama="{{ $surat->nama_dokumen ?? 'Dokumen Surat' }}">
                                Preview
                            </button>

                            {{-- DOWNLOAD --}}
                            <a href="{{ asset('storage/' . $surat->dokumen) }}"
                               download="{{ $surat->nama_dokumen ?? 'dokumen-surat' }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg border border-purple-200 bg-white text-purple-600 text-xs font-semibold hover:bg-purple-50 transition">
                                Download
                            </a>
                        @endif

                        {{-- EDIT --}}
                        <button type="button"
                                class="btn-edit-surat inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 transition"
                                data-id="{{ $surat->id }}"
                                data-jenis="{{ $surat->jenis_surat }}"
                                data-tanggal="{{ $surat->tanggal?->format('Y-m-d') }}"
                                data-keperluan="{{ $surat->keperluan }}"
                                data-dokumen-nama="{{ $surat->nama_dokumen ?? '' }}">
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

            <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-200 py-12 text-center">
                <p class="text-sm font-semibold text-slate-500">Belum ada catatan surat</p>
                <p class="text-xs text-slate-400 mt-1">Surat yang kamu simpan akan muncul di sini.</p>
            </div>

        @endforelse

    </div>

</div>


{{-- ===================================================== --}}
{{-- MODAL PREVIEW --}}
{{-- ===================================================== --}}

<div id="previewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">

    <div class="relative w-full max-w-5xl h-[90vh] bg-white rounded-2xl shadow-xl overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <div class="min-w-0">
                <h3 id="previewTitle" class="font-bold text-slate-900 truncate">Preview Dokumen</h3>
            </div>

            <button type="button" id="btnClosePreview"
                    class="ml-4 shrink-0 w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition flex items-center justify-center">
                ✕
            </button>
        </div>

        <div id="previewContent" class="w-full h-[calc(90vh-73px)] bg-slate-100"></div>

    </div>

</div>


{{-- ===================================================== --}}
{{-- MODAL EDIT SURAT --}}
{{-- ===================================================== --}}

<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">

    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden max-h-[90vh] flex flex-col">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0">
            <div>
                <h3 class="font-bold text-slate-900">Edit Surat</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbaiki data surat yang salah.</p>
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

                {{-- JENIS SURAT --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Surat</label>
                    <select name="jenis_surat" id="edit_jenis_surat" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                        <option value="surat_keterangan">Surat Izin Sakit</option>
                        <option value="surat_tugas">Surat Tugas</option>
                        <option value="surat_lainnya">Surat Lainnya</option>
                    </select>
                </div>

                {{-- TANGGAL --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                </div>

                {{-- KEPERLUAN --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Keperluan</label>
                    <textarea name="keperluan" id="edit_keperluan" rows="4" required
                              placeholder="Jelaskan keperluan surat..."
                              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100"></textarea>
                </div>

                {{-- DOKUMEN BARU --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Ganti Dokumen
                        <span class="text-xs text-slate-400 font-normal">(biarkan kosong kalau tidak ganti)</span>
                    </label>

                    <input type="file" name="dokumen" id="edit_dokumen"
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-purple-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-purple-600 hover:file:bg-purple-100">

                    <p id="edit_dokumen_sekarang" class="text-xs text-slate-400 mt-2"></p>
                    <p id="edit_nama_dokumen_baru" class="hidden text-xs text-purple-600 mt-2 font-semibold"></p>
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


{{-- ===================================================== --}}
{{-- JAVASCRIPT
-- ===================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /* =====================================================
       NAMA FILE SAAT DIPILIH (FORM BUAT)
    ====================================================== */

    const dokumenInput = document.getElementById('dokumen');
    const namaFile     = document.getElementById('namaFileDokumen');

    if (dokumenInput && namaFile) {
        dokumenInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                namaFile.textContent = '📄 ' + this.files[0].name;
                namaFile.classList.remove('hidden');
            } else {
                namaFile.textContent = '';
                namaFile.classList.add('hidden');
            }
        });
    }


    /* =====================================================
       PREVIEW DOKUMEN
    ====================================================== */

    const previewModal   = document.getElementById('previewModal');
    const previewContent = document.getElementById('previewContent');
    const previewTitle   = document.getElementById('previewTitle');
    const btnClosePrev   = document.getElementById('btnClosePreview');


    function openPreview(url, nama) {

        if (!previewModal || !previewContent || !previewTitle) return;

        previewTitle.textContent = nama || 'Preview Dokumen';

        const ext = url.split('?')[0].split('.').pop().toLowerCase();

        if (ext === 'pdf') {
            previewContent.innerHTML = `
                <iframe src="${url}"
                        class="w-full h-full border-0"
                        title="${nama || 'Dokumen PDF'}"></iframe>
            `;
        } else {
            previewContent.innerHTML = `
                <div class="w-full h-full flex items-center justify-center p-6 overflow-auto">
                    <img src="${url}"
                         alt="${nama || 'Dokumen'}"
                         class="max-w-full max-h-full object-contain rounded-lg shadow-sm">
                </div>
            `;
        }

        previewModal.classList.remove('hidden');
        previewModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }


    function closePreview() {
        if (!previewModal || !previewContent) return;

        previewModal.classList.add('hidden');
        previewModal.classList.remove('flex');
        previewContent.innerHTML = '';
        document.body.classList.remove('overflow-hidden');
    }


    document.querySelectorAll('.btn-preview-surat').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openPreview(this.dataset.url, this.dataset.nama);
        });
    });


    if (btnClosePrev) btnClosePrev.addEventListener('click', closePreview);

    if (previewModal) {
        previewModal.addEventListener('click', function (e) {
            if (e.target === previewModal) closePreview();
        });
    }


    /* =====================================================
       MODAL EDIT
    ====================================================== */

    const editModal        = document.getElementById('editModal');
    const editForm         = document.getElementById('editForm');
    const btnCloseEdit     = document.getElementById('btnCloseEdit');
    const btnCancelEdit    = document.getElementById('btnCancelEdit');

    const editJenis        = document.getElementById('edit_jenis_surat');
    const editTanggal      = document.getElementById('edit_tanggal');
    const editKeperluan    = document.getElementById('edit_keperluan');
    const editDokumen      = document.getElementById('edit_dokumen');
    const editDokumenSekarang = document.getElementById('edit_dokumen_sekarang');
    const editNamaDokumenBaru = document.getElementById('edit_nama_dokumen_baru');


    function openEditModal(data) {

        if (!editModal || !editForm) return;

        // Set action URL
        editForm.action = '/pengajuan_surat/' + data.id;

        // Isi field
        if (editJenis)     editJenis.value     = data.jenis;
        if (editTanggal)   editTanggal.value   = data.tanggal;
        if (editKeperluan) editKeperluan.value = data.keperluan;

        // Info dokumen sekarang
        if (editDokumenSekarang) {
            editDokumenSekarang.textContent = data.dokumenNama
                ? 'Dokumen saat ini: ' + data.dokumenNama
                : 'Belum ada dokumen.';
        }

        // Reset file input
        if (editDokumen) editDokumen.value = '';
        if (editNamaDokumenBaru) {
            editNamaDokumenBaru.textContent = '';
            editNamaDokumenBaru.classList.add('hidden');
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


    document.querySelectorAll('.btn-edit-surat').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openEditModal({
                id:          this.dataset.id,
                jenis:       this.dataset.jenis,
                tanggal:     this.dataset.tanggal,
                keperluan:   this.dataset.keperluan,
                dokumenNama: this.dataset.dokumenNama
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


    // Tampilkan nama file baru saat dipilih di modal edit
    if (editDokumen && editNamaDokumenBaru) {
        editDokumen.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                editNamaDokumenBaru.textContent = '📄 File baru: ' + this.files[0].name;
                editNamaDokumenBaru.classList.remove('hidden');
            } else {
                editNamaDokumenBaru.textContent = '';
                editNamaDokumenBaru.classList.add('hidden');
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