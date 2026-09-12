<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Rekap Lembur</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 11px;
        }

        .info {
            margin-bottom: 15px;
        }

        .info table {
            width: 100%;
            border: none;
        }

        .info td {
            border: none;
            padding: 3px 0;
        }

        .summary {
            margin-bottom: 15px;
        }

        .summary-box {
            border: 1px solid #ddd;
            padding: 8px 12px;
            width: 150px;
            text-align: center;
        }

        .summary-title {
            font-size: 9px;
            color: #666;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
        }

        table.data td {
            border: 1px solid #d1d5db;
            padding: 7px 6px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>REKAP LEMBUR PEGAWAI</h1>

        <p>
            Periode:
            {{ $tanggalAwal->translatedFormat('F Y') }}
        </p>
    </div>


    {{-- INFORMASI --}}
    <div class="info">
        <table>
            <tr>
                <td style="width: 120px;">Periode</td>
                <td>: {{ $tanggalAwal->translatedFormat('F Y') }}</td>
            </tr>

            <tr>
                <td>Total Lembur</td>
                <td>: {{ $jumlahLembur }} kali</td>
            </tr>
        </table>
    </div>


    {{-- TABEL DATA --}}
    <table class="data">

        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 150px;">Nama Pegawai</th>
                <th style="width: 65px;">Mulai</th>
                <th style="width: 65px;">Selesai</th>
                <th>Kegiatan</th>
                <th>Keterangan</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($dataLembur as $index => $item)

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td class="center">
                        {{ $item->tanggal
                            ? $item->tanggal->format('d/m/Y')
                            : '-' }}
                    </td>

                    <td>
                        {{ $item->user?->name ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $item->jam_mulai
                            ? date('H:i', strtotime($item->jam_mulai))
                            : '-' }}
                    </td>

                    <td class="center">
                        {{ $item->jam_selesai
                            ? date('H:i', strtotime($item->jam_selesai))
                            : '-' }}
                    </td>

                    <td>
                        {{ $item->kegiatan ?? '-' }}
                    </td>

                    <td>
                        {{ $item->keterangan ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="center">
                        Tidak ada data lembur pada periode ini.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- FOOTER --}}
    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>