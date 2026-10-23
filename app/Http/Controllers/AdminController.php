<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Http\Controllers\CutiTambahanController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\LupaAbsenController as AdminLupaAbsenController;
use App\Http\Controllers\admin\SuratController as AdminSuratController;
use App\Http\Controllers\admin\CutiController as AdminCutiController;
use App\Http\Controllers\Admin\LemburController as AdminLemburController;
use App\Http\Controllers\Admin\CutiLaluController as AdminCutiLaluController;
use App\Http\Controllers\Admin\LiburController as AdminLiburController;

class AdminController extends Controller
{
    /**
     * Halaman utama Admin Control Center
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'user');

        // Validasi tab
        $tabsValid = ['user', 'absensi', 'lupa', 'lembur', 'surat', 'cuti', 'cuti-lalu', 'libur', 'cuti-tambahan'];
        if (!in_array($tab, $tabsValid, true)) {
            $tab = 'user';
        }

        // Data per tab
        $data = [];

        if ($tab === 'user') {
            $roleFilter = $request->get('role', 'semua');

            $query = User::with('profil')
                ->orderByRaw("
                    CASE role
                        WHEN 'admin'   THEN 1
                        WHEN 'pegawai' THEN 2
                        WHEN 'ppnpn'   THEN 3
                        WHEN 'magang'  THEN 4
                        ELSE 5
                    END
                ")
                ->orderBy('name');

            if ($roleFilter !== 'semua') {
                $query->where('role', $roleFilter);
            }

            $data['users']      = $query->get();
            $data['roleFilter'] = $roleFilter;

            $data['counts'] = [
                'semua'   => User::count(),
                'admin'   => User::where('role', 'admin')->count(),
                'pegawai' => User::where('role', 'pegawai')->count(),
                'ppnpn'   => User::where('role', 'ppnpn')->count(),
                'magang'  => User::where('role', 'magang')->count(),
            ];
        }

        // Delegasi ke controller khusus absensi
        if ($tab === 'absensi') {
            return app(AdminAbsensiController::class)->index($request);
        }
        
        //  Admin Control (Lupa Absensi)
        if ($tab === 'lupa') {
            return app(AdminLupaAbsenController::class)->index($request);
        }

        // Admin Control (Surat Menyurat)
        if ($tab === 'surat') {
            return app(AdminSuratController::class)->index($request);
        }

        // Admin Control (Cuti)
        if ($tab === 'cuti') {
            return app(AdminCutiController::class)->index($request);
        }

        // Admin Control (Lembur)
        if ($tab === 'lembur') {
            return app(AdminLemburController::class)->index($request);
        }

        // Admin Control (Cuti Lalu)
        if ($tab === 'cuti-lalu') {
            return app(AdminCutiLaluController::class)->index($request);
        }

        //Admin Control (Libur)
        if ($tab === 'libur') {
            return app(AdminLiburController::class)->index($request);
        }

        //Admin Control (Cuti Tambahan untuk Pegawai)
        if ($tab === 'cuti-tambahan') {
            return app(CutiTambahanController::class)->adminIndex($request);
        }

        return view('admin.control', compact('tab', 'data'));
    }


    /* =========================================================
       USER MANAGEMENT
    ========================================================= */

    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:50',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9._-]+$/',
            ],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:admin,ppnpn,pegawai,magang'],
            'sub_role' => ['nullable', 'in:satpam,pramubakti'],
            'status'   => ['required', 'in:aktif,nonaktif'],
        ], [
            'username.unique' => 'Username sudah dipakai.',
            'username.regex'  => 'Username hanya boleh huruf, angka, titik, underscore, dan dash.',
            'password.min'    => 'Password minimal 8 karakter.',
        ]);

        if ($validated['role'] !== 'ppnpn') {
            $validated['sub_role'] = null;
        }

        User::create([
            'name'                    => $validated['name'],
            'username'                => $validated['username'],
            'password'                => $validated['password'],
            'role'                    => $validated['role'],
            'sub_role'                => $validated['sub_role'],
            'status'                  => $validated['status'],
            'jatah_cuti_tahunan'      => 12,
            'cuti_tahunan_sebelumnya' => 0,
            'tahun_cuti'              => now()->year,
        ]);

        return redirect()
            ->route('admin.control', ['tab' => 'user'])
            ->with('success', 'User berhasil ditambahkan.');
    }


    public function userUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
                'regex:/^[a-zA-Z0-9._-]+$/',
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'role'     => ['required', 'in:admin,ppnpn,pegawai,magang'],
            'sub_role' => ['nullable', 'in:satpam,pramubakti'],
            'status'   => ['required', 'in:aktif,nonaktif'],
        ]);

        if ($validated['role'] !== 'ppnpn') {
            $validated['sub_role'] = null;
        }

        $data = [
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'role'     => $validated['role'],
            'sub_role' => $validated['sub_role'],
            'status'   => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return redirect()
            ->route('admin.control', ['tab' => 'user'])
            ->with('success', 'User berhasil diperbarui.');
    }


    public function userDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        $punyaData = $user->absensis()->exists()
            || $user->pengajuancuti()->exists()
            || $user->lembur()->exists()
            || $user->pengajuanlupaabsen()->exists()
            || $user->pengajuansurat()->exists();

        if ($punyaData) {
            $user->update([
                'status'           => 'nonaktif',
                'tanggal_nonaktif' => now(),
            ]);

            return back()->with('success', 'User dinonaktifkan (data historis tetap aman).');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password user oleh admin.
     * Password baru di-generate otomatis, user wajib ganti saat login.
     */
    public function userResetPassword(User $user)
    {
        // Cegah admin reset password dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa reset password akunmu sendiri dari sini.');
        }

        // Generate password random 8 karakter (huruf + angka)
        $newPassword = \Illuminate\Support\Str::random(8);

        $user->update([
            'password'             => $newPassword, // otomatis di-hash karena cast 'hashed'
            'must_change_password' => true,         // wajib ganti saat login
        ]);

        // Simpan password sementara di session untuk ditampilkan sekali
        return back()
            ->with('success', "Password {$user->name} berhasil direset.")
            ->with('new_password', $newPassword)
            ->with('new_password_user', $user->username);
    }
}