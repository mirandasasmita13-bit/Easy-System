<?php

namespace App\Http\Controllers;

use App\Models\Pengajuancuti;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PengajuancutiController extends Controller
{
    /**
     * Halaman Cuti.
     */
    public function index()
    {
        $user = auth()->user();

        $riwayatCuti = Pengajuancuti::where('user_id', $user->id)
            ->latest()
            ->get();

        // Pakai helper di User model
        $sisaCuti = $user->sisaCutiTahunan();
        $cutiTahunanTerpakai = $user->totalCutiTerpakai();

        return view('pengajuan_cuti.index', compact(
            'riwayatCuti',
            'sisaCuti',
            'cutiTahunanTerpakai'
        ));
    }

    /**
     * Simpan data cuti.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'jenis_cuti' => ['required', 'in:tahunan,alasan_penting'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'surat' => [
                'required', 'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
                'max:5120',
            ],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $tanggalMulai   = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->startOfDay();
        $hariIni        = now()->startOfDay();

        // === CUTI TAHUNAN: tidak boleh mundur ===
        if ($request->jenis_cuti === 'tahunan' && $tanggalMulai->lt($hariIni)) {
            return back()->withInput()->with(
                'error',
                'Cuti tahunan harus dicatat sebelum atau pada hari cuti.'
            );
        }

        // === CUTI ALASAN PENTING: boleh mundur, maks 7 hari ===
        if ($request->jenis_cuti === 'alasan_penting' && $tanggalMulai->lt($hariIni)) {
            $selisihHari = $hariIni->diffInDays($tanggalMulai);
            if ($selisihHari > 7) {
                return back()->withInput()->with(
                    'error',
                    'Cuti alasan penting hanya boleh diajukan maksimal 7 hari setelah cuti.'
                );
            }
        }

        $jumlahHari = $this->hitungHariKerja($tanggalMulai, $tanggalSelesai);

        if ($jumlahHari <= 0) {
            return back()->withInput()->with(
                'error',
                'Tanggal cuti yang dipilih tidak memiliki hari kerja.'
            );
        }

        if ($request->jenis_cuti === 'alasan_penting' && $jumlahHari > 3) {
            return back()->withInput()->with(
                'error',
                'Cuti alasan penting maksimal 3 hari.'
            );
        }

        // === CEK SISA CUTI TAHUNAN ===
        if ($request->jenis_cuti === 'tahunan') {
            $sisaCuti = $user->sisaCutiTahunan();

            if ($jumlahHari > $sisaCuti) {
                return back()->withInput()->with(
                    'error',
                    'Cuti melebihi sisa cuti tahunan Anda. Sisa: ' . $sisaCuti . ' hari.'
                );
            }
        }

        // === SIMPAN SURAT ===
        $fileSurat  = $request->file('surat');
        $surat      = $fileSurat->store('surat-cuti', 'public');
        $namaSurat  = $fileSurat->getClientOriginalName();

        // === SIMPAN ===
        Pengajuancuti::create([
            'user_id'           => $user->id,
            'jenis_cuti'        => $request->jenis_cuti,
            'tanggal_pengajuan' => now()->toDateString(),
            'tanggal_mulai'     => $tanggalMulai,
            'tanggal_selesai'   => $tanggalSelesai,
            'jumlah_hari'       => $jumlahHari,
            'surat'             => $surat,
            'nama_surat'        => $namaSurat,
            'keterangan'        => $request->keterangan,
        ]);

        return back()->with('success', 'Data cuti berhasil disimpan.');
    }

    /**
     * Update data cuti (perbaikan).
     */
    public function update(Request $request, Pengajuancuti $pengajuancuti)
    {
        // Pastikan hanya pemilik yang bisa edit
        if ($pengajuancuti->user_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mengedit cuti ini.');
        }

        $user = auth()->user();

        $request->validate([
            'jenis_cuti' => ['required', 'in:tahunan,alasan_penting'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'surat' => [
                'nullable', 'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
                'max:5120',
            ],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ]);

        $tanggalMulai   = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai)->startOfDay();
        $hariIni        = now()->startOfDay();

        // === CUTI TAHUNAN: tidak boleh mundur ===
        if ($request->jenis_cuti === 'tahunan' && $tanggalMulai->lt($hariIni)) {
            return back()->withInput()->with(
                'error',
                'Cuti tahunan harus dicatat sebelum atau pada hari cuti.'
            );
        }

        // === CUTI ALASAN PENTING: boleh mundur, maks 7 hari ===
        if ($request->jenis_cuti === 'alasan_penting' && $tanggalMulai->lt($hariIni)) {
            $selisihHari = $hariIni->diffInDays($tanggalMulai);
            if ($selisihHari > 7) {
                return back()->withInput()->with(
                    'error',
                    'Cuti alasan penting hanya boleh diajukan maksimal 7 hari setelah cuti.'
                );
            }
        }

        $jumlahHari = $this->hitungHariKerja($tanggalMulai, $tanggalSelesai);

        if ($jumlahHari <= 0) {
            return back()->withInput()->with(
                'error',
                'Tanggal cuti yang dipilih tidak memiliki hari kerja.'
            );
        }

        if ($request->jenis_cuti === 'alasan_penting' && $jumlahHari > 3) {
            return back()->withInput()->with(
                'error',
                'Cuti alasan penting maksimal 3 hari.'
            );
        }

        // === CEK SISA CUTI TAHUNAN (exclude record yang lagi diedit) ===
        if ($request->jenis_cuti === 'tahunan') {

            // Hitung total cuti terpakai TANPA record ini
            $cutiManualDiTahun = $user->cutiSebelumnya()
                ->whereYear('tanggal_mulai', $user->tahun_cuti)
                ->where('jenis_cuti', 'tahunan')
                ->sum('jumlah_hari');

            $cutiSistemLain = $user->pengajuancuti()
                ->where('jenis_cuti', 'tahunan')
                ->whereYear('tanggal_mulai', $user->tahun_cuti)
                ->where('id', '!=', $pengajuancuti->id)   // exclude record ini
                ->sum('jumlah_hari');

            $totalTerpakai = $cutiManualDiTahun + $cutiSistemLain;
            $sisaCuti = max(0, $user->jatah_cuti_tahunan - $totalTerpakai);

            if ($jumlahHari > $sisaCuti) {
                return back()->withInput()->with(
                    'error',
                    'Cuti melebihi sisa cuti tahunan Anda. Sisa: ' . $sisaCuti . ' hari.'
                );
            }
        }

        // === DATA YANG DIUPDATE ===
        $data = [
            'jenis_cuti'      => $request->jenis_cuti,
            'tanggal_mulai'   => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'jumlah_hari'     => $jumlahHari,
            'keterangan'      => $request->keterangan,
        ];

        // === KALAU ADA SURAT BARU: HAPUS LAMA, SIMPAN BARU ===
        if ($request->hasFile('surat')) {

            // Hapus file lama
            if ($pengajuancuti->surat) {
                Storage::disk('public')->delete($pengajuancuti->surat);
            }

            $fileSurat = $request->file('surat');

            $data['surat']      = $fileSurat->store('surat-cuti', 'public');
            $data['nama_surat'] = $fileSurat->getClientOriginalName();
        }

        $pengajuancuti->update($data);

        return back()->with('success', 'Data cuti berhasil diperbarui.');
    }

    private function hitungHariKerja(Carbon $mulai, Carbon $selesai): int
    {
        $jumlah = 0;
        $tgl = $mulai->copy();

        while ($tgl->lte($selesai)) {
            if ($tgl->isWeekday()) $jumlah++;
            $tgl->addDay();
        }

        return $jumlah;
    }
}