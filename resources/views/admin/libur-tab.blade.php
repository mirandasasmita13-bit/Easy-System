{{-- ===================================================== --}}
{{-- TAB: LIBUR (TANGGAL MERAH) --}}
{{-- ===================================================== --}}

{{-- INFO BANNER --}}
<div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 mb-5 flex items-start gap-3">
    <span class="text-lg">📅</span>
    <div class="text-sm text-red-800">
        <p class="font-semibold">Tanggal Merah / Cuti Bersama</p>
        <p class="text-xs mt-0.5 text-red-700">
            Daftar hari libur nasional. Dipakai untuk <strong>skip perhitungan cuti</strong> otomatis dan
            <strong>menandai absensi</strong> supaya tidak salah hitung alfa.
        </p>
    </div>
</div>

{{-- TOOLBAR --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h3 class="text-lg font-bold text-slate-900">Tanggal Merah</h3>
        <p class="text-sm text-slate-400 mt-0.5">
            {{ $data['totalTahun'] }} libur tahun {{ $data['filterTahun'] }} ·
            Total: {{ $data['total'] }}
        </p>
    </div>

    <div class="flex flex-wrap gap-2">
        {{-- IMPORT NASIONAL --}}
        <form action="{{ route('admin.libur.import') }}" method="POST"
              onsubmit="return confirm('Import kalender nasional {{ $data['filterTahun'] }}?\n\nData yang sudah ada tidak akan ditimpa.');">
            @csrf
            <input type="hidden" name="tahun" value="{{ $data['filterTahun'] }}">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white px-4 py-2.5 text-sm font-bold shadow-lg shadow-blue-200 transition">
                📥 Import Nasional
            </button>
        </form>

        {{-- TAMBAH MANUAL --}}
        <button type="button"
                onclick="bukaModalLibur('tambah')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 text-sm font-bold shadow-lg shadow-amber-200 transition">
            <span>+</span>
            <span>Tambah Libur</span>
        </button>
    </div>
</div>

{{-- FILTER TAHUN --}}
<div class="flex flex-wrap items-end gap-3 mb-5">
    <div class="min-w-[120px]">
        <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun</label>
        <select id="filterTahun" onchange="applyFilterLibur()"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
            @for($y = now()->year + 1; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" {{ $data['filterTahun'] == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
</div>

{{-- TABLE --}}
<div class="overflow-x-auto rounded-xl border border-slate-100">
    <table class="w-full min-w-[700px]">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 w-[60px]">#</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Hari</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Keterangan</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse($data['liburs'] as $i => $l)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3 text-sm text-slate-400 font-mono">
                        {{ $i + 1 }}
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($l->tanggal)->format('d M Y') }}
                        </p>
                        <p class="text-[11px] text-slate-400 font-mono">
                            {{ \Carbon\Carbon::parse($l->tanggal)->format('Y-m-d') }}
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold
                            {{ in_array(\Carbon\Carbon::parse($l->tanggal)->dayOfWeek, [0, 6])
                                ? 'bg-red-100 text-red-700'
                                : 'bg-slate-100 text-slate-600' }}">
                            {{ \Carbon\Carbon::parse($l->tanggal)->isoFormat('dddd') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-700">
                        {{ $l->keterangan ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">

                            {{-- EDIT --}}
                            <button type="button"
                                    onclick="bukaModalLibur('edit', {{ $l->id }})"
                                    data-libur='@json($l)'
                                    class="btn-edit-libur inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                    title="Edit">
                                ✏️
                            </button>

                            {{-- HAPUS --}}
                            <form action="{{ route('admin.libur.destroy', $l->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus tanggal libur {{ \Carbon\Carbon::parse($l->tanggal)->format('d M Y') }}?');"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
                                        title="Hapus">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                    </svg>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center">
                        <p class="text-3xl mb-2">📭</p>
                        <p class="text-sm text-slate-400">Belum ada tanggal libur untuk tahun {{ $data['filterTahun'] }}.</p>
                        <p class="text-xs text-slate-400 mt-1">Klik "Import Nasional" atau "Tambah Libur" untuk mulai.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ===================================================== --}}
{{-- MODAL TAMBAH/EDIT --}}
{{-- ===================================================== --}}
<div id="modalLibur" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <div>
                <h3 id="modalLiburTitle" class="text-base font-bold text-slate-900">Tambah Tanggal Libur</h3>
                <p class="text-xs text-slate-400 mt-0.5">Isi tanggal dan keterangan.</p>
            </div>
            <button type="button" onclick="tutupModalLibur()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formLibur" method="POST" class="flex-1 overflow-y-auto">
            @csrf

            <div class="p-5 space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal" id="libur_tanggal" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Keterangan
                    </label>
                    <input type="text" name="keterangan" id="libur_keterangan" maxlength="255"
                           placeholder="Contoh: Tahun Baru 2026"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                </div>

            </div>

            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 shrink-0 bg-slate-50">
                <button type="button" onclick="tutupModalLibur()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-500 hover:bg-amber-600">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ===================================================== --}}
{{-- JAVASCRIPT --}}
{{-- ===================================================== --}}
<script>
    // =========================
    // FILTER
    // =========================
    function applyFilterLibur() {
        const tahun = document.getElementById('filterTahun').value;
        window.location.href = '{{ route('admin.control') }}?tab=libur&tahun=' + tahun;
    }

    // =========================
    // MODAL
    // =========================
    const modalLibur      = document.getElementById('modalLibur');
    const formLibur       = document.getElementById('formLibur');
    const modalLiburTitle = document.getElementById('modalLiburTitle');

    const URL_LIBUR_STORE  = '{{ route('admin.libur.store') }}';
    const URL_LIBUR_UPDATE = '{{ url('admin/control/libur') }}/';

    function bukaModalLibur(mode, id = null) {
        formLibur.reset();

        // Hapus _method lama kalau ada
        const old = formLibur.querySelector('input[name="_method"]');
        if (old) old.remove();

        if (mode === 'tambah') {
            modalLiburTitle.textContent = 'Tambah Tanggal Libur';
            formLibur.action = URL_LIBUR_STORE;

        } else {
            const btn = document.querySelector(`.btn-edit-libur[data-libur][onclick*="${id}"]`);
            if (!btn) return;
            const l = JSON.parse(btn.dataset.libur);

            modalLiburTitle.textContent = 'Edit Tanggal Libur';
            formLibur.action = URL_LIBUR_UPDATE + l.id;

            document.getElementById('libur_tanggal').value    = l.tanggal || '';
            document.getElementById('libur_keterangan').value = l.keterangan || '';

            const mi = document.createElement('input');
            mi.type = 'hidden';
            mi.name = '_method';
            mi.value = 'PUT';
            formLibur.appendChild(mi);
        }

        modalLibur.classList.remove('hidden');
        modalLibur.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalLibur() {
        modalLibur.classList.add('hidden');
        modalLibur.classList.remove('flex');
        document.body.style.overflow = '';
    }

    modalLibur.addEventListener('click', e => {
        if (e.target === modalLibur) tutupModalLibur();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') tutupModalLibur();
    });
</script>