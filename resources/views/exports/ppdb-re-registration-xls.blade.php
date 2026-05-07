<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export PPDB</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 11px;
            vertical-align: middle;
        }

        .meta-cell {
            border: none;
            padding: 2px 4px;
            text-align: left;
        }

        .meta-title {
            font-size: 14px;
            font-weight: 700;
        }

        .meta-school {
            font-size: 18px;
            font-weight: 700;
        }

        .section-title,
        .header-cell {
            font-weight: 700;
            text-align: center;
            white-space: normal;
        }
    </style>
</head>
<body>
    <table>
        @if(($scope ?? '') === 're-registration-audit')
            <thead>
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
            </thead>
            <tbody>
                @foreach($applications as $application)
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
                @endforeach
            </tbody>
        @elseif(($scope ?? '') === 're-registration')
            <thead>
                <tr>
                    <th>No.</th>
                    <th>NIPD</th>
                    <th>No pendaftaran</th>
                    <th>Nama</th>
                    <th>Asal Sekolah</th>
                    <th>Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>Jurusan Diterima</th>
                </tr>
            </thead>
            <tbody>
                @foreach($applications as $application)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $application->nipd ?: '-' }}</td>
                        <td>{{ $application->nomor_pendaftaran }}</td>
                        <td>{{ $application->nama_lengkap }}</td>
                        <td>{{ $application->asal_sekolah }}</td>
                        <td>{{ $application->tanggal_lahir?->format('d-m-Y') }}</td>
                        <td>{{ $application->jenis_kelamin === 'P' ? 'Perempuan' : 'Laki-laki' }}</td>
                        <td>{{ $application->programDiterima?->nama_jurusan }}</td>
                    </tr>
                @endforeach
            </tbody>
        @else
            @php($columnCount = count($studentColumns ?? []))
            <tr>
                <td colspan="{{ $columnCount }}" class="meta-cell meta-title">Daftar Peserta Didik</td>
            </tr>
            <tr>
                <td colspan="{{ $columnCount }}" class="meta-cell meta-school">{{ $exportMeta['school_name'] ?? 'SMKN 1 KOLAKA' }}</td>
            </tr>
            <tr>
                <td colspan="{{ $columnCount }}" class="meta-cell">{{ $exportMeta['school_address'] ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="{{ $columnCount }}" class="meta-cell">
                    Tanggal UPD Pendataan: {{ $exportMeta['updated_at'] ?? '-' }}
                    (TP. {{ $exportMeta['academic_year'] ?? '-' }},
                    {{ $exportMeta['operator_name'] ?? 'Admin' }}
                    ({{ $exportMeta['operator_email'] ?? '-' }}))
                </td>
            </tr>
            <tr>
                <td colspan="{{ $columnCount }}" class="meta-cell">&nbsp;</td>
            </tr>
            <thead>
                <tr>
                    @foreach(array_slice($studentColumns, 0, 24) as $column)
                        <th rowspan="2" class="header-cell">{{ $column['label'] }}</th>
                    @endforeach
                    <th colspan="6" class="section-title">Data Ayah</th>
                    <th colspan="6" class="section-title">Data Ibu</th>
                    <th colspan="6" class="section-title">Data Wali</th>
                    @foreach(array_slice($studentColumns, 42) as $column)
                        <th rowspan="2" class="header-cell">{{ $column['label'] }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach(array_slice($studentColumns, 24, 18) as $column)
                        <th class="header-cell">{{ $column['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($studentRows as $row)
                    <tr>
                        @foreach($studentColumns as $column)
                            <td>{{ $row[$column['key']] ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        @endif
    </table>
</body>
</html>
