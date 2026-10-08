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
        'keterangan',
        'surat',
        'nama_surat',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_mulai'     => 'date',
        'tanggal_selesai'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}