{{-- ===================================================== --}}
{{-- TAB: CUTI LALU (CUTI SEBELUM SISTEM) --}}
{{-- ===================================================== --}}

@php
    $jenisBadge = [
        'tahunan'        => 'bg-emerald-100 text-emerald-700',
        'alasan_penting' => 'bg-amber-100 text-amber-700',
    ];
    $jenisLabel = [
        'tahunan'        => 'Tahunan',
        'alasan_penting' => 'Alasan Penting',
    ];
@endphp

{{-- INFO BANNER --}}
<div class="rounded-xl border border-purple-100 bg-purple-50 px-4 py-3 mb-5 flex items-start gap-3">
    <span class="text-lg">💡</span>
    <div class="text-sm text-purple-800">
        <p class="font-semibold">Cuti Sebelum Sistem</p>
        <p class="text-xs mt-0.5 text-purple-700">
            Input cuti yang sudah diambil user <strong>sebelum sistem ini berjalan</strong>.
            Data ini dipakai untuk menghitung <strong>sisa cuti</strong> di tab Cuti. Khusus PPNPN.
        </p>
    </div>
</div>

{{-- TOOLBAR --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h3 class="text-lg font-bold text-slate-900">Cuti Sebelumnya</h3>
        <p class="text-sm text-slate-400 mt-0.5">
            {{ $data['cutiLalus']->count() }} data ditampilkan · Total: {{ $data['total'] }}
        </p>
    </div>

    <button type="button"
            onclick="bukaModalCutiLalu('tambah')"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 text-sm font-bold shadow-lg shadow-amber-200 transition">
        <span>+</span>
        <span>Tambah Cuti Sebelumnya</span>
    </button>
</div>

{{-- FILTER --}}
<div class="flex flex-wrap items-end gap-3 mb-5">
    <div class="min-w-[120px]">
        <label class="block text-xs font-semibold text-slate-600 mb-1">Tahun</label>
        <select id="filterTahun"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" {{ $data['filterTahun'] == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>

    <div class="flex-1 min-w-[200px]">
        <label class="block text-xs font-semibold text-slate-600 mb-1">User (PPNPN)</label>
        <select id="filterUser"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
            <option value="">— Semua PPNPN —</option>
            @foreach($data['users'] as $u)
                <option value="{{ $u->id }}" {{ $data['filterUser'] == $u->id ? 'selected' : '' }}>
                    {{ $u->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex gap-2">
        <button type="button" onclick="applyFilterCutiLalu()"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-sm font-bold shadow-sm">
            🔍 Filter
        </button>
        <button type="button" onclick="resetFilterCutiLalu()"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 text-sm font-semibold">
            Reset
        </button>
    </div>
</div>

{{-- TABLE --}}
<div class="overflow-x-auto rounded-xl border border-slate-100">
    <table class="w-full min-w-[1000px]">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">User</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Jenis</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Periode</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Hari</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Alasan</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Dicatat Oleh</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse($data['cutiLalus'] as $c)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-100 to-indigo-100 text-[10px] font-bold text-purple-700">
                                {{ strtoupper(substr($c->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $c->user->name ?? '—' }}
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono">
                                    {{ $c->user->username ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold {{ $jenisBadge[$c->jenis_cuti] ?? 'bg-slate-100' }}">
                            {{ $jenisLabel[$c->jenis_cuti] ?? $c->jenis_cuti }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-700">
                        <p class="font-mono text-xs">
                            {{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') }}
                            <span class="text-slate-400">→</span>
                            {{ \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') }}
                        </p>
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-bold text-slate-700">
                        {{ $c->jumlah_hari }}
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600 max-w-xs">
                        <p class="truncate" title="{{ $c->alasan }}">
                            {{ $c->alasan ? \Illuminate\Support\Str::limit($c->alasan, 40) : '—' }}
                        </p>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-500">
                        @if($c->dicatatOleh)
                            {{ $c->dicatatOleh->name }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">

                            {{-- EDIT --}}
                            <button type="button"
                                    onclick="bukaModalCutiLalu('edit', {{ $c->id }})"
                                    data-cuti-lalu='@json($c)'
                                    class="btn-edit-cuti-lalu inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                    title="Edit">
                                ✏️
                            </button>

                            {{-- HAPUS --}}
                            <form action="{{ route('admin.cuti-lalu.destroy', $c->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus cuti sebelumnya {{ $c->user->name ?? '' }} ({{ $c->jumlah_hari }} hari)?');"
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
                    <td colspan="7" class="px-4 py-12 text-center">
                        <p class="text-3xl mb-2">📭</p>
                        <p class="text-sm text-slate-400">Belum ada cuti sebelumnya.</p>
                        <p class="text-xs text-slate-400 mt-1">Klik "Tambah" untuk input cuti historis.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ===================================================== --}}
{{-- MODAL TAMBAH/EDIT --}}
{{-- ===================================================== --}}
<div id="modalCutiLalu" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        {{-- HEADER --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <div>
                <h3 id="modalCutiLaluTitle" class="text-base font-bold text-slate-900">Tambah Cuti Sebelumnya</h3>
                <p class="text-xs text-slate-400 mt-0.5">Isi data cuti historis PPNPN.</p>
            </div>
            <button type="button" onclick="tutupModalCutiLalu()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        {{-- FORM --}}
        <form id="formCutiLalu" method="POST" class="flex-1 overflow-y-auto">
            @csrf

            <div class="p-5 space-y-4">

                {{-- USER --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        User <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="cl_user_id" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="">— Pilih PPNPN —</option>
                        @foreach($data['users'] as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->username }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- JENIS --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jenis Cuti <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_cuti" id="cl_jenis_cuti" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="tahunan">Tahunan</option>
                        <option value="alasan_penting">Alasan Penting</option>
                    </select>
                </div>

                {{-- TANGGAL --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai" id="cl_tanggal_mulai" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_selesai" id="cl_tanggal_selesai" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>
                </div>

                {{-- JUMLAH HARI --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Jumlah Hari <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah_hari" id="cl_jumlah_hari" required min="1" max="365"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    <button type="button" onclick="hitungHariOtomatis()"
                            class="text-xs text-amber-600 hover:text-amber-700 font-semibold mt-1.5">
                        🔢 Hitung otomatis dari tanggal
                    </button>
                </div>

                {{-- ALASAN --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Alasan
                        <span class="text-xs text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="alasan" id="cl_alasan" rows="3" maxlength="500"
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                              placeholder="Contoh: cuti tahunan keluarga"></textarea>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 shrink-0 bg-slate-50">
                <button type="button" onclick="tutupModalCutiLalu()"
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
    function applyFilterCutiLalu() {
        const tahun  = document.getElementById('filterTahun').value;
        const userId = document.getElementById('filterUser').value;

        const params = new URLSearchParams({ tab: 'cuti-lalu', tahun });
        if (userId) params.set('user_id', userId);

        window.location.href = '{{ route('admin.control') }}?' + params.toString();
    }

    function resetFilterCutiLalu() {
        window.location.href = '{{ route('admin.control', ['tab' => 'cuti-lalu']) }}';
    }

    // =========================
    // MODAL
    // =========================
    const modalCutiLalu     = document.getElementById('modalCutiLalu');
    const formCutiLalu      = document.getElementById('formCutiLalu');
    const modalCutiLaluTitle = document.getElementById('modalCutiLaluTitle');

    const URL_CL_STORE  = '{{ route('admin.cuti-lalu.store') }}';
    const URL_CL_UPDATE = '{{ url('admin/control/cuti-lalu') }}/';

    function bukaModalCutiLalu(mode, id = null) {
        formCutiLalu.reset();

        // Hapus _method lama kalau ada
        const old = formCutiLalu.querySelector('input[name="_method"]');
        if (old) old.remove();

        if (mode === 'tambah') {
            modalCutiLaluTitle.textContent = 'Tambah Cuti Sebelumnya';
            formCutiLalu.action = URL_CL_STORE;

        } else {
            const btn = document.querySelector(`.btn-edit-cuti-lalu[data-cuti-lalu][onclick*="${id}"]`);
            if (!btn) return;
            const c = JSON.parse(btn.dataset.cutiLalu);

            modalCutiLaluTitle.textContent = 'Edit Cuti Sebelumnya';
            formCutiLalu.action = URL_CL_UPDATE + c.id;

            document.getElementById('cl_user_id').value         = c.user_id || '';
            document.getElementById('cl_jenis_cuti').value      = c.jenis_cuti || 'tahunan';
            document.getElementById('cl_tanggal_mulai').value   = c.tanggal_mulai || '';
            document.getElementById('cl_tanggal_selesai').value = c.tanggal_selesai || '';
            document.getElementById('cl_jumlah_hari').value     = c.jumlah_hari || 1;
            document.getElementById('cl_alasan').value          = c.alasan || '';

            // Method spoof PUT
            const mi = document.createElement('input');
            mi.type = 'hidden';
            mi.name = '_method';
            mi.value = 'PUT';
            formCutiLalu.appendChild(mi);
        }

        modalCutiLalu.classList.remove('hidden');
        modalCutiLalu.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalCutiLalu() {
        modalCutiLalu.classList.add('hidden');
        modalCutiLalu.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // Hitung otomatis jumlah hari dari tanggal
    function hitungHariOtomatis() {
        const mulai   = document.getElementById('cl_tanggal_mulai').value;
        const selesai = document.getElementById('cl_tanggal_selesai').value;

        if (!mulai || !selesai) return;

        const d1 = new Date(mulai);
        const d2 = new Date(selesai);

        if (d2 < d1) return;

        const diffTime = d2 - d1;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

        document.getElementById('cl_jumlah_hari').value = diffDays;
    }

    modalCutiLalu.addEventListener('click', e => {
        if (e.target === modalCutiLalu) tutupModalCutiLalu();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') tutupModalCutiLalu();
    });
</script>