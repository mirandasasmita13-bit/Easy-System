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
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'surat',
        'nama_surat',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jumlah_hari' => 'integer',
    ];

    /**
     * Cuti milik PPNPN
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}