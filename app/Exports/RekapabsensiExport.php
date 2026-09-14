<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Absensi;
use App\Models\Pengajuancuti;
use App\Models\Pengajuanlupaabsen;
use App\Models\Pengajuansurat;
use Carbon\Carbon;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use Maatwebsite\Excel\Events\AfterSheet;

class RekapabsensiExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $bulan;
    protected $tahun;
    protected $jumlahTanggal = 0;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    private function isLupaApproved($lupa): bool
    {
        if (!$lupa) return false;
        $approved = ['approved', 'disetujui', 'diterima', 'setuju', 'accept', 'accepted', 'terima'];
        foreach (['status', 'status_approval', 'status_pengajuan', 'approval_status', 'status_verifikasi'] as $field) {
            if (!isset($lupa->$field)) continue;
            if (in_array(strtolower(trim((string) $lupa->$field)), $approved, true)) return true;
        }
        return false;
    }

    public function array(): array
    {
        $tanggalAwal  = Carbon::create($this->tahun, $this->bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($this->tahun, $this->bulan, 1)->endOfMonth();

        $ppnpn = User::with('profil')
            ->where('role', 'ppnpn')
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        // ABSENSI
        $dataAbsensi = Absensi::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', fn($q) => $q->where('role', 'ppnpn')->where('status', 'aktif'))
            ->get();

        $absensi = [];
        foreach ($dataAbsensi as $item) {
            $absensi[$item->user_id][Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // CUTI
        $dataCuti = Pengajuancuti::whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->whereHas('user', fn($q) => $q->where('role', 'ppnpn')->where('status', 'aktif'))
            ->get();

        $cuti = [];
        foreach ($dataCuti as $item) {
            $mulai   = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);
            while ($mulai->lte($selesai)) {
                if (!$mulai->isWeekend() && $mulai->between($tanggalAwal, $tanggalAkhir)) {
                    $cuti[$item->user_id][$mulai->format('Y-m-d')] = $item;
                }
                $mulai->addDay();
            }
        }

        // LUPA ABSEN
        $dataLupa = Pengajuanlupaabsen::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', fn($q) => $q->where('role', 'ppnpn')->where('status', 'aktif'))
            ->get();

        $lupaAbsen = [];
        foreach ($dataLupa as $item) {
            $lupaAbsen[$item->user_id][Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // SURAT SAKIT
        $dataSakit = Pengajuansurat::whereBetween('tanggal', [
                $tanggalAwal->format('Y-m-d'),
                $tanggalAkhir->format('Y-m-d'),
            ])
            ->whereHas('user', fn($q) => $q->where('role', 'ppnpn')->where('status', 'aktif'))
            ->where(function ($q) {
                $q->whereRaw('LOWER(COALESCE(jenis_surat, "")) LIKE ?', ['%sakit%'])
                  ->orWhereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%sakit%'])
                  ->orWhere(function ($sub) {
                      $sub->whereRaw('LOWER(COALESCE(jenis_surat, "")) = ?', ['surat_keterangan'])
                          ->whereRaw('LOWER(COALESCE(keperluan, "")) LIKE ?', ['%berobat%']);
                  });
            })
            ->get();

        $suratSakit = [];
        foreach ($dataSakit as $item) {
            $suratSakit[$item->user_id][Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        // HEADER (baris 1: tanggal + nama hari, baris 2: jam legend)
        $hasil = [];
        $header1 = ['No', 'Nama PPNPN', 'Jabatan'];
        $header2 = ['', '', ''];

        $cursor = $tanggalAwal->copy();
        while ($cursor->lte($tanggalAkhir)) {
            $header1[] = $cursor->format('d');
            $header2[] = $cursor->translatedFormat('D');
            $this->jumlahTanggal++;
            $cursor->addDay();
        }

        $header1[] = 'Hadir'; $header1[] = 'Cuti';  $header1[] = 'CAP';
        $header1[] = 'Sakit'; $header1[] = 'Lupa';  $header1[] = 'Pending';
        $header2[] = ''; $header2[] = ''; $header2[] = '';
        $header2[] = ''; $header2[] = ''; $header2[] = '';

        $hasil[] = $header1;
        $hasil[] = $header2;

        // DATA PER PPNPN
        foreach ($ppnpn as $index => $user) {

            $baris = [
                $index + 1,
                $user->name,
                $user->profil?->jabatan ?? 'PPNPN',
            ];

            $jumlahHadir = 0; $jumlahCuti = 0; $jumlahCAP = 0;
            $jumlahSakit = 0; $jumlahLupa = 0; $jumlahPending = 0;

            $cursor = $tanggalAwal->copy();

            while ($cursor->lte($tanggalAkhir)) {

                $tanggalKey = $cursor->format('Y-m-d');

                $absensiHariIni = $absensi[$user->id][$tanggalKey] ?? null;
                $cutiHariIni    = $cuti[$user->id][$tanggalKey] ?? null;
                $lupaHariIni    = $lupaAbsen[$user->id][$tanggalKey] ?? null;
                $sakitHariIni   = $suratSakit[$user->id][$tanggalKey] ?? null;

                $lupaApproved = $this->isLupaApproved($lupaHariIni);

                $absensiValid = $absensiHariIni
                    && $absensiHariIni->jam_masuk
                    && ($absensiHariIni->status_approval !== 'pending' || $lupaApproved);

                $absensiPending = $absensiHariIni
                    && $absensiHariIni->status_approval === 'pending'
                    && !$lupaApproved;

                $kode = '-';
                $jamMasukText = null; $jamPulangText = null;

                if ($cursor->isWeekend()) {
                    if ($absensiValid) {
                        $kode = ($absensiHariIni->shift === 'malam') ? 'M' : 'H';
                        $jumlahHadir++;
                        $jamMasukText = $absensiHariIni->jam_masuk ? Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                        $jamPulangText = $absensiHariIni->jam_pulang ? Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') : null;
                    } elseif ($absensiPending) {
                        $kode = 'P'; $jumlahPending++;
                        $jamMasukText = $absensiHariIni->jam_masuk ? Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                    } else {
                        $kode = 'LIB';
                    }
                } else {
                    if ($sakitHariIni) {
                        $kode = 'S'; $jumlahSakit++;
                    } elseif ($cutiHariIni) {
                        if ($cutiHariIni->jenis_cuti === 'alasan_penting') { $kode = 'CAP'; $jumlahCAP++; }
                        else { $kode = 'C'; $jumlahCuti++; }
                    } elseif ($absensiValid) {
                        $kode = ($absensiHariIni->shift === 'malam') ? 'M' : 'H';
                        $jumlahHadir++;
                        $jamMasukText = $absensiHariIni->jam_masuk ? Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                        $jamPulangText = $absensiHariIni->jam_pulang ? Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') : null;
                    } elseif ($lupaApproved) {
                        $kode = 'H'; $jumlahHadir++;
                        $jamMasukText = $absensiHariIni && $absensiHariIni->jam_masuk ? Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                    } elseif ($lupaHariIni && !$absensiPending) {
                        $kode = 'LA'; $jumlahLupa++;
                    } elseif ($absensiPending) {
                        $kode = 'P'; $jumlahPending++;
                        $jamMasukText = $absensiHariIni->jam_masuk ? Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                    }
                }

                // Format cell: KODE di baris 1, ↓ jam masuk baris 2, ↑ jam pulang baris 3
                $display = $kode;
                if ($jamMasukText)  $display .= "\n↓ " . $jamMasukText;
                if ($jamPulangText) $display .= "\n↑ " . $jamPulangText;

                $baris[] = $display;
                $cursor->addDay();
            }

            $baris[] = $jumlahHadir;
            $baris[] = $jumlahCuti;
            $baris[] = $jumlahCAP;
            $baris[] = $jumlahSakit;
            $baris[] = $jumlahLupa;
            $baris[] = $jumlahPending;

            $hasil[] = $baris;
        }

        return $hasil;
    }

    public function styles(Worksheet $sheet): ?array
    {
        $lastColumn = $this->jumlahTanggal + 9;
        $lastColumnLetter = Coordinate::stringFromColumnIndex($lastColumn);

        // Header baris 1 & 2
        $sheet->getStyle("A1:{$lastColumnLetter}2")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '4C1D95']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EDE9FE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'C4B5FD'],
                ],
            ],
        ]);

        $sheet->getStyle("A1:{$lastColumnLetter}" . $sheet->getHighestRow())
            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(16);

        return [];
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,
            'B' => 24,
            'C' => 18,
        ];

        // Kolom tanggal — lebar 8.5 supaya muat "↓ 07:30"
        for ($i = 4; $i <= $this->jumlahTanggal + 3; $i++) {
            $column = Coordinate::stringFromColumnIndex($i);
            $widths[$column] = 8.5;
        }

        // Kolom ringkasan
        for ($i = $this->jumlahTanggal + 4; $i <= $this->jumlahTanggal + 9; $i++) {
            $column = Coordinate::stringFromColumnIndex($i);
            $widths[$column] = 8;
        }

        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $kolomAwalTanggal  = 4;
                $kolomAkhirTanggal = $this->jumlahTanggal + 3;

                // 🎨 PALET WARNA — SINKRON DENGAN VIEW & PDF
                $palette = [
                    'H'   => ['D1FAE5', '065F46'],
                    'M'   => ['EDE9FE', '6D28D9'],
                    'C'   => ['FEF3C7', '92400E'],
                    'CAP' => ['FFEDD5', 'C2410C'],
                    'LA'  => ['E0F2FE', '075985'],
                    'S'   => ['FFE4E6', '9F1239'],
                    'P'   => ['FED7AA', '9A3412'],
                    'LIB' => ['FEE2E2', 'B91C1C'],
                ];

                $lastCol = Coordinate::stringFromColumnIndex($this->jumlahTanggal + 9);

                // Wrap text seluruh tabel
                $sheet->getStyle("A1:{$lastCol}{$highestRow}")->getAlignment()->setWrapText(true);

                // Set tinggi baris data
                for ($row = 3; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(34);
                }

                // Header tanggal: baris 2 = nama hari
                // Header tanggal baris 1 sudah bernilai angka tanggal

                for ($col = $kolomAwalTanggal; $col <= $kolomAkhirTanggal; $col++) {

                    $column = Coordinate::stringFromColumnIndex($col);
                    $tanggal = Carbon::create($this->tahun, $this->bulan, $col - 3);

                    // Weekend header
                    if ($tanggal->isWeekend()) {
                        for ($r = 1; $r <= 2; $r++) {
                            $sheet->getStyle("{$column}{$r}")->getFill()->setFillType(Fill::FILL_SOLID);
                            $sheet->getStyle("{$column}{$r}")->getFill()->getStartColor()->setRGB('FEE2E2');
                            $sheet->getStyle("{$column}{$r}")->getFont()->getColor()->setRGB('B91C1C');
                        }
                    }

                    // Warna per cell data (mulai baris 3)
                    for ($row = 3; $row <= $highestRow; $row++) {
                        $cell = "{$column}{$row}";
                        $value = $sheet->getCell($cell)->getValue();

                        // Ambil kode = baris pertama sebelum newline
                        $kode = trim(explode("\n", (string) $value)[0] ?? '');

                        if (isset($palette[$kode])) {
                            [$bg, $fg] = $palette[$kode];
                            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID);
                            $sheet->getStyle($cell)->getFill()->getStartColor()->setRGB($bg);
                            $sheet->getStyle($cell)->getFont()->getColor()->setRGB($fg);
                            $sheet->getStyle($cell)->getFont()->setBold(true);
                        }
                    }
                }

                // Warna kolom ringkasan
                $summaryColors = [
                    4 => ['D1FAE5', '065F46'], // Hadir
                    5 => ['FEF3C7', '92400E'], // Cuti
                    6 => ['FFEDD5', 'C2410C'], // CAP
                    7 => ['FFE4E6', '9F1239'], // Sakit
                    8 => ['E0F2FE', '075985'], // Lupa
                    9 => ['FED7AA', '9A3412'], // Pending
                ];

                foreach ($summaryColors as $offset => [$bg, $fg]) {
                    $colIndex = $this->jumlahTanggal + $offset;
                    $columnLetter = Coordinate::stringFromColumnIndex($colIndex);

                    for ($row = 3; $row <= $highestRow; $row++) {
                        $cell = "{$columnLetter}{$row}";
                        $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID);
                        $sheet->getStyle($cell)->getFill()->getStartColor()->setRGB($bg);
                        $sheet->getStyle($cell)->getFont()->getColor()->setRGB($fg);
                        $sheet->getStyle($cell)->getFont()->setBold(true);
                    }
                }

                // Border
                $sheet->getStyle("A1:{$lastCol}{$highestRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle("A1:{$lastCol}{$highestRow}")
                    ->getBorders()->getAllBorders()->getColor()->setRGB('CBD5E1');

                $sheet->getStyle("D1:{$lastCol}{$highestRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Freeze pane di baris 3 (setelah 2 baris header)
                $sheet->freezePane('D3');
            },
        ];
    }
}