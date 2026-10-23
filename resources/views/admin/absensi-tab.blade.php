{{-- ===================================================== --}}
{{-- TAB: ABSENSI --}}
{{-- ===================================================== --}}

@php
    $ketBadge = [
        'H' => 'bg-emerald-100 text-emerald-700',
        'S' => 'bg-blue-100 text-blue-700',
        'I' => 'bg-amber-100 text-amber-700',
        'A' => 'bg-red-100 text-red-700',
        'C' => 'bg-purple-100 text-purple-700',
    ];
    $ketLabel = [
        'H' => 'Hadir',
        'S' => 'Sakit',
        'I' => 'Izin',
        'A' => 'Alfa',
        'C' => 'Cuti',
    ];
@endphp

{{-- FILTER BAR --}}
<div class="flex flex-wrap items-end gap-3 mb-5">

    {{-- TANGGAL --}}
    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
        <input type="date" id="filterTanggal" value="{{ $data['filterTanggal'] }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
    </div>

    {{-- USER --}}
    <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-semibold text-slate-600 mb-1">User</label>
        <select id="filterUser"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
            <option value="">— Semua User —</option>
            @foreach($data['users'] as $u)
                <option value="{{ $u->id }}" {{ $data['filterUser'] == $u->id ? 'selected' : '' }}>
                    {{ $u->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- KETERANGAN --}}
    <div class="min-w-[140px]">
        <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan</label>
        <select id="filterKet"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
            <option value="">— Semua —</option>
            @foreach($ketLabel as $k => $v)
                <option value="{{ $k }}" {{ $data['filterKet'] === $k ? 'selected' : '' }}>
                    {{ $k }} - {{ $v }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- BUTTON --}}
    <div class="flex gap-2">
        <button type="button" onclick="applyFilterAbsensi()"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-sm font-bold shadow-sm transition">
            🔍 Filter
        </button>
        <button type="button" onclick="resetFilterAbsensi()"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 text-sm font-semibold transition">
            Reset
        </button>
    </div>

</div>

