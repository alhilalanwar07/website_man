<?php

namespace App\Livewire\Frontend;

use App\Mail\PpdbRegistrationSubmittedMail;
use App\Models\PpdbAchievement;
use App\Models\PpdbApplication;
use App\Models\PpdbDocument;
use App\Models\ProgramKeahlian;
use App\Support\PpdbPeriodResolver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL as SignedUrl;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

#[Layout('components.layouts.app')]
#[Title('Form Pendaftaran PPDB - SMK Negeri 1 Kolaka')]
class PpdbFormPage extends Component
{
    use WithFileUploads;

    #[Url(as: 'periode')]
    public string $selectedPeriod = '';

    public string $nama_lengkap = '';
    public string $nisn = '';
    public string $nik = '';
    public string $jenis_kelamin = 'L';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $agama = 'Islam';
    public string $alamat_lengkap = '';
    public string $rt_rw = '';
    public string $kelurahan = '';
    public string $kecamatan = '';
    public string $tinggi_badan = '';
    public string $berat_badan = '';
    public string $gol_darah = '';
    public string $ukuran_seragam = '';
    public string $nomor_hp = '';
    public string $email = '';
    public string $asal_sekolah = '';
    public string $alamat_sekolah = '';
    public string $anak_ke = '';
    public string $jumlah_saudara = '';
    public string $nama_ayah = '';
    public string $tempat_tanggal_lahir_ayah = '';
    public string $pendidikan_terakhir_ayah = '';
    public string $pekerjaan_ayah = '';
    public string $penghasilan_ayah = '';
    public string $alamat_ayah = '';
    public string $kelurahan_ayah = '';
    public string $kecamatan_ayah = '';
    public string $nomor_hp_ayah = '';
    public string $nama_ibu = '';
    public string $tempat_tanggal_lahir_ibu = '';
    public string $pendidikan_terakhir_ibu = '';
    public string $pekerjaan_ibu = '';
    public string $penghasilan_ibu = '';
    public string $alamat_ibu = '';
    public string $kelurahan_ibu = '';
    public string $kecamatan_ibu = '';
    public string $nomor_hp_ibu = '';
    public string $nomor_hp_orang_tua = '';
    public string $track_id = '';
    public string $pilihan_program_1_id = '';
    public string $pilihan_program_2_id = '';
    public string $nilai_rata_rata = '';
    public string $catatan_pendaftar = '';
    public bool $persetujuan_data = false;

    public array $prestasi = [];

    public $file_kk;
    public $file_akta;
    public $file_rapor;
    public $file_pas_foto;
    public $file_skl;

    public ?string $submittedNumber = null;
    public ?string $submittedDownloadUrl = null;
    public ?string $submittedEmail = null;
    public bool $submissionEmailSent = false;

    public function mount(): void
    {
        $this->initializePrestasiRows();

        $period = $this->resolveSelectedPeriod();

        if ($period && $period->tracks->isNotEmpty()) {
            $this->track_id = (string) $period->tracks->first()->id;
        }

        if ($period && $this->selectedPeriod === '') {
            $this->selectedPeriod = (string) $period->id;
        }
    }

    public function updatedSelectedPeriod(): void
    {
        $period = $this->resolveSelectedPeriod();
        $this->track_id = $period && $period->tracks->isNotEmpty() ? (string) $period->tracks->first()->id : '';
    }

    public function addPrestasiRow(): void
    {
        if (count($this->prestasi) >= 3) {
            return;
        }

        $this->prestasi[] = $this->emptyPrestasiRow();
    }

    public function removePrestasiRow(int $index): void
    {
        if (! isset($this->prestasi[$index])) {
            return;
        }

        unset($this->prestasi[$index]);
        $this->prestasi = array_values($this->prestasi);

        $this->initializePrestasiRows();
    }

