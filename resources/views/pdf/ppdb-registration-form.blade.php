<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir PPDB</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #0f172a;
            margin: 0;
        }

        .page {
            padding: 24px;
            position: relative;
        }

        .watermark {
            position: absolute;
            top: 45%;
            left: 8%;
            right: 8%;
            text-align: center;
            font-size: 34px;
            color: #94a3b8;
            opacity: 0.12;
            transform: rotate(-22deg);
            letter-spacing: 2px;
            font-weight: 700;
            z-index: 0;
        }

        .header {
            border: 1px solid #1e293b;
            padding: 12px 14px;
            margin-bottom: 12px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
        }

        .header p {
            margin: 3px 0 0;
            font-size: 11px;
        }

        .meta {
            border: 1px solid #94a3b8;
            padding: 10px 12px;
            margin-bottom: 12px;
            background: #f8fafc;
        }

        .meta strong {
            font-size: 12px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            margin: 14px 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #cbd5e1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            text-align: left;
            width: 34%;
            background: #f8fafc;
            font-weight: 600;
        }

        .achievement th,
        .achievement td {
            width: auto;
            text-align: left;
        }

        .muted {
            color: #475569;
        }

        .footer {
            margin-top: 16px;
            font-size: 10px;
            color: #64748b;
        }

        .security-box {
            margin-top: 12px;
            border: 1px dashed #1d4ed8;
            background: #eff6ff;
            padding: 8px 10px;
            font-size: 10px;
            line-height: 1.5;
            word-break: break-word;
        }

        .security-title {
            margin: 0 0 4px;
            font-weight: 700;
            color: #1d4ed8;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .security-line {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="watermark">DOKUMEN RESMI - VERIFIKASI WAJIB</div>

        <div class="header">
            <h1>FORMULIR PENDAFTARAN PPDB</h1>
            <p>SMK Negeri 1 Kolaka</p>
        </div>

        <div class="meta">
            <strong>No. Pendaftaran: {{ $application->nomor_pendaftaran }}</strong>
            <p class="muted" style="margin:4px 0 0;">Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WITA</p>
        </div>

        <div class="section-title">A. Data Calon Peserta Didik</div>
        <table>
            <tr><th>Nama Lengkap</th><td>{{ $application->nama_lengkap }}</td></tr>
            <tr><th>NISN</th><td>{{ $application->nisn ?: '-' }}</td></tr>
            <tr><th>NIK</th><td>{{ $application->nik ?: '-' }}</td></tr>
            <tr><th>Tempat, Tanggal Lahir</th><td>{{ $application->tempat_lahir }}, {{ optional($application->tanggal_lahir)->translatedFormat('d F Y') }}</td></tr>
            <tr><th>Jenis Kelamin</th><td>{{ $application->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</td></tr>
            <tr><th>Agama</th><td>{{ $application->agama ?: '-' }}</td></tr>
            <tr><th>Alamat</th><td>{{ $application->alamat_lengkap }}</td></tr>
            <tr><th>RT/RW</th><td>{{ $application->rt_rw ?: '-' }}</td></tr>
            <tr><th>Kelurahan / Kecamatan</th><td>{{ $application->kelurahan ?: '-' }} / {{ $application->kecamatan ?: '-' }}</td></tr>
            <tr><th>No HP Aktif</th><td>{{ $application->nomor_hp }}</td></tr>
            <tr><th>Email Aktif</th><td>{{ $application->email }}</td></tr>
            <tr><th>Asal Sekolah</th><td>{{ $application->asal_sekolah }}</td></tr>
            <tr><th>Alamat Sekolah</th><td>{{ $application->alamat_sekolah ?: '-' }}</td></tr>
        </table>

        <div class="section-title">B. Pilihan PPDB</div>
        <table>
            <tr><th>Periode</th><td>{{ $application->period?->nama_periode ?? '-' }}</td></tr>
            <tr><th>Tahun Ajaran</th><td>{{ $application->period?->tahun_ajaran ?? '-' }}</td></tr>
            <tr><th>Jalur Pendaftaran</th><td>{{ $application->track?->nama_jalur ?? '-' }}</td></tr>
            <tr><th>Pilihan Jurusan 1</th><td>{{ $application->pilihanProgram1?->nama_jurusan ?? '-' }}</td></tr>
            <tr><th>Pilihan Jurusan 2</th><td>{{ $application->pilihanProgram2?->nama_jurusan ?? '-' }}</td></tr>
            <tr><th>Nilai Rata-rata</th><td>{{ $application->nilai_rata_rata !== null ? number_format((float) $application->nilai_rata_rata, 2) : '-' }}</td></tr>
        </table>

        <div class="section-title">C. Data Orang Tua / Wali</div>
        <table>
            <tr><th>Nama Ayah</th><td>{{ $application->nama_ayah ?: '-' }}</td></tr>
            <tr><th>No HP Ayah</th><td>{{ $application->nomor_hp_ayah ?: '-' }}</td></tr>
            <tr><th>Pekerjaan Ayah</th><td>{{ $application->pekerjaan_ayah ?: '-' }}</td></tr>
            <tr><th>Nama Ibu</th><td>{{ $application->nama_ibu ?: '-' }}</td></tr>
            <tr><th>No HP Ibu</th><td>{{ $application->nomor_hp_ibu ?: '-' }}</td></tr>
            <tr><th>Pekerjaan Ibu</th><td>{{ $application->pekerjaan_ibu ?: '-' }}</td></tr>
            <tr><th>No HP Orang Tua Utama</th><td>{{ $application->nomor_hp_orang_tua ?: '-' }}</td></tr>
        </table>

        <div class="section-title">D. Prestasi</div>
        @if($application->achievements->isEmpty())
            <table>
                <tr><th style="width:100%;">Keterangan</th></tr>
                <tr><td>Tidak ada data prestasi yang diinput.</td></tr>
            </table>
        @else
            <table class="achievement">
                <thead>
                    <tr>
                        <th style="width:6%;">No</th>
                        <th style="width:50%;">Nama Prestasi</th>
                        <th style="width:20%;">Juara</th>
                        <th style="width:24%;">Tingkat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($application->achievements as $achievement)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $achievement->achievement_name }}</td>
                            <td>{{ $achievement->achievement_rank }}</td>
                            <td>{{ $achievement->achievement_level }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(isset($documentSecurity) && is_array($documentSecurity))
            <div class="security-box">
                <p class="security-title">Keamanan Dokumen</p>
                <p class="security-line">Kode Dokumen: {{ $documentSecurity['document_code'] ?? '-' }}</p>
                <p class="security-line">Tanda Tangan Sistem: {{ $documentSecurity['signature_short'] ?? '-' }}</p>
                <p class="security-line">Tanggal Terbit: {{ $documentSecurity['issued_at_human'] ?? '-' }}</p>
                <p class="security-line">Verifikasi Online: {{ $documentSecurity['verification_url'] ?? '-' }}</p>
            </div>
        @endif

        <p class="footer">Dokumen ini dihasilkan otomatis oleh sistem PPDB SMK Negeri 1 Kolaka.</p>
    </div>
</body>
</html>