{{-- TOOLBAR --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
    <div>
        <h3 class="text-lg font-bold text-slate-900">Data Absensi</h3>
        <p class="text-sm text-slate-400 mt-0.5">
            Menampilkan {{ $data['absensis']->count() }} data
            (maks 100 data terbaru)
        </p>
    </div>

    {{-- TOMBOL TAMBAH MANUAL --}}
    <button type="button"
            onclick="bukaModalAbsenTambah()"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 text-sm font-bold shadow-lg shadow-amber-200 transition">
        <span>+</span>
        <span>Tambah Absensi Manual</span>
    </button>
</div>

{{-- TABLE --}}
<div class="overflow-x-auto rounded-xl border border-slate-100">
    <table class="w-full min-w-[900px]">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">User</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Shift</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Masuk</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Pulang</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Ket</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Jaga Pos</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse($data['absensis'] as $absen)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($absen->tanggal)->format('d/m/Y') }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            {{ \Carbon\Carbon::parse($absen->tanggal)->isoFormat('ddd') }}
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-100 to-indigo-100 text-[10px] font-bold text-purple-700">
                                {{ strtoupper(substr($absen->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $absen->user->name ?? '—' }}
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono">
                                    {{ $absen->user->username ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600">
                        {{ $absen->shift ? ucfirst($absen->shift) : '—' }}
                    </td>
                    <td class="px-4 py-3 text-sm font-mono text-slate-700">
                        {{ $absen->jam_masuk ? substr($absen->jam_masuk, 0, 5) : '—' }}
                    </td>
                    <td class="px-4 py-3 text-sm font-mono text-slate-700">
                        {{ $absen->jam_pulang ? substr($absen->jam_pulang, 0, 5) : '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-bold {{ $ketBadge[$absen->keterangan] ?? 'bg-slate-100 text-slate-600' }}"
                              title="{{ $ketLabel[$absen->keterangan] ?? $absen->keterangan }}">
                            {{ $absen->keterangan }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($absen->is_jaga_pos)
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-[11px] font-semibold text-indigo-700">
                                ✓ Ya
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">

                            {{-- EDIT --}}
                            <button type="button"
                                    onclick="bukaModalAbsenEdit({{ $absen->id }})"
                                    data-absen='@json($absen)'
                                    class="btn-edit-absen inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                    title="Edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                </svg>
                            </button>

                            {{-- HAPUS --}}
                            <form action="{{ route('admin.absensi.destroy', $absen->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus absensi {{ $absen->user->name ?? '' }} tanggal {{ $absen->tanggal }}?');"
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
                    <td colspan="8" class="px-4 py-12 text-center">
                        <p class="text-3xl mb-2">📭</p>
                        <p class="text-sm text-slate-400">Belum ada data absensi untuk filter ini.</p>
                        <p class="text-xs text-slate-400 mt-1">Klik "Tambah Absensi Manual" untuk input data baru.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ===================================================== --}}
{{-- MODAL ABSENSI (TAMBAH & EDIT) --}}
{{-- ===================================================== --}}
<div id="modalAbsen" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        {{-- HEADER --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <div>
                <h3 id="modalAbsenTitle" class="text-base font-bold text-slate-900">Tambah Absensi</h3>
                <p id="modalAbsenSubtitle" class="text-xs text-slate-400 mt-0.5">Input absensi manual oleh admin.</p>
            </div>
            <button type="button" onclick="tutupModalAbsen()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">
                ✕
            </button>
        </div>

        {{-- FORM --}}
        <form id="formAbsen" method="POST" class="flex-1 overflow-y-auto">
            @csrf

            <div class="p-5 space-y-4">

                {{-- USER (cuma muncul di mode TAMBAH) --}}
                <div id="fieldUserAbsen">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">User</label>
                    <select name="user_id" id="absen_user_id"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="">— Pilih User —</option>
                        @foreach($data['users'] as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->username }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- TANGGAL --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" id="absen_tanggal" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                </div>

                {{-- SHIFT --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Shift
                        <span class="text-xs text-slate-400 font-normal">(kosongin buat auto)</span>
                    </label>
                    <select name="shift" id="absen_shift"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="">— Auto dari jam masuk —</option>
                        <option value="pagi">Pagi</option>
                        <option value="malam">Malam</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    {{-- JAM MASUK --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="absen_jam_masuk"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>

                    {{-- JAM PULANG --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam Pulang</label>
                        <input type="time" name="jam_pulang" id="absen_jam_pulang"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>
                </div>

                {{-- TANGGAL PULANG --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tanggal Pulang
                        <span class="text-xs text-slate-400 font-normal">(kalau beda hari, mis. shift malam)</span>
                    </label>
                    <input type="date" name="tanggal_pulang" id="absen_tanggal_pulang"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                </div>

                {{-- KETERANGAN --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                    <select name="keterangan" id="absen_keterangan" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="H">H — Hadir</option>
                        <option value="S">S — Sakit</option>
                        <option value="I">I — Izin</option>
                        <option value="A">A — Alfa</option>
                        <option value="C">C — Cuti</option>
                    </select>
                </div>

                {{-- JAGA POS --}}
                <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <input type="checkbox" name="is_jaga_pos" id="absen_is_jaga_pos" value="1"
                           class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="absen_is_jaga_pos" class="text-sm font-semibold text-slate-700 cursor-pointer">
                        Jaga Pos
                    </label>
                </div>

                {{-- STATUS APPROVAL (cuma di mode tambah) --}}
                <div id="fieldStatusAbsen">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Approval</label>
                    <select name="status_approval" id="absen_status_approval"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="normal">Normal</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Untuk absensi manual dari admin, default: Normal.</p>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 shrink-0 bg-slate-50">
                <button type="button" onclick="tutupModalAbsen()"
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
    // =========================================================
    // FILTER
    // =========================================================
    function applyFilterAbsensi() {
        const tanggal = document.getElementById('filterTanggal').value;
        const userId  = document.getElementById('filterUser').value;
        const ket     = document.getElementById('filterKet').value;

        const params = new URLSearchParams({ tab: 'absensi' });
        if (tanggal) params.set('tanggal', tanggal);
        if (userId)  params.set('user_id', userId);
        if (ket)     params.set('keterangan', ket);

        window.location.href = '{{ route('admin.control') }}?' + params.toString();
    }

    function resetFilterAbsensi() {
        window.location.href = '{{ route('admin.control', ['tab' => 'absensi']) }}';
    }

    // =========================================================
    // MODAL
    // =========================================================
    const modalAbsen         = document.getElementById('modalAbsen');
    const formAbsen          = document.getElementById('formAbsen');
    const modalAbsenTitle    = document.getElementById('modalAbsenTitle');
    const modalAbsenSubtitle = document.getElementById('modalAbsenSubtitle');
    const fieldUserAbsen     = document.getElementById('fieldUserAbsen');
    const fieldStatusAbsen   = document.getElementById('fieldStatusAbsen');

    const URL_ABSEN_STORE  = '{{ route('admin.absensi.store') }}';
    const URL_ABSEN_UPDATE = '{{ url('admin/control/absensi') }}/';

    /**
     * Buka modal mode TAMBAH
     */
    function bukaModalAbsenTambah() {
        // Reset
        formAbsen.reset();

        // Hapus _method kalau ada
        const m = formAbsen.querySelector('input[name="_method"]');
        if (m) m.remove();

        formAbsen.action = URL_ABSEN_STORE;
        modalAbsenTitle.textContent    = 'Tambah Absensi';
        modalAbsenSubtitle.textContent = 'Input absensi manual oleh admin.';

        // Tampilkan field user & status
        fieldUserAbsen.style.display   = '';
        fieldStatusAbsen.style.display = '';

        // Set default tanggal = hari ini
        document.getElementById('absen_tanggal').value = '{{ now()->toDateString() }}';

        // Set default jam masuk = sekarang (kasar)
        const now = new Date();
        const jam = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        document.getElementById('absen_jam_masuk').value = jam;

        // Reset keterangan
        document.getElementById('absen_keterangan').value = 'H';
        document.getElementById('absen_status_approval').value = 'normal';
        document.getElementById('absen_is_jaga_pos').checked = false;

        showModal();
    }

    /**
     * Buka modal mode EDIT
     */
    function bukaModalAbsenEdit(absensiId) {
        const btn = document.querySelector(`.btn-edit-absen[data-absen][onclick*="${absensiId}"]`);
        if (!btn) return;

        const absen = JSON.parse(btn.dataset.absen);

        // Action PUT
        formAbsen.action = URL_ABSEN_UPDATE + absen.id;

        let methodInput = formAbsen.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            formAbsen.appendChild(methodInput);
        }
        methodInput.value = 'PUT';

        modalAbsenTitle.textContent    = 'Edit Absensi';
        modalAbsenSubtitle.textContent = 'Koreksi data absensi manual.';

        // Sembunyikan field user & status
        fieldUserAbsen.style.display   = 'none';
        fieldStatusAbsen.style.display = 'none';

        // Isi field
        document.getElementById('absen_tanggal').value         = absen.tanggal || '';
        document.getElementById('absen_shift').value           = absen.shift || '';
        document.getElementById('absen_jam_masuk').value       = absen.jam_masuk ? absen.jam_masuk.substring(0, 5) : '';
        document.getElementById('absen_jam_pulang').value      = absen.jam_pulang ? absen.jam_pulang.substring(0, 5) : '';
        document.getElementById('absen_tanggal_pulang').value  = absen.tanggal_pulang || '';
        document.getElementById('absen_keterangan').value      = absen.keterangan || 'H';
        document.getElementById('absen_is_jaga_pos').checked   = !!absen.is_jaga_pos;

        showModal();
    }

    function showModal() {
        modalAbsen.classList.remove('hidden');
        modalAbsen.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalAbsen() {
        modalAbsen.classList.add('hidden');
        modalAbsen.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // Backdrop close
    modalAbsen.addEventListener('click', function(e) {
        if (e.target === modalAbsen) tutupModalAbsen();
    });

    // ESC close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') tutupModalAbsen();
    });
</script>   