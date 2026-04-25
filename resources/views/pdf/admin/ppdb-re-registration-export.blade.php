<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengumuman Hasil PPDB</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        h1 { font-size: 18px; margin: 0 0 6px; }
        h2 { font-size: 14px; margin: 18px 0 8px; }
        p { margin: 0 0 4px; }
        .meta { margin-bottom: 16px; }
        .section { margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; vertical-align: top; }
        th { background: #e2e8f0; text-align: left; }
        .muted { color: #475569; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    @php
        $groupedApplications = $applications->groupBy(fn ($application) => $application->programDiterima?->nama_jurusan ?? 'Belum Ditentukan');
    @endphp

    <div class="meta">
        <h1>Pengumuman Hasil PPDB</h1>
        <p><strong>Periode:</strong> {{ $period->nama_periode }} ({{ $period->tahun_ajaran }})</p>
        <p><strong>Dokumen:</strong> Daftar pengumuman peserta berdasarkan jurusan diterima</p>
        @if(($filters['search'] ?? '') !== '')
            <p class="muted"><strong>Pencarian:</strong> {{ $filters['search'] }}</p>
        @endif
    </div>

    @foreach($groupedApplications as $programName => $programApplications)
        <div class="{{ $loop->first ? '' : 'page-break' }}">
            <div class="section">
                <h2>Jurusan: {{ $programName }}</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 38px;">No.</th>
                            <th style="width: 80px;">NIPD</th>
                            <th style="width: 120px;">No pendaftaran</th>
                            <th>Nama</th>
                            <th>Asal Sekolah</th>
                            <th style="width: 90px;">Tanggal Lahir</th>
                            <th style="width: 90px;">Jenis Kelamin</th>
                            <th>Jurusan Diterima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programApplications as $application)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $application->nipd ?: '-' }}</td>
                                <td>{{ $application->nomor_pendaftaran }}</td>
                                <td>{{ $application->nama_lengkap }}</td>
                                <td>{{ $application->asal_sekolah }}</td>
                                <td>{{ $application->tanggal_lahir?->format('d-m-Y') ?? '-' }}</td>
                                <td>{{ $application->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</td>
                                <td>{{ $application->programDiterima?->nama_jurusan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    @if($groupedApplications->isEmpty())
        <table>
            <tbody>
                <tr>
                    <td>Tidak ada data untuk filter yang dipilih.</td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
