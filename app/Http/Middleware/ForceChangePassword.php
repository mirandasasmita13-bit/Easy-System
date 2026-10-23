<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Jika user login & wajib ganti password
        if ($user && $user->must_change_password) {
            
            // Daftar rute yang DIIZINKAN diakses tanpa harus ganti password
            $allowedRoutes = [
                'profil*',       // Halaman profil (untuk ganti password)
                'logout',        // Logout
                'admin/control*' // Admin control (biar admin bisa reset password orang lain)
            ];

            // Jika rute saat ini BUKAN rute yang diizinkan, redirect ke profil
            if (!$request->is(...$allowedRoutes)) {
                return redirect()
                    ->route('profil.index')
                    ->with('warning', 'Password kamu baru saja direset oleh admin. Harap ganti password dulu sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}