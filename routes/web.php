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
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ManajemenCutiController;


// ROOT — Redirect ke dashboard atau login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
})->name('home');


// ============================================
// ROUTE PUBLIK (Belum login)
// ============================================
Route::get('/login',   [LoginController::class, 'index'])->name('login');
Route::post('/login',  [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register',  [LoginController::class, 'register'])->name('register');
Route::post('/register', [LoginController::class, 'storeRegister'])->name('register.store');


// ============================================
// ROUTE TERLINDUNG (Wajib login)
// ============================================
Route::middleware('auth')->group(function () {

    // DASHBOARD — otomatis diarahkan sesuai role (admin/ppnpn/pegawai)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ABSENSI (PPNPN)
    Route::get('/absensi',         [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi/masuk',  [AbsensiController::class, 'masuk'])->name('absensi.masuk');
    Route::post('/absensi/pulang', [AbsensiController::class, 'pulang'])->name('absensi.pulang');

    // CUTI (PPNPN)
    Route::get('/pengajuan_cuti',  [PengajuancutiController::class, 'index'])->name('pengajuan_cuti.index');
    Route::post('/pengajuan_cuti', [PengajuancutiController::class, 'store'])->name('pengajuan_cuti.store');

    // LUPA ABSEN
    Route::get('/lupa-absen',  [PengajuanlupaabsenController::class, 'index'])->name('lupa-absen.index');
    Route::post('/lupa-absen', [PengajuanlupaabsenController::class, 'store'])->name('lupa-absen.store');
    Route::post('/lupa-absen/{lupaAbsen}/approve', [PengajuanlupaabsenController::class, 'approve'])->name('lupa-absen.approve');
    Route::post('/lupa-absen/{lupaAbsen}/reject',  [PengajuanlupaabsenController::class, 'reject'])->name('lupa-absen.reject');

    // LEMBUR
    Route::get('/lembur',  [LemburController::class, 'index'])->name('lembur.index');
    Route::post('/lembur', [LemburController::class, 'store'])->name('lembur.store');
    Route::post('/lembur/{lembur}/approve', [LemburController::class, 'approve'])->name('lembur.approve');
    Route::post('/lembur/{lembur}/reject',  [LemburController::class, 'reject'])->name('lembur.reject');

    // SURAT LAINNYA (PPNPN)
    Route::get('/pengajuan_surat',  [PengajuansuratController::class, 'index'])->name('pengajuan_surat.index');
    Route::post('/pengajuan_surat', [PengajuansuratController::class, 'store'])->name('pengajuan_surat.store');
    Route::put('/pengajuan_surat/{pengajuanSurat}', [PengajuansuratController::class, 'update'])->name('pengajuan_surat.update');

    // CUTI TAMBAHAN (Pegawai)
    Route::get('/cuti-tambahan',  [CutiTambahanController::class, 'index'])->name('cuti_tambahan.index');
    Route::post('/cuti-tambahan', [CutiTambahanController::class, 'store'])->name('cuti_tambahan.store');

    // PROFIL
    Route::get('/profil',      [ProfilController::class, 'index'])->name('profil.index');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil',      [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');

    // REKAP ABSENSI SAYA (PPNPN)
    Route::get('/rekap-saya',              [RekapabsensiController::class, 'rekapSaya'])->name('rekap-saya');
    Route::get('/rekap-saya/export/excel', [RekapabsensiController::class, 'exportExcelSaya'])->name('rekap-saya.export.excel');
    Route::get('/rekap-saya/export/pdf',   [RekapabsensiController::class, 'exportPdfSaya'])->name('rekap-saya.export.pdf');

    // Preview File
    Route::get('/file/{type}/{id}', [FileController::class, 'preview'])->name('file.preview');


    // ============================================
    // ADMIN ONLY
    // ============================================
    Route::middleware('role:admin')->group(function () {

        // ============================================
        // DATA PPNPN
        // ============================================
        Route::get('/ppnpn', [PpnpnController::class, 'index'])->name('ppnpn.index');
        Route::post('/ppnpn/{user}/nonaktifkan', [PpnpnController::class, 'nonaktifkan'])->name('ppnpn.nonaktifkan');
        Route::post('/ppnpn/{user}/aktifkan',    [PpnpnController::class, 'aktifkan'])->name('ppnpn.aktifkan');


        // ============================================
        // MANAJEMEN CUTI
        // ============================================

        // 1. Halaman daftar semua PPNPN + cuti
        Route::get('/manajemen-cuti', [ManajemenCutiController::class, 'indexAll'])
            ->name('manajemen-cuti.index');

        // 2. Reset cuti SEMUA PPNPN (HARUS di atas route {user} agar tidak bentrok)
        Route::post('/ppnpn/reset-semua-cuti', [PpnpnController::class, 'resetSemuaCuti'])
            ->name('ppnpn.reset-semua-cuti');

        // 3. Reset cuti 1 PPNPN
        Route::post('/ppnpn/{user}/reset-cuti', [PpnpnController::class, 'resetCuti'])
            ->name('ppnpn.reset-cuti');

        // 4. Halaman detail cuti 1 PPNPN
        Route::get('/ppnpn/{user}/manajemen-cuti', [ManajemenCutiController::class, 'index'])
            ->name('ppnpn.manajemen-cuti');

        // 5. Simpan cuti baru
        Route::post('/ppnpn/{user}/manajemen-cuti', [ManajemenCutiController::class, 'store'])
            ->name('ppnpn.manajemen-cuti.store');

        // 6. Update cuti
        Route::put('/manajemen-cuti/{cuti}', [ManajemenCutiController::class, 'update'])
            ->name('manajemen-cuti.update');

        // 7. Hapus cuti
        Route::delete('/manajemen-cuti/{cuti}', [ManajemenCutiController::class, 'destroy'])
            ->name('manajemen-cuti.destroy');


        // ============================================
        // LAIN-LAIN
        // ============================================

        // Pengajuan (Approval Center)
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');

        // Rekap Absensi
        Route::get('/rekap',              [RekapabsensiController::class, 'index'])->name('rekapabsensi.index');
        Route::get('/rekap/export/excel', [RekapabsensiController::class, 'exportExcel'])->name('rekapabsensi.export.excel');
        Route::get('/rekap/export/pdf',   [RekapabsensiController::class, 'exportPdf'])->name('rekapabsensi.export.pdf');

        // Rekap Lembur
        Route::get('/rekap-lembur',              [RekaplemburController::class, 'index'])->name('rekaplembur.index');
        Route::get('/rekap-lembur/export/excel', [RekaplemburController::class, 'exportExcel'])->name('rekaplembur.export.excel');
        Route::get('/rekap-lembur/export/pdf',   [RekaplemburController::class, 'exportPdf'])->name('rekaplembur.export.pdf');

        // Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    });
});