<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Absensi extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'shift',
        'jam_masuk',
        'jam_pulang',
        'keterangan',
        'status_approval',
        'latitude',
        'longitude',
        'jarak',
        // BARU:
        'latitude_pulang',
        'longitude_pulang',
        'jarak_pulang',
        'foto_masuk',
        'foto_pulang',
    ];

    protected $casts = [
        'tanggal'         => 'date',
        'latitude'        => 'decimal:7',
        'longitude'       => 'decimal:7',
        'jarak'           => 'decimal:2',
        'latitude_pulang' => 'decimal:7',
        'longitude_pulang'=> 'decimal:7',
        'jarak_pulang'    => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuanLupaAbsen(): HasMany
    {
        return $this->hasMany(Pengajuanlupaabsen::class, 'absensi_id');
    }
}