@php
    $profil = \App\Models\ProfilSekolah::first();
    $logoPath = $profil && $profil->logo_path ? storage_path('app/public/' . $profil->logo_path) : null;
    $toBase64Image = static function (?string $path): ?string {
        if (! $path || ! file_exists($path)) {
            return null;
        }

        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'image/png',
        };

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    };

    $defaultLogoBase64 = $toBase64Image($logoPath);
    $leftLogoBase64 = $toBase64Image(storage_path('app/public/sultra_logo.png')) ?? $defaultLogoBase64;
    $rightLogoBase64 = $toBase64Image(storage_path('app/public/smk1kolaka.jpg')) ?? $defaultLogoBase64;

    $pasFotoDoc = $application->documents->firstWhere('jenis_dokumen', 'Pas Foto');
    $pasFotoPath = $pasFotoDoc ? storage_path('app/public/' . $pasFotoDoc->file_path) : null;
    $pasFotoBase64 = null;

    if ($pasFotoPath && file_exists($pasFotoPath)) {
        $ext = strtolower((string) pathinfo($pasFotoPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        $pasFotoBase64 = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($pasFotoPath));
    }

    $noPendaftaran = (string) ($application->nomor_pendaftaran ?? '-');
    $noPendaftaranAngka = preg_replace('/\D+/', '', $noPendaftaran) ?: '0';

    if (strlen($noPendaftaranAngka) > 4) {
        $noPendaftaranAngka = substr($noPendaftaranAngka, -4);
    }

    $noPendaftaranAngka = ltrim($noPendaftaranAngka, '0');
    $noPendaftaranAngka = $noPendaftaranAngka !== '' ? $noPendaftaranAngka : '0';

    $tahunAjaran = (string) ($application->period?->tahun_ajaran ?? (now()->format('Y') . '-' . ((int) now()->format('Y') + 1)));

    $formatDate = static function ($date): string {
        if ($date instanceof \Illuminate\Support\Carbon) {
            return $date->translatedFormat('d F Y');
        }

        if ($date instanceof \DateTimeInterface) {
            return \Illuminate\Support\Carbon::instance($date)->translatedFormat('d F Y');
        }

        return '-';
    };

    $period = $application->period;

    $contactEmail = trim((string) config('services.ppdb_contact.admin_email', 'admin@smkn1kolaka.sch.id'));
    $contactWhatsApp = preg_replace('/\D+/', '', (string) config('services.ppdb_contact.admin_whatsapp', ''));
    $contactPhone = trim((string) ($profil->nomor_telepon ?? '(0405) 231378'));

    if (is_string($contactWhatsApp) && str_starts_with($contactWhatsApp, '0')) {
        $contactWhatsApp = '62' . substr($contactWhatsApp, 1);
    }

    if (is_string($contactWhatsApp) && str_starts_with($contactWhatsApp, '8')) {
        $contactWhatsApp = '62' . $contactWhatsApp;
    }

    $contactPeople = $period?->contactPersons
        ? $period->contactPersons
            ->where('is_active', true)
            ->sortBy('urutan')
            ->values()
            ->map(function ($person) {
                $channels = [];

                if ($person->nomor_telepon) {
                    $channels[] = 'Telp: ' . $person->nomor_telepon;
                }

                if ($person->nomor_whatsapp) {
                    $channels[] = 'WA: ' . $person->nomor_whatsapp;
                }

                if ($person->email) {
                    $channels[] = 'Email: ' . $person->email;
                }

                return [
                    'nama' => strtoupper((string) $person->nama),
                    'jabatan' => strtoupper((string) ($person->jabatan ?: '-')),
                    'kontak' => $channels !== [] ? implode(' | ', $channels) : '-',
                ];
            })
        : collect();

    if ($contactPeople->isEmpty()) {
        $fallbackChannels = [];

        if ($contactPhone !== '') {
            $fallbackChannels[] = 'Telp: ' . $contactPhone;
        }

        if (is_string($contactWhatsApp) && $contactWhatsApp !== '') {
            $fallbackChannels[] = 'WA: ' . $contactWhatsApp;
        }

        if ($contactEmail !== '') {
            $fallbackChannels[] = 'Email: ' . $contactEmail;
        }

        $contactPeople = collect([
            [
                'nama' => 'PANITIA PPDB',
                'jabatan' => 'HELPDESK PPDB',
                'kontak' => $fallbackChannels !== [] ? implode(' | ', $fallbackChannels) : '-',
            ],
        ]);
    }

    $requirements = $period?->documentRequirements
        ? $period->documentRequirements
            ->where('is_active', true)
            ->sortBy('urutan')
            ->values()
            ->map(function ($item) {
                $label = (string) $item->nama_berkas;

                if ($item->keterangan) {
                    $label .= ' (' . $item->keterangan . ')';
                }

                return $label;
            })
            ->all()
        : [];

    if ($requirements === []) {
        $requirements = [
            'Membawa formulir pendaftaran cetak asli yang sudah ditandatangani.',
            'Fotokopi Kartu Keluarga (1 lembar) dan fotokopi Akta Kelahiran (1 lembar).',
            'Fotokopi rapor semester 1 s.d. 5 atau dokumen nilai rapor yang disahkan.',
            'Fotokopi SKL atau Ijazah (sesuai ketersediaan saat daftar ulang).',
            'Pas foto 3x4 latar biru (2 lembar).',
            'Map berwarna sesuai ketentuan jurusan pilihan utama.',
        ];
    }

    $importantDates = $period?->importantDates
        ? $period->importantDates
            ->where('is_active', true)
            ->sortBy('urutan')
            ->values()
            ->map(function ($item) use ($formatDate) {
                $dateLabel = $formatDate($item->tanggal_mulai);

                if ($item->tanggal_selesai && $item->tanggal_mulai && $item->tanggal_selesai->toDateString() !== $item->tanggal_mulai->toDateString()) {
                    $dateLabel .= ' s.d. ' . $formatDate($item->tanggal_selesai);
                }

                if ($item->keterangan) {
                    $dateLabel .= ' (' . $item->keterangan . ')';
                }

                return [
                    'label' => (string) $item->label,
                    'date' => $dateLabel,
                ];
            })
            ->all()
        : [];

    if ($importantDates === []) {
        $importantDates = [
            [
                'label' => 'Pendaftaran dibuka',
                'date' => $formatDate($application->period?->tanggal_mulai_pendaftaran),
            ],
            [
                'label' => 'Pendaftaran ditutup',
                'date' => $formatDate($application->period?->tanggal_selesai_pendaftaran),
            ],
            [
                'label' => 'Pengumuman hasil',
                'date' => $formatDate($application->period?->tanggal_pengumuman),
            ],
            [
                'label' => 'Daftar ulang mulai',
                'date' => $formatDate($application->period?->tanggal_mulai_daftar_ulang),
            ],
            [
                'label' => 'Daftar ulang selesai',
                'date' => $formatDate($application->period?->tanggal_selesai_daftar_ulang),
            ],
        ];
    }

    $mapColorPalette = ['Merah', 'Kuning', 'Hijau', 'Biru', 'Oranye', 'Ungu', 'Cokelat', 'Pink', 'Abu-abu', 'Putih'];

    $mapColorRows = $period?->mapColorRules
        ? $period->mapColorRules
            ->where('is_active', true)
            ->sortBy('urutan')
            ->values()
            ->map(function ($rule) {
                return [
                    'nama_jurusan' => (string) ($rule->programKeahlian?->nama_jurusan ?? 'Belum ditentukan'),
                    'warna_map' => (string) $rule->warna_map,
                ];
            })
        : collect();

    $selectedPrograms = array_filter([
        $application->pilihanProgram1?->nama_jurusan,
        $application->pilihanProgram2?->nama_jurusan,
        $application->pilihanProgram3?->nama_jurusan,
    ]);

    if ($mapColorRows->isEmpty()) {
        $programs = \App\Models\ProgramKeahlian::query()->tampil()->orderBy('nama_jurusan')->get(['nama_jurusan']);

        $mapColorRows = $programs->values()->map(function ($program, $index) use ($mapColorPalette) {
            return [
                'nama_jurusan' => (string) $program->nama_jurusan,
                'warna_map' => $mapColorPalette[$index % count($mapColorPalette)],
            ];
        });

        $fallbackPrograms = $selectedPrograms !== [] ? array_values($selectedPrograms) : ['Belum ditentukan'];

        if ($mapColorRows->isEmpty()) {
            $mapColorRows = collect($fallbackPrograms)->values()->map(function ($namaJurusan, $index) use ($mapColorPalette) {
                return [
                    'nama_jurusan' => (string) $namaJurusan,
                    'warna_map' => $mapColorPalette[$index % count($mapColorPalette)],
                ];
            });
        }
    }

    $tahunAjaranLabel = str_replace('/', '-', $tahunAjaran);
    $jenisKelaminLabel = $application->jenis_kelamin === 'L' ? 'LAKI - LAKI' : 'PEREMPUAN';
    $pilihanJurusan1 = (string) ($application->pilihanProgram1?->nama_jurusan ?? '-');
    $pilihanJurusan2 = (string) ($application->pilihanProgram2?->nama_jurusan ?? '-');
    $pilihanJurusan3 = (string) ($application->pilihanProgram3?->nama_jurusan ?? '-');
    $tanggalCetak = now()->translatedFormat('F Y');
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Formulir Pendaftaran SPMB - {{ $application->nama_lengkap }}</title>
    <style>
        @page {
            size: 210mm 330mm;
            margin: 12mm 11mm 12mm 11mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
            color: #111;
            line-height: 1.25;
            margin: 20px;
        }

        .sheet {
            width: 100%;
        }

        .page-break {
            page-break-before: always;
        }

        .page-one .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .page-one .kop-table td {
            border: 0;
            vertical-align: middle;
            padding: 0;
        }

        .page-one .logo-cell {
            width: 72px;
            text-align: center;
        }

        .page-one .logo-cell img {
            width: 58px;
            height: auto;
        }

        .page-one .kop-text {
            text-align: center;
        }

        .page-one .kop-text .line1,
        .page-one .kop-text .line2 {
            font-size: 10.8pt;
            font-weight: 700;
        }

        .page-one .kop-text .line3 {
            font-size: 15pt;
            font-weight: 700;
            letter-spacing: 0.45px;
        }

        .page-one .kop-text .line4,
        .page-one .kop-text .line5 {
            font-size: 9pt;
        }

        .page-one .kop-line {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
            margin: 4px 0 6px 0;
        }

        .page-one .header-grid {
            width: 100%;
            margin-bottom: 3px;
        }

        .page-one .header-panel {
            float: right;
            width: 112px;
            margin-left: 8px;
        }

        .page-one .title-cell {
            min-height: 38px;
        }

        .page-one .float-clear {
            clear: both;
        }

        .page-one .form-title {
            text-align: left;
            font-size: 11pt;
            font-weight: 700;
            margin-top: 2px;
        }

        .page-one .form-subtitle {
            text-align: left;
            font-size: 10.8pt;
            font-weight: 700;
            margin-top: 1px;
        }

        .page-one .registration-box {
            width: 112px;
            border: 1px solid #111;
            text-align: center;
            margin-bottom: 5px;
        }

        .page-one .registration-label {
            border-bottom: 1px solid #111;
            font-size: 8.2pt;
            font-weight: 700;
            padding: 2px 4px;
            line-height: 1.2;
        }

        .page-one .registration-number {
            font-size: 16pt;
            font-weight: 700;
            padding: 7px 4px;
        }

        .page-one .photo-box {
            width: 112px;
            height: 116px;
            border: 1px solid #111;
            display: table;
            border-collapse: separate;
        }

        .page-one .photo-box-inner {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            padding: 6px;
            font-size: 14pt;
            line-height: 1.15;
        }

        .page-one .photo-label {
            font-size: 17pt;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 8px;
        }

        .page-one .photo-size {
            font-size: 13pt;
            line-height: 1;
        }

        .page-one .photo-box img {
            width: 80px;
            height: 98px;
            object-fit: cover;
        }

        .page-one .section-title {
            margin: 7px 0 3px;
            font-size: 11pt;
            font-weight: 700;
        }

        .page-one .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .page-one .info-table td {
            border: 0;
            padding: 0.7px 0;
            vertical-align: top;
            font-size: 9.9pt;
        }

        .page-one .num-col {
            width: 20px;
            text-align: right;
            padding-right: 4px;
        }

        .page-one .label-col {
            width: 215px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .page-one .sep-col {
            width: 10px;
            text-align: center;
        }

        .page-one .value-col {
            font-weight: 400;
        }

        .page-one .achievement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1px;
        }

        .page-one .achievement-table th,
        .page-one .achievement-table td {
            border: 1px solid #111;
            padding: 3px 5px;
            font-size: 9.7pt;
        }

        .page-one .achievement-table th {
            text-align: center;
            font-weight: 700;
        }

        .page-one .jurusan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .page-one .jurusan-table td {
            border: 0;
            padding: 0.7px 0;
            font-size: 10.2pt;
        }

        .page-one .jurusan-label {
            width: 145px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .page-one .parent-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1px;
        }

        .page-one .parent-grid td {
            width: 50%;
            border: 0;
            vertical-align: top;
        }

        .page-one .parent-grid td:first-child {
            padding-right: 9px;
        }

        .page-one .parent-grid td:last-child {
            padding-left: 9px;
        }

        .page-one .parent-table {
            width: 100%;
            border-collapse: collapse;
        }

        .page-one .parent-table td {
            border: 0;
            padding: 0.55px 0;
            font-size: 9.8pt;
            vertical-align: top;
        }

        .page-one .parent-label {
            width: 154px;
        }

        .page-one .declaration-title {
            margin-top: 8px;
            font-size: 11pt;
            font-weight: 700;
            text-align: center;
        }

        .page-one .declaration-intro {
            margin-top: 5px;
            font-size: 10pt;
        }

        .page-one .declaration-list {
            margin: 4px 0 0 16px;
            padding: 0;
        }

        .page-one .declaration-list li {
            margin-bottom: 2px;
            font-size: 9.8pt;
            line-height: 1.28;
        }

        .page-one .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .page-one .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
        }

        .page-one .sign-space {
            height: 47px;
        }

        .page-one .sign-line {
            border-bottom: 1px dotted #111;
            width: 220px;
            margin: 0 auto;
            padding-bottom: 1px;
        }

        .security-box {
            margin-top: 10px;
            border: 1px dashed #222;
            padding: 6px 8px;
            font-size: 8pt;
            line-height: 1.45;
        }

        .lampiran-title {
            text-align: center;
            font-size: 12pt;
            font-weight: 700;
            margin-top: 2px;
            margin-bottom: 2px;
        }

        .lampiran-subtitle {
            text-align: center;
            font-size: 9.5pt;
            margin-bottom: 8px;
        }

        .list-box {
            border: 1px solid #111;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .list-box h4 {
            font-size: 10.4pt;
            margin-bottom: 4px;
        }

        .list-box ol,
        .list-box ul {
            margin-left: 18px;
        }

        .list-box li {
            margin-bottom: 2px;
            font-size: 9.8pt;
        }

        .footer-note {
            margin-top: 6px;
            font-size: 8.5pt;
            line-height: 1.4;
        }

        .boxed-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .boxed-table th,
        .boxed-table td {
            border: 1px solid #111;
            padding: 4px 6px;
            font-size: 9.6pt;
        }

        .boxed-table th {
            background: #f2f2f2;
            text-align: center;
            font-weight: 700;
        }

        .muted-note {
            margin-top: 4px;
            font-size: 8.5pt;
        }
    </style>
