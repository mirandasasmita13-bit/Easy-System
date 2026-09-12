<?php

namespace App\Http\Controllers;

use App\Models\Pengajuancuti;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PengajuancutiController extends Controller
{
    /**
     * Kuota dasar cuti tahunan.
     */
    private int $kuotaCutiTahunan = 12;


    /**
     * Halaman Cuti.
     */
    public function index()
    {
        $user = auth()->user();


        /**
         * Riwayat cuti milik PPNPN yang sedang login.
         */
        $riwayatCuti = Pengajuancuti::where(
            'user_id',
            $user->id
        )
            ->latest()
            ->get();


        /**
         * Cuti tahunan yang sudah dipakai
         * sebelum menggunakan sistem.
         *
         * Nilai ini nantinya dapat diedit oleh admin.
         */
        $cutiTahunanSebelumnya = (int) (
            $user->cuti_tahunan_sebelumnya ?? 0
        );


        /**
         * Cuti tahunan yang sudah tercatat
         * melalui sistem.
         */
        $cutiTahunanDariSistem = Pengajuancuti::where(
            'user_id',
            $user->id
        )
            ->where('jenis_cuti', 'tahunan')
            ->whereYear('tanggal_mulai', now()->year)
            ->sum('jumlah_hari');


        /**
         * Total cuti tahunan yang sudah digunakan.
         *
         * Cuti sebelumnya
         * +
         * Cuti melalui sistem.
         */
        $cutiTahunanTerpakai =
            $cutiTahunanSebelumnya +
            $cutiTahunanDariSistem;


        /**
         * Hitung sisa cuti.
         */
        $sisaCuti = max(
            0,
            $this->kuotaCutiTahunan - $cutiTahunanTerpakai
        );


        return view(
            'pengajuan_cuti.index',
            compact(
                'riwayatCuti',
                'sisaCuti',
                'cutiTahunanTerpakai'
            )
        );
    }


    /**
     * Simpan data cuti.
     */
    public function store(Request $request)
    {
        $user = auth()->user();


        /**
         * Validasi input.
         */
        $request->validate([

            'jenis_cuti' => [
                'required',
                'in:tahunan,alasan_penting',
            ],

            'tanggal_mulai' => [
                'required',
                'date',
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'surat' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
                'max:5120',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        /**
         * =====================================================
         * CUTI TAHUNAN
         * =====================================================
         *
         * Cuti tahunan harus dicatat sebelum cuti.
         *
         * Hari ini masih boleh.
         */
        if (
            $request->jenis_cuti === 'tahunan' &&
            Carbon::parse($request->tanggal_mulai)
                ->startOfDay()
                ->lt(now()->startOfDay())
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Cuti tahunan harus dicatat sebelum atau pada hari cuti.'
                );
        }


        /**
         * Ubah tanggal menjadi Carbon.
         */
        $tanggalMulai = Carbon::parse(
            $request->tanggal_mulai
        );

        $tanggalSelesai = Carbon::parse(
            $request->tanggal_selesai
        );


        /**
         * Hitung jumlah hari kerja.
         *
         * Sementara:
         * Senin - Jumat = dihitung
         * Sabtu - Minggu = tidak dihitung
         */
        $jumlahHari = $this->hitungHariKerja(
            $tanggalMulai,
            $tanggalSelesai
        );


        /**
         * Pastikan tanggal mempunyai
         * minimal satu hari kerja.
         */
        if ($jumlahHari <= 0) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Tanggal cuti yang dipilih tidak memiliki hari kerja.'
                );
        }


        /**
         * =====================================================
         * CUTI ALASAN PENTING
         * =====================================================
         *
         * Maksimal 3 hari.
         *
         * Tidak mengurangi cuti tahunan.
         */
        if (
            $request->jenis_cuti === 'alasan_penting' &&
            $jumlahHari > 3
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Cuti alasan penting maksimal 3 hari.'
                );
        }


        /**
         * =====================================================
         * CEK CUTI TAHUNAN
         * =====================================================
         */
        if ($request->jenis_cuti === 'tahunan') {

            /**
             * Pemakaian cuti sebelum sistem.
             */
            $cutiTahunanSebelumnya = (int) (
                $user->cuti_tahunan_sebelumnya ?? 0
            );


            /**
             * Pemakaian cuti dari sistem.
             */
            $cutiTahunanDariSistem = Pengajuancuti::where(
                'user_id',
                $user->id
            )
                ->where('jenis_cuti', 'tahunan')
                ->whereYear('tanggal_mulai', now()->year)
                ->sum('jumlah_hari');


            /**
             * Total pemakaian.
             */
            $cutiTahunanTerpakai =
                $cutiTahunanSebelumnya +
                $cutiTahunanDariSistem;


            /**
             * Sisa cuti.
             */
            $sisaCuti = max(
                0,
                $this->kuotaCutiTahunan - $cutiTahunanTerpakai
            );


            /**
             * Tidak boleh melebihi sisa.
             */
            if ($jumlahHari > $sisaCuti) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Cuti melebihi sisa cuti tahunan Anda. '
                        . 'Sisa cuti saat ini: '
                        . $sisaCuti
                        . ' hari.'
                    );
            }
        }


        /**
         * =====================================================
         * SIMPAN SURAT
         * =====================================================
         */
        $fileSurat = $request->file('surat');


        $surat = $fileSurat->store(
            'surat-cuti',
            'public'
        );


        /**
         * Nama file asli.
         */
        $namaSurat = $fileSurat->getClientOriginalName();


        /**
         * =====================================================
         * SIMPAN DATA CUTI
         * =====================================================
         */
        Pengajuancuti::create([

            'user_id' => $user->id,

            'jenis_cuti' => $request->jenis_cuti,

            'tanggal_mulai' => $tanggalMulai,

            'tanggal_selesai' => $tanggalSelesai,

            'jumlah_hari' => $jumlahHari,

            'surat' => $surat,

            'nama_surat' => $namaSurat,

            'keterangan' => $request->keterangan,
        ]);


        /**
         * Berhasil.
         */
        return back()->with(
            'success',
            'Data cuti berhasil disimpan.'
        );
    }


    /**
     * Hitung jumlah hari kerja.
     *
     * Sementara:
     * Senin - Jumat = dihitung.
     * Sabtu - Minggu = tidak dihitung.
     */
    private function hitungHariKerja(
        Carbon $tanggalMulai,
        Carbon $tanggalSelesai
    ): int {

        $jumlahHari = 0;

        $tanggal = $tanggalMulai->copy();


        while ($tanggal->lte($tanggalSelesai)) {

            if ($tanggal->isWeekday()) {
                $jumlahHari++;
            }

            $tanggal->addDay();
        }


        return $jumlahHari;
    }
}