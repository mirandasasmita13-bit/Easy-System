<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'username',
    'password',
    'role',
    'status',
    'tanggal_nonaktif',

    // Manajemen cuti
    'jatah_cuti_tahunan',
    'cuti_tahunan_sebelumnya',
    'tahun_cuti',
])]

#[Hidden([
    'password',
    'remember_token',
])]

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'password'                => 'hashed',
            'tanggal_nonaktif'        => 'date',
            'jatah_cuti_tahunan'      => 'integer',
            'cuti_tahunan_sebelumnya' => 'integer',
            'tahun_cuti'              => 'integer',
        ];
    }

    // --- RELASI ---

    public function profil(): HasOne
    {
        return $this->hasOne(Profil::class);
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function pengajuancuti(): HasMany
    {
        return $this->hasMany(Pengajuancuti::class);
    }

    public function pengajuanlupaabsen(): HasMany
    {
        return $this->hasMany(Pengajuanlupaabsen::class);
    }

    public function lembur(): HasMany
    {
        return $this->hasMany(Lembur::class);
    }

    public function pengajuansurat(): HasMany
    {
        return $this->hasMany(Pengajuansurat::class);
    }

    // --- SCOPE ---

    public function scopePpnpnAktif($query)
    {
        return $query->where('role', 'ppnpn')->where('status', 'aktif');
    }

    public function scopePpnpnSemua($query)
    {
        return $query->where('role', 'ppnpn');
    }

/**
 * Cuti tahunan yang diinput admin (sebelum sistem berjalan).
 */
    public function cutiManualDiTahun(): int
    {
        return $this->cutiSebelumnya()
            ->whereYear('tanggal_mulai', $this->tahun_cuti)
            ->where('jenis_cuti', 'tahunan')
            ->sum('jumlah_hari');
    }

/**
 * Cuti tahunan dari pengajuan PPNPN di sistem.
 */
    public function cutiTahunanDiSistem(): int
    {
        return $this->pengajuancuti()
            ->where('jenis_cuti', 'tahunan')
            ->whereYear('tanggal_mulai', $this->tahun_cuti)
            ->sum('jumlah_hari');
    }

/**
 * Total cuti tahunan terpakai = manual + sistem.
 */
    public function totalCutiTerpakai(): int
    {
        return $this->cutiManualDiTahun() + $this->cutiTahunanDiSistem();
    }

/**
 * Sisa cuti tahunan.
 */
    public function sisaCutiTahunan(): int
    {
        return max(0, $this->jatah_cuti_tahunan - $this->totalCutiTerpakai());
    }

    public function cutiSebelumnya(): HasMany
    {
        return $this->hasMany(CutiSebelumnya::class, 'user_id');
    }
}