<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\User;
use Illuminate\Http\Request;

class RekaplemburController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $userId = $request->input('user_id');

        // Daftar PPNPN
        $pegawai = User::with('profil')
            ->where('role', 'ppnpn')
            ->orderBy('name')
            ->get();

        // Data lembur periode ini
        $query = Lembur::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $dataLembur = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai')
            ->get();

        $jumlahLembur = $dataLembur->count();

        // Total jam disetujui vs semua
        $totalJamDisetujui = (float) $dataLembur
            ->where('status_approval', 'approved')
            ->sum('total_jam');

        $totalJamSemua = (float) $dataLembur->sum('total_jam');

        $jumlahPending = $dataLembur->where('status_approval', 'pending')->count();

        // Periode
        $tanggalAwal = now()
            ->setYear($tahun)
            ->setMonth($bulan)
            ->startOfMonth();

        return view('rekaplembur.index', compact(
            'dataLembur',
            'pegawai',
            'jumlahLembur',
            'totalJamDisetujui',
            'totalJamSemua',
            'jumlahPending',
            'bulan',
            'tahun',
            'userId',
            'tanggalAwal'
        ));
    }


    // Export Excel
    public function exportExcel(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $userId = $request->input('user_id');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RekaplemburExport($bulan, $tahun, $userId),
            'rekap-lembur-' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.xlsx'
        );
    }


    // Export PDF
    public function exportPdf(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $userId = $request->input('user_id');

        $query = Lembur::with('user.profil')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $dataLembur = $query
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        $tanggalAwal = now()
            ->setYear($tahun)
            ->setMonth($bulan)
            ->startOfMonth();

        $jumlahLembur = $dataLembur->count();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'rekaplembur.pdf',
            compact('dataLembur', 'jumlahLembur', 'tanggalAwal')
        )->setPaper('a4', 'landscape');

        return $pdf->download(
            'rekap-lembur-' . $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.pdf'
        );
    }
}