    public function submitApplication(): void
    {
        $period = $this->resolveSelectedPeriod();

        if (! $period) {
            $this->addError('period', 'Periode PPDB belum dibuka.');
            return;
        }

        if (! $period->isRegistrationOpen()) {
            $this->addError('period', 'Gelombang atau periode yang dipilih saat ini belum dibuka untuk pendaftaran.');
            return;
        }

        $this->email = strtolower(trim($this->email));
        $this->nomor_hp = $this->normalizePhoneNumber($this->nomor_hp);
        $this->nomor_hp_orang_tua = $this->normalizeOptionalPhone($this->nomor_hp_orang_tua) ?? '';
        $this->nomor_hp_ayah = $this->normalizeOptionalPhone($this->nomor_hp_ayah) ?? '';
        $this->nomor_hp_ibu = $this->normalizeOptionalPhone($this->nomor_hp_ibu) ?? '';

        $validated = $this->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'nullable|string|max:50',
            'alamat_lengkap' => 'required|string',
            'rt_rw' => 'nullable|string|max:30',
            'kelurahan' => 'nullable|string|max:120',
            'kecamatan' => 'nullable|string|max:120',
            'tinggi_badan' => 'nullable|integer|min:30|max:250',
            'berat_badan' => 'nullable|integer|min:2|max:300',
            'gol_darah' => 'nullable|string|max:10',
            'ukuran_seragam' => 'nullable|string|max:120',
            'nomor_hp' => ['required', 'regex:/^62\d{8,13}$/', Rule::unique('ppdb_applications', 'nomor_hp')],
            'email' => ['required', 'email', 'max:255', Rule::unique('ppdb_applications', 'email')],
            'asal_sekolah' => 'required|string|max:255',
            'alamat_sekolah' => 'nullable|string|max:255',
            'anak_ke' => 'nullable|integer|min:1|max:20',
            'jumlah_saudara' => 'nullable|integer|min:0|max:20',
            'nama_ayah' => 'nullable|string|max:255',
            'tempat_tanggal_lahir_ayah' => 'nullable|string|max:255',
            'pendidikan_terakhir_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'penghasilan_ayah' => 'nullable|string|max:255',
            'alamat_ayah' => 'nullable|string',
            'kelurahan_ayah' => 'nullable|string|max:120',
            'kecamatan_ayah' => 'nullable|string|max:120',
            'nomor_hp_ayah' => 'nullable|regex:/^62\d{8,13}$/',
            'nama_ibu' => 'nullable|string|max:255',
            'tempat_tanggal_lahir_ibu' => 'nullable|string|max:255',
            'pendidikan_terakhir_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'penghasilan_ibu' => 'nullable|string|max:255',
            'alamat_ibu' => 'nullable|string',
            'kelurahan_ibu' => 'nullable|string|max:120',
            'kecamatan_ibu' => 'nullable|string|max:120',
            'nomor_hp_ibu' => 'nullable|regex:/^62\d{8,13}$/',
            'nomor_hp_orang_tua' => 'nullable|regex:/^62\d{8,13}$/',
            'track_id' => ['required', Rule::exists('ppdb_tracks', 'id')->where('period_id', $period->id)],
            'pilihan_program_1_id' => 'required|exists:program_keahlian,id',
            'pilihan_program_2_id' => 'nullable|different:pilihan_program_1_id|exists:program_keahlian,id',
            'nilai_rata_rata' => 'nullable|numeric|min:0|max:100',
            'catatan_pendaftar' => 'nullable|string',
            'persetujuan_data' => 'accepted',
            'prestasi' => 'array|max:3',
            'prestasi.*.achievement_name' => 'nullable|string|max:255',
            'prestasi.*.achievement_rank' => 'nullable|string|max:100',
            'prestasi.*.achievement_level' => 'nullable|string|max:100',
            'file_kk' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'file_akta' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'file_rapor' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'file_pas_foto' => 'required|image|max:4096',
            'file_skl' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ], [
            'nomor_hp.regex' => 'Nomor HP wajib format aktif Indonesia (08... atau 62...).',
            'nomor_hp.unique' => 'Nomor HP sudah terdaftar. Gunakan nomor aktif lainnya.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar. Gunakan email aktif lainnya.',
            'nomor_hp_orang_tua.regex' => 'Nomor HP orang tua harus format aktif Indonesia.',
            'nomor_hp_ayah.regex' => 'Nomor HP ayah harus format aktif Indonesia.',
            'nomor_hp_ibu.regex' => 'Nomor HP ibu harus format aktif Indonesia.',
            'persetujuan_data.accepted' => 'Anda harus menyetujui pernyataan pendaftaran.',
            'prestasi.max' => 'Maksimal 3 data prestasi.',
        ]);

        $achievementRows = collect($validated['prestasi'] ?? [])
            ->map(fn (array $item): array => [
                'achievement_name' => trim((string) ($item['achievement_name'] ?? '')),
                'achievement_rank' => trim((string) ($item['achievement_rank'] ?? '')),
                'achievement_level' => trim((string) ($item['achievement_level'] ?? '')),
            ])
            ->filter(fn (array $item): bool =>
                $item['achievement_name'] !== '' || $item['achievement_rank'] !== '' || $item['achievement_level'] !== '')
            ->values();

        foreach ($achievementRows as $index => $achievementRow) {
            if ($achievementRow['achievement_name'] === '' || $achievementRow['achievement_rank'] === '' || $achievementRow['achievement_level'] === '') {
                $this->addError('prestasi.' . $index . '.achievement_name', 'Setiap prestasi yang diisi harus lengkap: nama prestasi, juara, dan tingkat.');
                return;
            }
        }

        $nomorPendaftaran = $this->generateRegistrationNumber($period);

        $application = PpdbApplication::create([
            'period_id' => $period->id,
            'track_id' => $validated['track_id'],
            'nomor_pendaftaran' => $nomorPendaftaran,
            'nama_lengkap' => $validated['nama_lengkap'],
            'nisn' => $validated['nisn'],
            'nik' => $validated['nik'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'agama' => $this->emptyToNull($validated['agama'] ?? null),
            'alamat_lengkap' => $validated['alamat_lengkap'],
            'rt_rw' => $this->emptyToNull($validated['rt_rw'] ?? null),
            'kelurahan' => $this->emptyToNull($validated['kelurahan'] ?? null),
            'kecamatan' => $this->emptyToNull($validated['kecamatan'] ?? null),
            'tinggi_badan' => $this->toNullableInt($validated['tinggi_badan'] ?? null),
            'berat_badan' => $this->toNullableInt($validated['berat_badan'] ?? null),
            'gol_darah' => $this->emptyToNull($validated['gol_darah'] ?? null),
            'ukuran_seragam' => $this->emptyToNull($validated['ukuran_seragam'] ?? null),
            'nomor_hp' => $validated['nomor_hp'],
            'email' => $validated['email'],
            'asal_sekolah' => $validated['asal_sekolah'],
            'alamat_sekolah' => $this->emptyToNull($validated['alamat_sekolah'] ?? null),
            'anak_ke' => $this->toNullableInt($validated['anak_ke'] ?? null),
            'jumlah_saudara' => $this->toNullableInt($validated['jumlah_saudara'] ?? null),
            'nama_ayah' => $this->emptyToNull($validated['nama_ayah'] ?? null),
            'tempat_tanggal_lahir_ayah' => $this->emptyToNull($validated['tempat_tanggal_lahir_ayah'] ?? null),
            'pendidikan_terakhir_ayah' => $this->emptyToNull($validated['pendidikan_terakhir_ayah'] ?? null),
            'pekerjaan_ayah' => $this->emptyToNull($validated['pekerjaan_ayah'] ?? null),
            'penghasilan_ayah' => $this->emptyToNull($validated['penghasilan_ayah'] ?? null),
            'alamat_ayah' => $this->emptyToNull($validated['alamat_ayah'] ?? null),
            'kelurahan_ayah' => $this->emptyToNull($validated['kelurahan_ayah'] ?? null),
            'kecamatan_ayah' => $this->emptyToNull($validated['kecamatan_ayah'] ?? null),
            'nomor_hp_ayah' => $this->emptyToNull($validated['nomor_hp_ayah'] ?? null),
            'nama_ibu' => $this->emptyToNull($validated['nama_ibu'] ?? null),
            'tempat_tanggal_lahir_ibu' => $this->emptyToNull($validated['tempat_tanggal_lahir_ibu'] ?? null),
            'pendidikan_terakhir_ibu' => $this->emptyToNull($validated['pendidikan_terakhir_ibu'] ?? null),
            'pekerjaan_ibu' => $this->emptyToNull($validated['pekerjaan_ibu'] ?? null),
            'penghasilan_ibu' => $this->emptyToNull($validated['penghasilan_ibu'] ?? null),
            'alamat_ibu' => $this->emptyToNull($validated['alamat_ibu'] ?? null),
            'kelurahan_ibu' => $this->emptyToNull($validated['kelurahan_ibu'] ?? null),
            'kecamatan_ibu' => $this->emptyToNull($validated['kecamatan_ibu'] ?? null),
            'nomor_hp_ibu' => $this->emptyToNull($validated['nomor_hp_ibu'] ?? null),
            'nomor_hp_orang_tua' => $this->emptyToNull($validated['nomor_hp_orang_tua'] ?? null),
            'pilihan_program_1_id' => $validated['pilihan_program_1_id'],
            'pilihan_program_2_id' => $validated['pilihan_program_2_id'] ?: null,
            'nilai_rata_rata' => $validated['nilai_rata_rata'] ?: null,
            'catatan_pendaftar' => $this->emptyToNull($validated['catatan_pendaftar'] ?? null),
            'persetujuan_data_at' => now(),
            'status_pendaftaran' => 'submitted',
            'status_berkas' => 'pending',
            'submitted_at' => now(),
        ]);

        $documents = [
            'Kartu Keluarga' => $this->file_kk,
            'Akta Kelahiran' => $this->file_akta,
            'Rapor / Nilai' => $this->file_rapor,
            'Pas Foto' => $this->file_pas_foto,
            'Surat Keterangan Lulus' => $this->file_skl,
        ];

        foreach ($documents as $jenis => $file) {
            if (! $file) {
                continue;
            }

            PpdbDocument::create([
                'application_id' => $application->id,
                'jenis_dokumen' => $jenis,
                'file_path' => $file->store('ppdb/documents', 'public'),
                'status_verifikasi' => 'pending',
            ]);
        }

        foreach ($achievementRows as $index => $achievementRow) {
            PpdbAchievement::create([
                'application_id' => $application->id,
                'achievement_name' => $achievementRow['achievement_name'],
                'achievement_rank' => $achievementRow['achievement_rank'],
                'achievement_level' => $achievementRow['achievement_level'],
                'sort_order' => $index + 1,
            ]);
        }

        $downloadUrl = $this->buildDownloadUrl($application);

        $this->submittedNumber = $nomorPendaftaran;
        $this->submittedDownloadUrl = $downloadUrl;
        $this->submittedEmail = $application->email;

        $this->sendSubmissionEmail($application, $downloadUrl);
        $this->resetForm();
    }

    protected function generateRegistrationNumber($period): string
    {
        $prefix = sprintf('PPDB-%s-G%s-', $period->tahun_mulai ?? now()->format('Y'), $period->gelombang_ke ?? 1);
        $sequence = PpdbApplication::where('period_id', $period->id)->count() + 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    protected function resetForm(): void
    {
        $trackId = $this->track_id;
        $this->reset([
            'nama_lengkap', 'nisn', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama',
            'alamat_lengkap', 'rt_rw', 'kelurahan', 'kecamatan', 'tinggi_badan', 'berat_badan', 'gol_darah',
            'ukuran_seragam', 'nomor_hp', 'email', 'asal_sekolah', 'alamat_sekolah', 'anak_ke', 'jumlah_saudara',
            'nama_ayah', 'tempat_tanggal_lahir_ayah', 'pendidikan_terakhir_ayah', 'pekerjaan_ayah', 'penghasilan_ayah',
            'alamat_ayah', 'kelurahan_ayah', 'kecamatan_ayah', 'nomor_hp_ayah', 'nama_ibu',
            'tempat_tanggal_lahir_ibu', 'pendidikan_terakhir_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'alamat_ibu',
            'kelurahan_ibu', 'kecamatan_ibu', 'nomor_hp_ibu', 'nomor_hp_orang_tua', 'pilihan_program_1_id',
            'pilihan_program_2_id', 'nilai_rata_rata', 'catatan_pendaftar', 'persetujuan_data', 'prestasi',
            'file_kk', 'file_akta', 'file_rapor', 'file_pas_foto', 'file_skl',
        ]);
        $this->jenis_kelamin = 'L';
        $this->agama = 'Islam';
        $this->track_id = $trackId;
        $this->persetujuan_data = false;
        $this->initializePrestasiRows();
        $this->resetValidation();
    }

    protected function initializePrestasiRows(): void
    {
        if ($this->prestasi === []) {
            $this->prestasi = [$this->emptyPrestasiRow()];
        }
    }

    protected function emptyPrestasiRow(): array
    {
        return [
            'achievement_name' => '',
            'achievement_rank' => '',
            'achievement_level' => '',
        ];
    }

    protected function normalizePhoneNumber(string $rawPhone): string
    {
        $digits = preg_replace('/\D+/', '', trim($rawPhone));

        if (! is_string($digits) || $digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }

    protected function normalizeOptionalPhone(?string $rawPhone): ?string
    {
        $normalized = $this->normalizePhoneNumber((string) ($rawPhone ?? ''));

        return $normalized !== '' ? $normalized : null;
    }

    protected function emptyToNull(?string $value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));

        return $trimmed !== '' ? $trimmed : null;
    }

    protected function toNullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    protected function buildDownloadUrl(PpdbApplication $application): string
    {
        return SignedUrl::temporarySignedRoute(
            'ppdb.form.download',
            now()->addDays(30),
            ['application' => $application->id]
        );
    }

    protected function sendSubmissionEmail(PpdbApplication $application, string $downloadUrl): void
    {
        $this->submissionEmailSent = false;

        try {
            Mail::to($application->email)->send(
                new PpdbRegistrationSubmittedMail($application, $downloadUrl)
            );

            $this->submissionEmailSent = true;
        } catch (Throwable $exception) {
            Log::warning('PPDB submission email failed to send.', [
                'application_id' => $application->id,
                'email' => $application->email,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $period = $this->resolveSelectedPeriod();
        $availablePeriods = app(PpdbPeriodResolver::class)->publicOptions();
        $selectedPeriodId = $period?->id;

        $programs = ProgramKeahlian::tampil()->orderBy('nama_jurusan')->get();
        $applicationsCount = $period ? PpdbApplication::where('period_id', $period->id)->count() : 0;

        return view('livewire.frontend.ppdb-form-page', compact('period', 'programs', 'applicationsCount', 'availablePeriods', 'selectedPeriodId'));
    }

    protected function resolveSelectedPeriod()
    {
        $resolver = app(PpdbPeriodResolver::class);

        return $resolver->resolvePublic(
            $resolver->resolveInput($this->selectedPeriod),
            [
                'tracks' => fn ($query) => $query->visible(),
                'quotas.programKeahlian',
            ]
        );
    }
}