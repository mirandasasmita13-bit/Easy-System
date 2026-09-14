<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Lembur extends Model
{
    protected $table = 'lembur';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'total_jam',
        'kegiatan',
        'keterangan',
        'foto',
        'status_approval',
        'approved_by',
        'approved_at',
        'catatan_admin',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'total_jam'   => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Hitung total jam dari jam_mulai & jam_selesai
    public static function hitungTotalJam($jamMulai, $jamSelesai): float
    {
        $mulai   = Carbon::parse($jamMulai);
        $selesai = Carbon::parse($jamSelesai);

        // Lembur lintas tengah malam
        if ($selesai->lessThan($mulai)) {
            $selesai->addDay();
        }

        return round($mulai->diffInMinutes($selesai) / 60, 2);
    }

    // Scope: hanya yang sudah approved
    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved');
    }
}