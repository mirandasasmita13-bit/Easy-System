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
    'sub_role',
    'status',
    'tanggal_nonaktif',
    'pendaftaran_dibuka',

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
            'pendaftaran_dibuka'      => 'boolean',
        ];
    }

    // RELASI

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

    public function cutiSebelumnya(): HasMany
    {
        return $this->hasMany(CutiSebelumnya::class, 'user_id');
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

    // --- CUTI ---

    public function cutiManualDiTahun(): int
    {
        return $this->cutiSebelumnya()
            ->whereYear('tanggal_mulai', $this->tahun_cuti)
            ->where('jenis_cuti', 'tahunan')
            ->sum('jumlah_hari');
    }

    public function cutiTahunanDiSistem(): int
    {
        return $this->pengajuancuti()
            ->where('jenis_cuti', 'tahunan')
            ->whereYear('tanggal_mulai', $this->tahun_cuti)
            ->sum('jumlah_hari');
    }

    public function totalCutiTerpakai(): int
    {
        return $this->cutiManualDiTahun() + $this->cutiTahunanDiSistem();
    }

    public function sisaCutiTahunan(): int
    {
        return max(0, $this->jatah_cuti_tahunan - $this->totalCutiTerpakai());
    }


    // --- ROLE HELPER ---

    /**
     * Cek apakah user adalah satpam
     */
    public function isSatpam(): bool
    {
        return $this->sub_role === 'satpam';
    }

    /**
     * Cek apakah user adalah pramubakti
     */
    public function isPramubakti(): bool
    {
        return $this->sub_role === 'pramubakti';
    }

    /**
     * Label role lengkap (buat ditampilin di UI)
     */
    public function labelRole(): string
    {
        if ($this->sub_role) {
            return ucfirst($this->sub_role);
        }

        return match($this->role) {
            'admin'    => 'Administrator',
            'ppnpn'    => 'PPNPN',
            'pegawai'  => 'Pegawai',
            'magang'   => 'Magang / PKL',
            default    => ucfirst($this->role),
        };
    }


    // PENGATURAN PENDAFTARAN

    public static function pendaftaranDibuka(): bool
    {
        return static::where('role', 'admin')
            ->where('pendaftaran_dibuka', true)
            ->exists();
    }

    public static function togglePendaftaran(): bool
    {
        $current = static::pendaftaranDibuka();

        static::where('role', 'admin')
            ->update(['pendaftaran_dibuka' => !$current]);

        return !$current;
    }
}