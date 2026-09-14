<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Lembur;
use App\Models\Pengajuancuti;
use App\Models\Pengajuanlupaabsen;
use App\Models\Pengajuansurat;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function preview(string $type, int $id)
    {
        $user = auth()->user();

        $map = [
            'cuti'        => [Pengajuancuti::class, 'surat'],
            'lupa-absen'  => [Pengajuanlupaabsen::class, 'bukti'],
            'lembur'      => [Lembur::class, 'foto'],
            'absensi'     => [Absensi::class, 'foto_masuk'],
            'surat'       => [Pengajuansurat::class, 'dokumen'],
        ];

        if (!isset($map[$type])) {
            abort(404, 'Jenis file tidak dikenali.');
        }

        [$modelClass, $fileColumn] = $map[$type];

        $record = $modelClass::findOrFail($id);

        // Authorization
        $isOwner = (int) $record->user_id === (int) $user->id;
        $isAdmin = $user->role === 'admin';

        if (!$isOwner && !$isAdmin) {
            abort(403, 'Anda tidak berhak mengakses file ini.');
        }

        $path = $record->{$fileColumn};
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('public')->response($path);
    }
}