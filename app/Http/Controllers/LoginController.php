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
        // POIN 1: Pakai username, bukan email
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

    // REGISTER
    public function register()
    {
        return view('auth.register');
    }

    public function storeRegister(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            // POIN 1: username, unique
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
        ]);

        User::create([
            'name'                   => $data['name'],
            'username'               => $data['username'],
            'password'               => $data['password'],
            'role'                   => 'ppnpn',
            'status'                 => 'aktif',
            'jatah_cuti_tahunan'     => 12,
            'cuti_tahunan_sebelumnya'=> 0,
            'tahun_cuti'             => now()->year,
        ]);

        return redirect('/login')
            ->with('success', 'Akun berhasil dibuat. Silakan login.');
    }
}