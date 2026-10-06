<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Pakai username, bukan email
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()
            ->withErrors([
                'username' => 'ID atau password salah.',
            ])
            ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }


    // =========================================================
    // REGISTER
    // =========================================================

    public function register()
    {
        // Cek dulu: pendaftaran dibuka atau tidak?
        if (!User::pendaftaranDibuka()) {
            return redirect()->route('login')
                ->with('error', 'Pendaftaran sedang ditutup. Silakan hubungi admin.');
        }

        return view('auth.register');
    }

    public function storeRegister(Request $request)
    {
        // Cek dulu: pendaftaran dibuka atau tidak?
        if (!User::pendaftaranDibuka()) {
            return redirect()->route('login')
                ->with('error', 'Pendaftaran sedang ditutup. Silakan hubungi admin.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            // Username: unique & format
            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9._-]+$/',
            ],

            'password' => [
                'required',
                'min:8',
                'confirmed',
            ],

            // role_sub: format "ppnpn", "ppnpn:satpam", "ppnpn:pramubakti", atau "magang"
            'role_sub' => [
                'required',
                'in:ppnpn,ppnpn:satpam,ppnpn:pramubakti,magang',
            ],
        ]);

        // Pecah role_sub jadi role + sub_role
        $roleSub = explode(':', $data['role_sub']);
        $role    = $roleSub[0];              // ppnpn / magang
        $subRole = $roleSub[1] ?? null;      // satpam / pramubakti / null

        User::create([
            'name'                   => $data['name'],
            'username'               => $data['username'],
            'password'               => $data['password'],
            'role'                   => $role,
            'sub_role'               => $subRole,
            'status'                 => 'aktif',
            'jatah_cuti_tahunan'     => 12,
            'cuti_tahunan_sebelumnya'=> 0,
            'tahun_cuti'             => now()->year,
        ]);

        return redirect('/login')
            ->with('success', 'Akun berhasil dibuat. Silakan login.');
    }
}