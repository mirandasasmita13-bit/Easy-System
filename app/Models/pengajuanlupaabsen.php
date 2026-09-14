<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengajuanlupaabsen extends Model
{
    protected $table = 'pengajuan_lupa_absen';

    protected $fillable = [
        'user_id',
        'absensi_id',       
        'tanggal',
        'jenis_absen',
        'jam',
        'bukti',
        'alasan',
        'status',           
        'approved_by',
        'approved_at',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function absensi(): BelongsTo
    {
        return $this->belongsTo(Absensi::class, 'absensi_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}