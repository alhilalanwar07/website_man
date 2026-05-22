@php
    $profil = \App\Models\ProfilSekolah::first();
    $logoPath = $profil && $profil->logo_path ? storage_path('app/public/' . $profil->logo_path) : null;
    $toBase64Image = static function (?string $path): ?string {
        if (! $path || ! file_exists($path)) { return null; }
        $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($ext) { 'png' => 'image/png', 'jpg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml', default => 'image/png' };
        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    };
    $defaultLogoBase64 = $toBase64Image($logoPath);
    $leftLogoBase64 = $toBase64Image(storage_path('app/public/sultra_logo.png')) ?? $defaultLogoBase64;
    $rightLogoBase64 = $toBase64Image(storage_path('app/public/MAN-2.png')) ?? $defaultLogoBase64;
    $meta = $documentMeta ?? [];
    $signLocation = $meta['sign_location'] ?? 'Kolaka';
    $signDate = $meta['sign_date'] ?? now()->translatedFormat('d F Y');
    $signTitle = $meta['sign_title'] ?? 'Kepala Sekolah';
    $signName = $meta['sign_name'] ?? '........................................';
    $signNip = $meta['sign_nip'] ?? null;
    $extractYear = static function (?string $value): string {
        if (! filled($value)) return '';
        preg_match('/(19|20)\d{2}/', (string) $value, $matches);
        return $matches[0] ?? '';
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Peserta Didik</title>
    <style>
        @page { size: 297mm 210mm; margin: 15mm 12mm 15mm 12mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Times New Roman", Times, serif; font-size: 8pt; color: #111; line-height: 1.25; }

        .page-break { page-break-before: always; }

        /* ===== KOP STYLES (matches registration form) ===== */
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
        .kop-table td { border: 0; vertical-align: middle; padding: 0; }
        .logo-cell { width: 92px; text-align: center; }
        .logo-cell img { width: 78px; height: auto; }
        .kop-text { text-align: center; }
        .kop-text .line1, .kop-text .line2 { font-size: 10.8pt; font-weight: 700; }
        .kop-text .line3 { font-size: 15pt; font-weight: 700; letter-spacing: 0.45px; }
        .kop-text .line4, .kop-text .line5 { font-size: 9pt; }
        .kop-line { border-top: 2px solid #000; border-bottom: 1px solid #000; height: 4px; margin: 4px 0 6px 0; }

        .doc-title { text-align: center; margin: 4px 0 6px; }
        .doc-title h1 { font-size: 11pt; font-weight: 700; text-decoration: underline; text-transform: uppercase; }
        .doc-title p { font-size: 9pt; margin-top: 2px; }

        /* ===== DATA TABLE ===== */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .data-table th, .data-table td { border: 1px solid #333; padding: 2px 3px; font-size: 7pt; vertical-align: top; }
        .data-table th { background: #f2f2f2; text-align: center; font-weight: 700; vertical-align: middle; }
        .data-table td.c { text-align: center; }

        .signature-table { width: 100%; border-collapse: collapse; margin-top: 12px; page-break-inside: avoid; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; font-size: 10pt; border: 0; }
        .signature-name { font-weight: 700; text-decoration: underline; text-transform: uppercase; }
    </style>
</head>
<body>
    @if($applications->isEmpty())
        <p style="text-align:center; margin-top:30px; font-size:10pt;">Tidak ada data peserta yang memenuhi kriteria.</p>
    @else
    {{-- ============ HALAMAN 1: DATA PRIBADI ============ --}}
    @include('pdf.admin._ppdb-dapodik-kop', ['title' => 'Daftar Peserta Didik — Data Pribadi'])

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:16px">No</th>
                <th style="width:90px">Nama Lengkap</th>
                <th style="width:40px">NIPD</th>
                <th style="width:16px">JK</th>
                <th style="width:55px">NISN</th>
                <th style="width:60px">Tempat Lahir</th>
                <th style="width:50px">Tgl Lahir</th>
                <th style="width:80px">NIK</th>
                <th style="width:35px">Agama</th>
                <th>Alamat</th>
                <th style="width:16px">RT</th>
                <th style="width:16px">RW</th>
                <th style="width:45px">Dusun</th>
                <th style="width:50px">Kelurahan</th>
                <th style="width:50px">Kecamatan</th>
                <th style="width:28px">Kode Pos</th>
                <th style="width:45px">Jenis Tinggal</th>
                <th style="width:50px">Transportasi</th>
                <th style="width:55px">HP</th>
                <th style="width:65px">Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $i => $app)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>{{ $app->nama_lengkap }}</td>
                <td>{{ $app->nipd }}</td>
                <td class="c">{{ $app->jenis_kelamin }}</td>
                <td>{{ $app->nisn }}</td>
                <td>{{ $app->tempat_lahir }}</td>
                <td class="c">{{ $app->tanggal_lahir?->format('d-m-Y') }}</td>
                <td>{{ $app->nik }}</td>
                <td>{{ $app->agama }}</td>
                <td>{{ $app->alamat_lengkap }}</td>
                <td class="c">{{ $app->rt }}</td>
                <td class="c">{{ $app->rw }}</td>
                <td>{{ $app->nama_dusun }}</td>
                <td>{{ $app->kelurahan }}</td>
                <td>{{ $app->kecamatan }}</td>
                <td class="c">{{ $app->kode_pos }}</td>
                <td>{{ $app->tempat_tinggal }}</td>
                <td>{{ $app->moda_transportasi }}</td>
                <td>{{ $app->nomor_hp }}</td>
                <td style="font-size:6pt">{{ $app->email }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ============ HALAMAN 2: DATA ORANG TUA ============ --}}
    <div class="page-break"></div>
    @include('pdf.admin._ppdb-dapodik-kop', ['title' => 'Daftar Peserta Didik — Data Orang Tua / Wali'])

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:16px">No</th>
                <th rowspan="2" style="width:90px">Nama Peserta</th>
                <th colspan="6">Data Ayah</th>
                <th colspan="6">Data Ibu</th>
                <th colspan="6">Data Wali</th>
            </tr>
            <tr>
                @for($g = 0; $g < 3; $g++)
                <th>Nama</th><th>Thn Lahir</th><th>Pendidikan</th><th>Pekerjaan</th><th>Penghasilan</th><th>NIK</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $i => $app)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>{{ $app->nama_lengkap }}</td>
                <td>{{ $app->nama_ayah }}</td>
                <td class="c">{{ $extractYear($app->tempat_tanggal_lahir_ayah) }}</td>
                <td>{{ $app->pendidikan_terakhir_ayah }}</td>
                <td>{{ $app->pekerjaan_ayah }}</td>
                <td>{{ $app->penghasilan_ayah }}</td>
                <td>{{ $app->nik_ayah }}</td>
                <td>{{ $app->nama_ibu }}</td>
                <td class="c">{{ $extractYear($app->tempat_tanggal_lahir_ibu) }}</td>
                <td>{{ $app->pendidikan_terakhir_ibu }}</td>
                <td>{{ $app->pekerjaan_ibu }}</td>
                <td>{{ $app->penghasilan_ibu }}</td>
                <td>{{ $app->nik_ibu }}</td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ============ HALAMAN 3: DATA TAMBAHAN ============ --}}
    <div class="page-break"></div>
    @include('pdf.admin._ppdb-dapodik-kop', ['title' => 'Daftar Peserta Didik — Data Tambahan'])

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:16px">No</th>
                <th style="width:90px">Nama</th>
                <th>Sekolah Asal</th>
                <th>No KK</th>
                <th>No Akta Lahir</th>
                <th>KIP</th>
                <th>KKS</th>
                <th>Anak Ke</th>
                <th>Jml Saudara</th>
                <th>BB</th>
                <th>TB</th>
                <th>LK</th>
                <th>Jarak (KM)</th>
                <th>Kebutuhan Khusus</th>
                <th>Lintang</th>
                <th>Bujur</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $i => $app)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>{{ $app->nama_lengkap }}</td>
                <td>{{ $app->asal_sekolah }}</td>
                <td>{{ $app->no_kk }}</td>
                <td>{{ $app->no_registrasi_akta_lahir }}</td>
                <td class="c">{{ $app->punya_kip ? 'Ya' : '' }}</td>
                <td>{{ $app->nomor_kartu_kesejahteraan }}</td>
                <td class="c">{{ $app->anak_ke }}</td>
                <td class="c">{{ $app->jumlah_saudara }}</td>
                <td class="c">{{ $app->berat_badan }}</td>
                <td class="c">{{ $app->tinggi_badan }}</td>
                <td class="c">{{ $app->lingkar_kepala }}</td>
                <td class="c">{{ $app->jarak_tempat_tinggal_km }}</td>
                <td>{{ $app->kebutuhan_khusus }}</td>
                <td>{{ $app->lintang }}</td>
                <td>{{ $app->bujur }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tanda Tangan --}}
    <table class="signature-table">
        <tr>
            <td></td>
            <td>
                <p>{{ $signLocation }}, {{ $signDate }}</p>
                <p>{{ $signTitle }}</p>
                <div style="height: 55px;"></div>
                <p class="signature-name">{{ $signName }}</p>
                @if($signNip)<p style="font-size:9pt">NIP. {{ $signNip }}</p>@endif
            </td>
        </tr>
    </table>
    @endif
</body>
</html>
