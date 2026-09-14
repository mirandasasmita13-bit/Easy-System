<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}</title>

    <style>
        @page { size: A3 landscape; margin: 12px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #222; }

        h2 { text-align: center; margin: 0 0 5px 0; font-size: 16px; color: #4C1D95; }
        .periode { text-align: center; margin-bottom: 12px; font-size: 10px; color: #555; }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #94A3B8; padding: 2px; text-align: center; vertical-align: middle; }

        th { font-weight: bold; background: #EDE9FE; color: #4C1D95; }

        th.no      { width: 25px; }
        th.nama    { width: 120px; }
        th.jabatan { width: 80px; }
        th.tgl     { width: 28px; }
        th.ringkas { width: 30px; }

        td.nama, td.jabatan { text-align: left; white-space: nowrap; overflow: hidden; }

        /* 🎨 PALET WARNA */
        .hadir   { background: #D1FAE5; color: #065F46; font-weight: bold; }
        .malam   { background: #EDE9FE; color: #6D28D9; font-weight: bold; }
        .cuti    { background: #FEF3C7; color: #92400E; font-weight: bold; }
        .cap     { background: #FFEDD5; color: #C2410C; font-weight: bold; }
        .lupa    { background: #E0F2FE; color: #075985; font-weight: bold; }
        .sakit   { background: #FFE4E6; color: #9F1239; font-weight: bold; }
        .pending { background: #FED7AA; color: #9A3412; font-weight: bold; }
        .libur   { background: #FEE2E2; color: #B91C1C; font-weight: bold; }
        .kosong  { background: #FFFFFF; color: #94A3B8; }

        .weekend-header { background: #FFE4E6; color: #9F1239; }
        .ringkasan { font-weight: bold; background: #F8FAFC; color: #334155; }

        /* Kode & jam dalam cell — 3 baris vertikal */
        .kode   { display: block; font-size: 8px; font-weight: bold; line-height: 1.1; }
        .jam-in { display: block; font-size: 6.5px; line-height: 1.1; margin-top: 1px; color: #475569; }
        .jam-out{ display: block; font-size: 6.5px; line-height: 1.1; color: #64748B; }

        .footer { margin-top: 10px; font-size: 8px; color: #666; }
    </style>
</head>

<body>

    <h2>REKAP ABSENSI PPNPN</h2>
    <div class="periode">Periode: {{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}</div>

    <table>
        <thead>
            <tr>
                <th class="no">No</th>
                <th class="nama">Nama PPNPN</th>
                <th class="jabatan">Jabatan</th>

                @foreach ($tanggal as $tgl)
                    <th class="tgl {{ $tgl->isWeekend() ? 'weekend-header' : '' }}">
                        {{ $tgl->format('d') }}<br>
                        <span style="font-size:7px;">{{ $tgl->translatedFormat('D') }}</span>
                    </th>
                @endforeach

                <th class="ringkas">Hadir</th>
                <th class="ringkas">Cuti</th>
                <th class="ringkas">CAP</th>
                <th class="ringkas">Sakit</th>
                <th class="ringkas">Lupa</th>
                <th class="ringkas">Pending</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($ppnpn as $index => $user)

                @php
                    $jumlahHadir = 0; $jumlahCuti = 0; $jumlahCAP = 0;
                    $jumlahSakit = 0; $jumlahLupa = 0; $jumlahPending = 0;
                @endphp

                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="nama">{{ $user->name }}</td>
                    <td class="jabatan">{{ $user->profil?->jabatan ?? 'PPNPN' }}</td>

                    @foreach ($tanggal as $tgl)
                        @php
                            $tanggalKey = $tgl->format('Y-m-d');
                            $absensiHariIni = $absensi[$user->id][$tanggalKey] ?? null;
                            $cutiHariIni    = $cuti[$user->id][$tanggalKey] ?? null;
                            $lupaHariIni    = $lupaAbsen[$user->id][$tanggalKey] ?? null;
                            $sakitHariIni   = $suratSakit[$user->id][$tanggalKey] ?? null;

                            $kode = '-'; $class = 'kosong';
                            $jamMasukText = null; $jamPulangText = null;

                            $lupaApproved = false;
                            if ($lupaHariIni) {
                                $approvedVals = ['approved', 'disetujui', 'diterima', 'setuju', 'accept', 'accepted', 'terima'];
                                foreach (['status', 'status_approval', 'status_pengajuan', 'approval_status'] as $f) {
                                    if (isset($lupaHariIni->$f) && in_array(strtolower(trim((string) $lupaHariIni->$f)), $approvedVals, true)) {
                                        $lupaApproved = true; break;
                                    }
                                }
                            }

                            $absensiValid = $absensiHariIni && $absensiHariIni->jam_masuk
                                && ($absensiHariIni->status_approval !== 'pending' || $lupaApproved);
                            $absensiPending = $absensiHariIni
                                && $absensiHariIni->status_approval === 'pending'
                                && !$lupaApproved;
                        @endphp

                        @if ($tgl->isWeekend())
                            @if ($absensiValid)
                                @php
                                    if ($absensiHariIni->shift === 'malam') { $kode = 'M'; $class = 'malam'; }
                                    else { $kode = 'H'; $class = 'hadir'; }
                                    $jumlahHadir++;
                                    $jamMasukText  = \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i');
                                    $jamPulangText = $absensiHariIni->jam_pulang ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') : null;
                                @endphp
                            @elseif ($absensiPending)
                                @php
                                    $kode = 'P'; $class = 'pending'; $jumlahPending++;
                                    $jamMasukText = $absensiHariIni->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                                @endphp
                            @else
                                @php $kode = 'LIB'; $class = 'libur'; @endphp
                            @endif
                        @else
                            @if ($sakitHariIni)
                                @php $kode = 'S'; $class = 'sakit'; $jumlahSakit++; @endphp
                            @elseif ($cutiHariIni)
                                @if ($cutiHariIni->jenis_cuti === 'alasan_penting')
                                    @php $kode = 'CAP'; $class = 'cap'; $jumlahCAP++; @endphp
                                @else
                                    @php $kode = 'C'; $class = 'cuti'; $jumlahCuti++; @endphp
                                @endif
                            @elseif ($absensiValid)
                                @php
                                    if ($absensiHariIni->shift === 'malam') { $kode = 'M'; $class = 'malam'; }
                                    else { $kode = 'H'; $class = 'hadir'; }
                                    $jumlahHadir++;
                                    $jamMasukText  = \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i');
                                    $jamPulangText = $absensiHariIni->jam_pulang ? \Carbon\Carbon::parse($absensiHariIni->jam_pulang)->format('H:i') : null;
                                @endphp
                            @elseif ($lupaApproved)
                                @php $kode = 'H'; $class = 'hadir'; $jumlahHadir++; @endphp
                            @elseif ($lupaHariIni && !$absensiPending)
                                @php $kode = 'LA'; $class = 'lupa'; $jumlahLupa++; @endphp
                            @elseif ($absensiPending)
                                @php
                                    $kode = 'P'; $class = 'pending'; $jumlahPending++;
                                    $jamMasukText = $absensiHariIni->jam_masuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : null;
                                @endphp
                            @endif
                        @endif

                        <td class="{{ $class }}">
                            <span class="kode">{{ $kode }}</span>
                            @if($jamMasukText)<span class="jam-in">↓{{ $jamMasukText }}</span>@endif
                            @if($jamPulangText)<span class="jam-out">↑{{ $jamPulangText }}</span>@endif
                        </td>
                    @endforeach

                    <td class="ringkasan">{{ $jumlahHadir }}</td>
                    <td class="ringkasan">{{ $jumlahCuti }}</td>
                    <td class="ringkasan">{{ $jumlahCAP }}</td>
                    <td class="ringkasan">{{ $jumlahSakit }}</td>
                    <td class="ringkasan">{{ $jumlahLupa }}</td>
                    <td class="ringkasan">{{ $jumlahPending }}</td>
                </tr>

            @empty
                <tr><td colspan="{{ $tanggal->count() + 9 }}">Tidak ada data PPNPN.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <strong>Keterangan:</strong>
        H = Hadir pagi · M = Masuk malam · LIB = Hari libur · C = Cuti tahunan · CAP = Cuti alasan penting ·
        S = Surat sakit · LA = Lupa absen · P = Pending ·
        <strong>↓ = Jam masuk · ↑ = Jam pulang.</strong>
        <br>
        Sabtu dan Minggu otomatis dianggap hari libur, kecuali terdapat data absensi.
        Absensi pending tidak dihitung sebagai kehadiran.
        Lupa absen yang sudah disetujui otomatis dihitung sebagai kehadiran.
    </div>

</body>
</html>