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
    $rightLogoBase64 = $toBase64Image(storage_path('app/public/MAN-2.png')) ?? $defaultLogoBase64;

    $meta = $documentMeta ?? [];
    $documentScope = $scope ?? 're-registration';
    $documentHeading = $documentScope === 're-registration-audit'
        ? 'Laporan Audit Daftar Ulang PPDB'
        : 'Pengumuman Hasil Seleksi PPDB';
    $openingText = $documentScope === 're-registration-audit'
        ? 'Dokumen ini merupakan rekap hasil audit verifikasi daftar ulang peserta PPDB pada periode yang dipilih.'
        : 'Berdasarkan hasil seleksi PPDB pada periode yang ditetapkan, berikut daftar nama peserta yang dinyatakan diterima dan ditempatkan pada peminatan masing-masing.';

    // Filter applications to only show those WITH an NIPD
    $filteredApplications = collect($applications)->filter(fn($app) => !empty($app->nipd));
    $groupedApplications = $filteredApplications->groupBy(fn ($application) => $application->programDiterima?->nama_jurusan ?? 'Belum Ditentukan');

    $signLocation = $meta['sign_location'] ?? 'Kolaka';
    $signDate = $meta['sign_date'] ?? now()->translatedFormat('d F Y');
    $signTitle = $meta['sign_title'] ?? 'Kepala Madrasah';
    $signName = $meta['sign_name'] ?? '........................................';
    $signNip = $meta['sign_nip'] ?? null;

    $qrData = "Tanda Tangan Digital\n" . $signTitle . " MAN 2 Kolaka\nNama: " . $signName . "\nNIP: " . ($signNip ?? '-') . "\nTanggal: " . $signDate;
    $qrCodeBase64 = null;
    if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
        try {
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode(
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->merge(storage_path('app/public/MAN-2.png'), 0.25, true)
                    ->size(120)
                    ->errorCorrection('H')
                    ->generate($qrData)
            );
        } catch (\Exception $e) {
            try {
                $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode(
                    \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(120)
                        ->errorCorrection('H')
                        ->generate($qrData)
                );
            } catch (\Exception $e2) {
                $qrCodeBase64 = null;
            }
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengumuman Hasil PPDB</title>
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

        .page-break {
            page-break-before: always;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .kop-table td {
            border: 0;
            vertical-align: middle;
            padding: 0;
        }

        .logo-cell {
            width: 92px;
            text-align: center;
        }

        .logo-cell img {
            width: 78px;
            height: auto;
        }

        .kop-text {
            text-align: center;
        }

        .kop-text .line1,
        .kop-text .line2 {
            font-size: 10.8pt;
            font-weight: 700;
        }

        .kop-text .line3 {
            font-size: 15pt;
            font-weight: 700;
            letter-spacing: 0.45px;
        }

        .kop-text .line4,
        .kop-text .line5 {
            font-size: 9pt;
        }

        .kop-line {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
            margin: 4px 0 6px 0;
        }

        .document-title {
            text-align: center;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .document-title h1 {
            margin: 0;
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .document-title p {
            margin-top: 4px;
            font-size: 10pt;
        }

        .opening-text {
            margin-bottom: 10px;
            text-align: justify;
        }

        .program-title {
            margin: 10px 0 6px;
            font-size: 11pt;
            font-weight: 700;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #111;
            padding: 4px 6px;
            font-size: 9.6pt;
            vertical-align: top;
        }

        .data-table th {
            background: #f2f2f2;
            text-align: center;
            font-weight: 700;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
        }

        .qr-box {
            margin: 8px auto;
            text-align: center;
        }

        .qr-box img {
            width: 90px;
            height: 90px;
            object-fit: contain;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .signature-nip {
            margin-top: 2px;
            font-size: 10pt;
        }

        .empty-state {
            margin-top: 10px;
            border: 1px solid #111;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    @foreach($groupedApplications as $programName => $programApplications)
        <div class="{{ $loop->first ? '' : 'page-break' }}">
            <table class="kop-table">
                <tr>
                    <td class="logo-cell">
                        @if($leftLogoBase64)
                            <img src="{{ $leftLogoBase64 }}" alt="Logo Sultra">
                        @endif
                    </td>
                    <td class="kop-text">
                <div class="line1">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                <div class="line2">KANTOR KEMENTERIAN AGAMA KABUPATEN KOLAKA</div>
                <div class="line3">{{ strtoupper($profil->nama_sekolah ?? 'MAN 2 KOLAKA') }}</div>
                <div class="line4">{{ $profil->alamat_lengkap ?? 'Jl. Pemuda No. 12, Kelurahan Laloeha, Kecamatan Kolaka, Kabupaten Kolaka, Sulawesi Tenggara 93511' }}{{ $profil->nomor_telepon ? ' | Telp. ' . $profil->nomor_telepon : '' }}</div>
                <div class="line5">Email: {{ $profil->email_resmi ?? 'info@man2kolaka.sch.id' }}</div>
                    </td>
                    <td class="logo-cell">
                        @if($rightLogoBase64)
                            <img src="{{ $rightLogoBase64 }}" alt="Logo MAN">
                        @endif
                    </td>
                </tr>
            </table>

            <div class="kop-line"></div>

            <div class="document-title">
                <h1>{{ $documentHeading }}</h1>
                <p>Periode {{ $period->nama_periode }} ({{ $period->tahun_ajaran }})</p>
            </div>

            <p class="opening-text">{{ $openingText }}</p>

            @if(($filters['search'] ?? '') !== '')
                <p style="margin-bottom: 6px;"><strong>Filter pencarian:</strong> {{ $filters['search'] }}</p>
            @endif

            <p class="program-title">Peminatan: {{ $programName }}</p>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 36px;">No.</th>
                        <th style="width: 78px;">NIPD</th>
                        <th style="width: 116px;">No. Pendaftaran</th>
                        <th>Nama Peserta</th>
                        <th>Asal Sekolah</th>
                        <th style="width: 84px;">Tgl Lahir</th>
                        <th style="width: 40px;">L/P</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programApplications as $application)
                        <tr>
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td>{{ $application->nipd ?: '-' }}</td>
                            <td>{{ $application->nomor_pendaftaran }}</td>
                            <td>{{ strtoupper((string) $application->nama_lengkap) }}</td>
                            <td>{{ strtoupper((string) $application->asal_sekolah) }}</td>
                            <td style="text-align: center;">{{ $application->tanggal_lahir?->format('d-m-Y') ?? '-' }}</td>
                            <td style="text-align: center;">{{ $application->jenis_kelamin === 'P' ? 'P' : 'L' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="signature-table">
                <tr>
                    <td></td>
                    <td>
                        <p>{{ $signLocation }}, {{ $signDate }}</p>
                        <p>{{ $signTitle }}</p>
                        @if($qrCodeBase64)
                            <div class="qr-box">
                                <img src="{{ $qrCodeBase64 }}" alt="QR Code Signature">
                            </div>
                        @else
                            <div style="height: 70px;"></div>
                        @endif
                        <p class="signature-name">{{ $signName }}</p>
                        @if($signNip)
                            <p class="signature-nip">NIP. {{ $signNip }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

    @if($groupedApplications->isEmpty())
        <div class="empty-state">Tidak ada data peserta yang memenuhi kriteria pencarian dan telah memiliki NIPD.</div>
    @endif
</body>
</html>
