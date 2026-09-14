<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengajuancuti;
use App\Models\Pengajuanlupaabsen;
use App\Models\Pengajuansurat;
use App\Models\Lembur;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Filter
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);
        $ppnpnId = $request->get('ppnpn');

        // Periode
        $tanggalAwal = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();

        // =====================================================
        // DAFTAR PPNPN (aktif saja — untuk filter dropdown)
        // =====================================================
        $ppnpn = User::with('profil')
            ->where('role', 'ppnpn')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        // =====================================================
        // DATA CUTI
        // =====================================================
        $cutiQuery = Pengajuancuti::with('user.profil')
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn');
            })
            ->where(function ($q) use ($tanggalAwal, $tanggalAkhir) {
                $q->whereBetween('tanggal_mulai', [
                        $tanggalAwal->format('Y-m-d'),
                        $tanggalAkhir->format('Y-m-d'),
                    ])
                    ->orWhereBetween('tanggal_selesai', [
                        $tanggalAwal->format('Y-m-d'),
                        $tanggalAkhir->format('Y-m-d'),
                    ])
                    ->orWhere(function ($q2) use ($tanggalAwal, $tanggalAkhir) {
                        $q2->where('tanggal_mulai', '<=', $tanggalAwal->format('Y-m-d'))
                           ->where('tanggal_selesai', '>=', $tanggalAkhir->format('Y-m-d'));
                    });
            });

        if ($ppnpnId) {
            $cutiQuery->where('user_id', $ppnpnId);
        }

        $cuti = $cutiQuery->latest('tanggal_mulai')->get();

        // =====================================================
        // DATA LUPA ABSEN
        // =====================================================
        $lupaQuery = Pengajuanlupaabsen::with('user.profil')
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn');
            })
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ]);

        if ($ppnpnId) {
            $lupaQuery->where('user_id', $ppnpnId);
        }

        $lupaAbsen = $lupaQuery->latest('tanggal')->get();

        // =====================================================
        // DATA LEMBUR
        // =====================================================
        $lemburQuery = Lembur::with('user.profil')
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn');
            })
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ]);

        if ($ppnpnId) {
            $lemburQuery->where('user_id', $ppnpnId);
        }

        $lembur = $lemburQuery->latest('tanggal')->get();

        // =====================================================
        // DATA SURAT (SAKIT & LAINNYA)
        // =====================================================
        $suratQuery = Pengajuansurat::with('user.profil')
            ->whereHas('user', function ($q) {
                $q->where('role', 'ppnpn');
            })
            ->whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ]);

        if ($ppnpnId) {
            $suratQuery->where('user_id', $ppnpnId);
        }

        $surat = $suratQuery->latest('tanggal')->get();

        // =====================================================
        // RINGKASAN
        // =====================================================
        $jumlahCuti = $cuti->where('jenis_cuti', 'tahunan')->count();
        $jumlahCutiAlasanPenting = $cuti->where('jenis_cuti', 'alasan_penting')->count();
        $jumlahLupaAbsen = $lupaAbsen->count();
        $jumlahLembur = $lembur->count();
        $jumlahSurat = $surat->count();

        // =====================================================
        // AKTIVITAS SEMUA (gabungan)
        // =====================================================
        $semuaAktivitas = collect();

        // CUTI
        foreach ($cuti as $item) {
            $semuaAktivitas->push([
                'tanggal'    => Carbon::parse($item->tanggal_mulai),
                'ppnpn'      => $item->user?->name ?? '-',
                'aktivitas'  => match ($item->jenis_cuti) {
                    'tambahan'       => 'Cuti Tambahan',
                    'alasan_penting' => 'Cuti Alasan Penting',
                    default          => 'Cuti Tahunan',
                },
                'tipe'       => 'cuti',
                'keterangan' => $item->keterangan ?? '-',
                'bukti'      => $item->surat ?? null,
                'nama_bukti' => $item->nama_surat ?? ($item->surat ? basename($item->surat) : null),
                'tipe_bukti' => $item->surat ? 'surat' : null,
            ]);
        }

        // LUPA ABSEN
        foreach ($lupaAbsen as $item) {
            $semuaAktivitas->push([
                'tanggal'    => Carbon::parse($item->tanggal),
                'ppnpn'      => $item->user?->name ?? '-',
                'aktivitas'  => 'Lupa Absen',
                'tipe'       => 'lupa-absen',
                'keterangan' => $item->alasan ?? '-',
                'bukti'      => $item->bukti ?? null,
                'nama_bukti' => $item->bukti ? basename($item->bukti) : null,
                'tipe_bukti' => $item->bukti ? 'bukti' : null,
            ]);
        }

        // LEMBUR
        foreach ($lembur as $item) {
            $semuaAktivitas->push([
                'tanggal'    => Carbon::parse($item->tanggal),
                'ppnpn'      => $item->user?->name ?? '-',
                'aktivitas'  => 'Lembur',
                'tipe'       => 'lembur',
                'keterangan' => $item->kegiatan ?? '-',
                'bukti'      => $item->foto ?? null,
                'nama_bukti' => $item->foto ? basename($item->foto) : null,
                'tipe_bukti' => $item->foto ? 'foto' : null,
            ]);
        }

        // SURAT (SAKIT & LAINNYA)
        foreach ($surat as $item) {
            $labelSurat = match ($item->jenis_surat) {
                'surat_keterangan' => 'Surat Sakit',
                'surat_tugas'      => 'Surat Tugas',
                default            => 'Surat Lainnya',
            };

            $semuaAktivitas->push([
                'tanggal'    => Carbon::parse($item->tanggal),
                'ppnpn'      => $item->user?->name ?? '-',
                'aktivitas'  => $labelSurat,
                'tipe'       => 'surat',
                'keterangan' => $item->keperluan ?? '-',
                'bukti'      => $item->dokumen ?? null,
                'nama_bukti' => $item->nama_dokumen ?? ($item->dokumen ? basename($item->dokumen) : null),
                'tipe_bukti' => $item->dokumen ? 'dokumen' : null,
            ]);
        }

        // Sort terbaru
        $semuaAktivitas = $semuaAktivitas
            ->sortByDesc(fn($item) => $item['tanggal']->timestamp)
            ->values();

        return view('laporan.index', compact(
            'bulan',
            'tahun',
            'ppnpnId',
            'tanggalAwal',
            'tanggalAkhir',
            'ppnpn',
            'cuti',
            'lupaAbsen',
            'lembur',
            'surat',
            'jumlahCuti',
            'jumlahCutiAlasanPenting',
            'jumlahLupaAbsen',
            'jumlahLembur',
            'jumlahSurat',
            'semuaAktivitas'
        ));
    }
}