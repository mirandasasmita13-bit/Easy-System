{{-- ===================================================== --}}
{{-- TAB: LUPA ABSEN --}}
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
    @php
        $pillLabels = [
            'pending'  => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'semua'    => 'Semua',
        ];
    @endphp

    @foreach($pillLabels as $key => $label)
        <a href="{{ route('admin.control', ['tab' => 'lupa', 'status' => $key]) }}"
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
        <button type="button" onclick="applyFilterLupa()"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-sm font-bold shadow-sm">
            🔍 Filter
        </button>
        <button type="button" onclick="resetFilterLupa()"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 text-sm font-semibold">
            Reset
        </button>
    </div>
</div>

{{-- TOOLBAR --}}
<div class="flex items-center justify-between mb-3">
    <div>
        <h3 class="text-lg font-bold text-slate-900">Pengajuan Lupa Absen</h3>
        <p class="text-sm text-slate-400 mt-0.5">
            {{ $data['pengajuans']->count() }} pengajuan ditampilkan
        </p>
    </div>
</div>

{{-- TABLE --}}
<div class="overflow-x-auto rounded-xl border border-slate-100">
    <table class="w-full min-w-[950px]">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">User</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Jenis</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Jam</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Bukti</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Status</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse($data['pengajuans'] as $p)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            Diajukan: {{ \Carbon\Carbon::parse($p->created_at)->format('d/m H:i') }}
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-100 to-indigo-100 text-[10px] font-bold text-purple-700">
                                {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">
                                    {{ $p->user->name ?? '—' }}
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono">
                                    {{ $p->user->username ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($p->jenis_absen === 'masuk')
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-[11px] font-semibold text-blue-700">
                                → Masuk
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-1 text-[11px] font-semibold text-orange-700">
                                ← Pulang
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-sm font-mono font-semibold text-slate-700">
                        {{ $p->jam ? substr($p->jam, 0, 5) : '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($p->bukti)
                            <a href="{{ asset('storage/' . $p->bukti) }}" target="_blank"
                               class="inline-flex items-center gap-1 rounded-md bg-slate-100 hover:bg-slate-200 px-2 py-1 text-[11px] font-semibold text-slate-700 transition">
                                📎 Lihat
                            </a>
                        @else
                            <span class="text-[11px] text-red-500">Tidak ada</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-[11px] font-bold {{ $statusBadge[$p->status] ?? 'bg-slate-100' }}">
                            {{ $statusIcon[$p->status] ?? '' }} {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                <div class="flex items-center justify-center gap-1.5">

                    {{-- DETAIL --}}
                    <button type="button"
                            onclick="bukaDetailLupa({{ $p->id }})"
                            data-lupa='@json($p)'
                            class="btn-detail-lupa inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition"
                            title="Detail">
                        👁
                    </button>

                    @if($p->status === 'pending')
                        {{-- EDIT --}}
                        <button type="button"
                                onclick="bukaEditLupa({{ $p->id }})"
                                data-lupa='@json($p)'
                                class="btn-edit-lupa inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                title="Edit">
                            ✏️
                        </button>

                        {{-- APPROVE --}}
                        <button type="button"
                                onclick="bukaApprove({{ $p->id }})"
                                data-lupa='@json($p)'
                                class="btn-approve-lupa inline-flex items-center justify-center h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition"
                                title="Approve">
                            ✅
                        </button>

                        {{-- REJECT --}}
                        <button type="button"
                                onclick="bukaReject({{ $p->id }})"
                                data-lupa='@json($p)'
                                class="btn-reject-lupa inline-flex items-center justify-center h-8 w-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
                                title="Reject">
                            ❌
                        </button>
                    @else
                        {{-- CANCEL --}}
                        <button type="button"
                                onclick="bukaCancel({{ $p->id }})"
                                data-lupa='@json($p)'
                                class="btn-cancel-lupa inline-flex items-center justify-center h-8 w-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition"
                                title="Batalkan Approval">
                            ↩️
                        </button>
                    @endif

                </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center">
                        <p class="text-3xl mb-2">📭</p>
                        <p class="text-sm text-slate-400">Belum ada pengajuan.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ===================================================== --}}
{{-- MODAL DETAIL --}}
{{-- ===================================================== --}}
<div id="modalDetailLupa" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <h3 class="text-base font-bold text-slate-900">Detail Pengajuan</h3>
            <button type="button" onclick="tutupDetailLupa()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>
        <div class="p-5 overflow-y-auto space-y-3 text-sm" id="detailLupaContent">
            {{-- diisi JS --}}
        </div>
        <div class="border-t border-slate-100 px-5 py-4 flex justify-end shrink-0 bg-slate-50">
            <button type="button" onclick="tutupDetailLupa()"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                Tutup
            </button>
        </div>
    </div>
</div>


{{-- ===================================================== --}}
{{-- MODAL APPROVE --}}
{{-- ===================================================== --}}
<div id="modalApprove" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-base font-bold text-emerald-700">✅ Approve Pengajuan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Data absensi akan otomatis diperbarui.</p>
            </div>
            <button type="button" onclick="tutupApprove()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formApprove" method="POST">
            @csrf
            <div class="p-5 space-y-3">
                <div class="rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2 text-xs text-emerald-700">
                    <p id="approveInfo"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Catatan Admin
                        <span class="text-xs text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="catatan_admin" rows="3" maxlength="500"
                              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                              placeholder="Catatan untuk user..."></textarea>
                </div>
            </div>

            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 bg-slate-50">
                <button type="button" onclick="tutupApprove()"
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
<div id="modalReject" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-base font-bold text-red-700">❌ Tolak Pengajuan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Catatan wajib diisi.</p>
            </div>
            <button type="button" onclick="tutupReject()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formReject" method="POST">
            @csrf
            <div class="p-5 space-y-3">
                <div class="rounded-lg bg-red-50 border border-red-100 px-3 py-2 text-xs text-red-700">
                    <p id="rejectInfo"></p>
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
                <button type="button" onclick="tutupReject()"
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
{{-- MODAL EDIT --}}
{{-- ===================================================== --}}
<div id="modalEditLupa" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <div>
                <h3 class="text-base font-bold text-slate-900">✏️ Edit Pengajuan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Koreksi pengajuan yang salah input.</p>
            </div>
            <button type="button" onclick="tutupEditLupa()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formEditLupa" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            @csrf
            @method('PUT')

            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal" required
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jenis Absen</label>
                        <select name="jenis_absen" id="edit_jenis_absen" required
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                            <option value="masuk">Masuk</option>
                            <option value="pulang">Pulang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam</label>
                        <input type="time" name="jam" id="edit_jam" required
                               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alasan</label>
                    <textarea name="alasan" id="edit_alasan" rows="3" required maxlength="1000"
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Bukti Baru
                        <span class="text-xs text-slate-400 font-normal">(kosongin kalau tidak ganti)</span>
                    </label>
                    <input type="file" name="bukti" accept="image/*,application/pdf"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm">
                    <p class="text-xs text-slate-400 mt-1" id="edit_bukti_lama"></p>
                </div>
            </div>

            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 shrink-0 bg-slate-50">
                <button type="button" onclick="tutupEditLupa()"
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
{{-- MODAL CANCEL --}}
{{-- ===================================================== --}}
<div id="modalCancel" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-base font-bold text-amber-700">↩️ Batalkan Approval</h3>
                <p class="text-xs text-slate-400 mt-0.5">Pengajuan akan kembali ke pending.</p>
            </div>
            <button type="button" onclick="tutupCancel()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>

        <form id="formCancel" method="POST">
            @csrf
            <div class="p-5 space-y-3">
                <div class="rounded-lg bg-amber-50 border border-amber-100 px-3 py-2 text-xs text-amber-700">
                    <p id="cancelInfo"></p>
                </div>
                <div class="rounded-lg bg-red-50 border border-red-100 px-3 py-2 text-xs text-red-700">
                    ⚠️ Jika sudah di-approve, jam absensi akan <strong>dikembalikan</strong> ke nilai sebelum approval.
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
                <button type="button" onclick="tutupCancel()"
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
    function applyFilterLupa() {
        const status  = '{{ $data['filterStatus'] }}';
        const tanggal = document.getElementById('filterTanggal').value;
        const userId  = document.getElementById('filterUser').value;

        const params = new URLSearchParams({ tab: 'lupa', status });
        if (tanggal) params.set('tanggal', tanggal);
        if (userId)  params.set('user_id', userId);

        window.location.href = '{{ route('admin.control') }}?' + params.toString();
    }

    function resetFilterLupa() {
        window.location.href = '{{ route('admin.control', ['tab' => 'lupa']) }}';
    }

    // =========================
    // MODAL HELPERS
    // =========================
    function showModal(el) { el.classList.remove('hidden'); el.classList.add('flex'); document.body.style.overflow = 'hidden'; }
    function hideModal(el) { el.classList.add('hidden'); el.classList.remove('flex'); document.body.style.overflow = ''; }

    // =========================
    // DETAIL
    // =========================
    const modalDetail = document.getElementById('modalDetailLupa');

    function bukaDetailLupa(id) {
        const btn = document.querySelector(`.btn-detail-lupa[data-lupa][onclick*="${id}"]`);
        if (!btn) return;
        const p = JSON.parse(btn.dataset.lupa);

        const statusColor = { pending: 'text-amber-600', approved: 'text-emerald-600', rejected: 'text-red-600' }[p.status] || '';

        let html = `
            <div class="grid grid-cols-2 gap-3">
                <div><p class="text-xs text-slate-400">User</p><p class="font-semibold">${p.user?.name ?? '—'}</p></div>
                <div><p class="text-xs text-slate-400">Tanggal</p><p class="font-semibold">${p.tanggal}</p></div>
                <div><p class="text-xs text-slate-400">Jenis Absen</p><p class="font-semibold">${p.jenis_absen}</p></div>
                <div><p class="text-xs text-slate-400">Jam</p><p class="font-semibold font-mono">${(p.jam || '').substring(0,5)}</p></div>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Alasan</p>
                <p class="rounded-lg bg-slate-50 px-3 py-2">${p.alasan ?? '—'}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Bukti</p>
                ${p.bukti
                    ? `<a href="/storage/${p.bukti}" target="_blank" class="text-blue-600 underline text-sm">📎 Lihat Bukti</a>`
                    : '<p class="text-red-500 text-sm">Tidak ada bukti</p>'}
            </div>
            <div class="pt-2 border-t">
                <p class="text-xs text-slate-400">Status</p>
                <p class="font-bold uppercase ${statusColor}">${p.status}</p>
                ${p.catatan_admin ? `<p class="text-xs text-slate-500 mt-1">Catatan admin: ${p.catatan_admin}</p>` : ''}
            </div>
        `;

        document.getElementById('detailLupaContent').innerHTML = html;
        showModal(modalDetail);
    }
    function tutupDetailLupa() { hideModal(modalDetail); }

    // =========================
    // APPROVE
    // =========================
    const modalApprove = document.getElementById('modalApprove');
    const formApprove  = document.getElementById('formApprove');

    function bukaApprove(id) {
        const btn = document.querySelector(`.btn-approve-lupa[data-lupa][onclick*="${id}"]`);
        if (!btn) return;
        const p = JSON.parse(btn.dataset.lupa);

        formApprove.action = '{{ url('admin/control/lupa') }}/' + id + '/approve';
        document.getElementById('approveInfo').textContent =
            `Approve perbaikan absen ${p.jenis_absen} untuk ${p.user?.name ?? ''} pada ${p.tanggal} jam ${(p.jam || '').substring(0,5)}.`;

        showModal(modalApprove);
    }
    function tutupApprove() { hideModal(modalApprove); }

    // =========================
    // REJECT
    // =========================
    const modalReject = document.getElementById('modalReject');
    const formReject  = document.getElementById('formReject');

    function bukaReject(id) {
        const btn = document.querySelector(`.btn-reject-lupa[data-lupa][onclick*="${id}"]`);
        if (!btn) return;
        const p = JSON.parse(btn.dataset.lupa);

        formReject.action = '{{ url('admin/control/lupa') }}/' + id + '/reject';
        document.getElementById('rejectInfo').textContent =
            `Tolak pengajuan perbaikan absen ${p.jenis_absen} untuk ${p.user?.name ?? ''} tanggal ${p.tanggal}.`;

        showModal(modalReject);
    }
    function tutupReject() { hideModal(modalReject); }

    // Close modal by backdrop
    [modalDetail, modalApprove, modalReject].forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) hideModal(m); });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            hideModal(modalDetail);
            hideModal(modalApprove);
            hideModal(modalReject);
        }
    });

    // =========================
    // EDIT
    // =========================
    const modalEditLupa = document.getElementById('modalEditLupa');
    const formEditLupa  = document.getElementById('formEditLupa');

    function bukaEditLupa(id) {
        const btn = document.querySelector(`.btn-edit-lupa[data-lupa][onclick*="${id}"]`);
        if (!btn) return;
        const p = JSON.parse(btn.dataset.lupa);

        formEditLupa.action = '{{ url('admin/control/lupa') }}/' + id;

        document.getElementById('edit_tanggal').value      = p.tanggal || '';
        document.getElementById('edit_jenis_absen').value  = p.jenis_absen || 'masuk';
        document.getElementById('edit_jam').value          = (p.jam || '').substring(0, 5);
        document.getElementById('edit_alasan').value       = p.alasan || '';
        document.getElementById('edit_bukti_lama').textContent = p.bukti
            ? 'Bukti lama: ' + p.bukti
            : 'Belum ada bukti.';

        showModal(modalEditLupa);
    }
    function tutupEditLupa() { hideModal(modalEditLupa); }

    // =========================
    // CANCEL
    // =========================
    const modalCancel = document.getElementById('modalCancel');
    const formCancel  = document.getElementById('formCancel');

    function bukaCancel(id) {
        const btn = document.querySelector(`.btn-cancel-lupa[data-lupa][onclick*="${id}"]`);
        if (!btn) return;
        const p = JSON.parse(btn.dataset.lupa);

        formCancel.action = '{{ url('admin/control/lupa') }}/' + id + '/cancel';
        document.getElementById('cancelInfo').textContent =
            `Batalkan pengajuan ${p.jenis_absen} untuk ${p.user?.name ?? ''} tanggal ${p.tanggal} (status: ${p.status}).`;

        showModal(modalCancel);
    }
    function tutupCancel() { hideModal(modalCancel); }

    // Update backdrop close handler — GANTI yang lama
    [modalDetail, modalApprove, modalReject, modalEditLupa, modalCancel].forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) hideModal(m); });
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            hideModal(modalDetail);
            hideModal(modalApprove);
            hideModal(modalReject);
            hideModal(modalEditLupa);
            hideModal(modalCancel);
        }
    });
</script>