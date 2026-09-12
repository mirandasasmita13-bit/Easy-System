<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $profil = $user->profil;

        return view('profil.index', compact('user', 'profil'));
    }

    public function edit()
    {
        $user = auth()->user();

        $profil = $user->profil;

        return view('profil.edit', compact('user', 'profil'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | VALIDASI BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'ppnpn') {

            $data = $request->validate([
                'nik' => ['nullable', 'string', 'max:20'],

                'foto' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ]);

            // PPNPN hanya menyimpan NIK dan foto
            $data = [
                'nik' => $request->nik,
            ];

        } elseif ($user->role === 'pegawai') {

            $data = $request->validate([
                'nip' => ['nullable', 'string', 'max:50'],
                'pangkat_gol' => ['nullable', 'string', 'max:100'],
                'jabatan' => ['nullable', 'string', 'max:255'],
                'unit_kerja' => ['nullable', 'string', 'max:255'],

                'foto' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ]);

            // Pegawai hanya menyimpan data kepegawaiannya
            $data = [
                'nip' => $request->nip,
                'pangkat_gol' => $request->pangkat_gol,
                'jabatan' => $request->jabatan,
                'unit_kerja' => $request->unit_kerja,
            ];

        } else {

            // Admin hanya boleh mengubah foto
            $request->validate([
                'foto' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ]);

            $data = [];
        }


        /*
        |--------------------------------------------------------------------------
        | BUAT PROFIL JIKA BELUM ADA
        |--------------------------------------------------------------------------
        */

        $profil = $user->profil;

        if (!$profil) {
            $profil = $user->profil()->create([]);
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN FOTO
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('foto')) {
            $data['foto'] = $request
                ->file('foto')
                ->store('foto-profil', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE PROFIL
        |--------------------------------------------------------------------------
        */

        $profil->update($data);


        return redirect()
            ->route('profil.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}