<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi PPDB</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px;background:#0f172a;color:#ffffff;">
                            <p style="margin:0;font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:700;color:#93c5fd;">PPDB MAN 2 Kolaka</p>
                            <h1 style="margin:12px 0 0;font-size:24px;line-height:1.2;">Pendaftaran Berhasil Diterima</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px;font-size:14px;line-height:1.6;">Halo <strong>{{ $application->nama_lengkap }}</strong>, terima kasih sudah mendaftar PPDB secara online.</p>
                            <p style="margin:0 0 16px;font-size:14px;line-height:1.6;">Berikut ringkasan data pendaftaran Anda:</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                                <tr>
                                    <td style="padding:10px 14px;font-size:13px;color:#475569;width:42%;">Nomor Pendaftaran</td>
                                    <td style="padding:10px 14px;font-size:13px;font-weight:700;color:#0f172a;">{{ $application->nomor_pendaftaran }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px;font-size:13px;color:#475569;">Jalur</td>
                                    <td style="padding:10px 14px;font-size:13px;color:#0f172a;">{{ $application->track?->nama_jalur ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px;font-size:13px;color:#475569;">Pilihan Peminatan 1</td>
                                    <td style="padding:10px 14px;font-size:13px;color:#0f172a;">{{ $application->pilihanProgram1?->nama_jurusan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 14px;font-size:13px;color:#475569;">Pilihan Peminatan 2</td>
                                    <td style="padding:10px 14px;font-size:13px;color:#0f172a;">{{ $application->pilihanProgram2?->nama_jurusan ?? '-' }}</td>
                                </tr>
                            </table>

                            <div style="margin-top:20px;padding:14px;border:1px solid #bfdbfe;background:#eff6ff;border-radius:12px;">
                                <p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#1d4ed8;">Unduh Formulir Pendaftaran</p>
                                <p style="margin:0 0 10px;font-size:13px;line-height:1.6;color:#334155;">Formulir PDF juga sudah dilampirkan pada email ini sebagai arsip.</p>
                                <p style="margin:0 0 10px;font-size:13px;line-height:1.6;color:#334155;">Dokumen PDF dilengkapi kode keamanan dan tautan verifikasi online untuk mencegah pemalsuan.</p>
                                <p style="margin:0 0 10px;font-size:13px;line-height:1.6;color:#334155;">Klik tombol berikut untuk mengunduh formulir pendaftaran Anda dalam format PDF:</p>
                                <a href="{{ $downloadUrl }}" style="display:inline-block;padding:10px 14px;border-radius:10px;background:#1d4ed8;color:#ffffff;text-decoration:none;font-size:13px;font-weight:700;">Unduh Formulir PDF</a>
                            </div>

                            <p style="margin:18px 0 0;font-size:13px;line-height:1.6;color:#475569;">Simpan nomor pendaftaran Anda dan cek perkembangan seleksi di halaman status PPDB secara berkala.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;border-top:1px solid #e2e8f0;background:#f8fafc;">
                            <p style="margin:0;font-size:12px;color:#64748b;">Email ini dikirim otomatis oleh sistem PPDB MAN 2 Kolaka.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
