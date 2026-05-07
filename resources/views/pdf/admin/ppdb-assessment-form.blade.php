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
    $rightLogoBase64 = $toBase64Image(storage_path('app/public/smk1kolaka.jpg')) ?? $defaultLogoBase64;
    $meta = $documentMeta ?? [];
    $tahunAjaran = $period->tahun_ajaran ?? '-';
    $gelombang = $period->gelombang_label ?? ('Gelombang ' . ($period->gelombang_ke ?? '-'));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Penilaian Tes Wawancara & Akademik</title>
    <style>
        @page { size: 297mm 210mm; margin: 15mm 12mm 15mm 12mm; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Times New Roman", Times, serif; font-size: 10pt; color: #111; line-height: 1.25; }

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

        .doc-title { text-align: center; margin: 6px 0 8px; }
        .doc-title h1 { font-size: 12pt; font-weight: 700; text-decoration: underline; text-transform: uppercase; }
        .doc-title p { font-size: 10pt; margin-top: 2px; }

        /* ===== DATA TABLE ===== */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .data-table th, .data-table td { border: 1px solid #333; padding: 3px 5px; vertical-align: middle; }
        .data-table th { background: #f2f2f2; text-align: center; font-weight: 700; font-size: 8.5pt; }
        .data-table td { font-size: 9pt; }
        .data-table td.c { text-align: center; }
        .data-table td.empty-fill { height: 24px; }

        .info-note { font-size: 9pt; margin-top: 8px; }
        .info-note ul { margin-left: 18px; }
        .info-note li { margin-bottom: 2px; }

        .signature-table { width: 100%; border-collapse: collapse; margin-top: 16px; page-break-inside: avoid; }
        .signature-table td { width: 50%; text-align: center; vertical-align: top; font-size: 10pt; border: 0; }
        .sign-name { font-weight: 700; text-decoration: underline; text-transform: uppercase; }
    </style>
</head>
<body>
    {{-- KOP SURAT --}}
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

    {{-- JUDUL --}}
    <div class="doc-title">
        <h1>Form Penilaian Tes Wawancara & Akademik</h1>
        <p>Tahun Pelajaran {{ $tahunAjaran }} — {{ $gelombang }}</p>
    </div>

    @if($applications->isEmpty())
        <p style="text-align:center; margin-top:30px;">Tidak ada data calon siswa yang memenuhi kriteria.</p>
    @else

    {{-- TABEL PENILAIAN --}}
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:22px;">No</th>
                <th rowspan="2" style="width:85px;">No. Pendaftaran</th>
                <th rowspan="2" style="width:60px;">NISN</th>
                <th rowspan="2" style="width:130px;">Nama Lengkap</th>
                <th rowspan="2" style="width:70px;">Tanggal Lahir</th>
                <th rowspan="2" style="width:55px;">Nilai Akademik</th>
                <th rowspan="2" style="width:55px;">Nilai Wawancara</th>
                <th colspan="{{ $programs->count() }}">Jurusan yang Direkomendasikan (✓)</th>
            </tr>
            <tr>
                @foreach($programs as $program)
                <th style="min-width:40px; font-size: 7.5pt;">{{ $program->nama_jurusan }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $i => $app)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td style="font-size:7.5pt;">{{ $app->nomor_pendaftaran }}</td>
                <td>{{ $app->nisn }}</td>
                <td>{{ $app->nama_lengkap }}</td>
                <td class="c">{{ $app->tanggal_lahir?->format('d-m-Y') }}</td>
                <td class="empty-fill"></td>
                <td class="empty-fill"></td>
                @foreach($programs as $program)
                <td class="c empty-fill">
                    @if($app->pilihan_program_1_id === $program->id)
                        <span style="font-size:7pt; color:#999;">☆</span>
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- KETERANGAN --}}
    <div class="info-note">
        <p><strong>Keterangan:</strong></p>
        <ul>
            <li>Kolom <strong>Nilai Akademik</strong> dan <strong>Nilai Wawancara</strong> diisi oleh guru penguji.</li>
            <li>Berikan tanda <strong>✓ (centang)</strong> pada kolom jurusan yang direkomendasikan.</li>
            <li>Tanda <strong>☆</strong> menandakan pilihan pertama calon siswa (sebagai referensi).</li>
        </ul>
    </div>

    {{-- TANDA TANGAN --}}
    <table class="signature-table">
        <tr>
            <td>
                <p>Mengetahui,</p>
                <p>{{ $meta['sign_title'] ?? 'Kepala Sekolah' }}</p>
                <div style="height: 55px;"></div>
                <p class="sign-name">{{ $meta['sign_name'] ?? '........................................' }}</p>
                @if($meta['sign_nip'] ?? null)<p style="font-size:9pt">NIP. {{ $meta['sign_nip'] }}</p>@endif
            </td>
            <td>
                <p>Kolaka, .........................................</p>
                <p>Guru Penguji Tes</p>
                <div style="height: 55px;"></div>
                <p class="sign-name">........................................</p>
                <p style="font-size:9pt">NIP. ........................................</p>
            </td>
        </tr>
    </table>

    @endif
</body>
</html>
