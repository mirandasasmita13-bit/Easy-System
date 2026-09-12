<?php

namespace App\Exports;

use App\Models\Lembur;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekaplemburExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $bulan;
    protected $tahun;
    protected $userId;

    public function __construct($bulan, $tahun, $userId = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->userId = $userId;
    }

    /**
     * Ambil data lembur
     */
    public function collection(): Enumerable
    {
        $query = Lembur::with('user.profil')
            ->whereYear('tanggal', $this->tahun)
            ->whereMonth('tanggal', $this->bulan);

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        return $query
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();
    }

    /**
     * Judul kolom Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Pegawai',
            'Jam Mulai',
            'Jam Selesai',
            'Kegiatan',
            'Keterangan',
        ];
    }

    /**
     * Isi setiap baris Excel
     */
    public function map($lembur): array
    {
        static $no = 0;

        $no++;

        return [
            $no,

            $lembur->tanggal
                ? $lembur->tanggal->format('d/m/Y')
                : '-',

            $lembur->user?->name ?? '-',

            $lembur->jam_mulai
                ? date('H:i', strtotime($lembur->jam_mulai))
                : '-',

            $lembur->jam_selesai
                ? date('H:i', strtotime($lembur->jam_selesai))
                : '-',

            $lembur->kegiatan ?? '-',

            $lembur->keterangan ?? '-',
        ];
    }

    /**
     * Styling header Excel
     */

    public function styles(Worksheet $sheet): ?array
{
    return [
        1 => [
            'font' => [
                'bold' => true,
            ],
        ],
    ];
}
}