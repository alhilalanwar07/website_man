<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Daftar Ulang PPDB</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        p { margin: 0 0 4px; }
        .meta { margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; vertical-align: top; }
        th { background: #e2e8f0; text-align: left; }
        .muted { color: #475569; }
    </style>
</head>
<body>
    <div class="meta">
        <h1>Export Daftar Ulang PPDB</h1>
        <p><strong>Periode:</strong> {{ $period->nama_periode }} ({{ $period->tahun_ajaran }})</p>
        <p><strong>Jenis export:</strong> {{ $scope === 're-registration-audit' ? 'Audit daftar ulang' : 'Daftar ulang' }}</p>
        @if(($filters['status_daftar_ulang'] ?? '') !== '' || ($filters['search'] ?? '') !== '')
            <p class="muted">
                Filter:
                {{ ($filters['status_daftar_ulang'] ?? '') !== '' ? 'status ' . $filters['status_daftar_ulang'] : '' }}
                {{ ($filters['search'] ?? '') !== '' ? ' | pencarian "' . $filters['search'] . '"' : '' }}
            </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 34px;">No</th>
                <th>Nomor Pendaftaran</th>
                <th>Nama Lengkap</th>
                <th>Jalur</th>
                <th>Jurusan</th>
                <th>Status</th>
                <th>Konfirmasi</th>
                <th>Verifikasi</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $index => $application)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $application->nomor_pendaftaran }}</td>
                    <td>{{ $application->nama_lengkap }}</td>
                    <td>{{ $application->track?->nama_jalur ?? '-' }}</td>
                    <td>{{ $application->programDiterima?->nama_jurusan ?? '-' }}</td>
                    <td>{{ $application->status_daftar_ulang_label }}</td>
                    <td>{{ $application->daftar_ulang_at?->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>{{ $application->verified_daftar_ulang_at?->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>{{ $application->catatan_daftar_ulang ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Tidak ada data untuk filter yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
