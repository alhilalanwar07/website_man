<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PpdbApplication;
use App\Models\PpdbPeriod;
use App\Models\ProgramKeahlian;
use App\Models\ProfilSekolah;
use App\Support\PpdbPeriodResolver;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PpdbExportController extends Controller
{
    public function __invoke(Request $request): Response|StreamedResponse
    {
        $resolver = app(PpdbPeriodResolver::class);
        $activePeriod = $resolver->resolveAdmin($request->integer('period_id'));

        abort_unless($activePeriod, 404, 'Periode PPDB tidak ditemukan.');

        $scope = $request->string('scope')->value();
        $type = $request->string('type')->value() ?: 'applicant';
        $isAuditExport = $scope === 're-registration-audit';
        $isAssessmentExport = $type === 'assessment';
        $format = strtolower($request->string('format')->value() ?: 'csv');

        if ($format === 'xls' || $format === 'xlsx') {
            $format = 'xlsx';
        }

        $applications = PpdbApplication::with(['track', 'pilihanProgram1', 'programDiterima', 'reRegistrationVerifier'])
            ->where('period_id', $activePeriod->id)
            ->when($isAssessmentExport, fn ($query) => $query->whereIn('hasil_seleksi', ['passed', 'lulus']))
            ->when(in_array($scope, ['re-registration', 'applicant-data', 'assessment-form'], true), fn ($query) => $query->whereIn('hasil_seleksi', ['passed', 'lulus']))
            ->when($isAuditExport, function ($query) use ($request) {
                $query->whereIn('hasil_seleksi', ['passed', 'lulus'])
                    ->whereIn('status_daftar_ulang', ['verified', 'rejected'])
                    ->whereNotNull('verified_daftar_ulang_at')
                    ->when($request->filled('audit_officer'), fn ($auditQuery) => $auditQuery->where('verified_daftar_ulang_by', $request->integer('audit_officer')))
                    ->when($request->filled('audit_status'), fn ($auditQuery) => $auditQuery->where('status_daftar_ulang', $request->string('audit_status')->value()))
                    ->when($request->filled('audit_date_from'), fn ($auditQuery) => $auditQuery->whereDate('verified_daftar_ulang_at', '>=', $request->string('audit_date_from')->value()))
                    ->when($request->filled('audit_date_to'), fn ($auditQuery) => $auditQuery->whereDate('verified_daftar_ulang_at', '<=', $request->string('audit_date_to')->value()));
            })
            ->when($request->filled('track_id'), fn ($query) => $query->where('track_id', $request->integer('track_id')))
            ->when($request->filled('program_id'), fn ($query) => $query->where('program_diterima_id', $request->integer('program_id')))
            ->when($request->filled('verification_status'), fn ($query) => $query->whereVerificationStatus($request->string('verification_status')->value()))
            ->when($request->filled('registration_status'), fn ($query) => $query->where('status_daftar_ulang', $request->string('registration_status')->value()))
            ->when($request->filled('selection_result'), fn ($query) => $query->where('hasil_seleksi', $request->string('selection_result')->value()))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->value();

                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nomor_pendaftaran', 'like', "%{$search}%")
                        ->orWhere('asal_sekolah', 'like', "%{$search}%")
                        ->orWhere('nipd', 'like', "%{$search}%");
                });
            })
            ->orderBy($isAuditExport ? 'verified_daftar_ulang_by' : 'track_id')
            ->orderByDesc($isAuditExport ? 'verified_daftar_ulang_at' : 'skor_seleksi')
            ->orderBy('nama_lengkap')
            ->get();

        $filenamePrefix = match ($scope) {
            're-registration' => 'hasil-daftar-ulang-ppdb',
            're-registration-audit' => 'audit-daftar-ulang-ppdb',
            'applicant-data' => 'data-peserta-didik',
            default => 'hasil-ppdb',
        };

        if ($isAssessmentExport) {
            $filenamePrefix = 'penilaian-calon-siswa';
        }

        $timestamp = now()->format('Ymd-His');

        if ($format === 'xlsx') {
            $filename = $filenamePrefix . '-' . $timestamp . '.xlsx';

            $buildMethod = match (true) {
                $isAssessmentExport => fn () => $this->buildAssessmentSpreadsheet($applications, $activePeriod),
                $scope === 'applicant-data' => fn () => $this->buildApplicantDataSpreadsheet($applications, $activePeriod, $request),
                default => fn () => $this->buildAssessmentSpreadsheet($applications, $activePeriod),
            };

            return response()->streamDownload(function () use ($buildMethod): void {
                $spreadsheet = $buildMethod();
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                $spreadsheet->disconnectWorksheets();
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        if ($format === 'pdf') {
            $profilSekolah = ProfilSekolah::query()->first();
            $kepalaSekolah = Pegawai::query()
                ->aktif()
                ->where(function (Builder $query): void {
                    $query->where('jabatan', 'like', '%Kepala Sekolah%')
                        ->orWhere('jabatan', 'like', '%Kepsek%');
                })
                ->orderByDesc('updated_at')
                ->first();

            $logoAbsolutePath = null;
            $logoPath = trim((string) ($profilSekolah?->logo_path ?? ''));

            if ($logoPath !== '') {
                $storageLogoPath = storage_path('app/public/' . ltrim($logoPath, '/'));

                if (is_file($storageLogoPath)) {
                    $logoAbsolutePath = $storageLogoPath;
                }
            }

            $documentMeta = [
                'school_name' => $profilSekolah?->nama_sekolah ?: 'SMK Negeri 1 Kolaka',
                'school_npsn' => $profilSekolah?->npsn,
                'school_address' => $profilSekolah?->alamat_lengkap,
                'school_phone' => $profilSekolah?->nomor_telepon,
                'school_email' => $profilSekolah?->email_resmi,
                'logo_absolute_path' => $logoAbsolutePath,
                'sign_location' => 'Kolaka',
                'sign_date' => now()->translatedFormat('d F Y'),
                'sign_title' => $kepalaSekolah?->jabatan ?: 'Kepala Sekolah',
                'sign_name' => $kepalaSekolah?->nama_lengkap ?: '........................................',
                'sign_nip' => $kepalaSekolah?->nip,
            ];

            $filters = [
                'status_daftar_ulang' => $request->string('registration_status')->value(),
                'program_id' => $request->integer('program_id'),
                'search' => $request->string('search')->value(),
            ];

            // Use comprehensive Dapodik-style template for re-registration scope
            if ($scope === 're-registration') {
                $pdf = Pdf::loadView('pdf.admin.ppdb-dapodik-export', [
                    'applications' => $applications,
                    'period' => $activePeriod,
                    'filters' => $filters,
                    'documentMeta' => $documentMeta,
                ])->setPaper('a4', 'landscape');

                return $pdf->download($filenamePrefix . '-' . $timestamp . '.pdf');
            }

            // Assessment form for teachers (wawancara & akademik)
            if ($scope === 'assessment-form') {
                $allPrograms = ProgramKeahlian::query()
                    ->tampil()
                    ->orderBy('nama_jurusan')
                    ->get(['id', 'nama_jurusan']);

                $pdf = Pdf::loadView('pdf.admin.ppdb-assessment-form', [
                    'applications' => $applications,
                    'period' => $activePeriod,
                    'programs' => $allPrograms,
                    'documentMeta' => $documentMeta,
                ])->setPaper('a4', 'landscape');

                return $pdf->download('form-penilaian-tes-' . $timestamp . '.pdf');
            }

            $pdf = Pdf::loadView('pdf.admin.ppdb-re-registration-export', [
                'applications' => $applications,
                'period' => $activePeriod,
                'scope' => $scope,
                'filters' => $filters,
                'documentMeta' => $documentMeta,
            ])->setPaper('a4', 'landscape');

            return $pdf->download($filenamePrefix . '-' . $timestamp . '.pdf');
        }

        // Legacy xls format removed — all Excel exports now use xlsx via PhpSpreadsheet

        $filename = $filenamePrefix . '-' . $timestamp . '.csv';

        return response()->streamDownload(function () use ($applications, $activePeriod, $isAuditExport): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $isAuditExport ? [
                'Periode',
                'Tahun Ajaran',
                'Nomor Pendaftaran',
                'Nama Lengkap',
                'Jalur',
                'Program Diterima',
                'Status Daftar Ulang',
                'Diproses Oleh',
                'Tanggal Diproses',
                'Catatan Audit',
            ] : [
                'Periode',
                'Tahun Ajaran',
                'Nomor Pendaftaran',
                'Nama Lengkap',
                'Asal Sekolah',
                'Jalur',
                'Pilihan 1',
                'Program Diterima',
                'Status Pendaftaran',
                'Status Berkas',
                'Status Verifikasi Gabungan',
                'Hasil Seleksi',
                'Status Daftar Ulang',
                'Skor Seleksi',
                'Ranking Jalur',
                'Ranking Program',
                'Tanggal Submit',
                'Tanggal Daftar Ulang',
                'Diproses Oleh',
                'Tanggal Diproses',
            ]);

            foreach ($applications as $application) {
                fputcsv($handle, $isAuditExport ? [
                    $activePeriod->nama_periode,
                    $activePeriod->tahun_ajaran,
                    $application->nomor_pendaftaran,
                    $application->nama_lengkap,
                    $application->track?->nama_jalur,
                    $application->programDiterima?->nama_jurusan,
                    $application->status_daftar_ulang,
                    $application->reRegistrationVerifier?->name,
                    $application->verified_daftar_ulang_at?->format('Y-m-d H:i:s'),
                    $application->catatan_daftar_ulang,
                ] : [
                    $activePeriod->nama_periode,
                    $activePeriod->tahun_ajaran,
                    $application->nomor_pendaftaran,
                    $application->nama_lengkap,
                    $application->asal_sekolah,
                    $application->track?->nama_jalur,
                    $application->pilihanProgram1?->nama_jurusan,
                    $application->programDiterima?->nama_jurusan,
                    $application->status_pendaftaran,
                    $application->status_berkas,
                    $application->verification_status_label,
                    $application->hasil_seleksi,
                    $application->status_daftar_ulang,
                    $application->skor_seleksi,
                    $application->ranking_jalur,
                    $application->ranking_program,
                    $application->submitted_at?->format('Y-m-d H:i:s'),
                    $application->daftar_ulang_at?->format('Y-m-d H:i:s'),
                    $application->reRegistrationVerifier?->name,
                    $application->verified_daftar_ulang_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function buildApplicantExportMeta(Request $request, mixed $period, ?ProfilSekolah $profilSekolah): array
    {
        $operator = $request->user();

        return [
            'school_name' => strtoupper((string) ($profilSekolah?->nama_sekolah ?: 'SMKN 1 KOLAKA')),
            'school_address' => (string) ($profilSekolah?->alamat_lengkap ?: '-'),
            'updated_at' => now()->format('d-m-Y H:i:s'),
            'academic_year' => (string) ($period?->tahun_ajaran ?: '-'),
            'operator_name' => (string) ($operator?->name ?: 'Admin'),
            'operator_email' => (string) ($operator?->email ?: '-'),
        ];
    }

    protected function buildApplicantExcelColumns(): array
    {
        return [
            ['key' => 'no', 'label' => 'No'],
            ['key' => 'nama', 'label' => 'Nama'],
            ['key' => 'nipd', 'label' => 'NIPD'],
            ['key' => 'jk', 'label' => 'JK'],
            ['key' => 'nisn', 'label' => 'NISN'],
            ['key' => 'tempat_lahir', 'label' => 'Tempat Lahir'],
            ['key' => 'tanggal_lahir', 'label' => 'Tanggal Lahir'],
            ['key' => 'nik', 'label' => 'NIK'],
            ['key' => 'agama', 'label' => 'Agama'],
            ['key' => 'alamat', 'label' => 'Alamat'],
            ['key' => 'rt', 'label' => 'RT'],
            ['key' => 'rw', 'label' => 'RW'],
            ['key' => 'dusun', 'label' => 'Dusun'],
            ['key' => 'kelurahan', 'label' => 'Kelurahan'],
            ['key' => 'kecamatan', 'label' => 'Kecamatan'],
            ['key' => 'kode_pos', 'label' => 'Kode Pos'],
            ['key' => 'jenis_tinggal', 'label' => 'Jenis Tinggal'],
            ['key' => 'alat_transportasi', 'label' => 'Alat Transportasi'],
            ['key' => 'telepon', 'label' => 'Telepon'],
            ['key' => 'hp', 'label' => 'HP'],
            ['key' => 'email', 'label' => 'E-Mail'],
            ['key' => 'skhun', 'label' => 'SKHUN'],
            ['key' => 'penerima_kps', 'label' => 'Penerima KPS'],
            ['key' => 'no_kps', 'label' => 'No. KPS'],
            ['key' => 'nama_ayah', 'label' => 'Nama'],
            ['key' => 'tahun_lahir_ayah', 'label' => 'Tahun Lahir'],
            ['key' => 'pendidikan_ayah', 'label' => 'Jenjang Pendidikan'],
            ['key' => 'pekerjaan_ayah', 'label' => 'Pekerjaan'],
            ['key' => 'penghasilan_ayah', 'label' => 'Penghasilan'],
            ['key' => 'nik_ayah', 'label' => 'NIK'],
            ['key' => 'nama_ibu', 'label' => 'Nama'],
            ['key' => 'tahun_lahir_ibu', 'label' => 'Tahun Lahir'],
            ['key' => 'pendidikan_ibu', 'label' => 'Jenjang Pendidikan'],
            ['key' => 'pekerjaan_ibu', 'label' => 'Pekerjaan'],
            ['key' => 'penghasilan_ibu', 'label' => 'Penghasilan'],
            ['key' => 'nik_ibu', 'label' => 'NIK'],
            ['key' => 'nama_wali', 'label' => 'Nama'],
            ['key' => 'tahun_lahir_wali', 'label' => 'Tahun Lahir'],
            ['key' => 'pendidikan_wali', 'label' => 'Jenjang Pendidikan'],
            ['key' => 'pekerjaan_wali', 'label' => 'Pekerjaan'],
            ['key' => 'penghasilan_wali', 'label' => 'Penghasilan'],
            ['key' => 'nik_wali', 'label' => 'NIK'],
            ['key' => 'rombel', 'label' => 'Rombel Saat Ini'],
            ['key' => 'no_un', 'label' => 'No Peserta Ujian Nasional'],
            ['key' => 'no_seri_ijazah', 'label' => 'No Seri Ijazah'],
            ['key' => 'penerima_kip', 'label' => 'Penerima KIP'],
            ['key' => 'nomor_kip', 'label' => 'Nomor KIP'],
            ['key' => 'nama_di_kip', 'label' => 'Nama di KIP'],
            ['key' => 'nomor_kks', 'label' => 'Nomor KKS'],
            ['key' => 'no_registrasi_akta_lahir', 'label' => 'No Registrasi Akta Lahir'],
            ['key' => 'bank', 'label' => 'Bank'],
            ['key' => 'nomor_rekening_bank', 'label' => 'Nomor Rekening Bank'],
            ['key' => 'rekening_atas_nama', 'label' => 'Rekening Atas Nama'],
            ['key' => 'layak_pip', 'label' => 'Layak PIP (usulan dari sekolah)'],
            ['key' => 'alasan_layak_pip', 'label' => 'Alasan Layak PIP'],
            ['key' => 'kebutuhan_khusus', 'label' => 'Kebutuhan Khusus'],
            ['key' => 'sekolah_asal', 'label' => 'Sekolah Asal'],
            ['key' => 'anak_ke', 'label' => 'Anak ke-berapa'],
            ['key' => 'lintang', 'label' => 'Lintang'],
            ['key' => 'bujur', 'label' => 'Bujur'],
            ['key' => 'no_kk', 'label' => 'No KK'],
            ['key' => 'berat_badan', 'label' => 'Berat Badan'],
            ['key' => 'tinggi_badan', 'label' => 'Tinggi Badan'],
            ['key' => 'lingkar_kepala', 'label' => 'Lingkar Kepala'],
            ['key' => 'jumlah_saudara', 'label' => 'Jml. Saudara Kandung'],
            ['key' => 'jarak_rumah_km', 'label' => 'Jarak Rumah ke Sekolah (KM)'],
        ];
    }

    protected function buildApplicantExcelRows($applications): array
    {
        return $applications
            ->values()
            ->map(function (PpdbApplication $application, int $index): array {
                $hasWelfareCard = filled($application->nomor_kartu_kesejahteraan) || filled($application->jenis_kesejahteraan);

                return [
                    'no' => $index + 1,
                    'nama' => $application->nama_lengkap,
                    'nipd' => $application->nipd,
                    'jk' => $application->jenis_kelamin,
                    'nisn' => $application->nisn,
                    'tempat_lahir' => $application->tempat_lahir,
                    'tanggal_lahir' => $application->tanggal_lahir?->format('d-m-Y'),
                    'nik' => $application->nik,
                    'agama' => $application->agama,
                    'alamat' => $application->alamat_lengkap,
                    'rt' => $application->rt,
                    'rw' => $application->rw,
                    'dusun' => $application->nama_dusun,
                    'kelurahan' => $application->kelurahan,
                    'kecamatan' => $application->kecamatan,
                    'kode_pos' => $application->kode_pos,
                    'jenis_tinggal' => $application->tempat_tinggal,
                    'alat_transportasi' => $application->moda_transportasi,
                    'telepon' => $application->nomor_telepon_rumah,
                    'hp' => $application->nomor_hp,
                    'email' => $application->email,
                    'skhun' => '',
                    'penerima_kps' => $hasWelfareCard ? 'Ya' : '',
                    'no_kps' => '',
                    'nama_ayah' => $application->nama_ayah,
                    'tahun_lahir_ayah' => $this->extractYearFromCombinedBirth($application->tempat_tanggal_lahir_ayah),
                    'pendidikan_ayah' => $application->pendidikan_terakhir_ayah,
                    'pekerjaan_ayah' => $application->pekerjaan_ayah,
                    'penghasilan_ayah' => $application->penghasilan_ayah,
                    'nik_ayah' => $application->nik_ayah,
                    'nama_ibu' => $application->nama_ibu,
                    'tahun_lahir_ibu' => $this->extractYearFromCombinedBirth($application->tempat_tanggal_lahir_ibu),
                    'pendidikan_ibu' => $application->pendidikan_terakhir_ibu,
                    'pekerjaan_ibu' => $application->pekerjaan_ibu,
                    'penghasilan_ibu' => $application->penghasilan_ibu,
                    'nik_ibu' => $application->nik_ibu,
                    'nama_wali' => '',
                    'tahun_lahir_wali' => '',
                    'pendidikan_wali' => '',
                    'pekerjaan_wali' => '',
                    'penghasilan_wali' => '',
                    'nik_wali' => '',
                    'rombel' => '',
                    'no_un' => '',
                    'no_seri_ijazah' => '',
                    'penerima_kip' => $application->punya_kip ? 'Ya' : '',
                    'nomor_kip' => '',
                    'nama_di_kip' => '',
                    'nomor_kks' => $application->nomor_kartu_kesejahteraan,
                    'no_registrasi_akta_lahir' => $application->no_registrasi_akta_lahir,
                    'bank' => '',
                    'nomor_rekening_bank' => '',
                    'rekening_atas_nama' => '',
                    'layak_pip' => '',
                    'alasan_layak_pip' => '',
                    'kebutuhan_khusus' => $application->kebutuhan_khusus,
                    'sekolah_asal' => $application->asal_sekolah,
                    'anak_ke' => $application->anak_ke,
                    'lintang' => $application->lintang,
                    'bujur' => $application->bujur,
                    'no_kk' => $application->no_kk,
                    'berat_badan' => $application->berat_badan,
                    'tinggi_badan' => $application->tinggi_badan,
                    'lingkar_kepala' => $application->lingkar_kepala,
                    'jumlah_saudara' => $application->jumlah_saudara,
                    'jarak_rumah_km' => $application->jarak_tempat_tinggal_km,
                ];
            })
            ->all();
    }

    protected function extractYearFromCombinedBirth(?string $value): string
    {
        if (! filled($value)) {
            return '';
        }

        preg_match('/(19|20)\d{2}/', (string) $value, $matches);

        return $matches[0] ?? '';
    }

    protected function buildAssessmentSpreadsheet($applications, ?PpdbPeriod $period): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Penilaian');

        // Get all programs for checkboxes
        $allPrograms = ProgramKeahlian::query()
            ->tampil()
            ->orderBy('nama_jurusan')
            ->get(['id', 'nama_jurusan']);

        // Headers
        $headers = ['No', 'Nomor Pendaftaran', 'Nama Lengkap', 'Tanggal Lahir', 'Nilai Wawancara', 'Nilai Akademik'];
        
        // Add program columns as checkboxes
        foreach ($allPrograms as $program) {
            $headers[] = $program->nama_jurusan;
        }

        // Add teacher info columns
        $headers = array_merge($headers, ['Nama Guru (Kosongkan)', 'Tanggal (Kosongkan)', 'TTD (Kosongkan)']);

        $this->writeRow($sheet, 1, $headers);
        $this->styleHeaderBlock($sheet, 1, 1, count($headers));

        // Data rows
        $rowIndex = 2;
        foreach ($applications as $index => $application) {
            $rowData = [
                $index + 1,
                $application->nomor_pendaftaran,
                $application->nama_lengkap,
                $application->tanggal_lahir?->format('d-m-Y') ?? '',
                '', // Nilai Wawancara (kosong untuk diisi)
                '', // Nilai Akademik (kosong untuk diisi)
            ];

            // Add program checkboxes (empty, user fills manually)
            foreach ($allPrograms as $program) {
                $isSelected = $application->program_diterima_id === $program->id ? '☑' : '☐';
                $rowData[] = $isSelected;
            }

            // Add teacher info (empty)
            $rowData = array_merge($rowData, ['', '', '']);

            $this->writeRow($sheet, $rowIndex, $rowData);
            $rowIndex++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        
        // Set widths for program columns
        for ($i = 7; $i < 7 + count($allPrograms); $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setWidth(12);
        }

        // Teacher info columns
        $lastCol = 7 + count($allPrograms);
        for ($i = $lastCol; $i < $lastCol + 3; $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($col)->setWidth(15);
        }

        $sheet->freezePane('A2');

        return $spreadsheet;
    }

    protected function buildApplicantDataSpreadsheet($applications, ?PpdbPeriod $period, Request $request): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Peserta Didik');

        $profilSekolah = ProfilSekolah::query()->first();
        $meta = $this->buildApplicantExportMeta($request, $period, $profilSekolah);

        // Meta rows
        $columns = $this->buildApplicantExcelColumns();
        $colCount = count($columns);
        $lastCol = Coordinate::stringFromColumnIndex($colCount);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'Daftar Peserta Didik');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', $meta['school_name'] ?? 'SMKN 1 KOLAKA');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(16);

        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', $meta['school_address'] ?? '-');

        $sheet->mergeCells("A4:{$lastCol}4");
        $sheet->setCellValue('A4', 'Tanggal: ' . ($meta['updated_at'] ?? '-') . ' (TP. ' . ($meta['academic_year'] ?? '-') . ', ' . ($meta['operator_name'] ?? 'Admin') . ')');

        // Header row 1 (row 6) - individual cols rowspan=2 + grouped parent headers
        $headerRow1 = 6;
        $headerRow2 = 7;
        $dataStartRow = 8;

        // Individual columns (0-23) with merge
        for ($i = 0; $i < 24; $i++) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $headerRow1, $columns[$i]['label']);
            $sheet->mergeCells($col . $headerRow1 . ':' . $col . $headerRow2);
        }

        // Grouped parent headers
        $ayahStart = Coordinate::stringFromColumnIndex(25);
        $ayahEnd = Coordinate::stringFromColumnIndex(30);
        $sheet->mergeCells($ayahStart . $headerRow1 . ':' . $ayahEnd . $headerRow1);
        $sheet->setCellValue($ayahStart . $headerRow1, 'Data Ayah');

        $ibuStart = Coordinate::stringFromColumnIndex(31);
        $ibuEnd = Coordinate::stringFromColumnIndex(36);
        $sheet->mergeCells($ibuStart . $headerRow1 . ':' . $ibuEnd . $headerRow1);
        $sheet->setCellValue($ibuStart . $headerRow1, 'Data Ibu');

        $waliStart = Coordinate::stringFromColumnIndex(37);
        $waliEnd = Coordinate::stringFromColumnIndex(42);
        $sheet->mergeCells($waliStart . $headerRow1 . ':' . $waliEnd . $headerRow1);
        $sheet->setCellValue($waliStart . $headerRow1, 'Data Wali');

        // Sub-headers for parents (row 7)
        for ($i = 24; $i < 42; $i++) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $headerRow2, $columns[$i]['label']);
        }

        // Remaining individual columns (42+) with merge
        for ($i = 42; $i < $colCount; $i++) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . $headerRow1, $columns[$i]['label']);
            $sheet->mergeCells($col . $headerRow1 . ':' . $col . $headerRow2);
        }

        // Style headers
        $this->styleHeaderBlock($sheet, $headerRow1, 1, $colCount);
        $this->styleHeaderBlock($sheet, $headerRow2, 25, 42);

        // Data rows
        $rows = $this->buildApplicantExcelRows($applications);
        foreach ($rows as $rowIndex => $row) {
            $excelRow = $dataStartRow + $rowIndex;
            $colIdx = 1;
            foreach ($columns as $column) {
                $col = Coordinate::stringFromColumnIndex($colIdx);
                $value = $row[$column['key']] ?? '';
                $sheet->setCellValue($col . $excelRow, $value);
                $colIdx++;
            }
        }

        // Apply borders to data area
        $lastDataRow = $dataStartRow + count($rows) - 1;
        if ($lastDataRow >= $dataStartRow) {
            $sheet->getStyle("A{$headerRow1}:{$lastCol}{$lastDataRow}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        // Column widths
        $widths = [5, 22, 10, 4, 12, 14, 12, 18, 8, 25, 4, 4, 10, 12, 12, 8, 12, 14, 14, 14, 20, 8, 8, 8];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i + 1))->setWidth($w);
        }
        // Parent and remaining columns
        for ($i = 25; $i <= $colCount; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth(14);
        }

        $sheet->freezePane('A' . $dataStartRow);

        return $spreadsheet;
    }

    protected function writeRow(Worksheet $sheet, int $row, array $data): void
    {
        foreach ($data as $colIndex => $value) {
            $col = Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($col . $row, $value);
        }
    }

    protected function styleHeaderBlock(Worksheet $sheet, int $row, int $startCol, int $endCol): void
    {
        $startColStr = Coordinate::stringFromColumnIndex($startCol);
        $endColStr = Coordinate::stringFromColumnIndex($endCol);
        $range = $startColStr . $row . ':' . $endColStr . $row;

        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE2E8F0'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);
    }
}
