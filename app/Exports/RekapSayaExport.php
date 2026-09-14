<?php

namespace App\Exports;

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

use Maatwebsite\Excel\Events\AfterSheet;

class RekapSayaExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    protected $user;
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->user  = auth()->user();
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    private function isLupaApproved($lupa): bool
    {
        if (!$lupa) return false;
        $approved = ['approved', 'disetujui', 'diterima', 'setuju', 'accept', 'accepted', 'terima'];
        foreach (['status', 'status_approval', 'status_pengajuan', 'approval_status'] as $field) {
            if (!isset($lupa->$field)) continue;
            if (in_array(strtolower(trim((string) $lupa->$field)), $approved, true)) return true;
        }
        return false;
    }

    public function array(): array
    {
        $tanggalAwal  = Carbon::create($this->tahun, $this->bulan, 1)->startOfMonth();
        $tanggalAkhir = Carbon::create($this->tahun, $this->bulan, 1)->endOfMonth();

        $dataAbsensi = Absensi::where('user_id', $this->user->id)
            ->whereBetween('tanggal', [$tanggalAwal->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->get();

        $absensi = [];
        foreach ($dataAbsensi as $item) {
            $absensi[Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        $dataCuti = Pengajuancuti::where('user_id', $this->user->id)
            ->whereDate('tanggal_mulai', '<=', $tanggalAkhir)
            ->whereDate('tanggal_selesai', '>=', $tanggalAwal)
            ->get();

        $cuti = [];
        foreach ($dataCuti as $item) {
            $mulai   = Carbon::parse($item->tanggal_mulai);
            $selesai = Carbon::parse($item->tanggal_selesai);
            while ($mulai->lte($selesai)) {
                if (!$mulai->isWeekend() && $mulai->between($tanggalAwal, $tanggalAkhir)) {
                    $cuti[$mulai->format('Y-m-d')] = $item;
                }
                $mulai->addDay();
            }
        }

        $dataLupa = Pengajuanlupaabsen::where('user_id', $this->user->id)
            ->whereBetween('tanggal', [$tanggalAwal->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
            ->get();

        $lupaAbsen = [];
        foreach ($dataLupa as $item) {
            $lupaAbsen[Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        $dataSakit = Pengajuansurat::where('user_id', $this->user->id)
            ->whereBetween('tanggal', [$tanggalAwal->format('Y-m-d'), $tanggalAkhir->format('Y-m-d')])
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
            $suratSakit[Carbon::parse($item->tanggal)->format('Y-m-d')] = $item;
        }

        $hasil = [];
        $hasil[] = ['Tanggal', 'Hari', 'Shift', 'Status', 'Jam Masuk', 'Jam Pulang', 'Keterangan'];

        $cursor = $tanggalAwal->copy();
        while ($cursor->lte($tanggalAkhir)) {

            $tanggalKey = $cursor->format('Y-m-d');

            $absensiHariIni    = $absensi[$tanggalKey] ?? null;
            $cutiHariIni       = $cuti[$tanggalKey] ?? null;
            $lupaHariIni       = $lupaAbsen[$tanggalKey] ?? null;
            $suratSakitHariIni = $suratSakit[$tanggalKey] ?? null;

            $lupaApproved = $this->isLupaApproved($lupaHariIni);

            $shift = '-';
            $status = 'Belum Absen';
            $jamMasuk = '-';
            $jamPulang = '-';
            $keterangan = '-';

            $absensiValid = $absensiHariIni
                && $absensiHariIni->jam_masuk
                && ($absensiHariIni->status_approval !== 'pending' || $lupaApproved);

            $absensiPending = $absensiHariIni
                && $absensiHariIni->status_approval === 'pending'
                && !$lupaApproved;

            if ($absensiValid) {
                $jamMasuk  = Carbon::parse($absensiHariIni->jam_masuk)->format('H:i');
                $jamPulang = $absensiHariIni->jam_pulang
                    ? Carbon::parse($absensiHariIni->jam_pulang)->format('H:i')
                    : '-';
                $shift = $absensiHariIni->shift === 'malam' ? 'Shift Malam' : 'Shift Pagi';
            }

            if ($cursor->isWeekend()) {
                if ($absensiValid) {
                    $status = 'Hadir'; $keterangan = 'Hadir';
                } else {
                    $status = 'Libur'; $keterangan = 'Hari libur';
                }
            } else {
                if ($absensiPending) {
                    $status = 'Pending'; $keterangan = 'Menunggu approval';
                } elseif ($suratSakitHariIni) {
                    $status = 'Sakit'; $keterangan = 'Surat sakit';
                } elseif ($cutiHariIni) {
                    $jenisCuti = strtolower(trim($cutiHariIni->jenis_cuti ?? ''));
                    if (str_contains($jenisCuti, 'alasan') || str_contains($jenisCuti, 'penting')) {
                        $status = 'Alasan Penting'; $keterangan = 'Cuti alasan penting';
                    } else {
                        $status = 'Cuti Tahunan'; $keterangan = 'Cuti tahunan';
                    }
                } elseif ($absensiValid) {
                    $status = 'Hadir'; $keterangan = 'Hadir';
                } elseif ($lupaApproved) {
                    $status = 'Hadir'; $keterangan = 'Lupa absen disetujui';
                    $shift = 'Shift Pagi';
                } elseif ($lupaHariIni) {
                    $status = 'Lupa Absen'; $keterangan = 'Perbaikan absensi';
                }
            }

            $hasil[] = [
                $cursor->format('d/m/Y'),
                $cursor->translatedFormat('l'),
                $shift,
                $status,
                $jamMasuk,
                $jamPulang,
                $keterangan,
            ];

            $cursor->addDay();
        }

        return $hasil;
    }

    public function styles(Worksheet $sheet): ?array
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle("A1:G1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '4C1D95']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EDE9FE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'C4B5FD'],
                ],
            ],
        ]);

        $sheet->getStyle("A1:G{$lastRow}")
            ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A1:G{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A1:G{$lastRow}")
            ->getBorders()->getAllBorders()->getColor()->setRGB('CBD5E1');
        $sheet->getStyle("A1:G{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:G{$lastRow}")->getAlignment()->setWrapText(true);

        $sheet->getRowDimension(1)->setRowHeight(28);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, 'B' => 14, 'C' => 18,
            'D' => 20, 'E' => 14, 'F' => 14, 'G' => 25,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->freezePane('A2');

                // 🎨 PALET WARNA — SINKRON
                $statusColors = [
                    'Hadir'          => ['D1FAE5', '065F46'], // Emerald
                    'Sakit'          => ['FFE4E6', '9F1239'], // Rose
                    'Cuti Tahunan'   => ['FEF3C7', '92400E'], // Amber
                    'Alasan Penting' => ['FFEDD5', 'C2410C'], // Orange
                    'Lupa Absen'     => ['E0F2FE', '075985'], // Sky
                    'Pending'        => ['FED7AA', '9A3412'], // Peach
                    'Libur'          => ['FEE2E2', 'B91C1C'], // Red
                ];

                for ($row = 2; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(22);

                    $status = $sheet->getCell("D{$row}")->getValue();

                    if (isset($statusColors[$status])) {
                        [$bg, $fg] = $statusColors[$status];
                        $cell = $sheet->getStyle("D{$row}");
                        $cell->getFill()->setFillType(Fill::FILL_SOLID);
                        $cell->getFill()->getStartColor()->setRGB($bg);
                        $cell->getFont()->getColor()->setRGB($fg);
                        $cell->getFont()->setBold(true);
                    }

                    // Shift malam → violet
                    if ($sheet->getCell("C{$row}")->getValue() === 'Shift Malam') {
                        $shiftCell = $sheet->getStyle("C{$row}");
                        $shiftCell->getFill()->setFillType(Fill::FILL_SOLID);
                        $shiftCell->getFill()->getStartColor()->setRGB('EDE9FE');
                        $shiftCell->getFont()->getColor()->setRGB('6D28D9');
                        $shiftCell->getFont()->setBold(true);
                    }
                }
            },
        ];
    }
}