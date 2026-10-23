{{-- ===================================================== --}}
{{-- TAB: LEMBUR --}}
{{-- ===================================================== --}}

@php
    $statusBadge = [
        'pending'  => 'bg-amber-100 text-amber-700',
        'approved' => 'bg-emerald-100 text-emerald-700',
        'rejected' => 'bg-red-100 text-red-700',
    ];
    $statusIcon = [
        'pending'  => '⏳',
        'approved' => '✅',
        'rejected' => '❌',
    ];
@endphp

{{-- PILL FILTER STATUS --}}
<div class="flex flex-wrap items-center gap-2 mb-4">
    @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'semua' => 'Semua'] as $key => $label)
        <a href="{{ route('admin.control', ['tab' => 'lembur', 'status' => $key]) }}"
           class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition
                  {{ $data['filterStatus'] === $key
                      ? 'bg-amber-500 text-white shadow-sm'
                      : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <span>{{ $label }}</span>
            <span class="rounded-full bg-white/30 px-2 py-0.5 text-xs">
                {{ $data['counts'][$key] }}
            </span>
        </a>
    @endforeach
</div>

{{-- FILTER DETAIL --}}
<div class="flex flex-wrap items-end gap-3 mb-5">
    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
        <input type="date" id="filterTanggal" value="{{ $data['filterTanggal'] }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
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
        <button type="button" onclick="applyFilterLembur()"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-sm font-bold shadow-sm">
            🔍 Filter
        </button>
        <button type="button" onclick="resetFilterLembur()"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 text-sm font-semibold">
            Reset
        </button>
    </div>
</div>

{{-- TOOLBAR --}}
<div class="flex items-center justify-between mb-3">
    <div>
        <h3 class="text-lg font-bold text-slate-900">Data Lembur</h3>
        <p class="text-sm text-slate-400 mt-0.5">
            {{ $data['lemburs']->count() }} data ditampilkan
        </p>
    </div>
</div>

{{-- TABLE --}}
<div class="overflow-x-auto rounded-xl border border-slate-100">
    <table class="w-full min-w-[1000px]">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">User</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Jam</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Total</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Kegiatan</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Foto</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Status</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse($data['lemburs'] as $l)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($l->tanggal)->format('d/m/Y') }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            {{ \Carbon\Carbon::parse($l->tanggal)->isoFormat('ddd') }}
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-amber-100 to-orange-100 text-[10px] font-bold text-amber-700">
                                {{ strtoupper(substr($l->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $l->user->name ?? '—' }}
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono">
                                    {{ $l->user->username ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm font-mono text-slate-700">
                        {{ substr($l->jam_mulai, 0, 5) }}
                        <span class="text-slate-400">→</span>
                        {{ substr($l->jam_selesai, 0, 5) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center justify-center min-w-[40px] rounded-md bg-indigo-50 px-2 py-1 text-xs font-bold text-indigo-700">
                            {{ $l->total_jam ?? '—' }}j
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600 max-w-xs">
                        <p class="truncate" title="{{ $l->kegiatan }}">{{ $l->kegiatan }}</p>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($l->foto)
                            <a href="{{ asset('storage/' . $l->foto) }}" target="_blank"
                               class="inline-flex items-center gap-1 rounded-md bg-slate-100 hover:bg-slate-200 px-2 py-1 text-[11px] font-semibold text-slate-700 transition">
                                🖼 Lihat
                            </a>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-[11px] font-bold {{ $statusBadge[$l->status_approval] ?? 'bg-slate-100' }}">
                            {{ $statusIcon[$l->status_approval] ?? '' }} {{ ucfirst($l->status_approval) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">

                            {{-- DETAIL --}}
                            <button type="button"
                                    onclick="bukaDetailLembur({{ $l->id }})"
                                    data-lembur='@json($l)'
                                    class="btn-detail-lembur inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition"
                                    title="Detail">
                                👁
                            </button>

                            @if($l->status_approval === 'pending')
                                {{-- EDIT --}}
                                <button type="button"
                                        onclick="bukaEditLembur({{ $l->id }})"
                                        data-lembur='@json($l)'
                                        class="btn-edit-lembur inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                        title="Edit">
                                    ✏️
                                </button>

                                {{-- APPROVE --}}
                                <button type="button"
                                        onclick="bukaApproveLembur({{ $l->id }})"
                                        data-lembur='@json($l)'
                                        class="btn-approve-lembur inline-flex items-center justify-center h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition"
                                        title="Approve">
                                    ✅
                                </button>

                                {{-- REJECT --}}
                                <button type="button"
                                        onclick="bukaRejectLembur({{ $l->id }})"
                                        data-lembur='@json($l)'
                                        class="btn-reject-lembur inline-flex items-center justify-center h-8 w-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
                                        title="Reject">
                                    ❌
                                </button>
                            @else
                                {{-- CANCEL --}}
                                <button type="button"
                                        onclick="bukaCancelLembur({{ $l->id }})"
                                        data-lembur='@json($l)'
                                        class="btn-cancel-lembur inline-flex items-center justify-center h-8 w-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition"
                                        title="Batalkan Approval">
                                    ↩️
                                </button>
                            @endif

                            {{-- HAPUS --}}
                            <form action="{{ route('admin.lembur.destroy', $l->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus lembur {{ $l->user->name ?? '' }} tanggal {{ $l->tanggal }}?');"
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
                        <p class="text-sm text-slate-400">Belum ada data lembur.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ===================================================== --}}
{{-- MODAL DETAIL --}}
{{-- ===================================================== --}}
<div id="modalDetailLembur" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <h3 class="text-base font-bold text-slate-900">Detail Lembur</h3>
            <button type="button" onclick="tutupDetailLembur()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>
        <div class="p-5 overflow-y-auto space-y-3 text-sm" id="detailLemburContent"></div>
        <div class="border-t border-slate-100 px-5 py-4 flex justify-end shrink-0 bg-slate-50">
            <button type="button" onclick="tutupDetailLembur()"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                Tutup
            </button>
        </div>
    </div>
</div>


{{-- ===================================================== --}}
{{-- MODAL EDIT --}}
{{-- ===================================================== --}}
<div id="modalEditLembur" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <div>
                <h3 class="text-base font-bold text-slate-900">✏️ Edit Lembur</h3>
                <p class="text-xs text-slate-400 mt-0.5">Koreksi data lembur.</p>
            </div>
            <button type="button" onclick="tutupEditLembur()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formEditLembur" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            @csrf
            @method('PUT')

            <div class="p-5 space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal_lembur" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="edit_jam_mulai" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="edit_jam_selesai" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Total Jam
                        <span class="text-xs text-slate-400 font-normal">(kosongin kalau mau auto)</span>
                    </label>
                    <input type="number" step="0.01" name="total_jam" id="edit_total_jam" min="0.1" max="24"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kegiatan</label>
                    <input type="text" name="kegiatan" id="edit_kegiatan" required maxlength="255"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan</label>
                    <textarea name="keterangan" id="edit_keterangan_lembur" rows="3" maxlength="1000"
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Foto Baru
                        <span class="text-xs text-slate-400 font-normal">(kosongin kalau tidak ganti)</span>
                    </label>
                    <input type="file" name="foto" accept="image/*"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                    <p class="text-xs text-slate-400 mt-1" id="edit_foto_lama"></p>
                </div>

            </div>

            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 shrink-0 bg-slate-50">
                <button type="button" onclick="tutupEditLembur()"
                        class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-500 hover:bg-blue-600">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ===================================================== --}}
{{-- MODAL APPROVE --}}
{{-- ===================================================== --}}
<div id="modalApproveLembur" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-base font-bold text-emerald-700">✅ Approve Lembur</h3>
                <p class="text-xs text-slate-400 mt-0.5">Lembur akan tercatat sebagai disetujui.</p>
            </div>
            <button type="button" onclick="tutupApproveLembur()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formApproveLembur" method="POST">
            @csrf
            <div class="p-5 space-y-3">
                <div class="rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2 text-xs text-emerald-700">
                    <p id="approveInfoLembur"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Catatan Admin <span class="text-xs text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="catatan_admin" rows="3" maxlength="500"
                              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                              placeholder="Catatan untuk user..."></textarea>
                </div>
            </div>

            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 bg-slate-50">
                <button type="button" onclick="tutupApproveLembur()"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-emerald-500 hover:bg-emerald-600">
                    Ya, Approve
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ===================================================== --}}
{{-- MODAL REJECT --}}
{{-- ===================================================== --}}
<div id="modalRejectLembur" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-base font-bold text-red-700">❌ Tolak Lembur</h3>
                <p class="text-xs text-slate-400 mt-0.5">Catatan wajib diisi.</p>
            </div>
            <button type="button" onclick="tutupRejectLembur()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formRejectLembur" method="POST">
            @csrf
            <div class="p-5 space-y-3">
                <div class="rounded-lg bg-red-50 border border-red-100 px-3 py-2 text-xs text-red-700">
                    <p id="rejectInfoLembur"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="catatan_admin" rows="3" maxlength="500" required
                              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100"
                              placeholder="Alasan kenapa ditolak..."></textarea>
                </div>
            </div>

            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 bg-slate-50">
                <button type="button" onclick="tutupRejectLembur()"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-red-500 hover:bg-red-600">
                    Ya, Tolak
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ===================================================== --}}
{{-- MODAL CANCEL --}}
{{-- ===================================================== --}}
<div id="modalCancelLembur" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-base font-bold text-amber-700">↩️ Batalkan Approval</h3>
                <p class="text-xs text-slate-400 mt-0.5">Lembur kembali ke pending.</p>
            </div>
            <button type="button" onclick="tutupCancelLembur()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formCancelLembur" method="POST">
            @csrf
            <div class="p-5 space-y-3">
                <div class="rounded-lg bg-amber-50 border border-amber-100 px-3 py-2 text-xs text-amber-700">
                    <p id="cancelInfoLembur"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Alasan Pembatalan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alasan_cancel" rows="3" maxlength="500" required
                              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                              placeholder="Contoh: Salah pencet tombol approve"></textarea>
                </div>
            </div>

            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 bg-slate-50">
                <button type="button" onclick="tutupCancelLembur()"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-amber-500 hover:bg-amber-600">
                    Ya, Batalkan
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
    function applyFilterLembur() {
        const status  = '{{ $data['filterStatus'] }}';
        const tanggal = document.getElementById('filterTanggal').value;
        const userId  = document.getElementById('filterUser').value;

        const params = new URLSearchParams({ tab: 'lembur', status });
        if (tanggal) params.set('tanggal', tanggal);
        if (userId)  params.set('user_id', userId);

        window.location.href = '{{ route('admin.control') }}?' + params.toString();
    }

    function resetFilterLembur() {
        window.location.href = '{{ route('admin.control', ['tab' => 'lembur']) }}';
    }

    // =========================
    // MODAL HELPERS
    // =========================
    function showModal(el) { el.classList.remove('hidden'); el.classList.add('flex'); document.body.style.overflow = 'hidden'; }
    function hideModal(el) { el.classList.add('hidden'); el.classList.remove('flex'); document.body.style.overflow = ''; }

    // =========================
    // DETAIL
    // =========================
    const modalDetailLembur = document.getElementById('modalDetailLembur');

    function bukaDetailLembur(id) {
        const btn = document.querySelector(`.btn-detail-lembur[data-lembur][onclick*="${id}"]`);
        if (!btn) return;
        const l = JSON.parse(btn.dataset.lembur);

        const statusColor = { pending: 'text-amber-600', approved: 'text-emerald-600', rejected: 'text-red-600' }[l.status_approval] || '';

        let html = `
            <div class="grid grid-cols-2 gap-3">
                <div><p class="text-xs text-slate-400">User</p><p class="font-semibold">${l.user?.name ?? '—'}</p></div>
                <div><p class="text-xs text-slate-400">Tanggal</p><p class="font-semibold">${l.tanggal}</p></div>
                <div><p class="text-xs text-slate-400">Jam</p><p class="font-mono">${(l.jam_mulai||'').substring(0,5)} → ${(l.jam_selesai||'').substring(0,5)}</p></div>
                <div><p class="text-xs text-slate-400">Total Jam</p><p class="font-semibold">${l.total_jam ?? '—'} jam</p></div>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Kegiatan</p>
                <p class="rounded-lg bg-slate-50 px-3 py-2">${l.kegiatan ?? '—'}</p>
            </div>
            ${l.keterangan ? `
                <div>
                    <p class="text-xs text-slate-400 mb-1">Keterangan</p>
                    <p class="rounded-lg bg-slate-50 px-3 py-2 whitespace-pre-line">${l.keterangan}</p>
                </div>
            ` : ''}
            <div>
                <p class="text-xs text-slate-400 mb-1">Foto</p>
                ${l.foto
                    ? `<a href="/storage/${l.foto}" target="_blank" class="text-blue-600 underline text-sm">🖼 Lihat Foto</a>`
                    : '<p class="text-slate-400 text-sm">Tidak ada foto</p>'}
            </div>
            <div class="pt-2 border-t">
                <p class="text-xs text-slate-400">Status</p>
                <p class="font-bold uppercase ${statusColor}">${l.status_approval}</p>
                ${l.catatan_admin ? `<p class="text-xs text-slate-500 mt-1">Catatan admin: ${l.catatan_admin}</p>` : ''}
            </div>
        `;

        document.getElementById('detailLemburContent').innerHTML = html;
        showModal(modalDetailLembur);
    }
    function tutupDetailLembur() { hideModal(modalDetailLembur); }

    // =========================
    // EDIT
    // =========================
    const modalEditLembur = document.getElementById('modalEditLembur');
    const formEditLembur  = document.getElementById('formEditLembur');

    function bukaEditLembur(id) {
        const btn = document.querySelector(`.btn-edit-lembur[data-lembur][onclick*="${id}"]`);
        if (!btn) return;
        const l = JSON.parse(btn.dataset.lembur);

        formEditLembur.action = '{{ url('admin/control/lembur') }}/' + id;

        document.getElementById('edit_tanggal_lembur').value    = l.tanggal || '';
        document.getElementById('edit_jam_mulai').value         = (l.jam_mulai || '').substring(0, 5);
        document.getElementById('edit_jam_selesai').value       = (l.jam_selesai || '').substring(0, 5);
        document.getElementById('edit_total_jam').value         = l.total_jam || '';
        document.getElementById('edit_kegiatan').value          = l.kegiatan || '';
        document.getElementById('edit_keterangan_lembur').value = l.keterangan || '';
        document.getElementById('edit_foto_lama').textContent   = l.foto ? 'Foto lama: ' + l.foto : 'Belum ada foto.';

        showModal(modalEditLembur);
    }
    function tutupEditLembur() { hideModal(modalEditLembur); }

    // =========================
    // APPROVE
    // =========================
    const modalApproveLembur = document.getElementById('modalApproveLembur');
    const formApproveLembur  = document.getElementById('formApproveLembur');

    function bukaApproveLembur(id) {
        const btn = document.querySelector(`.btn-approve-lembur[data-lembur][onclick*="${id}"]`);
        if (!btn) return;
        const l = JSON.parse(btn.dataset.lembur);

        formApproveLembur.action = '{{ url('admin/control/lembur') }}/' + id + '/approve';
        document.getElementById('approveInfoLembur').textContent =
            `Approve lembur ${l.user?.name ?? ''} tanggal ${l.tanggal} (${l.total_jam ?? '—'} jam).`;

        showModal(modalApproveLembur);
    }
    function tutupApproveLembur() { hideModal(modalApproveLembur); }

    // =========================
    // REJECT
    // =========================
    const modalRejectLembur = document.getElementById('modalRejectLembur');
    const formRejectLembur  = document.getElementById('formRejectLembur');

    function bukaRejectLembur(id) {
        const btn = document.querySelector(`.btn-reject-lembur[data-lembur][onclick*="${id}"]`);
        if (!btn) return;
        const l = JSON.parse(btn.dataset.lembur);

        formRejectLembur.action = '{{ url('admin/control/lembur') }}/' + id + '/reject';
        document.getElementById('rejectInfoLembur').textContent =
            `Tolak lembur ${l.user?.name ?? ''} tanggal ${l.tanggal}.`;

        showModal(modalRejectLembur);
    }
    function tutupRejectLembur() { hideModal(modalRejectLembur); }

    // =========================
    // CANCEL
    // =========================
    const modalCancelLembur = document.getElementById('modalCancelLembur');
    const formCancelLembur  = document.getElementById('formCancelLembur');

    function bukaCancelLembur(id) {
        const btn = document.querySelector(`.btn-cancel-lembur[data-lembur][onclick*="${id}"]`);
        if (!btn) return;
        const l = JSON.parse(btn.dataset.lembur);

        formCancelLembur.action = '{{ url('admin/control/lembur') }}/' + id + '/cancel';
        document.getElementById('cancelInfoLembur').textContent =
            `Batalkan approval lembur ${l.user?.name ?? ''} tanggal ${l.tanggal} (status: ${l.status_approval}).`;

        showModal(modalCancelLembur);
    }
    function tutupCancelLembur() { hideModal(modalCancelLembur); }

    // Backdrop close
    [modalDetailLembur, modalEditLembur, modalApproveLembur, modalRejectLembur, modalCancelLembur].forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) hideModal(m); });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            hideModal(modalDetailLembur);
            hideModal(modalEditLembur);
            hideModal(modalApproveLembur);
            hideModal(modalRejectLembur);
            hideModal(modalCancelLembur);
        }
    });
</script>