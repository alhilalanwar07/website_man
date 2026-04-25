<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Daftar Ulang PPDB</title>
</head>
<body>
    <table border="1">
        <thead>
            @if($isAuditExport)
                <tr>
                    <th>Periode</th>
                    <th>Tahun Ajaran</th>
                    <th>Nomor Pendaftaran</th>
                    <th>Nama Lengkap</th>
                    <th>Jalur</th>
                    <th>Program Diterima</th>
                    <th>Status Daftar Ulang</th>
                    <th>Diproses Oleh</th>
                    <th>Tanggal Diproses</th>
                    <th>Catatan Audit</th>
                </tr>
            @else
                <tr>
                    <th>Periode</th>
                    <th>Tahun Ajaran</th>
                    <th>Nomor Pendaftaran</th>
                    <th>Nama Lengkap</th>
                    <th>Asal Sekolah</th>
                    <th>Jalur</th>
                    <th>Program Diterima</th>
                    <th>Status Daftar Ulang</th>
                    <th>Skor Seleksi</th>
                    <th>Tanggal Konfirmasi</th>
                    <th>Diproses Oleh</th>
                    <th>Tanggal Verifikasi</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach($applications as $application)
                @if($isAuditExport)
                    <tr>
                        <td>{{ $period->nama_periode }}</td>
                        <td>{{ $period->tahun_ajaran }}</td>
                        <td>{{ $application->nomor_pendaftaran }}</td>
                        <td>{{ $application->nama_lengkap }}</td>
                        <td>{{ $application->track?->nama_jalur }}</td>
                        <td>{{ $application->programDiterima?->nama_jurusan }}</td>
                        <td>{{ $application->status_daftar_ulang_label }}</td>
                        <td>{{ $application->reRegistrationVerifier?->name }}</td>
                        <td>{{ $application->verified_daftar_ulang_at?->format('d-m-Y H:i') }}</td>
                        <td>{{ $application->catatan_daftar_ulang }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $period->nama_periode }}</td>
                        <td>{{ $period->tahun_ajaran }}</td>
                        <td>{{ $application->nomor_pendaftaran }}</td>
                        <td>{{ $application->nama_lengkap }}</td>
                        <td>{{ $application->asal_sekolah }}</td>
                        <td>{{ $application->track?->nama_jalur }}</td>
                        <td>{{ $application->programDiterima?->nama_jurusan }}</td>
                        <td>{{ $application->status_daftar_ulang_label }}</td>
                        <td>{{ $application->skor_seleksi }}</td>
                        <td>{{ $application->daftar_ulang_at?->format('d-m-Y H:i') }}</td>
                        <td>{{ $application->reRegistrationVerifier?->name }}</td>
                        <td>{{ $application->verified_daftar_ulang_at?->format('d-m-Y H:i') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
