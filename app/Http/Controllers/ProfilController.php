<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    /**
     * Halaman profil — split card + tabs
     */
    public function index()
    {
        $user   = auth()->user();
        $profil = $user->profil;

        return view('profil.index', compact('user', 'profil'));
    }

    /**
     * Halaman edit — bisa dihapus kalau sudah digabung.
     * Tapi tetap dipertahankan untuk kompatibilitas.
     */
    public function edit()
    {
        $user   = auth()->user();
        $profil = $user->profil;

        return view('profil.edit', compact('user', 'profil'));
    }

    /**
     * Update profil — nama & username (user) + field profil (per role)
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // ---------------------------------------------
        // VALIDASI UMUM — semua role
        // ---------------------------------------------
        $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9._-]+$/',
                'unique:users,username,' . $user->id,
            ],

            'foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'username.unique' => 'Username sudah dipakai user lain.',
            'username.regex'  => 'Username hanya boleh huruf, angka, titik, underscore, dan dash.',
        ]);

        // ---------------------------------------------
        // UPDATE USER (nama & username)
        // ---------------------------------------------
        $user->update([
            'name'     => $request->name,
            'username' => $request->username,
        ]);

        // ---------------------------------------------
        // VALIDASI PER ROLE — field profil
        // ---------------------------------------------
        $data = [];

        if ($user->role === 'ppnpn') {
            $request->validate([
                'nik'        => ['nullable', 'string', 'max:20'],
                'jabatan'    => ['nullable', 'string', 'max:255'],
                'unit_kerja' => ['nullable', 'string', 'max:255'],
            ]);
            $data['nik']        = $request->nik;
            $data['jabatan']    = $request->jabatan;
            $data['unit_kerja'] = $request->unit_kerja;

        } elseif ($user->role === 'pegawai') {
            $request->validate([
                'nip'         => ['nullable', 'string', 'max:50'],
                'pangkat_gol' => ['nullable', 'string', 'max:100'],
                'jabatan'     => ['nullable', 'string', 'max:255'],
                'unit_kerja'  => ['nullable', 'string', 'max:255'],
            ]);
            $data['nip']         = $request->nip;
            $data['pangkat_gol'] = $request->pangkat_gol;
            $data['jabatan']     = $request->jabatan;
            $data['unit_kerja']  = $request->unit_kerja;

        } else {
            // Admin
            $request->validate([
                'jabatan'    => ['nullable', 'string', 'max:255'],
                'unit_kerja' => ['nullable', 'string', 'max:255'],
            ]);
            $data['jabatan']    = $request->jabatan;
            $data['unit_kerja'] = $request->unit_kerja;
        }

        // ---------------------------------------------
        // PROFIL — buat kalau belum ada
        // ---------------------------------------------
        $profil = $user->profil;
        if (!$profil) {
            $profil = $user->profil()->create([]);
        }

        // ---------------------------------------------
        // SIMPAN FOTO
        // ---------------------------------------------
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-profil', 'public');
        }

        $profil->update($data);

        return redirect()
            ->route('profil.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'min:8', 'confirmed', 'different:current_password'],
            'password_confirmation' => ['required', 'string'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
            'password.different'        => 'Password baru tidak boleh sama dengan password lama.',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama salah.'])
                ->withInput()
                ->with('tab', 'security');
        }

        $user->update([
            'password' => $request->password,
        ]);

        return back()->with('success', 'Password berhasil diubah.')->with('tab', 'security');
    }
}