</head>

<body>
    <div class="sheet page-one">
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    @if($leftLogoBase64)
                        <img src="{{ $leftLogoBase64 }}" alt="Logo Sultra">
                    @endif
                </td>
                <td class="kop-text">
                    <div class="line1">PEMERINTAH PROVINSI SULAWESI TENGGARA</div>
                    <div class="line2">DINAS PENDIDIKAN DAN KEBUDAYAAN</div>
                    <div class="line3">SMK NEGERI 1 KOLAKA</div>
                    <div class="line4">Jl. Pendidikan No. 49, Telp./Fax. (0405) 231378, Kab. Kolaka, 93517</div>
                    <div class="line5">Email: smk1kolaka@gmail.com</div>
                </td>
                <td class="logo-cell">
                    @if($rightLogoBase64)
                        <img src="{{ $rightLogoBase64 }}" alt="Logo SMK">
                    @endif
                </td>
            </tr>
        </table>

        <div class="kop-line"></div>

        <div class="header-grid">
            <div class="header-panel">
                <div style="width: 116px;">
                    <div class="registration-box">
                        <div class="registration-label">NO. PENDAFTARAN</div>
                        <div class="registration-number">{{ $noPendaftaranAngka }}</div>
                    </div>
                    <div class="photo-box">
                        <div class="photo-box-inner">
                            @if($pasFotoBase64)
                                <img src="{{ $pasFotoBase64 }}" alt="Pas Foto">
                            @else
                                <div class="photo-label">PHOTO</div>
                                <div class="photo-size">3 X 4</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="title-cell">
                <div class="form-title">FORMULIR PENDAFTARAN SPMB (SISTEM PENERIMAAN MURID BARU)</div>
                <div class="form-subtitle">SMK NEGERI 1 KOLAKA TP. {{ $tahunAjaranLabel }}</div>
            </div>
        </div>

        <div class="section-title">A. DATA CALON PESERTA DIDIK</div>
        <table class="info-table">
            <tr>
                <td class="num-col">1.</td>
                <td class="label-col">Nama lengkap</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ strtoupper((string) $application->nama_lengkap) }}</td>
            </tr>
            <tr>
                <td class="num-col">2.</td>
                <td class="label-col">NISN</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->nisn ?: '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">3.</td>
                <td class="label-col">Tempat/tanggal lahir</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->tempat_lahir ?: '-' }}, {{ optional($application->tanggal_lahir)->format('d-m-Y') ?: '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">4.</td>
                <td class="label-col">Asal sekolah</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ strtoupper((string) ($application->asal_sekolah ?: '-')) }}</td>
            </tr>
            <tr>
                <td class="num-col">5.</td>
                <td class="label-col">Alamat sekolah</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->alamat_sekolah ?: '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">6.</td>
                <td class="label-col">Jenis kelamin</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $jenisKelaminLabel }}</td>
            </tr>
            <tr>
                <td class="num-col">7.</td>
                <td class="label-col">Jumlah saudara</td>
                <td class="sep-col">:</td>
                <td class="value-col">Anak ke : {{ $application->anak_ke ?: '-' }} dari : {{ $application->jumlah_saudara ?: '-' }} saudara</td>
            </tr>
            <tr>
                <td class="num-col">8.</td>
                <td class="label-col">Alamat rumah</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->alamat_lengkap ?: '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">9.</td>
                <td class="label-col">RT/RW, Kel., Kec.</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->rt_rw ?: '-' }} | Kel : {{ $application->kelurahan ?: '-' }} | Kec : {{ $application->kecamatan ?: '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">10.</td>
                <td class="label-col">Tinggi badan</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->tinggi_badan ? $application->tinggi_badan . ' cm' : '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">11.</td>
                <td class="label-col">Berat badan</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->berat_badan ? $application->berat_badan . ' kg' : '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">12.</td>
                <td class="label-col">Golongan darah</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->gol_darah ?: '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">13.</td>
                <td class="label-col">Ukuran seragam</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->ukuran_seragam ?: '-' }}</td>
            </tr>
            <tr>
                <td class="num-col">14.</td>
                <td class="label-col">Prestasi yang pernah di raih</td>
                <td class="sep-col">:</td>
                <td class="value-col"></td>
            </tr>
        </table>

        <div class="float-clear"></div>

        <table class="achievement-table">
            <thead>
                <tr>
                    <th style="width: 58px;">NO</th>
                    <th>Prestasi Yang Pernah Di Raih:</th>
                    <th style="width: 128px;">Juara</th>
                    <th style="width: 128px;">Tingkat</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $achievements = $application->achievements ?? collect();
                @endphp
                @for($i = 0; $i < 3; $i++)
                    <tr>
                        <td style="text-align: center;">{{ $i + 1 }}</td>
                        <td>{{ $achievements[$i]->achievement_name ?? '' }}</td>
                        <td>{{ $achievements[$i]->achievement_rank ?? '' }}</td>
                        <td>{{ $achievements[$i]->achievement_level ?? '' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="section-title">B. PILIHAN JURUSAN :</div>
        <table class="jurusan-table">
            <tr>
                <td class="jurusan-label">JURUSAN KE - 1</td>
                <td class="sep-col">:</td>
                <td>{{ $pilihanJurusan1 }}</td>
            </tr>
            <tr>
                <td class="jurusan-label">JURUSAN KE - 2</td>
                <td class="sep-col">:</td>
                <td>{{ $pilihanJurusan2 }}</td>
            </tr>
            <tr>
                <td class="jurusan-label">JURUSAN KE - 3</td>
                <td class="sep-col">:</td>
                <td>{{ $pilihanJurusan3 }}</td>
            </tr>
        </table>

        <div class="section-title">C. DATA ORANG TUA</div>
        <table class="parent-grid">
            <tr>
                <td>
                    <table class="parent-table">
                        <tr>
                            <td class="parent-label">Nama Ayah</td>
                            <td class="sep-col">:</td>
                            <td>{{ strtoupper((string) ($application->nama_ayah ?: '-')) }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Tempat/Tanggal Lahir Ayah</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->tempat_tanggal_lahir_ayah ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Pendidikan Terakhir Ayah</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->pendidikan_terakhir_ayah ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Pekerjaan Ayah</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->pekerjaan_ayah ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Penghasilan Ayah</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->penghasilan_ayah ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Alamat Ayah</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->alamat_ayah ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Kelurahan Ayah</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->kelurahan_ayah ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Kecamatan Ayah</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->kecamatan_ayah ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">No. Tlp/HP/Wa Ayah</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->nomor_hp_ayah ?: '-' }}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="parent-table">
                        <tr>
                            <td class="parent-label">Nama Ibu</td>
                            <td class="sep-col">:</td>
                            <td>{{ strtoupper((string) ($application->nama_ibu ?: '-')) }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Tempat/Tanggal Lahir Ibu</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->tempat_tanggal_lahir_ibu ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Pendidikan Terakhir Ibu</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->pendidikan_terakhir_ibu ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Pekerjaan Ibu</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->pekerjaan_ibu ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Penghasilan Ibu</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->penghasilan_ibu ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Alamat Ibu</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->alamat_ibu ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Kelurahan Ibu</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->kelurahan_ibu ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">Kecamatan Ibu</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->kecamatan_ibu ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="parent-label">No. Tlp/HP/Wa Ibu</td>
                            <td class="sep-col">:</td>
                            <td>{{ $application->nomor_hp_ibu ?: '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="declaration-title">PERNYATAAN CALON MURID BARU</div>
        <p class="declaration-intro">Saya bertanggung jawab kebenaran data dan bersedia untuk :</p>
        <ol class="declaration-list">
            <li>Dicabut status peserta tes calon murid baru di SMK Negeri 1 Kolaka jika memberikan data palsu.</li>
            <li>Belajar sungguh-sungguh serta mematuhi seluruh peraturan dan tata tertib sekolah hingga tamat.</li>
            <li>Berakhlak mulia di mana pun berada serta menjaga nama baik diri sendiri, keluarga, dan sekolah.</li>
            <li>Orang tua/wali bersedia bekerja sama dengan pihak sekolah dalam pembinaan calon murid baru.</li>
        </ol>

        <table class="signature-table">
            <tr>
                <td>
                    <p>Mengetahui,</p>
                    <p>Orang Tua / Wali</p>
                    <div class="sign-space"></div>
                    <div class="sign-line"></div>
                    <p style="margin-top: 4px;">Tanda Tangan Dan Nama Jelas</p>
                </td>
                <td>
                    <p>Kolaka, {{ $tanggalCetak }}</p>
                    <p>Calon Murid Baru</p>
                    <div class="sign-space"></div>
                    <div class="sign-line"></div>
                    <p style="margin-top: 4px;">Tanda Tangan Dan Nama Jelas</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <div class="sheet">
        <div class="lampiran-title">LAMPIRAN INFORMASI PENTING CALON MURID BARU</div>
        <div class="lampiran-subtitle">Gunakan halaman ini sebagai panduan persyaratan dan jadwal resmi SPMB</div>

        <div class="list-box">
            <h4>1. Persyaratan berkas saat verifikasi/daftar ulang</h4>
            <ol>
                @foreach($requirements as $requirement)
                    <li>{{ $requirement }}</li>
                @endforeach
            </ol>
        </div>

        <div class="list-box">
            <h4>2. Tanggal-tanggal penting</h4>
            <table class="boxed-table" style="margin-top: 2px;">
                <thead>
                    <tr>
                        <th style="width: 48px;">No</th>
                        <th>Kegiatan</th>
                        <th style="width: 220px;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($importantDates as $index => $item)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ $item['label'] }}</td>
                            <td style="text-align: center;">{{ $item['date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="muted-note">Catatan: Jadwal mengikuti periode aktif {{ $application->period?->nama_periode ?? 'SPMB berjalan' }} dan dapat diperbarui panitia.</p>
        </div>

        <div class="list-box">
            <h4>3. Kontak person panitia</h4>
            <table class="boxed-table" style="margin-top: 2px;">
                <thead>
                    <tr>
                        <th style="width: 48px;">No</th>
                        <th>Nama</th>
                        <th style="width: 180px;">Jabatan</th>
                        <th style="width: 260px;">Kontak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contactPeople as $index => $person)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ $person['nama'] }}</td>
                            <td style="text-align: center;">{{ $person['jabatan'] }}</td>
                            <td>{{ $person['kontak'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="list-box">
            <h4>4. Ketentuan warna map per jurusan</h4>
            <table class="boxed-table" style="margin-top: 2px;">
                <thead>
                    <tr>
                        <th style="width: 48px;">No</th>
                        <th>Program keahlian</th>
                        <th style="width: 150px;">Warna map</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mapColorRows as $index => $row)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{!! in_array($row['nama_jurusan'], $selectedPrograms, true) ? '<strong>' . e($row['nama_jurusan']) . ' (Pilihan Anda)</strong>' : e($row['nama_jurusan']) !!}</td>
                            <td style="text-align: center;">{{ $row['warna_map'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="muted-note">Jika terdapat perubahan warna map dari panitia, gunakan ketentuan terbaru pada pengumuman resmi sekolah.</p>
        </div>

        <div class="footer-note">
            Dokumen ini dicetak otomatis oleh sistem SPMB SMK Negeri 1 Kolaka.<br>
            Simpan dokumen ini dan bawa saat proses verifikasi serta daftar ulang.
        </div>

        @if(isset($documentSecurity) && is_array($documentSecurity))
            <div class="security-box" style="margin-top: 10px;">
                <strong>Referensi Dokumen</strong><br>
                Nomor pendaftaran: {{ $application->nomor_pendaftaran ?: '-' }} | 
                Kode dokumen: {{ $documentSecurity['document_code'] ?? '-' }}
            </div>
        @endif
    </div>
</body>

</html>