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
    'email',
    'password',
    'role',
    'cuti_tahunan_sebelumnya',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'cuti_tahunan_sebelumnya' => 'integer',
        ];
    }


    // RELASI PROFIL
    public function profil(): HasOne
    {
        return $this->hasOne(Profil::class);
    }


    // RELASI ABSENSI
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }


    // RELASI CUTI
    public function pengajuancuti(): HasMany
    {
        return $this->hasMany(Pengajuancuti::class);
    }


    // RELASI LUPA ABSEN
    public function pengajuanlupaabsen(): HasMany
    {
        return $this->hasMany(Pengajuanlupaabsen::class);
    }


    // RELASI LEMBUR
    public function lembur(): HasMany
    {
        return $this->hasMany(Lembur::class);
    }


    // RELASI SURAT
    public function pengajuansurat(): HasMany
    {
        return $this->hasMany(Pengajuansurat::class);
    }
}