{{-- ===================================================== --}}
{{-- TAB: CUTI --}}
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

{{-- PILL FILTER JENIS --}}
<div class="flex flex-wrap items-center gap-2 mb-4">
    @foreach(['semua' => 'Semua', 'tahunan' => 'Tahunan', 'alasan_penting' => 'Alasan Penting'] as $key => $label)
        <a href="{{ route('admin.control', ['tab' => 'cuti', 'jenis_cuti' => $key === 'semua' ? null : $key, 'tahun' => $data['filterTahun']]) }}"
           class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition
                  {{ ($data['filterJenis'] ?? 'semua') === ($key === 'semua' ? null : $key) || ($key === 'semua' && !$data['filterJenis'])
                      ? 'bg-amber-500 text-white shadow-sm'
                      : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- FILTER DETAIL --}}
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

    <div class="flex gap-2">
        <button type="button" onclick="applyFilterCuti()"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-sm font-bold shadow-sm">
            🔍 Filter
        </button>
        <button type="button" onclick="resetFilterCuti()"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 text-sm font-semibold">
            Reset
        </button>
    </div>
</div>

{{-- TOGGLE: LIST vs SISA CUTI --}}
<div class="flex items-center gap-1.5 mb-5 border-b border-slate-100">
    <button type="button" id="tabListCuti" onclick="switchCutiView('list')"
            class="px-4 py-2 text-sm font-semibold border-b-2 border-amber-500 text-amber-600 -mb-px">
        📋 Daftar Pengajuan
    </button>
    <button type="button" id="tabSisaCuti" onclick="switchCutiView('sisa')"
            class="px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 -mb-px">
        📊 Rekap Sisa Cuti (PPNPN)
    </button>
</div>


