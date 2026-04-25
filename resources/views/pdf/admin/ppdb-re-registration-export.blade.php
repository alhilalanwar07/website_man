<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengumuman Hasil PPDB</title>
    <style>
        @page {
            margin: 20mm 16mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #0f172a;
        }

        p {
            margin: 0;
            line-height: 1.45;
        }

        .page-break {
            page-break-before: always;
        }

        .kop {
            width: 100%;
            border-bottom: 3px double #0f172a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .kop-logo {
            width: 14%;
            vertical-align: middle;
            text-align: center;
        }

        .kop-logo img {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }

        .kop-text {
            width: 86%;
            text-align: center;
            vertical-align: middle;
        }

        .kop-line-1 {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .kop-line-2 {
            margin-top: 2px;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .kop-line-3,
        .kop-line-4 {
            margin-top: 2px;
            font-size: 10px;
            color: #334155;
        }

        .document-title {
            text-align: center;
            margin-bottom: 10px;
        }

        .document-title h1 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.6px;
        }

        .document-title p {
            margin-top: 4px;
            font-size: 11px;
        }

        .opening-text {
            margin-bottom: 10px;
            text-align: justify;
        }

        .program-title {
            margin: 10px 0 6px;
            font-size: 12px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #94a3b8;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #e2e8f0;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
        }

        .signature-wrap {
            margin-top: 16px;
            width: 100%;
        }

        .signature-box {
            width: 280px;
            margin-left: auto;
            text-align: center;
        }

        .signature-space {
            height: 68px;
            margin: 8px 0;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .signature-nip {
            margin-top: 2px;
            font-size: 10px;
        }

        .empty-state {
            margin-top: 10px;
            border: 1px solid #94a3b8;
            padding: 8px;
        }
    </style>
</head>
<body>
    @php
        $meta = $documentMeta ?? [];
        $schoolName = $meta['school_name'] ?? 'SMK Negeri 1 Kolaka';
        $schoolNpsn = $meta['school_npsn'] ?? null;
        $schoolAddress = $meta['school_address'] ?? '-';
        $schoolPhone = $meta['school_phone'] ?? '-';
        $schoolEmail = $meta['school_email'] ?? '-';
        $logoPath = $meta['logo_absolute_path'] ?? null;
        $documentScope = $scope ?? 're-registration';
        $documentHeading = $documentScope === 're-registration-audit'
            ? 'Laporan Audit Daftar Ulang PPDB'
            : 'Pengumuman Hasil Seleksi PPDB';
        $openingText = $documentScope === 're-registration-audit'
            ? 'Dokumen ini merupakan rekap hasil audit verifikasi daftar ulang peserta PPDB pada periode yang dipilih.'
            : 'Berdasarkan hasil seleksi PPDB pada periode yang ditetapkan, berikut daftar nama peserta yang dinyatakan diterima dan ditempatkan pada program keahlian masing-masing.';
        $groupedApplications = $applications->groupBy(fn ($application) => $application->programDiterima?->nama_jurusan ?? 'Belum Ditentukan');
        $signLocation = $meta['sign_location'] ?? 'Kolaka';
        $signDate = $meta['sign_date'] ?? now()->translatedFormat('d F Y');
        $signTitle = $meta['sign_title'] ?? 'Kepala Sekolah';
        $signName = $meta['sign_name'] ?? '........................................';
        $signNip = $meta['sign_nip'] ?? null;
    @endphp

    @foreach($groupedApplications as $programName => $programApplications)
        <div class="{{ $loop->first ? '' : 'page-break' }}">
            <table class="kop">
                <tr>
                    <td class="kop-logo">
                        @if($logoPath)
                            <img src="{{ $logoPath }}" alt="Logo Sekolah">
                        @endif
                    </td>
                    <td class="kop-text">
                        <p class="kop-line-1">Pemerintah Provinsi Sulawesi Tenggara</p>
                        <p class="kop-line-2">{{ $schoolName }}</p>
                        <p class="kop-line-3">Alamat: {{ $schoolAddress }}</p>
                        <p class="kop-line-4">NPSN: {{ $schoolNpsn ?: '-' }} | Telp: {{ $schoolPhone ?: '-' }} | Email: {{ $schoolEmail ?: '-' }}</p>
                    </td>
                </tr>
            </table>

            <div class="document-title">
                <h1>{{ $documentHeading }}</h1>
                <p>Periode {{ $period->nama_periode }} ({{ $period->tahun_ajaran }})</p>
            </div>

            <p class="opening-text">{{ $openingText }}</p>

            @if(($filters['search'] ?? '') !== '')
                <p style="margin-bottom: 6px;"><strong>Filter pencarian:</strong> {{ $filters['search'] }}</p>
            @endif

            <p class="program-title">Program Keahlian: {{ $programName }}</p>
            <table>
                <thead>
                    <tr>
                        <th style="width: 36px;">No.</th>
                        <th style="width: 78px;">NIPD</th>
                        <th style="width: 116px;">No. Pendaftaran</th>
                        <th>Nama Peserta</th>
                        <th>Asal Sekolah</th>
                        <th style="width: 84px;">Tgl Lahir</th>
                        <th style="width: 84px;">L/P</th>
                        <th>Program Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programApplications as $application)
                        <tr>
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td>{{ $application->nipd ?: '-' }}</td>
                            <td>{{ $application->nomor_pendaftaran }}</td>
                            <td>{{ $application->nama_lengkap }}</td>
                            <td>{{ $application->asal_sekolah }}</td>
                            <td style="text-align: center;">{{ $application->tanggal_lahir?->format('d-m-Y') ?? '-' }}</td>
                            <td style="text-align: center;">{{ $application->jenis_kelamin === 'P' ? 'P' : 'L' }}</td>
                            <td>{{ $application->programDiterima?->nama_jurusan ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    @if($groupedApplications->isEmpty())
        <div class="empty-state">Tidak ada data untuk filter yang dipilih.</div>
    @endif

    <div class="signature-wrap">
        <div class="signature-box">
            <p>{{ $signLocation }}, {{ $signDate }}</p>
            <p>{{ $signTitle }}</p>
            <div class="signature-space"></div>
            <p class="signature-name">{{ $signName }}</p>
            @if($signNip)
                <p class="signature-nip">NIP. {{ $signNip }}</p>
            @endif
        </div>
    </div>
</body>
</html>
