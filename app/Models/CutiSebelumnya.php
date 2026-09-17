<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiSebelumnya extends Model
{
    protected $table = 'cuti_sebelumnya';

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'jenis_cuti',
        'alasan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'jumlah_hari'     => 'integer',
    ];

    // RELASI
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    // SCOPE
    public function scopeUntukTahun($query, int $tahun)
    {
        return $query->whereYear('tanggal_mulai', $tahun);
    }
}