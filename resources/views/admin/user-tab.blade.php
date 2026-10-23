{{-- ===================================================== --}}
{{-- TAB: DATA USER --}}
{{-- ===================================================== --}}

@php
    $roleBadge = [
        'admin'   => 'bg-red-100 text-red-700',
        'ppnpn'   => 'bg-purple-100 text-purple-700',
        'pegawai' => 'bg-blue-100 text-blue-700',
        'magang'  => 'bg-amber-100 text-amber-700',
    ];
@endphp

{{-- PILL FILTER ROLE --}}
<div class="flex flex-wrap items-center gap-2 mb-4">
    @php
        $pillColors = [
            'semua'   => '#64748b',
            'admin'   => '#ef4444',
            'pegawai' => '#3b82f6',
            'ppnpn'   => '#a855f7',
            'magang'  => '#f59e0b',
        ];
        $pillLabels = [
            'semua'   => 'Semua',
            'admin'   => 'Admin',
            'pegawai' => 'Pegawai',
            'ppnpn'   => 'PPNPN',
            'magang'  => 'Magang',
        ];
    @endphp

    @foreach($pillLabels as $key => $label)
        <a href="{{ route('admin.control', ['tab' => 'user', 'role' => $key]) }}"
           class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition
                  {{ $data['roleFilter'] === $key
                      ? 'text-white shadow-sm'
                      : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
           @if($data['roleFilter'] === $key)
               style="background-color: {{ $pillColors[$key] }};"
           @endif>
            <span>{{ $label }}</span>
            <span class="rounded-full bg-white/30 px-2 py-0.5 text-xs">
                {{ $data['counts'][$key] }}
            </span>
        </a>
    @endforeach
</div>

{{-- TOOLBAR --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h3 class="text-lg font-bold text-slate-900">Data User</h3>
        <p class="text-sm text-slate-400 mt-0.5">
            Total: {{ $data['users']->count() }} user
            @if($data['roleFilter'] !== 'semua')
                <span class="text-amber-600 font-semibold">
                    (filter: {{ ucfirst($data['roleFilter']) }})
                </span>
            @endif
        </p>
    </div>

    <button type="button"
            onclick="bukaModalUser('tambah')"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 text-sm font-bold shadow-lg shadow-amber-200 transition">
        <span>+</span>
        <span>Tambah User</span>
    </button>
</div>

{{-- TABLE --}}
<div class="overflow-x-auto rounded-xl border border-slate-100">
    <table class="w-full min-w-[800px]">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Nama</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Username</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Role</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Sub-Role</th>
                <th class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Status</th>
                <th class="px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
            @forelse($data['users'] as $user)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            {{-- FOTO / INISIAL --}}
                            @if($user->profil && $user->profil->foto)
                                <img src="{{ asset('storage/' . $user->profil->foto) }}" 
                                     class="h-8 w-8 shrink-0 rounded-full object-cover ring-2 ring-slate-100" 
                                     alt="{{ $user->name }}">
                            @else
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-purple-100 to-indigo-100 text-xs font-bold text-purple-700">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <p class="text-sm font-semibold text-slate-800 truncate">
                                {{ $user->name }}
                            </p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600 font-mono">
                        {{ $user->username }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-[11px] font-semibold {{ $roleBadge[$user->role] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600">
                        {{ $user->sub_role ? ucfirst($user->sub_role) : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($user->status === 'aktif')
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-500">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">

                            {{-- EDIT --}}
                            <button type="button"
                                    onclick="bukaModalUser('edit', {{ $user->id }})"
                                    data-user='@json($user)'
                                    class="btn-edit-user inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                    title="Edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                </svg>
                            </button>

                            {{-- RESET PASSWORD (BARU) --}}
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.user.reset-password', $user->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Reset password {{ $user->name }}?\n\nPassword baru akan di-generate otomatis.\nUser wajib ganti saat login.');"
                                      class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition"
                                            title="Reset Password">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif

                            {{-- HAPUS --}}
                            <form action="{{ route('admin.user.destroy', $user->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus/nonaktifkan user {{ $user->name }}?');"
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
                        <p class="text-sm text-slate-400">Belum ada user.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- ===================================================== --}}
{{-- MODAL USER (Tambah & Edit) --}}
{{-- ===================================================== --}}
<div id="modalUser" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

        {{-- HEADER --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 shrink-0">
            <div>
                <h3 id="modalUserTitle" class="text-base font-bold text-slate-900">Tambah User</h3>
                <p class="text-xs text-slate-400 mt-0.5">Isi data user dengan lengkap.</p>
            </div>
            <button type="button" onclick="tutupModalUser()"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">
                ✕
            </button>
        </div>

        {{-- FORM --}}
        <form id="formUser" method="POST" class="flex-1 overflow-y-auto">
            @csrf

            <div class="p-5 space-y-4">

                {{-- NAMA --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="user_name" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                           placeholder="Nama lengkap">
                </div>

                {{-- USERNAME --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" name="username" id="user_username" required
                           pattern="[a-zA-Z0-9._-]+"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                           placeholder="contoh: budi.santoso">
                </div>

                {{-- PASSWORD --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Password
                        <span id="passHint" class="text-xs text-slate-400 font-normal"></span>
                    </label>
                    <input type="text" name="password" id="user_password"
                           class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                           placeholder="Minimal 8 karakter">
                    <p class="text-xs text-slate-400 mt-1">Password diinput manual, kasih ke user.</p>
                </div>

                {{-- ROLE --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role</label>
                    <select name="role" id="user_role" required
                            onchange="updateSubRole()"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="ppnpn">PPNPN</option>
                        <option value="magang">Magang / PKL</option>
                        <option value="pegawai">Pegawai</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                {{-- SUB ROLE --}}
                <div id="subRoleField">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Sub-Role</label>
                    <select name="sub_role" id="user_sub_role"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="">— Tidak ada —</option>
                        <option value="satpam">Satpam</option>
                        <option value="pramubakti">Pramubakti</option>
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Khusus PPNPN (satpam / pramubakti).</p>
                </div>

                {{-- STATUS --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status</label>
                    <select name="status" id="user_status" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="border-t border-slate-100 px-5 py-4 flex justify-end gap-2 shrink-0 bg-slate-50">
                <button type="button" onclick="tutupModalUser()"
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
    const modalUser = document.getElementById('modalUser');
    const formUser = document.getElementById('formUser');
    const modalUserTitle = document.getElementById('modalUserTitle');
    const passHint = document.getElementById('passHint');

    // Route templates
    const URL_STORE = '{{ route('admin.user.store') }}';
    const URL_UPDATE = '{{ url('admin/control/user') }}/';  // + {id}

    function bukaModalUser(mode, userId = null) {

        // Reset form
        formUser.reset();

        if (mode === 'tambah') {
            modalUserTitle.textContent = 'Tambah User';
            formUser.action = URL_STORE;
            passHint.textContent = '(wajib diisi)';

            // Hapus method spoof kalau ada
            const methodInput = formUser.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();

        } else {
            // Cari data user dari tombol yang diklik
            const btn = document.querySelector(`.btn-edit-user[data-user][onclick*="${userId}"]`);
            if (!btn) return;

            const user = JSON.parse(btn.dataset.user);

            modalUserTitle.textContent = 'Edit User: ' + user.name;
            formUser.action = URL_UPDATE + user.id;
            passHint.textContent = '(kosongin kalau gak ganti)';

            // Isi field
            document.getElementById('user_name').value = user.name;
            document.getElementById('user_username').value = user.username;
            document.getElementById('user_password').value = '';
            document.getElementById('user_role').value = user.role;
            document.getElementById('user_sub_role').value = user.sub_role || '';
            document.getElementById('user_status').value = user.status;

            // Tambah method spoof
            let methodInput = formUser.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                formUser.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            updateSubRole();
        }

        modalUser.classList.remove('hidden');
        modalUser.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function tutupModalUser() {
        modalUser.classList.add('hidden');
        modalUser.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function updateSubRole() {
        const role = document.getElementById('user_role').value;
        const subField = document.getElementById('subRoleField');

        if (role === 'ppnpn') {
            subField.style.display = '';
        } else {
            subField.style.display = 'none';
            document.getElementById('user_sub_role').value = '';
        }
    }

    // Modal close on backdrop click
    modalUser.addEventListener('click', function(e) {
        if (e.target === modalUser) tutupModalUser();
    });

    // ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') tutupModalUser();
    });

    // Init
    updateSubRole();
</script>