{{-- ===================================================== --}}
{{-- VIEW: DAFTAR PENGAJUAN --}}
{{-- ===================================================== --}}
<div id="viewListCuti">

    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Daftar Pengajuan Cuti</h3>
            <p class="text-sm text-slate-400 mt-0.5">
                {{ $data['cutis']->count() }} pengajuan ditampilkan
            </p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-100">
        <table class="w-full min-w-[950px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">User</th>
                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Jenis</th>
                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Periode</th>
                    <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Hari</th>
                    <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Surat</th>
                    <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                @forelse($data['cutis'] as $c)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 text-[10px] font-bold text-emerald-700">
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
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Diajukan: {{ \Carbon\Carbon::parse($c->tanggal_pengajuan)->format('d/m/Y') }}
                            </p>
                        </td>
                        <td class="px-4 py-3 text-center text-sm font-bold text-slate-700">
                            {{ $c->jumlah_hari }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($c->surat)
                                <a href="{{ asset('storage/' . $c->surat) }}" target="_blank"
                                   class="inline-flex items-center gap-1 rounded-md bg-slate-100 hover:bg-slate-200 px-2 py-1 text-[11px] font-semibold text-slate-700 transition">
                                    📎 Lihat
                                </a>
                            @else
                                <span class="text-[11px] text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button"
                                        onclick="bukaDetailCuti({{ $c->id }})"
                                        data-cuti='@json($c)'
                                        class="btn-detail-cuti inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition"
                                        title="Detail">
                                    👁
                                </button>

                                <button type="button"
                                        onclick="bukaEditCuti({{ $c->id }})"
                                        data-cuti='@json($c)'
                                        class="btn-edit-cuti inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                        title="Edit">
                                    ✏️
                                </button>

                                <form action="{{ route('admin.cuti.destroy', $c->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin hapus cuti {{ $c->user->name ?? '' }} ({{ $c->jumlah_hari }} hari)?');"
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
                        <td colspan="6" class="px-4 py-12 text-center">
                            <p class="text-3xl mb-2">📭</p>
                            <p class="text-sm text-slate-400">Belum ada pengajuan cuti.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ===================================================== --}}
{{-- VIEW: REKAP SISA CUTI (PPNPN) --}}
{{-- ===================================================== --}}
<div id="viewSisaCuti" class="hidden">

    <div class="mb-3">
        <h3 class="text-lg font-bold text-slate-900">Rekap Sisa Cuti PPNPN {{ $data['filterTahun'] }}</h3>
        <p class="text-sm text-slate-400 mt-0.5">
            Sisa = Jatah − Cuti Sebelumnya − Terpakai tahun ini
        </p>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-100">
        <table class="w-full min-w-[800px]">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">User</th>
                    <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Sub-Role</th>
                    <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Jatah</th>
                    <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Sebelumnya</th>
                    <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Terpakai</th>
                    <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Sisa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($data['sisaCutiPerUser'] as $row)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-slate-800">{{ $row['user']->name }}</p>
                            <p class="text-[11px] text-slate-400 font-mono">{{ $row['user']->username }}</p>
                        </td>

                        {{-- Kolom Sub-Role (menggantikan Role) --}}
                        <td class="px-4 py-3">
                            @if($row['user']->sub_role)
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold bg-purple-100 text-purple-700">
                                    {{ ucfirst($row['user']->sub_role) }}
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center text-sm">{{ $row['jatah'] }}</td>
                        <td class="px-4 py-3 text-center text-sm text-slate-500">{{ $row['sebelumnya'] }}</td>
                        <td class="px-4 py-3 text-center text-sm font-semibold text-amber-600">{{ $row['terpakai'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center min-w-[36px] rounded-md px-2 py-1 text-sm font-bold
                                {{ $row['sisa'] > 5 ? 'bg-emerald-100 text-emerald-700' : ($row['sisa'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ $row['sisa'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <p class="text-sm text-slate-400">Belum ada data PPNPN.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ===================================================== --}}
{{-- MODAL DETAIL CUTI --}}
{{-- ===================================================== --}}
<div id="modalDetailCuti" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <h3 class="text-base font-bold text-slate-900">Detail Pengajuan Cuti</h3>
            <button type="button" onclick="tutupDetailCuti()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>
        <div class="p-5 overflow-y-auto space-y-3 text-sm" id="detailCutiContent"></div>
        <div class="border-t border-slate-100 px-5 py-4 flex justify-end shrink-0 bg-slate-50">
            <button type="button" onclick="tutupDetailCuti()"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                Tutup
            </button>
        </div>
    </div>
</div>


{{-- ===================================================== --}}
{{-- MODAL EDIT CUTI --}}
{{-- ===================================================== --}}
<div id="modalEditCuti" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <div>
                <h3 class="text-base font-bold text-slate-900">✏️ Edit Pengajuan Cuti</h3>
                <p class="text-xs text-slate-400 mt-0.5">Koreksi data kalau ada salah input.</p>
            </div>
            <button type="button" onclick="tutupEditCuti()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formEditCuti" method="POST" class="flex-1 overflow-y-auto">
            @csrf
            @method('PUT')

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Cuti</label>
                    <select name="jenis_cuti" id="edit_jenis_cuti" required
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="tahunan">Tahunan</option>
                        <option value="alasan_penting">Alasan Penting</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jumlah Hari</label>
                    <input type="number" name="jumlah_hari" id="edit_jumlah_hari" required min="1" max="365"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                    <textarea name="keterangan" id="edit_keterangan" rows="3" maxlength="1000"
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"></textarea>
                </div>
            </div>

            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 shrink-0 bg-slate-50">
                <button type="button" onclick="tutupEditCuti()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-500 hover:bg-amber-600">
                    Simpan Perubahan
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
    function applyFilterCuti() {
        const tahun  = document.getElementById('filterTahun').value;
        const userId = document.getElementById('filterUser').value;

        const params = new URLSearchParams({ tab: 'cuti', tahun });
        if (userId) params.set('user_id', userId);
        const currentJenis = '{{ $data['filterJenis'] ?? '' }}';
        if (currentJenis) params.set('jenis_cuti', currentJenis);

        window.location.href = '{{ route('admin.control') }}?' + params.toString();
    }

    function resetFilterCuti() {
        window.location.href = '{{ route('admin.control', ['tab' => 'cuti']) }}';
    }

    // =========================
    // SWITCH VIEW
    // =========================
    function switchCutiView(view) {
        const viewList = document.getElementById('viewListCuti');
        const viewSisa = document.getElementById('viewSisaCuti');
        const tabList  = document.getElementById('tabListCuti');
        const tabSisa  = document.getElementById('tabSisaCuti');

        if (view === 'list') {
            viewList.classList.remove('hidden');
            viewSisa.classList.add('hidden');
            tabList.classList.add('border-amber-500', 'text-amber-600');
            tabList.classList.remove('border-transparent', 'text-slate-500');
            tabSisa.classList.remove('border-amber-500', 'text-amber-600');
            tabSisa.classList.add('border-transparent', 'text-slate-500');
        } else {
            viewList.classList.add('hidden');
            viewSisa.classList.remove('hidden');
            tabSisa.classList.add('border-amber-500', 'text-amber-600');
            tabSisa.classList.remove('border-transparent', 'text-slate-500');
            tabList.classList.remove('border-amber-500', 'text-amber-600');
            tabList.classList.add('border-transparent', 'text-slate-500');
        }
    }

    // =========================
    // MODAL HELPERS
    // =========================
    function showModal(el) { el.classList.remove('hidden'); el.classList.add('flex'); document.body.style.overflow = 'hidden'; }
    function hideModal(el) { el.classList.add('hidden'); el.classList.remove('flex'); document.body.style.overflow = ''; }

    // =========================
    // DETAIL
    // =========================
    const modalDetailCuti = document.getElementById('modalDetailCuti');

    function bukaDetailCuti(id) {
        const btn = document.querySelector(`.btn-detail-cuti[data-cuti][onclick*="${id}"]`);
        if (!btn) return;
        const c = JSON.parse(btn.dataset.cuti);

        let html = `
            <div class="grid grid-cols-2 gap-3">
                <div><p class="text-xs text-slate-400">User</p><p class="font-semibold">${c.user?.name ?? '—'}</p></div>
                <div><p class="text-xs text-slate-400">Username</p><p class="font-mono text-xs">${c.user?.username ?? '—'}</p></div>
                <div><p class="text-xs text-slate-400">Jenis Cuti</p><p class="font-semibold">${c.jenis_cuti}</p></div>
                <div><p class="text-xs text-slate-400">Jumlah Hari</p><p class="font-semibold">${c.jumlah_hari} hari</p></div>
                <div><p class="text-xs text-slate-400">Tanggal Mulai</p><p class="font-semibold">${c.tanggal_mulai}</p></div>
                <div><p class="text-xs text-slate-400">Tanggal Selesai</p><p class="font-semibold">${c.tanggal_selesai}</p></div>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Keterangan</p>
                <p class="rounded-lg bg-slate-50 px-3 py-2 whitespace-pre-line">${c.keterangan ?? '—'}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Surat</p>
                ${c.surat
                    ? `<a href="/storage/${c.surat}" target="_blank" class="text-blue-600 underline text-sm">📎 ${c.nama_surat ?? 'Lihat Surat'}</a>`
                    : '<p class="text-slate-400 text-sm">Tidak ada surat</p>'}
            </div>
        `;

        document.getElementById('detailCutiContent').innerHTML = html;
        showModal(modalDetailCuti);
    }
    function tutupDetailCuti() { hideModal(modalDetailCuti); }

    // =========================
    // EDIT
    // =========================
    const modalEditCuti = document.getElementById('modalEditCuti');
    const formEditCuti  = document.getElementById('formEditCuti');

    function bukaEditCuti(id) {
        const btn = document.querySelector(`.btn-edit-cuti[data-cuti][onclick*="${id}"]`);
        if (!btn) return;
        const c = JSON.parse(btn.dataset.cuti);

        formEditCuti.action = '{{ url('admin/control/cuti') }}/' + id;

        document.getElementById('edit_jenis_cuti').value      = c.jenis_cuti || 'tahunan';
        document.getElementById('edit_tanggal_mulai').value   = c.tanggal_mulai || '';
        document.getElementById('edit_tanggal_selesai').value = c.tanggal_selesai || '';
        document.getElementById('edit_jumlah_hari').value     = c.jumlah_hari || 1;
        document.getElementById('edit_keterangan').value      = c.keterangan || '';

        showModal(modalEditCuti);
    }
    function tutupEditCuti() { hideModal(modalEditCuti); }

    [modalDetailCuti, modalEditCuti].forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) hideModal(m); });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            hideModal(modalDetailCuti);
            hideModal(modalEditCuti);
        }
    });
</script>