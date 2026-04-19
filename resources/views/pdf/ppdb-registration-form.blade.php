@php
    $profil = \App\Models\ProfilSekolah::first();
    $logoPath = $profil && $profil->logo_path ? storage_path('app/public/' . $profil->logo_path) : null;
    $logoBase64 = null;

    if ($logoPath && file_exists($logoPath)) {
        $ext = strtolower((string) pathinfo($logoPath, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'image/png',
        };

        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($logoPath));
    }

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
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Formulir Pendaftaran SPMB - {{ $application->nama_lengkap }}</title>
    <style>
        @page {
            size: 210mm 330mm;
            margin: 13mm 12mm 13mm 12mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 10.5pt;
            color: #111;
            line-height: 1.35;
        }

        .sheet {
            width: 100%;
        }

        .page-break {
            page-break-before: always;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .kop-table td {
            border: 0;
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell {
            width: 64px;
            text-align: center;
        }

        .logo-cell img {
            width: 54px;
            height: auto;
        }

        .kop-text {
            text-align: center;
        }

        .kop-text .line1,
        .kop-text .line2 {
            font-size: 10.5pt;
            font-weight: 700;
        }

        .kop-text .line3 {
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 0.7px;
        }

        .kop-text .line4,
        .kop-text .line5 {
            font-size: 8.7pt;
        }

        .kop-line {
            border-top: 2.3px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
            margin: 4px 0 8px 0;
        }

        .header-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .header-grid td {
            border: 0;
            vertical-align: top;
        }

        .title-cell {
            padding-right: 8px;
        }

        .form-title {
            text-align: center;
            font-size: 10.8pt;
            font-weight: 700;
            margin-top: 4px;
        }

        .form-subtitle {
            text-align: center;
            font-size: 10.6pt;
            font-weight: 700;
            margin-top: 2px;
        }

        .registration-box {
            width: 112px;
            border: 1px solid #111;
            text-align: center;
        }

        .registration-label {
            border-bottom: 1px solid #111;
            font-size: 8pt;
            font-weight: 700;
            padding: 2px 4px;
            line-height: 1.2;
        }

        .registration-number {
            border-bottom: 1px solid #111;
            font-size: 16pt;
            font-weight: 700;
            padding: 4px 4px;
        }

        .photo-box {
            height: 102px;
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .photo-box-inner {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            padding: 4px;
        }

        .photo-box img {
            width: 78px;
            height: 94px;
            object-fit: cover;
        }

        .section-title {
            margin: 9px 0 4px;
            font-size: 10.7pt;
            font-weight: 700;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            border: 0;
            padding: 1.2px 0;
            vertical-align: top;
            font-size: 10pt;
        }

        .num-col {
            width: 20px;
            text-align: right;
            padding-right: 4px;
        }

        .label-col {
            width: 195px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .sep-col {
            width: 10px;
            text-align: center;
        }

        .value-col {
            font-weight: 400;
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

        .statement-box {
            margin-top: 8px;
            border: 1px solid #111;
            padding: 6px 8px;
        }

        .statement-title {
            font-size: 10.4pt;
            font-weight: 700;
            text-align: center;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .statement-box ol {
            margin: 4px 0 0 18px;
            padding: 0;
        }

        .statement-box li {
            margin-bottom: 2px;
            font-size: 9.6pt;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9.8pt;
        }

        .sign-space {
            height: 52px;
        }

        .sign-line {
            border-bottom: 1px solid #111;
            width: 205px;
            margin: 0 auto;
            padding-bottom: 2px;
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
    </style>
</head>

<body>
    <div class="sheet">
        <table class="kop-table">
            <tr>
                <td class="logo-cell">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo Sekolah">
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
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo Sekolah">
                    @endif
                </td>
            </tr>
        </table>

        <div class="kop-line"></div>

        <table class="header-grid">
            <tr>
                <td class="title-cell">
                    <div class="form-title">FORMULIR PENDAFTARAN SPMB (SISTEM PENERIMAAN MURID BARU)</div>
                    <div class="form-subtitle">SMK NEGERI 1 KOLAKA TP. {{ $tahunAjaran }}</div>
                </td>
                <td style="width: 116px;">
                    <div class="registration-box">
                        <div class="registration-label">NO. PENDAFTARAN</div>
                        <div class="registration-number">{{ $noPendaftaranAngka }}</div>
                        <div class="photo-box">
                            <div class="photo-box-inner">
                                @if($pasFotoBase64)
                                    <img src="{{ $pasFotoBase64 }}" alt="Pas Foto">
                                @endif
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

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
                <td class="value-col">{{ $application->jenis_kelamin === 'L' ? 'LAKI-LAKI' : 'PEREMPUAN' }}</td>
            </tr>
            <tr>
                <td class="num-col">7.</td>
                <td class="label-col">Jumlah saudara</td>
                <td class="sep-col">:</td>
                <td class="value-col">Anak ke-{{ $application->anak_ke ?: '-' }} dari {{ $application->jumlah_saudara ?: '-' }} saudara</td>
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
                <td class="value-col">{{ $application->rt_rw ?: '-' }}, Kel. {{ $application->kelurahan ?: '-' }}, Kec. {{ $application->kecamatan ?: '-' }}</td>
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
        </table>

        <div class="section-title">B. PRESTASI DAN PILIHAN JURUSAN</div>
        <table class="boxed-table">
            <thead>
                <tr>
                    <th style="width: 34px;">No</th>
                    <th>Prestasi yang pernah diraih</th>
                    <th style="width: 94px;">Juara</th>
                    <th style="width: 100px;">Tingkat</th>
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

        <table class="info-table" style="margin-top: 5px;">
            <tr>
                <td class="num-col"></td>
                <td class="label-col">Jurusan pilihan 1</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->pilihanProgram1?->nama_jurusan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col"></td>
                <td class="label-col">Jurusan pilihan 2</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->pilihanProgram2?->nama_jurusan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="num-col"></td>
                <td class="label-col">Jurusan pilihan 3</td>
                <td class="sep-col">:</td>
                <td class="value-col">{{ $application->pilihanProgram3?->nama_jurusan ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">C. DATA ORANG TUA</div>
        <table class="boxed-table">
            <thead>
                <tr>
                    <th style="width: 170px;">Item</th>
                    <th>Data Ayah</th>
                    <th>Data Ibu</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Nama</strong></td>
                    <td>{{ strtoupper((string) ($application->nama_ayah ?: '-')) }}</td>
                    <td>{{ strtoupper((string) ($application->nama_ibu ?: '-')) }}</td>
                </tr>
                <tr>
                    <td><strong>Tempat/Tgl Lahir</strong></td>
                    <td>{{ $application->tempat_tanggal_lahir_ayah ?: '-' }}</td>
                    <td>{{ $application->tempat_tanggal_lahir_ibu ?: '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Pendidikan Terakhir</strong></td>
                    <td>{{ $application->pendidikan_terakhir_ayah ?: '-' }}</td>
                    <td>{{ $application->pendidikan_terakhir_ibu ?: '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Pekerjaan</strong></td>
                    <td>{{ $application->pekerjaan_ayah ?: '-' }}</td>
                    <td>{{ $application->pekerjaan_ibu ?: '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Penghasilan</strong></td>
                    <td>{{ $application->penghasilan_ayah ?: '-' }}</td>
                    <td>{{ $application->penghasilan_ibu ?: '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Alamat</strong></td>
                    <td>{{ $application->alamat_ayah ?: '-' }}</td>
                    <td>{{ $application->alamat_ibu ?: '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Kelurahan/Kecamatan</strong></td>
                    <td>{{ $application->kelurahan_ayah ?: '-' }} / {{ $application->kecamatan_ayah ?: '-' }}</td>
                    <td>{{ $application->kelurahan_ibu ?: '-' }} / {{ $application->kecamatan_ibu ?: '-' }}</td>
                </tr>
                <tr>
                    <td><strong>No. HP/WA</strong></td>
                    <td>{{ $application->nomor_hp_ayah ?: '-' }}</td>
                    <td>{{ $application->nomor_hp_ibu ?: '-' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="statement-box">
            <div class="statement-title">PERNYATAAN CALON MURID BARU</div>
            <p style="font-size: 9.7pt;">Saya bertanggung jawab atas kebenaran data yang diinput dan bersedia untuk:</p>
            <ol>
                <li>Menerima konsekuensi pembatalan jika data terbukti tidak benar.</li>
                <li>Mematuhi seluruh tata tertib SMK Negeri 1 Kolaka selama masa pendidikan.</li>
                <li>Menjaga nama baik diri sendiri, keluarga, dan sekolah di lingkungan manapun.</li>
                <li>Orang tua/wali bersedia bekerja sama dengan sekolah dalam pembinaan peserta didik.</li>
            </ol>
        </div>

        <table class="signature-table">
            <tr>
                <td>
                    <p>Mengetahui,</p>
                    <p>Orang Tua / Wali</p>
                    <div class="sign-space"></div>
                    <div class="sign-line">Tanda Tangan dan Nama Jelas</div>
                </td>
                <td>
                    <p>Kolaka, {{ now()->translatedFormat('d F Y') }}</p>
                    <p>Calon Murid Baru</p>
                    <div class="sign-space"></div>
                    <div class="sign-line">Tanda Tangan dan Nama Jelas</div>
                </td>
            </tr>
        </table>

        @if(isset($documentSecurity) && is_array($documentSecurity))
            <div class="security-box">
                <strong>KEAMANAN DOKUMEN DIGITAL</strong><br>
                Kode: {{ $documentSecurity['document_code'] ?? '-' }} | 
                Tanda Tangan Sistem: {{ $documentSecurity['signature_short'] ?? '-' }} | 
                Terbit: {{ $documentSecurity['issued_at_human'] ?? '-' }}<br>
                Verifikasi: {{ $documentSecurity['verification_url'] ?? '-' }}
            </div>
        @endif
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