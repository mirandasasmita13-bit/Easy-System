<?php

namespace App\Exports;

use App\Models\Absensi;
use App\Models\Pengajuancuti;
use App\Models\Pengajuanlupaabsen;
use App\Models\PengajuanSurat;
use App\Models\Lembur;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Maatwebsite\Excel\Events\AfterSheet;


class RekapSayaExport implements
    FromArray,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    protected $user;
    protected $bulan;
    protected $tahun;

    protected $jumlahTanggal = 0;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    |
    | Disamakan dengan RekapabsensiController:
    |
    | new RekapSayaExport($bulan, $tahun)
    |
    */

    public function __construct($bulan, $tahun)
    {
        $this->user = auth()->user();
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }


    // ========================================================================
    // DATA EXCEL
    // ========================================================================

    public function array(): array
    {
        $tanggalAwal = Carbon::create(
            $this->tahun,
            $this->bulan,
            1
        )->startOfMonth();

        $tanggalAkhir = Carbon::create(
            $this->tahun,
            $this->bulan,
            1
        )->endOfMonth();


        // ====================================================================
        // ABSENSI SAYA
        // ====================================================================

        $dataAbsensi = Absensi::where(
            'user_id',
            $this->user->id
        )
            ->whereBetween(
                'tanggal',
                [
                    $tanggalAwal->format('Y-m-d'),
                    $tanggalAkhir->format('Y-m-d'),
                ]
            )
            ->get();

        $absensi = [];

        foreach ($dataAbsensi as $item) {

            $tanggalKey = Carbon::parse(
                $item->tanggal
            )->format('Y-m-d');

            $absensi[$tanggalKey] = $item;
        }


        // ====================================================================
        // CUTI SAYA
        // ====================================================================
        //
        // PENTING:
        // Weekend TIDAK dimasukkan ke dalam rekap cuti.
        //
        // Contoh:
        // Cuti 6 hari kalender
        // Senin - Sabtu
        //
        // Yang masuk sebagai CUTI:
        // Senin, Selasa, Rabu, Kamis, Jumat
        //
        // Sabtu tetap LIBUR.
        //
        // ====================================================================

        $dataCuti = Pengajuancuti::where(
            'user_id',
            $this->user->id
        )
            ->whereDate(
                'tanggal_mulai',
                '<=',
                $tanggalAkhir
            )
            ->whereDate(
                'tanggal_selesai',
                '>=',
                $tanggalAwal
            )
            ->get();

        $cuti = [];

        foreach ($dataCuti as $item) {

            $mulai = Carbon::parse(
                $item->tanggal_mulai
            );

            $selesai = Carbon::parse(
                $item->tanggal_selesai
            );

            while ($mulai->lte($selesai)) {

                /*
                |--------------------------------------------------------------------------
                | HANYA HARI KERJA
                |--------------------------------------------------------------------------
                */

                if (
                    !$mulai->isWeekend()
                    && $mulai->between(
                        $tanggalAwal,
                        $tanggalAkhir
                    )
                ) {

                    $tanggalKey = $mulai->format('Y-m-d');

                    $cuti[$tanggalKey] = $item;
                }

                $mulai->addDay();
            }
        }


        // ====================================================================
        // LUPA ABSEN
        // ====================================================================

        $dataLupaAbsen = Pengajuanlupaabsen::where(
            'user_id',
            $this->user->id
        )
            ->whereBetween(
                'tanggal',
                [
                    $tanggalAwal->format('Y-m-d'),
                    $tanggalAkhir->format('Y-m-d'),
                ]
            )
            ->get();

        $lupaAbsen = [];

        foreach ($dataLupaAbsen as $item) {

            $tanggalKey = Carbon::parse(
                $item->tanggal
            )->format('Y-m-d');

            $lupaAbsen[$tanggalKey] = $item;
        }


        // ====================================================================
        // LEMBUR
        // ====================================================================

        $dataLembur = Lembur::where(
            'user_id',
            $this->user->id
        )
            ->whereBetween(
                'tanggal',
                [
                    $tanggalAwal->format('Y-m-d'),
                    $tanggalAkhir->format('Y-m-d'),
                ]
            )
            ->get();

        $lembur = [];

        foreach ($dataLembur as $item) {

            $tanggalKey = Carbon::parse(
                $item->tanggal
            )->format('Y-m-d');

            $lembur[$tanggalKey] = $item;
        }


        // ====================================================================
        // SURAT SAKIT
        // ====================================================================
        //
        // Surat dianggap sakit apabila:
        //
        // 1. jenis_surat mengandung "sakit"
        // 2. keperluan mengandung "sakit"
        // 3. jenis_surat = surat_keterangan
        //    DAN keperluan mengandung "berobat"
        //
        // Disamakan dengan RekapabsensiController.
        //
        // ====================================================================

        $dataSuratSakit = PengajuanSurat::where(
            'user_id',
            $this->user->id
        )
            ->whereBetween(
                'tanggal',
                [
                    $tanggalAwal->format('Y-m-d'),
                    $tanggalAkhir->format('Y-m-d'),
                ]
            )
            ->where(function ($query) {

                $query

                    // Jenis surat mengandung "sakit"
                    ->whereRaw(
                        'LOWER(TRIM(COALESCE(jenis_surat, ""))) LIKE ?',
                        ['%sakit%']
                    )

                    // Keperluan mengandung "sakit"
                    ->orWhereRaw(
                        'LOWER(TRIM(COALESCE(keperluan, ""))) LIKE ?',
                        ['%sakit%']
                    )

                    // Surat keterangan untuk berobat
                    ->orWhere(function ($q) {

                        $q->whereRaw(
                            'LOWER(TRIM(COALESCE(jenis_surat, ""))) = ?',
                            ['surat_keterangan']
                        )

                        ->whereRaw(
                            'LOWER(TRIM(COALESCE(keperluan, ""))) LIKE ?',
                            ['%berobat%']
                        );
                    });
            })
            ->get();

        $suratSakit = [];

        foreach ($dataSuratSakit as $item) {

            $tanggalKey = Carbon::parse(
                $item->tanggal
            )->format('Y-m-d');

            $suratSakit[$tanggalKey] = $item;
        }


        // ====================================================================
        // HEADER
        // ====================================================================

        $hasil = [];

        $hasil[] = [
            'Tanggal',
            'Hari',
            'Shift',
            'Status',
            'Jam Masuk',
            'Jam Pulang',
            'Keterangan',
            'Lembur',
        ];


        // ====================================================================
        // DATA PER TANGGAL
        // ====================================================================

        $cursor = $tanggalAwal->copy();

        while ($cursor->lte($tanggalAkhir)) {

            $tanggalKey = $cursor->format('Y-m-d');

            $absensiHariIni =
                $absensi[$tanggalKey] ?? null;

            $cutiHariIni =
                $cuti[$tanggalKey] ?? null;

            $lupaHariIni =
                $lupaAbsen[$tanggalKey] ?? null;

            $lemburHariIni =
                $lembur[$tanggalKey] ?? null;

            $suratSakitHariIni =
                $suratSakit[$tanggalKey] ?? null;


            // =================================================================
            // DEFAULT
            // =================================================================

            $shift = '-';
            $status = 'Belum Absen';
            $jamMasuk = '-';
            $jamPulang = '-';
            $keterangan = '-';
            $lemburStatus = '-';


            // =================================================================
            // DATA ABSENSI
            // =================================================================

            if (
                $absensiHariIni &&
                $absensiHariIni->jam_masuk
            ) {

                $jamMasukValue = Carbon::parse(
                    $absensiHariIni->jam_masuk
                );

                $jamMasuk =
                    $jamMasukValue->format('H:i');


                $jamPulang =
                    $absensiHariIni->jam_pulang
                        ? Carbon::parse(
                            $absensiHariIni->jam_pulang
                        )->format('H:i')
                        : '-';


                // =============================================================
                // SHIFT DARI DATABASE
                // =============================================================

                if (
                    $absensiHariIni->shift === 'malam'
                ) {

                    $shift = 'Shift Malam';

                } elseif (
                    $absensiHariIni->shift === 'pagi'
                ) {

                    $shift = 'Shift Pagi';

                } else {

                    $shift = '-';
                }
            }


            // =================================================================
            // STATUS
            // =================================================================
            //
            // Aturan:
            //
            // 1. WEEKEND + ADA ABSENSI AKTUAL
            //    => HADIR
            //
            // 2. WEEKEND + TIDAK ADA ABSENSI
            //    => LIBUR
            //
            // 3. HARI KERJA + SAKIT
            //    => SAKIT
            //
            // 4. HARI KERJA + CUTI
            //    => CUTI
            //
            // 5. HARI KERJA + LUPA ABSEN
            //    => LUPA ABSEN
            //
            // 6. ADA ABSENSI
            //    => HADIR
            //
            // 7. LAINNYA
            //    => BELUM ABSEN
            //
            // =================================================================


            // =================================================================
            // WEEKEND
            // =================================================================

            if ($cursor->isWeekend()) {

                /*
                |--------------------------------------------------------------------------
                | Kalau benar-benar melakukan absensi di weekend,
                | tetap dianggap HADIR.
                |--------------------------------------------------------------------------
                */

                if (
                    $absensiHariIni &&
                    $absensiHariIni->jam_masuk
                ) {

                    $status = 'Hadir';
                    $keterangan = 'Hadir';

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Tidak ada absensi aktual.
                    | Cuti/sakit/lupa tidak mengubah weekend menjadi cuti/sakit.
                    |--------------------------------------------------------------------------
                    */

                    $status = 'Libur';
                    $keterangan = 'Hari libur';
                }

            } else {

                // =============================================================
                // HARI KERJA
                // =============================================================

                if ($suratSakitHariIni) {

                    $status = 'Sakit';
                    $keterangan = 'Surat sakit';

                } elseif ($cutiHariIni) {

                    $jenisCuti = strtolower(
                        trim(
                            $cutiHariIni->jenis_cuti ?? ''
                        )
                    );


                    if (
                        str_contains(
                            $jenisCuti,
                            'tambahan'
                        )
                    ) {

                        $status = 'Cuti Tambahan';
                        $keterangan = 'Cuti tambahan';

                    } elseif (
                        str_contains(
                            $jenisCuti,
                            'alasan'
                        )
                        || str_contains(
                            $jenisCuti,
                            'penting'
                        )
                    ) {

                        $status = 'Alasan Penting';
                        $keterangan = 'Cuti alasan penting';

                    } else {

                        $status = 'Cuti Tahunan';
                        $keterangan = 'Cuti tahunan';
                    }

                } elseif ($lupaHariIni) {

                    $status = 'Lupa Absen';
                    $keterangan = 'Perbaikan absensi';

                } elseif (
                    $absensiHariIni &&
                    $absensiHariIni->jam_masuk
                ) {

                    $status = 'Hadir';
                    $keterangan = 'Hadir';
                }
            }


            // =================================================================
            // LEMBUR
            // =================================================================

            if ($lemburHariIni) {

                $lemburStatus = 'Ya';
            }


            // =================================================================
            // MASUKKAN KE EXCEL
            // =================================================================

            $hasil[] = [
                $cursor->format('d/m/Y'),
                $cursor->translatedFormat('l'),
                $shift,
                $status,
                $jamMasuk,
                $jamPulang,
                $keterangan,
                $lemburStatus,
            ];


            $this->jumlahTanggal++;

            $cursor->addDay();
        }


        return $hasil;
    }


    // ========================================================================
    // STYLE
    // ========================================================================

    public function styles(Worksheet $sheet): ?array
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle(
            "A1:H1"
        )->applyFromArray([

            'font' => [
                'bold' => true,
            ],

            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'EDE9FE',
                ],
            ],

            'alignment' => [
                'horizontal' =>
                    Alignment::HORIZONTAL_CENTER,

                'vertical' =>
                    Alignment::VERTICAL_CENTER,
            ],

            'borders' => [
                'allBorders' => [
                    'borderStyle' =>
                        Border::BORDER_THIN,

                    'color' => [
                        'rgb' => 'D1D5DB',
                    ],
                ],
            ],
        ]);


        $sheet->getStyle(
            "A1:H{$lastRow}"
        )->getAlignment()
            ->setVertical(
                Alignment::VERTICAL_CENTER
            );


        $sheet->getStyle(
            "A1:H{$lastRow}"
        )->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            );


        $sheet->getStyle(
            "A1:H{$lastRow}"
        )->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );


        $sheet->getStyle(
            "A1:H{$lastRow}"
        )->getAlignment()
            ->setWrapText(true);


        $sheet->getRowDimension(1)
            ->setRowHeight(25);


        return [];
    }


    // ========================================================================
    // LEBAR KOLOM
    // ========================================================================

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 14,
            'C' => 18,
            'D' => 20,
            'E' => 14,
            'F' => 14,
            'G' => 25,
            'H' => 12,
        ];
    }


    // ========================================================================
    // EVENT
    // ========================================================================

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (
                AfterSheet $event
            ) {

                $sheet =
                    $event->sheet->getDelegate();

                $highestRow =
                    $sheet->getHighestRow();


                // =============================================================
                // FREEZE HEADER
                // =============================================================

                $sheet->freezePane('A2');


                // =============================================================
                // TINGGI BARIS
                // =============================================================

                for (
                    $row = 2;
                    $row <= $highestRow;
                    $row++
                ) {

                    $sheet->getRowDimension($row)
                        ->setRowHeight(22);
                }


                // =============================================================
                // WARNA STATUS
                // =============================================================

                for (
                    $row = 2;
                    $row <= $highestRow;
                    $row++
                ) {

                    $status =
                        $sheet
                            ->getCell("D{$row}")
                            ->getValue();


                    $cell =
                        $sheet->getStyle(
                            "D{$row}"
                        );


                    // =========================================================
                    // HADIR
                    // =========================================================

                    if ($status === 'Hadir') {

                        $cell->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            );

                        $cell->getFill()
                            ->getStartColor()
                            ->setRGB(
                                'DCFCE7'
                            );

                        $cell->getFont()
                            ->getColor()
                            ->setRGB(
                                '166534'
                            );

                        $cell->getFont()
                            ->setBold(true);
                    }


                    // =========================================================
                    // SAKIT
                    // =========================================================

                    if ($status === 'Sakit') {

                        $cell->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            );

                        $cell->getFill()
                            ->getStartColor()
                            ->setRGB(
                                'FEF3C7'
                            );

                        $cell->getFont()
                            ->getColor()
                            ->setRGB(
                                '92400E'
                            );

                        $cell->getFont()
                            ->setBold(true);
                    }


                    // =========================================================
                    // CUTI TAHUNAN
                    // =========================================================

                    if ($status === 'Cuti Tahunan') {

                        $cell->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            );

                        $cell->getFill()
                            ->getStartColor()
                            ->setRGB(
                                'EDE9FE'
                            );

                        $cell->getFont()
                            ->getColor()
                            ->setRGB(
                                '6D28D9'
                            );

                        $cell->getFont()
                            ->setBold(true);
                    }


                    // =========================================================
                    // ALASAN PENTING
                    // =========================================================

                    if ($status === 'Alasan Penting') {

                        $cell->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            );

                        $cell->getFill()
                            ->getStartColor()
                            ->setRGB(
                                'FCE7F3'
                            );

                        $cell->getFont()
                            ->getColor()
                            ->setRGB(
                                '9D174D'
                            );

                        $cell->getFont()
                            ->setBold(true);
                    }


                    // =========================================================
                    // CUTI TAMBAHAN
                    // =========================================================

                    if ($status === 'Cuti Tambahan') {

                        $cell->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            );

                        $cell->getFill()
                            ->getStartColor()
                            ->setRGB(
                                'E0F2FE'
                            );

                        $cell->getFont()
                            ->getColor()
                            ->setRGB(
                                '0369A1'
                            );

                        $cell->getFont()
                            ->setBold(true);
                    }


                    // =========================================================
                    // LUPA ABSEN
                    // =========================================================

                    if ($status === 'Lupa Absen') {

                        $cell->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            );

                        $cell->getFill()
                            ->getStartColor()
                            ->setRGB(
                                'F3E8FF'
                            );

                        $cell->getFont()
                            ->getColor()
                            ->setRGB(
                                '7E22CE'
                            );

                        $cell->getFont()
                            ->setBold(true);
                    }


                    // =========================================================
                    // SHIFT MALAM
                    // =========================================================

                    if (
                        $sheet
                            ->getCell("C{$row}")
                            ->getValue()
                            === 'Shift Malam'
                    ) {

                        $sheet
                            ->getStyle("C{$row}")
                            ->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            );

                        $sheet
                            ->getStyle("C{$row}")
                            ->getFill()
                            ->getStartColor()
                            ->setRGB(
                                'DBEAFE'
                            );

                        $sheet
                            ->getStyle("C{$row}")
                            ->getFont()
                            ->getColor()
                            ->setRGB(
                                '1D4ED8'
                            );

                        $sheet
                            ->getStyle("C{$row}")
                            ->getFont()
                            ->setBold(true);
                    }


                    // =========================================================
                    // LIBUR
                    // =========================================================

                    if ($status === 'Libur') {

                        $cell->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            );

                        $cell->getFill()
                            ->getStartColor()
                            ->setRGB(
                                'FECACA'
                            );

                        $cell->getFont()
                            ->getColor()
                            ->setRGB(
                                'B91C1C'
                            );

                        $cell->getFont()
                            ->setBold(true);
                    }
                }
            },
        ];
    }
}