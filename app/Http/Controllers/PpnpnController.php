<?php

namespace App\Http\Controllers;

use App\Models\User;

class PpnpnController extends Controller
{
    public function index()
    {
        $ppnpn = User::with('profil')
            ->where('role', 'ppnpn')
            ->orderBy('name')
            ->get();

        return view('ppnpn.index', compact('ppnpn'));
    }
}