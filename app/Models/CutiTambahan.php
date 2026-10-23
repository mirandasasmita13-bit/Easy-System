<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CutiTambahan extends Model
{
    protected $table = 'cuti_tambahan';

    protected $fillable = [
        'user_id',
        'nomor_sict',
        'tanggal_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'status',
        'dibatalkan_at',
        'dibatalkan_oleh',
        'alasan_batal',
        'diedit_at',
        'diedit_oleh',
        'surat',
        'nama_surat',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_mulai'     => 'date',
        'tanggal_selesai'   => 'date',
        'dibatalkan_at'     => 'datetime',
        'diedit_at'         => 'datetime',
    ];

    // =========================================================
    // RELASI
    // =========================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dibatalkanOleh()
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh');
    }

    public function dieditOleh()
    {
        return $this->belongsTo(User::class, 'diedit_oleh');
    }

    // =========================================================
    // SCOPE
    // =========================================================

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'dibatalkan');
    }
}