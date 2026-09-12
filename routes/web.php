<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PengajuancutiController;
use App\Http\Controllers\PengajuanlupaabsenController;
use App\Http\Controllers\PengajuansuratController;
use App\Http\Controllers\CutiTambahanController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\RekapabsensiController;
use App\Http\Controllers\RekaplemburController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PpnpnController;
use App\Http\Controllers\ProfilController;


// LOGIN
        Route::get('/login', [LoginController::class, 'index']
            )->name('login');

        Route::post('/login', [LoginController::class, 'login']);

        Route::post('/logout', [LoginController::class, 'logout']
            )->name('logout');

        Route::get('/register', [LoginController::class, 'register']
            )->name('register');

        Route::post('/register', [LoginController::class, 'storeRegister']
            )->name('register.store');

// HALAMAN YANG MEMBUTUHKAN LOGIN
        Route::middleware('auth')->group(function () {

// Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

// ABSENSI
        Route::get('/absensi', [AbsensiController::class, 'index']);
        Route::post('/absensi/masuk', [AbsensiController::class, 'masuk']);
        Route::post('/absensi/pulang', [AbsensiController::class, 'pulang']);

//CUTI
        Route::get('/pengajuan_cuti', [PengajuancutiController::class, 'index']
            )->name('pengajuan_cuti.index');

        Route::post('/pengajuan_cuti', [PengajuancutiController::class, 'store']
            )->name('pengajuan_cuti.store');

//CUTI TAMBAHAN PEGAWAI
        Route::get('/cuti-tambahan', [CutiTambahanController::class, 'index']
            )->name('cuti_tambahan.index');

        Route::post('/cuti-tambahan', [CutiTambahanController::class, 'store']
            )->name('cuti_tambahan.store');

//LUPA ABSEN
        Route::get('/lupa-absen', [PengajuanlupaabsenController::class, 'index']
            )->name('lupa-absen.index');

        Route::post('/lupa-absen', [PengajuanlupaabsenController::class, 'store']
            )->name('lupa-absen.store');

//SURAT LAINNYA
        Route::get('/pengajuan_surat', [PengajuansuratController::class, 'index']
            )->name('pengajuan_surat.index');
            
        Route::post('/pengajuan_surat', [PengajuansuratController::class, 'store']
            )->name('pengajuan_surat.store');

//LEMBUR
        Route::get('/lembur', [LemburController::class, 'index']
            )->name('lembur.index');
        
        Route::post('/lembur', [LemburController::class, 'store']
            )->name('lembur.store');

// PROFIL
        Route::get('/profil', [ProfilController::class, 'index']
            )->name('profil.index');

        Route::get('/profil/edit', [ProfilController::class, 'edit']
            )->name('profil.edit');

        Route::put('/profil', [ProfilController::class, 'update']
            )->name('profil.update');

// LAPORAN
        Route::get('/laporan', [LaporanController::class, 'index'])
            ->middleware('role:admin')
            ->name('laporan');

// pengajuan 
        Route::get('/pengajuan', function () { return view('pengajuan.index');
            })->name('pengajuan.index');  

// PPNPN
        Route::get('/ppnpn', [PpnpnController::class, 'index']
            )->name('ppnpn.index');

// Rekap Saya(individu)
        Route::get('/rekap-saya', [RekapabsensiController::class, 'rekapSaya'])
            ->name('rekap-saya');

        Route::get('/rekap-saya/export/excel', [RekapabsensiController::class, 'exportExcelSaya']
            )->name('rekap-saya.export.excel');

        Route::get('/rekap-saya/export/pdf', [RekapabsensiController::class, 'exportPdfSaya']
            )->name('rekap-saya.export.pdf');

//REKAP ABSENSI ADMIN

        Route::get('/rekap', [RekapabsensiController::class, 'index']
            )->middleware('role:admin')
             ->name('rekapabsensi.index');

        Route::get('/rekap/export/excel', [RekapabsensiController::class, 'exportExcel']
            )->middleware('role:admin')
             ->name('rekapabsensi.export.excel');

        Route::get('/rekap/export/pdf', [RekapabsensiController::class, 'exportPdf']
            )->middleware('role:admin')
             ->name('rekapabsensi.export.pdf');

// Rekap Lembur
        Route::get('/rekap-lembur', [RekaplemburController::class, 'index']
            )->middleware('role:admin')
             ->name('rekaplembur.index');

        Route::get('/rekap-lembur/export/excel', [RekaplemburController::class, 'exportExcel']
            )->middleware('role:admin')
             ->name('rekaplembur.export.excel');

        Route::get('/rekap-lembur/export/pdf', [RekaplemburController::class, 'exportPdf']
            )->middleware('role:admin')
             ->name('rekaplembur.export.pdf');
        
        Route::get('/ppnpn', [PpnpnController::class, 'index']
            )->middleware('role:admin')
             ->name('ppnpn.index');

});