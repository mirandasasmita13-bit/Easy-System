<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengajuancuti extends Model
{
    protected $table = 'pengajuan_cuti';

    protected $fillable = [
        'user_id',
        'jenis_cuti',
        'tanggal_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'surat',
        'nama_surat',
        'keterangan',
        // TIDAK ADA approval fields
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_mulai'     => 'date',
        'tanggal_selesai'   => 'date',
        'jumlah_hari'       => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeTahunan($query)
    {
        return $query->where('jenis_cuti', 'tahunan');
    }

    public function scopeAlasanPenting($query)
    {
        return $query->where('jenis_cuti', 'alasan_penting');
    }
}