<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}</title>

    <style>
        @page { size: A4 portrait; margin: 12px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #222; }

        h2 { text-align: center; margin: 0 0 4px 0; font-size: 15px; color: #4C1D95; }
        .periode { text-align: center; margin-bottom: 3px; font-size: 10px; color: #555; }
        .nama-user { text-align: center; margin-bottom: 10px; font-size: 10px; font-weight: bold; color: #334155; }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table th {
            background: #EDE9FE;
            color: #4C1D95;
            font-weight: bold;
            padding: 6px 4px;
            border: 1px solid #C4B5FD;
            font-size: 9px;
            text-align: center;
        }
        table td {
            border: 1px solid #CBD5E1;
            padding: 5px 4px;
            font-size: 9px;
            vertical-align: middle;
            text-align: center;
        }
        table td.left { text-align: left; padding-left: 8px; }

        /* Lebar kolom */
        th.col-tgl      { width: 65px; }
        th.col-hari     { width: 65px; }
        th.col-shift    { width: 80px; }
        th.col-status   { width: 100px; }
        th.col-jam      { width: 55px; }
        th.col-ket      { width: auto; }

        /* 🎨 PALET WARNA — SINKRON DENGAN EXCEL */
        .s-hadir    { background: #D1FAE5; color: #065F46; font-weight: bold; } /* Emerald */
        .s-malam    { background: #EDE9FE; color: #6D28D9; font-weight: bold; } /* Violet */
        .s-cuti     { background: #FEF3C7; color: #92400E; font-weight: bold; } /* Amber */
        .s-cap      { background: #FFEDD5; color: #C2410C; font-weight: bold; } /* Orange */
        .s-lupa     { background: #E0F2FE; color: #075985; font-weight: bold; } /* Sky */
        .s-sakit    { background: #FFE4E6; color: #9F1239; font-weight: bold; } /* Rose */
        .s-pending  { background: #FED7AA; color: #9A3412; font-weight: bold; } /* Peach */
        .s-libur    { background: #FEE2E2; color: #B91C1C; font-weight: bold; } /* Red */

        .footer {
            margin-top: 10px;
            padding: 8px 10px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            font-size: 8px;
            color: #475569;
            line-height: 1.5;
        }
        .footer strong { color: #1E293B; }
    </style>
</head>

<body>

    <h2>REKAP ABSENSI SAYA</h2>
    <div class="periode">
        Periode: {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}
    </div>
    <div class="nama-user">
        {{ $user->name }} — {{ $user->profil?->jabatan ?? 'PPNPN' }}
    </div>


    {{-- ============================================
         TABEL — SAMA PERSIS KAYAK EXCEL
    ============================================ --}}
    <table>
        <thead>
            <tr>
                <th class="col-tgl">Tanggal</th>
                <th class="col-hari">Hari</th>
                <th class="col-shift">Shift</th>
                <th class="col-status">Status</th>
                <th class="col-jam">Jam Masuk</th>
                <th class="col-jam">Jam Pulang</th>
                <th class="col-ket">Keterangan</th>
            </tr>
        </thead>

        <tbody>
            @php
                $cursor = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
                $akhir  = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth();
            @endphp

            @while($cursor->lte($akhir))

                @php
                    $tanggalKey = $cursor->format('Y-m-d');

                    $absensiHariIni    = $absensi[$tanggalKey] ?? null;
                    $cutiHariIni       = $cuti[$tanggalKey] ?? null;
                    $lupaHariIni       = $lupaAbsen[$tanggalKey] ?? null;
                    $suratSakitHariIni = $suratSakit[$tanggalKey] ?? null;

                    // Cek approval lupa absen
                    $lupaApproved = false;
                    if ($lupaHariIni) {
                        $approvedVals = ['approved', 'disetujui', 'diterima', 'setuju', 'accept', 'accepted', 'terima'];
                        foreach (['status', 'status_approval', 'status_pengajuan', 'approval_status'] as $f) {
                            if (isset($lupaHariIni->$f)
                                && in_array(strtolower(trim((string) $lupaHariIni->$f)), $approvedVals, true)) {
                                $lupaApproved = true;
                                break;
                            }
                        }
                    }

                    $absensiValid = $absensiHariIni
                        && $absensiHariIni->jam_masuk
                        && ($absensiHariIni->status_approval !== 'pending' || $lupaApproved);

                    $absensiPending = $absensiHariIni
                        && $absensiHariIni->status_approval === 'pending'
                        && !$lupaApproved;

                    // Default
                    $shift      = '-';
                    $status     = 'Belum Absen';
                    $classStatus = '';
                    $jamMasuk   = '-';
                    $jamPulang  = '-';
                    $keterangan = '-';

                    if ($absensiValid) {
                        $jamMasuk  = \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i');
                        $jamPulang = $absensiHariIni->jam_pulang
                            ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i')
                            : '-';
                        $shift = $absensiHariIni->shift === 'malam' ? 'Shift Malam' : 'Shift Pagi';
                    }

                    // Tentukan status (logic sama dengan Excel)
                    if ($cursor->isWeekend()) {
                        if ($absensiValid) {
                            $status = 'Hadir';
                            $keterangan = 'Hadir';
                            $classStatus = $absensiHariIni->shift === 'malam' ? 's-malam' : 's-hadir';
                        } else {
                            $status = 'Libur';
                            $keterangan = 'Hari libur';
                            $classStatus = 's-libur';
                        }
                    } else {
                        if ($absensiPending) {
                            $status = 'Pending';
                            $keterangan = 'Menunggu approval';
                            $classStatus = 's-pending';
                        } elseif ($suratSakitHariIni) {
                            $status = 'Sakit';
                            $keterangan = 'Surat sakit';
                            $classStatus = 's-sakit';
                        } elseif ($cutiHariIni) {
                            $jenisCuti = strtolower(trim($cutiHariIni->jenis_cuti ?? ''));
                            if (str_contains($jenisCuti, 'alasan') || str_contains($jenisCuti, 'penting')) {
                                $status = 'Alasan Penting';
                                $keterangan = 'Cuti alasan penting';
                                $classStatus = 's-cap';
                            } else {
                                $status = 'Cuti Tahunan';
                                $keterangan = 'Cuti tahunan';
                                $classStatus = 's-cuti';
                            }
                        } elseif ($absensiValid) {
                            $status = 'Hadir';
                            $keterangan = 'Hadir';
                            $classStatus = $absensiHariIni->shift === 'malam' ? 's-malam' : 's-hadir';
                        } elseif ($lupaApproved) {
                            $status = 'Hadir';
                            $keterangan = 'Lupa absen disetujui';
                            $shift = 'Shift Pagi';
                            $classStatus = 's-hadir';
                        } elseif ($lupaHariIni) {
                            $status = 'Lupa Absen';
                            $keterangan = 'Perbaikan absensi';
                            $classStatus = 's-lupa';
                        }
                    }

                    // Cek shift malam untuk kolom shift
                    $classShift = $shift === 'Shift Malam' ? 's-malam' : '';
                @endphp

                <tr>
                    <td>{{ $cursor->format('d/m/Y') }}</td>
                    <td>{{ $cursor->translatedFormat('l') }}</td>
                    <td class="{{ $classShift }}">{{ $shift }}</td>
                    <td class="{{ $classStatus }}">{{ $status }}</td>
                    <td class="{{ $classStatus }}">{{ $jamMasuk }}</td>
                    <td class="{{ $classStatus }}">{{ $jamPulang }}</td>
                    <td class="left">{{ $keterangan }}</td>
                </tr>

                @php $cursor->addDay(); @endphp
            @endwhile
        </tbody>
    </table>


    {{-- FOOTER --}}
    <div class="footer">
        <strong>Keterangan:</strong>
        H = Hadir pagi · M = Masuk malam · LIB = Hari libur · C = Cuti tahunan · CAP = Cuti alasan penting ·
        S = Surat sakit · LA = Lupa absen · P = Pending · L = Lembur
        <br>
        Sabtu dan Minggu otomatis dianggap hari libur, kecuali terdapat data absensi.
        Absensi pending tidak dihitung sebagai kehadiran.
        Lupa absen yang sudah disetujui otomatis dihitung sebagai kehadiran.
    </div>

</body>
</html>