{{-- ===================================================== --}}
{{-- TAB: SURAT --}}
{{-- ===================================================== --}}

{{-- FILTER BAR --}}
<div class="flex flex-wrap items-end gap-3 mb-5">

    {{-- TANGGAL --}}
    <div class="flex-1 min-w-[160px]">
        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
        <input type="date" id="filterTanggal" value="{{ $data['filterTanggal'] }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
    </div>

    {{-- JENIS SURAT --}}
    <div class="flex-1 min-w-[180px]">
        <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Surat</label>
        <select id="filterJenis"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
            <option value="">— Semua Jenis —</option>
            @foreach($data['jenisSuratList'] as $js)
                <option value="{{ $js }}" {{ $data['filterJenis'] === $js ? 'selected' : '' }}>
                    {{ $js }}
                </option>
            @endforeach
        </select>
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

    {{-- BUTTON --}}
    <div class="flex gap-2">
        <button type="button" onclick="applyFilterSurat()"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-sm font-bold shadow-sm">
            🔍 Filter
        </button>
        <button type="button" onclick="resetFilterSurat()"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 text-sm font-semibold">
            Reset
        </button>
    </div>

</div>

{{-- TOOLBAR --}}
<div class="flex items-center justify-between mb-3">
    <div>
        <h3 class="text-lg font-bold text-slate-900">Pengajuan Surat</h3>
        <p class="text-sm text-slate-400 mt-0.5">
            {{ $data['surats']->count() }} pengajuan ditampilkan · Total: {{ $data['total'] }}
        </p>
    </div>
</div>

{{-- TABLE --}}
<div class="overflow-x-auto rounded-xl border border-slate-100">
    <table class="w-full min-w-[900px]">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">User</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Jenis Surat</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Keperluan</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Dokumen</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse($data['surats'] as $s)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}
                        </p>
                        <p class="text-[11px] text-slate-400">
                            Diajukan: {{ \Carbon\Carbon::parse($s->created_at)->format('d/m H:i') }}
                        </p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-100 to-cyan-100 text-[10px] font-bold text-blue-700">
                                {{ strtoupper(substr($s->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ $s->user->name ?? '—' }}
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono">
                                    {{ $s->user->username ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-[11px] font-semibold text-blue-700">
                            {{ $s->jenis_surat }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600 max-w-xs">
                        <p class="truncate" title="{{ $s->keperluan }}">
                            {{ \Illuminate\Support\Str::limit($s->keperluan, 60) }}
                        </p>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($s->dokumen)
                            <a href="{{ asset('storage/' . $s->dokumen) }}" target="_blank"
                               class="inline-flex items-center gap-1 rounded-md bg-slate-100 hover:bg-slate-200 px-2 py-1 text-[11px] font-semibold text-slate-700 transition">
                                📎 Lihat
                            </a>
                        @else
                            <span class="text-[11px] text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">

                            {{-- DETAIL --}}
                            <button type="button"
                                    onclick="bukaDetailSurat({{ $s->id }})"
                                    data-surat='@json($s)'
                                    class="btn-detail-surat inline-flex items-center justify-center h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition"
                                    title="Detail">
                                👁
                            </button>

                            {{-- HAPUS --}}
                            <form action="{{ route('admin.surat.destroy', $s->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus pengajuan surat {{ $s->jenis_surat }} dari {{ $s->user->name ?? '' }}?');"
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
                        <p class="text-sm text-slate-400">Belum ada pengajuan surat.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ===================================================== --}}
{{-- MODAL DETAIL --}}
{{-- ===================================================== --}}
<div id="modalDetailSurat" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <h3 class="text-base font-bold text-slate-900">Detail Pengajuan Surat</h3>
            <button type="button" onclick="tutupDetailSurat()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">✕</button>
        </div>
        <div class="p-5 overflow-y-auto space-y-3 text-sm" id="detailSuratContent">
            {{-- diisi JS --}}
        </div>
        <div class="border-t border-slate-100 px-5 py-4 flex justify-end shrink-0 bg-slate-50">
            <button type="button" onclick="tutupDetailSurat()"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">
                Tutup
            </button>
        </div>
    </div>
</div>


{{-- ===================================================== --}}
{{-- JAVASCRIPT --}}
{{-- ===================================================== --}}
<script>
    // =========================
    // FILTER
    // =========================
    function applyFilterSurat() {
        const tanggal = document.getElementById('filterTanggal').value;
        const jenis   = document.getElementById('filterJenis').value;
        const userId  = document.getElementById('filterUser').value;

        const params = new URLSearchParams({ tab: 'surat' });
        if (tanggal) params.set('tanggal', tanggal);
        if (jenis)   params.set('jenis_surat', jenis);
        if (userId)  params.set('user_id', userId);

        window.location.href = '{{ route('admin.control') }}?' + params.toString();
    }

    function resetFilterSurat() {
        window.location.href = '{{ route('admin.control', ['tab' => 'surat']) }}';
    }

    // =========================
    // MODAL DETAIL
    // =========================
    const modalDetailSurat = document.getElementById('modalDetailSurat');

    function showModal(el) { el.classList.remove('hidden'); el.classList.add('flex'); document.body.style.overflow = 'hidden'; }
    function hideModal(el) { el.classList.add('hidden'); el.classList.remove('flex'); document.body.style.overflow = ''; }

    function bukaDetailSurat(id) {
        const btn = document.querySelector(`.btn-detail-surat[data-surat][onclick*="${id}"]`);
        if (!btn) return;
        const s = JSON.parse(btn.dataset.surat);

        let html = `
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-xs text-slate-400">User</p>
                    <p class="font-semibold">${s.user?.name ?? '—'}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Username</p>
                    <p class="font-mono text-xs">${s.user?.username ?? '—'}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Tanggal Surat</p>
                    <p class="font-semibold">${s.tanggal}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Jenis Surat</p>
                    <p class="font-semibold">${s.jenis_surat}</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Keperluan</p>
                <p class="rounded-lg bg-slate-50 px-3 py-2 whitespace-pre-line">${s.keperluan ?? '—'}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 mb-1">Dokumen</p>
                ${s.dokumen
                    ? `<a href="/storage/${s.dokumen}" target="_blank" class="text-blue-600 underline text-sm">📎 ${s.nama_dokumen ?? 'Lihat Dokumen'}</a>`
                    : '<p class="text-slate-400 text-sm">Tidak ada dokumen</p>'}
            </div>
            <div class="pt-2 border-t">
                <p class="text-xs text-slate-400">Diajukan pada</p>
                <p class="text-xs">${new Date(s.created_at).toLocaleString('id-ID')}</p>
            </div>
        `;

        document.getElementById('detailSuratContent').innerHTML = html;
        showModal(modalDetailSurat);
    }
    function tutupDetailSurat() { hideModal(modalDetailSurat); }

    modalDetailSurat.addEventListener('click', e => { if (e.target === modalDetailSurat) tutupDetailSurat(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') tutupDetailSurat(); });
</script>