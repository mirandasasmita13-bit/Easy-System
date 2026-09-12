<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Absensi;
use App\Models\Pengajuancuti;
use App\Models\Pengajuanlupaabsen;
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


class RekapabsensiExport implements
    FromArray,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    protected $bulan;
    protected $tahun;

    protected $jumlahTanggal = 0;


    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }


    // =========================================================
    // DATA EXCEL
    // =========================================================

    public function array(): array
    {
        $tanggalAwal = Carbon::create(
            $this->tahun,
            $this->bulan,
            1
        );

        $tanggalAkhir = $tanggalAwal->copy()->endOfMonth();


        // =====================================================
        // PEGAWAI
        // =====================================================

        $pegawai = User::with('profil')
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();


        // =====================================================
        // ABSENSI
        // =====================================================

        $dataAbsensi = Absensi::whereBetween(
            'tanggal',
            [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ]
        )->get();

        $absensi = [];

        foreach ($dataAbsensi as $item) {

            $tanggalKey = Carbon::parse(
                $item->tanggal
            )->format('Y-m-d');

            $absensi[$item->user_id][$tanggalKey] = $item;
        }


        // =====================================================
        // CUTI
        // =====================================================

        $dataCuti = Pengajuancuti::where(function ($query) use (
            $tanggalAwal,
            $tanggalAkhir
        ) {

            $query
                ->whereBetween('tanggal_mulai', [
                    $tanggalAwal->format('Y-m-d'),
                    $tanggalAkhir->format('Y-m-d'),
                ])

                ->orWhereBetween('tanggal_selesai', [
                    $tanggalAwal->format('Y-m-d'),
                    $tanggalAkhir->format('Y-m-d'),
                ])

                ->orWhere(function ($q) use (
                    $tanggalAwal,
                    $tanggalAkhir
                ) {

                    $q->where(
                        'tanggal_mulai',
                        '<=',
                        $tanggalAwal->format('Y-m-d')
                    )

                    ->where(
                        'tanggal_selesai',
                        '>=',
                        $tanggalAkhir->format('Y-m-d')
                    );
                });

        })->get();


        $cuti = [];

        foreach ($dataCuti as $item) {

            $mulai = Carbon::parse(
                $item->tanggal_mulai
            );

            $selesai = Carbon::parse(
                $item->tanggal_selesai
            );

            while ($mulai->lte($selesai)) {

                $tanggalKey = $mulai->format('Y-m-d');

                $cuti[$item->user_id][$tanggalKey] = $item;

                $mulai->addDay();
            }
        }


        // =====================================================
        // LUPA ABSEN
        // =====================================================

        $dataLupaAbsen = Pengajuanlupaabsen::whereBetween(
            'tanggal',
            [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ]
        )->get();

        $lupaAbsen = [];

        foreach ($dataLupaAbsen as $item) {

            $tanggalKey = Carbon::parse(
                $item->tanggal
            )->format('Y-m-d');

            $lupaAbsen[$item->user_id][$tanggalKey] = $item;
        }


        // =====================================================
        // HEADER
        // =====================================================

        $hasil = [];

        $header = [
            'No',
            'Nama Pegawai',
            'Jabatan',
        ];


        $cursor = $tanggalAwal->copy();

        while ($cursor->lte($tanggalAkhir)) {

            $header[] = $cursor->format('d');

            $this->jumlahTanggal++;

            $cursor->addDay();
        }


        // =====================================================
        // RINGKASAN
        // =====================================================

        $header[] = 'Hadir';
        $header[] = 'Cuti';
        $header[] = 'Cuti Tambahan';
        $header[] = 'Lupa Absen';

        $hasil[] = $header;


        // =====================================================
        // DATA PEGAWAI
        // =====================================================

        foreach ($pegawai as $index => $user) {

            $baris = [
                $index + 1,
                $user->name,
                $user->profil?->jabatan ?? 'Pegawai',
            ];


            $jumlahHadir = 0;
            $jumlahCuti = 0;
            $jumlahCutiTambahan = 0;
            $jumlahLupaAbsen = 0;


            $cursor = $tanggalAwal->copy();


            while ($cursor->lte($tanggalAkhir)) {

                $tanggalKey = $cursor->format('Y-m-d');


                $absensiHariIni =
                    $absensi[$user->id][$tanggalKey] ?? null;

                $cutiHariIni =
                    $cuti[$user->id][$tanggalKey] ?? null;

                $lupaHariIni =
                    $lupaAbsen[$user->id][$tanggalKey] ?? null;


                $kode = '-';


                // =================================================
                // WEEKEND
                // =================================================

                if ($cursor->isWeekend()) {

                    /*
                     * Weekend + ADA ABSEN
                     * = tetap hadir
                     */

                    if (
                        $absensiHariIni &&
                        $absensiHariIni->jam_masuk
                    ) {

                        /*
                         * Shift dibaca langsung dari database.
                         *
                         * pagi  = H
                         * malam = M
                         */

                        if ($absensiHariIni->shift === 'malam') {

                            $kode = 'M';

                        } else {

                            $kode = 'H';
                        }


                        $jumlahHadir++;

                    } else {

                        /*
                         * Weekend + TIDAK ADA ABSEN
                         * = LIBUR
                         */

                        $kode = 'LIB';
                    }


                // =================================================
                // CUTI
                // =================================================

                } elseif ($cutiHariIni) {

                    if (
                        $cutiHariIni->jenis_cuti === 'tambahan'
                    ) {

                        $kode = 'CT';

                        $jumlahCutiTambahan++;

                    } else {

                        $kode = 'C';

                        $jumlahCuti++;
                    }


                // =================================================
                // LUPA ABSEN
                // =================================================

                } elseif (
                    $lupaHariIni &&
                    !$absensiHariIni
                ) {

                    $kode = 'LA';

                    $jumlahLupaAbsen++;


                // =================================================
                // ABSENSI
                // =================================================

                } elseif (
                    $absensiHariIni &&
                    $absensiHariIni->jam_masuk
                ) {

                    /*
                     * Shift dibaca langsung dari database.
                     *
                     * pagi  = H
                     * malam = M
                     */

                    if ($absensiHariIni->shift === 'malam') {

                        $kode = 'M';

                    } else {

                        $kode = 'H';
                    }


                    $jumlahHadir++;
                }


                $baris[] = $kode;

                $cursor->addDay();
            }


            // =====================================================
            // RINGKASAN
            // =====================================================

            $baris[] = $jumlahHadir;
            $baris[] = $jumlahCuti;
            $baris[] = $jumlahCutiTambahan;
            $baris[] = $jumlahLupaAbsen;


            $hasil[] = $baris;
        }


        return $hasil;
    }


    // =========================================================
    // STYLE DASAR
    // =========================================================

    public function styles(Worksheet $sheet): ?array
    {
        /*
         * Kolom:
         *
         * A = No
         * B = Nama Pegawai
         * C = Jabatan
         * D dst = Tanggal
         *
         * Setelah tanggal:
         * Hadir
         * Cuti
         * Cuti Tambahan
         * Lupa Absen
         *
         * Total tambahan setelah tanggal = 4 kolom.
         *
         * Jadi:
         * jumlahTanggal + 7
         *
         * karena 3 kolom awal + jumlah tanggal + 4 ringkasan
         */

        $lastColumn = $this->jumlahTanggal + 7;

        $lastColumnLetter =
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                $lastColumn
            );


        // =====================================================
        // HEADER
        // =====================================================

        $sheet->getStyle(
            "A1:{$lastColumnLetter}1"
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


        // =====================================================
        // SEMUA CELL
        // =====================================================

        $sheet->getStyle(
            "A1:{$lastColumnLetter}" .
            ($sheet->getHighestRow())
        )->getAlignment()->setVertical(
            Alignment::VERTICAL_CENTER
        );


        // =====================================================
        // TINGGI HEADER
        // =====================================================

        $sheet->getRowDimension(1)
            ->setRowHeight(25);


        return [];
    }


    // =========================================================
    // LEBAR KOLOM
    // =========================================================

    public function columnWidths(): array
    {
        $widths = [

            'A' => 6,
            'B' => 28,
            'C' => 25,

        ];


        // =====================================================
        // KOLOM TANGGAL
        // =====================================================

        for (
            $i = 4;
            $i <= $this->jumlahTanggal + 3;
            $i++
        ) {

            $column =
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                    $i
                );

            $widths[$column] = 6;
        }


        return $widths;
    }


    // =========================================================
    // EVENT UNTUK WARNA CELL
    // =========================================================

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


                /*
                 * Kolom tanggal dimulai dari D.
                 */

                $kolomAwalTanggal = 4;

                $kolomAkhirTanggal =
                    $this->jumlahTanggal + 3;


                // =================================================
                // WARNA HEADER TANGGAL
                // =================================================

                for (
                    $col = $kolomAwalTanggal;
                    $col <= $kolomAkhirTanggal;
                    $col++
                ) {

                    $column =
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                            $col
                        );


                    $tanggal = Carbon::create(
                        $this->tahun,
                        $this->bulan,
                        $col - 3
                    );


                    // =================================================
                    // SABTU / MINGGU
                    // =================================================

                    if ($tanggal->isWeekend()) {

                        $sheet->getStyle(
                            "{$column}1"
                        )->getFill()->setFillType(
                            Fill::FILL_SOLID
                        );

                        $sheet->getStyle(
                            "{$column}1"
                        )->getFill()->getStartColor()
                            ->setRGB('FECACA');

                        $sheet->getStyle(
                            "{$column}1"
                        )->getFont()
                            ->getColor()
                            ->setRGB('B91C1C');
                    }


                    // =================================================
                    // WARNA CELL STATUS
                    // =================================================

                    for (
                        $row = 2;
                        $row <= $highestRow;
                        $row++
                    ) {

                        $cell =
                            "{$column}{$row}";

                        $kode =
                            $sheet->getCell($cell)
                                ->getValue();


                        // =================================================
                        // H = HADIR / SHIFT PAGI
                        // =================================================

                        if ($kode === 'H') {

                            $sheet->getStyle($cell)
                                ->getFill()
                                ->setFillType(
                                    Fill::FILL_SOLID
                                );

                            $sheet->getStyle($cell)
                                ->getFill()
                                ->getStartColor()
                                ->setRGB(
                                    'DCFCE7'
                                );

                            $sheet->getStyle($cell)
                                ->getFont()
                                ->getColor()
                                ->setRGB(
                                    '166534'
                                );

                            $sheet->getStyle($cell)
                                ->getFont()
                                ->setBold(true);
                        }


                        // =================================================
                        // M = SHIFT MALAM
                        // =================================================

                        elseif ($kode === 'M') {

                            $sheet->getStyle($cell)
                                ->getFill()
                                ->setFillType(
                                    Fill::FILL_SOLID
                                );

                            $sheet->getStyle($cell)
                                ->getFill()
                                ->getStartColor()
                                ->setRGB(
                                    'DBEAFE'
                                );

                            $sheet->getStyle($cell)
                                ->getFont()
                                ->getColor()
                                ->setRGB(
                                    '1D4ED8'
                                );

                            $sheet->getStyle($cell)
                                ->getFont()
                                ->setBold(true);
                        }


                        // =================================================
                        // LIB = LIBUR
                        // =================================================

                        elseif ($kode === 'LIB') {

                            $sheet->getStyle($cell)
                                ->getFill()
                                ->setFillType(
                                    Fill::FILL_SOLID
                                );

                            $sheet->getStyle($cell)
                                ->getFill()
                                ->getStartColor()
                                ->setRGB(
                                    'FECACA'
                                );

                            $sheet->getStyle($cell)
                                ->getFont()
                                ->getColor()
                                ->setRGB(
                                    'B91C1C'
                                );

                            $sheet->getStyle($cell)
                                ->getFont()
                                ->setBold(true);
                        }
                    }
                }


                // =====================================================
                // BORDER SELURUH TABEL
                // =====================================================

                $lastColumn =
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                        $this->jumlahTanggal + 7
                    );


                $sheet->getStyle(
                    "A1:{$lastColumn}{$highestRow}"
                )->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(
                        Border::BORDER_THIN
                    );


                // =====================================================
                // CENTER KOLOM TANGGAL
                // =====================================================

                $sheet->getStyle(
                    "D1:{$lastColumn}{$highestRow}"
                )->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );


                // =====================================================
                // FREEZE HEADER
                // =====================================================

                $sheet->freezePane('D2');
            },
        ];
    }
}