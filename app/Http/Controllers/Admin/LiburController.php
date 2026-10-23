<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TanggalMerah;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LiburController extends Controller
{
    /**
     * Daftar tanggal merah
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $query = TanggalMerah::orderBy('tanggal', 'desc');

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        $data['liburs']      = $query->get();
        $data['filterTahun'] = $tahun;

        // Total
        $data['total'] = TanggalMerah::count();

        // Total tahun ini
        $data['totalTahun'] = TanggalMerah::whereYear('tanggal', $tahun)->count();

        return view('admin.control', [
            'tab'  => 'libur',
            'data' => $data,
        ]);
    }


    /**
     * Tambah tanggal merah
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal'    => ['required', 'date', 'unique:tanggal_merah,tanggal'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'tanggal.unique' => 'Tanggal ini sudah terdaftar sebagai hari libur.',
        ]);

        TanggalMerah::create($validated);

        return redirect()
            ->route('admin.control', ['tab' => 'libur'])
            ->with('success', 'Tanggal libur berhasil ditambahkan.');
    }


    /**
     * Update tanggal merah
     */
    public function update(Request $request, $id)
    {
        $libur = TanggalMerah::findOrFail($id);

        $validated = $request->validate([
            'tanggal'    => ['required', 'date', 'unique:tanggal_merah,tanggal,' . $id],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'tanggal.unique' => 'Tanggal ini sudah terdaftar sebagai hari libur.',
        ]);

        $libur->update($validated);

        return redirect()
            ->route('admin.control', ['tab' => 'libur'])
            ->with('success', 'Tanggal libur berhasil diperbarui.');
    }


    /**
     * Hapus tanggal merah
     */
    public function destroy($id)
    {
        $libur = TanggalMerah::findOrFail($id);
        $libur->delete();

        return back()->with('success', 'Tanggal libur berhasil dihapus.');
    }


    /**
     * Import kalender nasional 2026 (hardcoded)
     */
    public function importNasional(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        // Data hari libur nasional Indonesia 2026 (SKB 3 Menteri)
        // Silakan update kalau ada perubahan resmi
        $liburNasional = [
            // 2025 (kalau butuh)
            // '2025-01-01' => 'Tahun Baru 2025',
            // ...

            // 2026
            '2026-01-01' => 'Tahun Baru 2026',
            '2026-01-29' => 'Tahun Baru Imlek 2577',
            '2026-03-19' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)',
            '2026-03-20' => 'Wafat Isa Almasih',
            '2026-03-21' => 'Cuti Bersama Nyepi',
            '2026-04-03' => 'Wafat Isa Almasih (Kenaikan?)', // sesuaikan
            '2026-05-01' => 'Hari Buruh Internasional',
            '2026-05-14' => 'Kenaikan Isa Almasih',
            '2026-06-01' => 'Hari Lahir Pancasila',
            '2026-06-27' => 'Idul Adha 1447 H',
            '2026-07-17' => 'Tahun Baru Islam 1448 H',
            '2026-08-17' => 'HUT Kemerdekaan RI',
            '2026-08-26' => 'Maulid Nabi Muhammad SAW',
            '2026-12-25' => 'Hari Raya Natal',
            '2026-12-26' => 'Cuti Bersama Natal',
        ];

        $inserted = 0;
        foreach ($liburNasional as $tanggal => $keterangan) {
            if (Carbon::parse($tanggal)->year != $tahun) {
                continue;
            }
            // Skip kalau sudah ada
            if (TanggalMerah::where('tanggal', $tanggal)->exists()) {
                continue;
            }
            TanggalMerah::create([
                'tanggal'    => $tanggal,
                'keterangan' => $keterangan,
            ]);
            $inserted++;
        }

        return redirect()
            ->route('admin.control', ['tab' => 'libur', 'tahun' => $tahun])
            ->with('success', "Berhasil import {$inserted} tanggal libur nasional {$tahun}.");
    }
}