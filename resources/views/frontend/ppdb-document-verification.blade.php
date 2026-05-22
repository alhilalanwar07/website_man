<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen PPDB</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#0f172a;">
    <div style="max-width:760px;margin:24px auto;padding:0 12px;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
            <div style="padding:20px 24px;background:#0f172a;color:#fff;">
                <p style="margin:0;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#93c5fd;font-weight:700;">PPDB MAN 2 Kolaka</p>
                <h1 style="margin:10px 0 0;font-size:22px;">Verifikasi Keaslian Dokumen</h1>
            </div>

            <div style="padding:20px 24px;">
                @if($isValid)
                    <div style="border:1px solid #86efac;background:#f0fdf4;color:#166534;border-radius:12px;padding:14px 16px;font-weight:700;">
                        Dokumen VALID dan ditandatangani oleh sistem PPDB.
                    </div>

                    <p style="margin:14px 0 10px;font-size:14px;color:#334155;">Cocokkan data ini dengan dokumen cetak yang Anda terima:</p>

                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <tr>
                            <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;width:36%;">Nomor Pendaftaran</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;font-weight:700;">{{ $application->nomor_pendaftaran }}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;">Nama Lengkap</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;">{{ $application->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;">Tanggal Lahir</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;">{{ optional($application->tanggal_lahir)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;">Pilihan Peminatan 1</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;">{{ $application->pilihanProgram1?->nama_jurusan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;">Kode Dokumen</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;">{{ $verifiedContext['document_code'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;">Tanda Tangan Sistem</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;">{{ $verifiedContext['signature_short'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:8px;border:1px solid #e2e8f0;background:#f8fafc;">Terbit Dokumen</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;">{{ $verifiedContext['issued_at_human'] ?? '-' }}</td>
                        </tr>
                    </table>
                @else
                    <div style="border:1px solid #fecaca;background:#fff1f2;color:#b91c1c;border-radius:12px;padding:14px 16px;font-weight:700;">
                        Dokumen TIDAK VALID atau parameter verifikasi tidak sesuai.
                    </div>

                    <p style="margin:12px 0 0;font-size:13px;color:#7f1d1d;line-height:1.6;">Dokumen ini terindikasi telah dimodifikasi, dipalsukan, atau menggunakan link verifikasi yang tidak sah. Silakan hubungi admin PPDB untuk pengecekan lanjutan.</p>
                @endif

                <div style="margin-top:14px;border-top:1px solid #e2e8f0;padding-top:10px;font-size:12px;color:#64748b;line-height:1.5;word-break:break-word;">
                    <p style="margin:0;">Ref issued: {{ $issuedAt ?: '-' }}</p>
                    <p style="margin:3px 0 0;">Ref signature: {{ $signature ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
