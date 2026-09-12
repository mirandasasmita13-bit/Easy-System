<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengajuanlupaabsen extends Model
{
    protected $table = 'pengajuan_lupa_absen';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jenis_absen',
        'jam',
        'bukti',
        'alasan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}