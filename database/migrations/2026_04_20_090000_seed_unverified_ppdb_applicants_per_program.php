<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    private const BATCH_MARKER = '[MIG-PPDB-UNVERIFIED-20260420]';

    public function up(): void
    {
        $period = DB::table('ppdb_periods')
            ->where('is_active', true)
            ->orderByDesc('id')
            ->first();

        if (! $period) {
            $period = DB::table('ppdb_periods')->orderByDesc('id')->first();
        }

        if (! $period) {
            return;
        }

        $programs = DB::table('program_keahlian')
            ->where('status_tampil', true)
            ->orderBy('id')
            ->get(['id', 'kode_jurusan', 'nama_jurusan']);

        if ($programs->isEmpty()) {
            $programs = DB::table('program_keahlian')
                ->orderBy('id')
                ->get(['id', 'kode_jurusan', 'nama_jurusan']);
        }

        if ($programs->isEmpty()) {
            return;
        }

        $defaultTrackId = DB::table('ppdb_tracks')
            ->where('period_id', $period->id)
            ->orderBy('urutan')
            ->orderBy('id')
            ->value('id');

        if (! $defaultTrackId) {
            return;
        }

        $quotaTrackByProgram = DB::table('ppdb_quotas')
            ->where('period_id', $period->id)
            ->where('status_aktif', true)
            ->whereNotNull('track_id')
            ->orderBy('id')
            ->get(['program_keahlian_id', 'track_id'])
            ->groupBy('program_keahlian_id')
            ->map(fn ($rows) => (int) $rows->first()->track_id);

        $programIds = $programs->pluck('id')->values()->all();
        $schools = [
            'SMP Negeri 1 Kolaka',
            'SMP Negeri 2 Kolaka',
            'SMP Negeri 3 Kolaka',
            'SMP Negeri 4 Kolaka',
            'SMP Negeri 1 Pomalaa',
            'SMP Negeri 2 Pomalaa',
            'MTs Negeri Kolaka',
            'SMP Negeri 1 Wundulako',
            'SMP Negeri 1 Latambaga',
            'SMP Negeri 1 Baula',
        ];
        $kelurahan = ['Laloeha', 'Sea', 'Balandete', 'Lamokato', 'Watuliandu', 'Sabilambo', 'Tahoa', 'Dawi-Dawi'];
        $kecamatan = ['Kolaka', 'Wundulako', 'Pomalaa', 'Latambaga', 'Baula'];
        $cities = ['Kolaka', 'Pomalaa', 'Wundulako', 'Kendari', 'Baubau'];
        $maleFirstNames = ['Ahmad', 'Rizky', 'Fajar', 'Akbar', 'Rafli', 'Arfan', 'Ilham', 'Reza', 'Dimas', 'Yusuf'];
        $femaleFirstNames = ['Aulia', 'Nadya', 'Salsa', 'Fitri', 'Nabila', 'Putri', 'Tiara', 'Aisyah', 'Intan', 'Nurmala'];
        $middleNames = ['Pratama', 'Saputra', 'Ramadhan', 'Hidayat', 'Rahma', 'Safitri', 'Lestari', 'Cahyani', 'Maulana', 'Febrianti'];
        $lastNames = ['Hasan', 'Rahman', 'Siregar', 'Mahendra', 'Firdaus', 'Amin', 'Kurniawan', 'Yunus', 'Akmal', 'Pangestika'];
        $fatherJobs = ['Petani', 'Wiraswasta', 'Nelayan', 'Karyawan Swasta', 'PNS', 'Pedagang'];
        $motherJobs = ['Ibu Rumah Tangga', 'Wiraswasta', 'Guru', 'Pedagang', 'PNS'];
        $religions = ['Islam', 'Islam', 'Islam', 'Kristen', 'Hindu'];

        $insertedAt = now();
        $year = (int) ($period->tahun_mulai ?: now()->year);

        foreach ($programs->values() as $programIndex => $program) {
            $trackId = (int) ($quotaTrackByProgram->get($program->id) ?? $defaultTrackId);

            for ($sequence = 1; $sequence <= 10; $sequence++) {
                $seed = ($programIndex + 1) * 100 + $sequence;
                $isMale = (($seed % 2) === 0);

                $firstName = $isMale
                    ? $maleFirstNames[$seed % count($maleFirstNames)]
                    : $femaleFirstNames[$seed % count($femaleFirstNames)];
                $middleName = $middleNames[($seed + 3) % count($middleNames)];
                $lastName = $lastNames[($seed + 5) % count($lastNames)];
                $fullName = trim($firstName . ' ' . $middleName . ' ' . $lastName);

                $school = $schools[$seed % count($schools)];
                $city = $cities[$seed % count($cities)];
                $kelurahanValue = $kelurahan[$seed % count($kelurahan)];
                $kecamatanValue = $kecamatan[$seed % count($kecamatan)];
                $rt = str_pad((string) (($seed % 9) + 1), 3, '0', STR_PAD_LEFT);
                $rw = str_pad((string) ((($seed + 2) % 9) + 1), 3, '0', STR_PAD_LEFT);

                $birthDate = now()
                    ->subYears(15 + ($seed % 3))
                    ->subDays(($seed * 7) % 360)
                    ->toDateString();

                $submissionTime = now()->subDays(($seed % 12) + 1)->subMinutes(($seed * 5) % 55);
                $programCode = strtoupper((string) ($program->kode_jurusan ?: ('JUR' . $program->id)));

                $nomorPendaftaran = $this->generateUniqueRegistrationNumber($year, $programCode, $sequence);
                $nisn = $this->generateUniqueNisn((int) $program->id, $sequence);
                $nik = $this->generateUniqueNik((int) $program->id, $sequence);
                $nomorHp = $this->generateUniquePhone((int) $program->id, $sequence);
                $nomorHpAyah = $this->generateUniquePhone((int) $program->id + 30, $sequence);
                $nomorHpIbu = $this->generateUniquePhone((int) $program->id + 60, $sequence);
                $email = $this->generateUniqueEmail($fullName, (int) $program->id, $sequence);

                $pilihanProgram2 = $this->resolveProgramChoice($programIds, (int) $program->id, 1);
                $pilihanProgram3 = $this->resolveProgramChoice($programIds, (int) $program->id, 2);

                $fatherName = 'Bapak ' . $middleNames[($seed + 1) % count($middleNames)] . ' ' . $lastName;
                $motherName = 'Ibu ' . $middleNames[($seed + 2) % count($middleNames)] . ' ' . $lastName;

                $applicationId = DB::table('ppdb_applications')->insertGetId([
                    'period_id' => $period->id,
                    'track_id' => $trackId,
                    'nomor_pendaftaran' => $nomorPendaftaran,
                    'nama_lengkap' => $fullName,
                    'nisn' => $nisn,
                    'nik' => $nik,
                    'jenis_kelamin' => $isMale ? 'L' : 'P',
                    'tempat_lahir' => $city,
                    'tanggal_lahir' => $birthDate,
                    'agama' => $religions[$seed % count($religions)],
                    'alamat_lengkap' => 'Jl. Pendidikan No. ' . (($seed % 40) + 1) . ', ' . $kelurahanValue . ', ' . $kecamatanValue . ', Kab. Kolaka',
                    'rt_rw' => $rt . '/' . $rw,
                    'kelurahan' => $kelurahanValue,
                    'kecamatan' => $kecamatanValue,
                    'tinggi_badan' => 150 + ($seed % 20),
                    'berat_badan' => 42 + ($seed % 18),
                    'gol_darah' => ['A', 'B', 'O', 'AB'][$seed % 4],
                    'ukuran_seragam' => ['S', 'M', 'L', 'XL'][$seed % 4],
                    'nomor_hp' => $nomorHp,
                    'email' => $email,
                    'asal_sekolah' => $school,
                    'alamat_sekolah' => 'Jl. Sekolah Raya No. ' . (($seed % 25) + 1) . ', Kolaka',
                    'anak_ke' => (($seed % 3) + 1),
                    'jumlah_saudara' => (($seed % 4) + 1),
                    'nama_ayah' => $fatherName,
                    'tempat_tanggal_lahir_ayah' => 'Kolaka, 12-08-1980',
                    'pendidikan_terakhir_ayah' => ['SMA', 'D3', 'S1'][$seed % 3],
                    'pekerjaan_ayah' => $fatherJobs[$seed % count($fatherJobs)],
                    'penghasilan_ayah' => ['< 2 Juta', '2-4 Juta', '4-6 Juta'][($seed + 1) % 3],
                    'alamat_ayah' => 'Alamat sama dengan siswa',
                    'kelurahan_ayah' => $kelurahanValue,
                    'kecamatan_ayah' => $kecamatanValue,
                    'nomor_hp_ayah' => $nomorHpAyah,
                    'nama_ibu' => $motherName,
                    'tempat_tanggal_lahir_ibu' => 'Kolaka, 03-11-1984',
                    'pendidikan_terakhir_ibu' => ['SMA', 'D3', 'S1'][$seed % 3],
                    'pekerjaan_ibu' => $motherJobs[$seed % count($motherJobs)],
                    'penghasilan_ibu' => ['< 2 Juta', '2-4 Juta', '4-6 Juta'][($seed + 2) % 3],
                    'alamat_ibu' => 'Alamat sama dengan siswa',
                    'kelurahan_ibu' => $kelurahanValue,
                    'kecamatan_ibu' => $kecamatanValue,
                    'nomor_hp_ibu' => $nomorHpIbu,
                    'nomor_hp_orang_tua' => $nomorHpAyah,
                    'pilihan_program_1_id' => $program->id,
                    'pilihan_program_2_id' => $pilihanProgram2,
                    'pilihan_program_3_id' => $pilihanProgram3,
                    'nilai_rata_rata' => number_format(78 + ($seed % 14) + (($seed % 4) * 0.25), 2, '.', ''),
                    'catatan_pendaftar' => 'Data pendaftaran diinput mandiri oleh calon siswa. ' . self::BATCH_MARKER,
                    'catatan_verifikator' => null,
                    'status_pendaftaran' => 'submitted',
                    'status_berkas' => 'pending',
                    'hasil_seleksi' => 'pending',
                    'status_daftar_ulang' => 'not_available',
                    'submitted_at' => $submissionTime,
                    'verified_at' => null,
                    'verified_by' => null,
                    'persetujuan_data_at' => $submissionTime,
                    'created_at' => $insertedAt,
                    'updated_at' => $insertedAt,
                ]);

                $documents = [
                    'Kartu Keluarga',
                    'Akta Kelahiran',
                    'Rapor Semester 1 s.d. 5',
                    'Pas Foto 3x4',
                ];

                foreach ($documents as $docIndex => $documentType) {
                    DB::table('ppdb_documents')->insert([
                        'application_id' => $applicationId,
                        'jenis_dokumen' => $documentType,
                        'file_path' => 'ppdb/simulasi/' . strtolower($programCode) . '/' . strtolower(Str::slug($documentType)) . '-' . $applicationId . '-' . ($docIndex + 1) . '.pdf',
                        'status_verifikasi' => 'pending',
                        'catatan_verifikasi' => null,
                        'created_at' => $insertedAt,
                        'updated_at' => $insertedAt,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        DB::table('ppdb_applications')
            ->where('catatan_pendaftar', 'like', '%' . self::BATCH_MARKER . '%')
            ->delete();
    }

    private function generateUniqueRegistrationNumber(int $year, string $programCode, int $sequence): string
    {
        $normalizedCode = strtoupper(preg_replace('/[^A-Z0-9]/', '', $programCode) ?: 'JUR');
        $base = 'PPDB-' . $year . '-' . $normalizedCode . '-' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
        $candidate = $base;
        $suffix = 1;

        while (DB::table('ppdb_applications')->where('nomor_pendaftaran', $candidate)->exists()) {
            $candidate = $base . '-' . str_pad((string) $suffix, 2, '0', STR_PAD_LEFT);
            $suffix++;
        }

        return $candidate;
    }

    private function generateUniquePhone(int $programId, int $sequence): string
    {
        $base = '08'
            . str_pad((string) (50 + ($programId % 40)), 2, '0', STR_PAD_LEFT)
            . str_pad((string) ($programId % 100), 2, '0', STR_PAD_LEFT)
            . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT)
            . '31';
        $candidate = $base;
        $suffix = 10;

        while (DB::table('ppdb_applications')->where('nomor_hp', $candidate)->exists()) {
            $candidate = substr($base, 0, 10) . str_pad((string) $suffix, 2, '0', STR_PAD_LEFT);
            $suffix++;
        }

        return $candidate;
    }

    private function generateUniqueEmail(string $fullName, int $programId, int $sequence): string
    {
        $slug = Str::of($fullName)->lower()->replaceMatches('/[^a-z0-9]+/', '.')->trim('.')->value();
        $baseLocal = $slug . '.p' . $programId . '.n' . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
        $candidate = $baseLocal . '@pendaftar.test';
        $suffix = 1;

        while (DB::table('ppdb_applications')->where('email', $candidate)->exists()) {
            $candidate = $baseLocal . '.' . $suffix . '@pendaftar.test';
            $suffix++;
        }

        return $candidate;
    }

    private function generateUniqueNisn(int $programId, int $sequence): string
    {
        $candidate = sprintf('99%02d%06d', $programId % 100, $sequence);
        $counter = 1;

        while (DB::table('ppdb_applications')->where('nisn', $candidate)->exists()) {
            $candidate = sprintf('99%02d%06d', $programId % 100, $sequence + ($counter * 100));
            $counter++;
        }

        return $candidate;
    }

    private function generateUniqueNik(int $programId, int $sequence): string
    {
        $candidate = sprintf('7401%02d%02d%08d', $programId % 100, $sequence % 100, ($programId * 1000) + $sequence);
        $counter = 1;

        while (DB::table('ppdb_applications')->where('nik', $candidate)->exists()) {
            $candidate = sprintf('7401%02d%02d%08d', $programId % 100, $sequence % 100, ($programId * 1000) + $sequence + ($counter * 50));
            $counter++;
        }

        return $candidate;
    }

    private function resolveProgramChoice(array $programIds, int $currentProgramId, int $offset): ?int
    {
        if (count($programIds) < 2) {
            return null;
        }

        $currentIndex = array_search($currentProgramId, $programIds, true);

        if ($currentIndex === false) {
            return null;
        }

        $targetIndex = ($currentIndex + $offset) % count($programIds);
        $targetId = $programIds[$targetIndex] ?? null;

        return $targetId === $currentProgramId ? null : $targetId;
    }
};
