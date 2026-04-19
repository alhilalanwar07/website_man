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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL as SignedUrl;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

#[Layout('components.layouts.ppdb-form')]
#[Title('Form Pendaftaran PPDB')]
class PpdbFormPage extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $currentStep = 1;

    public const TOTAL_STEPS = 7;
    public const STEP_LABELS = [
        1 => 'Pernyataan', 2 => 'Data Pribadi', 3 => 'Sekolah & Jurusan',
        4 => 'Data Orang Tua', 5 => 'Prestasi', 6 => 'Berkas', 7 => 'Konfirmasi',
    ];

    #[Url(as: 'periode')]
    public string $selectedPeriod = '';

    // Step 1
    public bool $persetujuan_data = false;

    // Step 2: Data Pribadi
    public string $nama_lengkap = '';
    public string $nisn = '';
    public ?string $nisnRealtimeWarning = null;
    public string $nik = '';
    public string $jenis_kelamin = 'L';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $agama = 'Islam';
    public string $alamat_lengkap = '';
    public string $rt_rw = '';
    public string $kelurahan = '';
    public string $kecamatan = '';
    public string $anak_ke = '';
    public string $jumlah_saudara = '';
    public string $tinggi_badan = '';
    public string $berat_badan = '';
    public string $gol_darah = '';
    public string $ukuran_seragam = '';
    public string $nomor_hp = '';
    public string $email = '';

    // Step 3: Sekolah & Jurusan
    public string $asal_sekolah = '';
    public string $alamat_sekolah = '';
    public string $nilai_rata_rata = '';
    public string $track_id = '';
    public string $pilihan_program_1_id = '';
    public string $pilihan_program_2_id = '';
    public string $pilihan_program_3_id = '';

    // Step 4: Data Orang Tua (TTL split)
    public string $nama_ayah = '';
    public string $tempat_lahir_ayah = '';
    public string $tanggal_lahir_ayah = '';
    public string $pendidikan_terakhir_ayah = '';
    public string $pekerjaan_ayah = '';
    public string $penghasilan_ayah = '';
    public string $alamat_ayah = '';
    public string $kelurahan_ayah = '';
    public string $kecamatan_ayah = '';
    public string $nomor_hp_ayah = '';
    public string $nama_ibu = '';
    public string $tempat_lahir_ibu = '';
    public string $tanggal_lahir_ibu = '';
    public string $pendidikan_terakhir_ibu = '';
    public string $pekerjaan_ibu = '';
    public string $penghasilan_ibu = '';
    public string $alamat_ibu = '';
    public string $kelurahan_ibu = '';
    public string $kecamatan_ibu = '';
    public string $nomor_hp_ibu = '';
    public bool $alamat_ibu_sama = false;

    // Step 5
    public array $prestasi = [];

    // Step 6: Berkas (updated)
    public $file_kk;
    public $file_akta;
    public $file_rapor_cover;
    public $file_rapor_nilai;
    public $file_pas_foto;
    public $file_skl;
    public string $catatan_pendaftar = '';

    // Submission
    public ?string $submittedNumber = null;
    public ?string $submittedDownloadUrl = null;
    public ?string $submittedEmail = null;
    public bool $submissionEmailSent = false;

    // ── Lifecycle ──

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

        $this->refreshNisnWarning();
    }

    public function updatedSelectedPeriod(): void
    {
        $period = $this->resolveSelectedPeriod();
        $this->track_id = $period && $period->tracks->isNotEmpty() ? (string) $period->tracks->first()->id : '';
    }

    public function updatedNisn(string $value): void
    {
        $this->nisn = $value;
        $this->refreshNisnWarning();

        if ($this->nisnRealtimeWarning === null) {
            $this->resetValidation('nisn');
        }
    }

    public function applyDraft(array $draft): void
    {
        if ($draft === []) {
            return;
        }

        $allowedPaths = $this->draftFieldWhitelist();
        $restored = [];

        foreach ($draft as $path => $value) {
            if (! is_string($path)) {
                continue;
            }

            if (! in_array($path, $allowedPaths, true) && ! str_starts_with($path, 'prestasi.')) {
                continue;
            }

            data_set($restored, $path, $value);
        }

        foreach ($allowedPaths as $path) {
            if (! array_key_exists($path, $restored)) {
                continue;
            }

            $this->{$path} = $this->sanitizeDraftValue($path, $restored[$path]);
        }

        if (array_key_exists('prestasi', $restored) && is_array($restored['prestasi'])) {
            $this->prestasi = collect($restored['prestasi'])
                ->take(3)
                ->map(fn ($row): array => [
                    'achievement_name' => is_array($row) ? (string) ($row['achievement_name'] ?? '') : '',
                    'achievement_rank' => is_array($row) ? (string) ($row['achievement_rank'] ?? '') : '',
                    'achievement_level' => is_array($row) ? (string) ($row['achievement_level'] ?? '') : '',
                ])
                ->values()
                ->all();
        }

        $this->currentStep = max(1, min(self::TOTAL_STEPS, (int) $this->currentStep));
        $this->initializePrestasiRows();
        $this->refreshNisnWarning();
        $this->resetValidation();
    }

    // ── Step Navigation ──

    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > self::TOTAL_STEPS || $step > $this->currentStep) {
            return;
        }
        $this->currentStep = $step;
        $this->dispatch('step-changed');
    }

    public function nextStep(): void
    {
        if ($this->currentStep >= self::TOTAL_STEPS) {
            return;
        }
        if (! $this->validateCurrentStep()) {
            return;
        }
        $this->currentStep++;
        $this->dispatch('step-changed');
    }

    public function previousStep(): void
    {
        if ($this->currentStep <= 1) {
            return;
        }
        $this->currentStep--;
        $this->dispatch('step-changed');
    }

    // ── Per-Step Validation ──

    protected function validateCurrentStep(): bool
    {
        return match ($this->currentStep) {
            1 => $this->validateStep1(),
            2 => $this->validateStep2(),
            3 => $this->validateStep3(),
            4 => $this->validateStep4(),
            5 => $this->validateStep5(),
            6 => $this->validateStep6(),
            default => true,
        };
    }

    protected function validateStep1(): bool
    {
        $this->validate(['persetujuan_data' => 'accepted'], [
            'persetujuan_data.accepted' => 'Anda harus menyetujui pernyataan untuk melanjutkan.',
        ]);
        return true;
    }

    protected function validateStep2(): bool
    {
        $this->nomor_hp = $this->normalizePhoneNumber($this->nomor_hp);
        $this->email = strtolower(trim($this->email));
        $this->refreshNisnWarning();

        $this->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => ['required', 'digits:10', Rule::unique('ppdb_applications', 'nisn')],
            'nik' => ['required', 'string', 'max:20', Rule::unique('ppdb_applications', 'nik')],
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:' . now()->subYears(14)->format('Y-m-d')],
            'agama' => 'required|string|max:50',
            'alamat_lengkap' => 'required|string',
            'rt_rw' => 'nullable|string|max:30',
            'kelurahan' => 'required|string|max:120',
            'kecamatan' => 'required|string|max:120',
            'anak_ke' => 'required|integer|min:1|max:20',
            'jumlah_saudara' => 'required|integer|min:0|max:20',
            'tinggi_badan' => 'required|integer|min:30|max:250',
            'berat_badan' => 'required|integer|min:2|max:300',
            'gol_darah' => 'required|string|max:10',
            'ukuran_seragam' => 'required|string|max:120',
            'nomor_hp' => ['required', 'regex:/^62\d{8,13}$/', Rule::unique('ppdb_applications', 'nomor_hp')],
            'email' => ['required', 'email', 'max:255', Rule::unique('ppdb_applications', 'email')],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus 10 digit angka.',
            'nisn.unique' => 'NISN sudah terdaftar di sistem.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar di sistem.',
            'tanggal_lahir.before_or_equal' => 'Usia minimal 14 tahun untuk mendaftar.',
            'kelurahan.required' => 'Kelurahan wajib diisi.',
            'kecamatan.required' => 'Kecamatan wajib diisi.',
            'anak_ke.required' => 'Anak ke- wajib diisi.',
            'jumlah_saudara.required' => 'Jumlah saudara wajib diisi.',
            'tinggi_badan.required' => 'Tinggi badan wajib diisi.',
            'berat_badan.required' => 'Berat badan wajib diisi.',
            'gol_darah.required' => 'Golongan darah wajib diisi.',
            'ukuran_seragam.required' => 'Ukuran seragam wajib diisi.',
            'nomor_hp.regex' => 'Nomor HP wajib format Indonesia (08... atau 62...).',
            'nomor_hp.unique' => 'Nomor HP sudah terdaftar di sistem.',
            'email.unique' => 'Email sudah terdaftar. Gunakan email lain.',
        ]);
        return true;
    }

    protected function validateStep3(): bool
    {
        $period = $this->resolveSelectedPeriod();

        $this->validate([
            'asal_sekolah' => 'required|string|max:255',
            'alamat_sekolah' => 'required|string|max:255',
            'nilai_rata_rata' => 'required|numeric|min:0|max:100',
            'track_id' => ['required', Rule::exists('ppdb_tracks', 'id')->where('period_id', $period?->id)],
            'pilihan_program_1_id' => 'required|exists:program_keahlian,id',
            'pilihan_program_2_id' => 'required|exists:program_keahlian,id',
            'pilihan_program_3_id' => 'required|exists:program_keahlian,id',
        ], [
            'alamat_sekolah.required' => 'Alamat sekolah wajib diisi.',
            'nilai_rata_rata.required' => 'Nilai rata-rata rapor wajib diisi.',
            'pilihan_program_2_id.required' => 'Jurusan pilihan 2 wajib dipilih.',
            'pilihan_program_3_id.required' => 'Jurusan pilihan 3 wajib dipilih.',
        ]);

        // Enforce unique jurusan selection
        $selected = [$this->pilihan_program_1_id, $this->pilihan_program_2_id, $this->pilihan_program_3_id];
        if (count($selected) !== count(array_unique($selected))) {
            $this->addError('pilihan_program_2_id', 'Pilihan jurusan tidak boleh sama. Silakan pilih jurusan yang berbeda.');
            return false;
        }
        return true;
    }

    protected function validateStep4(): bool
    {
        $this->nomor_hp_ayah = $this->normalizeOptionalPhone($this->nomor_hp_ayah) ?? '';
        $this->nomor_hp_ibu = $this->normalizeOptionalPhone($this->nomor_hp_ibu) ?? '';

        // Copy address from father if checked
        if ($this->alamat_ibu_sama) {
            $this->alamat_ibu = $this->alamat_ayah;
            $this->kelurahan_ibu = $this->kelurahan_ayah;
            $this->kecamatan_ibu = $this->kecamatan_ayah;
        }

        $this->validate([
            'nama_ayah' => 'required|string|max:255',
            'tempat_lahir_ayah' => 'required|string|max:100',
            'tanggal_lahir_ayah' => 'required|date',
            'pendidikan_terakhir_ayah' => 'required|string|max:255',
            'pekerjaan_ayah' => 'required|string|max:255',
            'penghasilan_ayah' => 'required|string|max:255',
            'alamat_ayah' => 'required|string',
            'kelurahan_ayah' => 'required|string|max:120',
            'kecamatan_ayah' => 'required|string|max:120',
            'nomor_hp_ayah' => 'nullable|regex:/^62\d{8,13}$/',
            'nama_ibu' => 'required|string|max:255',
            'tempat_lahir_ibu' => 'required|string|max:100',
            'tanggal_lahir_ibu' => 'required|date',
            'pendidikan_terakhir_ibu' => 'required|string|max:255',
            'pekerjaan_ibu' => 'required|string|max:255',
            'penghasilan_ibu' => 'required|string|max:255',
            'alamat_ibu' => 'required|string',
            'kelurahan_ibu' => 'required|string|max:120',
            'kecamatan_ibu' => 'required|string|max:120',
            'nomor_hp_ibu' => 'nullable|regex:/^62\d{8,13}$/',
        ], [
            'nama_ayah.required' => 'Nama ayah wajib diisi.',
            'nama_ibu.required' => 'Nama ibu wajib diisi.',
            'tempat_lahir_ayah.required' => 'Tempat lahir ayah wajib diisi.',
            'tanggal_lahir_ayah.required' => 'Tanggal lahir ayah wajib diisi.',
            'tempat_lahir_ibu.required' => 'Tempat lahir ibu wajib diisi.',
            'tanggal_lahir_ibu.required' => 'Tanggal lahir ibu wajib diisi.',
            'pekerjaan_ayah.required' => 'Pekerjaan ayah wajib diisi.',
            'pekerjaan_ibu.required' => 'Pekerjaan ibu wajib diisi.',
            'penghasilan_ayah.required' => 'Penghasilan ayah wajib diisi.',
            'penghasilan_ibu.required' => 'Penghasilan ibu wajib diisi.',
            'alamat_ayah.required' => 'Alamat ayah wajib diisi.',
            'alamat_ibu.required' => 'Alamat ibu wajib diisi.',
            'kelurahan_ayah.required' => 'Kelurahan ayah wajib diisi.',
            'kecamatan_ayah.required' => 'Kecamatan ayah wajib diisi.',
            'kelurahan_ibu.required' => 'Kelurahan ibu wajib diisi.',
            'kecamatan_ibu.required' => 'Kecamatan ibu wajib diisi.',
            'nomor_hp_ayah.regex' => 'Format nomor HP ayah tidak valid.',
            'nomor_hp_ibu.regex' => 'Format nomor HP ibu tidak valid.',
        ]);

        // At least one parent phone required
        if (trim($this->nomor_hp_ayah) === '' && trim($this->nomor_hp_ibu) === '') {
            $this->addError('nomor_hp_ayah', 'Minimal salah satu nomor HP/WA orang tua (ayah atau ibu) wajib diisi.');
            return false;
        }
        return true;
    }

    public function updatedAlamatIbuSama(bool $value): void
    {
        if ($value) {
            $this->alamat_ibu = $this->alamat_ayah;
            $this->kelurahan_ibu = $this->kelurahan_ayah;
            $this->kecamatan_ibu = $this->kecamatan_ayah;
        }
    }

    protected function validateStep5(): bool
    {
        $this->validate([
            'prestasi' => 'array|max:3',
            'prestasi.*.achievement_name' => 'nullable|string|max:255',
            'prestasi.*.achievement_rank' => 'nullable|string|max:100',
            'prestasi.*.achievement_level' => 'nullable|string|max:100',
        ]);

        $filled = collect($this->prestasi)->filter(fn (array $r): bool => trim($r['achievement_name'] ?? '') !== '' || trim($r['achievement_rank'] ?? '') !== '' || trim($r['achievement_level'] ?? '') !== '');
        foreach ($filled as $i => $r) {
            if (trim($r['achievement_name'] ?? '') === '' || trim($r['achievement_rank'] ?? '') === '' || trim($r['achievement_level'] ?? '') === '') {
                $this->addError("prestasi.{$i}.achievement_name", 'Prestasi yang diisi harus lengkap: nama, juara, dan tingkat.');
                return false;
            }
        }
        return true;
    }

    protected function validateStep6(): bool
    {
        $this->validate([
            'file_kk' => 'required|file|mimes:pdf|max:4096',
            'file_akta' => 'required|file|mimes:pdf|max:4096',
            'file_rapor_cover' => 'required|file|mimes:pdf|max:4096',
            'file_rapor_nilai' => 'required|file|mimes:pdf|max:4096',
            'file_pas_foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'file_skl' => 'required|file|mimes:pdf|max:4096',
            'catatan_pendaftar' => 'nullable|string',
        ], [
            'file_kk.mimes' => 'KK harus berformat PDF.',
            'file_akta.mimes' => 'Akta harus berformat PDF.',
            'file_rapor_cover.mimes' => 'Halaman depan rapor harus PDF.',
            'file_rapor_nilai.mimes' => 'Nilai rapor harus PDF.',
            'file_pas_foto.image' => 'Pas foto harus berupa gambar (JPG/PNG).',
            'file_pas_foto.max' => 'Ukuran pas foto maksimal 2 MB.',
            'file_skl.mimes' => 'Ijazah/SKL harus berformat PDF.',
        ]);
        return true;
    }

    // ── Prestasi ──

    public function addPrestasiRow(): void
    {
        if (count($this->prestasi) >= 3) return;
        $this->prestasi[] = $this->emptyPrestasiRow();
    }

    public function removePrestasiRow(int $index): void
    {
        if (! isset($this->prestasi[$index])) return;
        unset($this->prestasi[$index]);
        $this->prestasi = array_values($this->prestasi);
        $this->initializePrestasiRows();
    }

    // ── Submission ──

    public function submitApplication(): void
    {
        $rateLimitKey = 'ppdb-submit:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $this->addError('period', 'Terlalu banyak percobaan. Silakan tunggu beberapa saat.');
            return;
        }
        RateLimiter::hit($rateLimitKey, 60);

        $period = $this->resolveSelectedPeriod();
        if (! $period) { $this->addError('period', 'Periode PPDB belum dibuka.'); return; }
        if (! $period->isRegistrationOpen()) { $this->addError('period', 'Pendaftaran belum dibuka.'); return; }

        for ($step = 1; $step <= 6; $step++) {
            $this->currentStep = $step;
            try { if (! $this->validateCurrentStep()) return; }
            catch (\Illuminate\Validation\ValidationException $e) { return; }
        }
        $this->currentStep = 7;

        $achievementRows = collect($this->prestasi)
            ->map(fn (array $i): array => ['achievement_name' => trim($i['achievement_name'] ?? ''), 'achievement_rank' => trim($i['achievement_rank'] ?? ''), 'achievement_level' => trim($i['achievement_level'] ?? '')])
            ->filter(fn (array $i): bool => $i['achievement_name'] !== '')
            ->values();

        $nomorPendaftaran = $this->generateRegistrationNumber($period);

        // Combine TTL for DB compatibility
        $ttlAyah = trim($this->tempat_lahir_ayah) . ', ' . $this->tanggal_lahir_ayah;
        $ttlIbu = trim($this->tempat_lahir_ibu) . ', ' . $this->tanggal_lahir_ibu;
        // Determine primary parent phone
        $hpOrtu = $this->emptyToNull($this->nomor_hp_ayah) ?? $this->emptyToNull($this->nomor_hp_ibu) ?? '';

        $application = PpdbApplication::create([
            'period_id' => $period->id,
            'track_id' => $this->track_id,
            'nomor_pendaftaran' => $nomorPendaftaran,
            'nama_lengkap' => $this->nama_lengkap,
            'nisn' => $this->nisn,
            'nik' => $this->nik,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'agama' => $this->emptyToNull($this->agama),
            'alamat_lengkap' => $this->alamat_lengkap,
            'rt_rw' => $this->emptyToNull($this->rt_rw),
            'kelurahan' => $this->emptyToNull($this->kelurahan),
            'kecamatan' => $this->emptyToNull($this->kecamatan),
            'tinggi_badan' => $this->toNullableInt($this->tinggi_badan),
            'berat_badan' => $this->toNullableInt($this->berat_badan),
            'gol_darah' => $this->emptyToNull($this->gol_darah),
            'ukuran_seragam' => $this->emptyToNull($this->ukuran_seragam),
            'nomor_hp' => $this->nomor_hp,
            'email' => $this->email,
            'asal_sekolah' => $this->asal_sekolah,
            'alamat_sekolah' => $this->emptyToNull($this->alamat_sekolah),
            'anak_ke' => $this->toNullableInt($this->anak_ke),
            'jumlah_saudara' => $this->toNullableInt($this->jumlah_saudara),
            'nama_ayah' => $this->nama_ayah,
            'tempat_tanggal_lahir_ayah' => $ttlAyah,
            'pendidikan_terakhir_ayah' => $this->emptyToNull($this->pendidikan_terakhir_ayah),
            'pekerjaan_ayah' => $this->emptyToNull($this->pekerjaan_ayah),
            'penghasilan_ayah' => $this->emptyToNull($this->penghasilan_ayah),
            'alamat_ayah' => $this->emptyToNull($this->alamat_ayah),
            'kelurahan_ayah' => $this->emptyToNull($this->kelurahan_ayah),
            'kecamatan_ayah' => $this->emptyToNull($this->kecamatan_ayah),
            'nomor_hp_ayah' => $this->emptyToNull($this->nomor_hp_ayah),
            'nama_ibu' => $this->nama_ibu,
            'tempat_tanggal_lahir_ibu' => $ttlIbu,
            'pendidikan_terakhir_ibu' => $this->emptyToNull($this->pendidikan_terakhir_ibu),
            'pekerjaan_ibu' => $this->emptyToNull($this->pekerjaan_ibu),
            'penghasilan_ibu' => $this->emptyToNull($this->penghasilan_ibu),
            'alamat_ibu' => $this->emptyToNull($this->alamat_ibu),
            'kelurahan_ibu' => $this->emptyToNull($this->kelurahan_ibu),
            'kecamatan_ibu' => $this->emptyToNull($this->kecamatan_ibu),
            'nomor_hp_ibu' => $this->emptyToNull($this->nomor_hp_ibu),
            'nomor_hp_orang_tua' => $this->emptyToNull($hpOrtu),
            'pilihan_program_1_id' => $this->pilihan_program_1_id,
            'pilihan_program_2_id' => $this->pilihan_program_2_id ?: null,
            'pilihan_program_3_id' => $this->pilihan_program_3_id ?: null,
            'nilai_rata_rata' => $this->nilai_rata_rata ?: null,
            'catatan_pendaftar' => $this->emptyToNull($this->catatan_pendaftar),
            'persetujuan_data_at' => now(),
            'status_pendaftaran' => 'submitted',
            'status_berkas' => 'pending',
            'submitted_at' => now(),
        ]);

        $documents = [
            'Kartu Keluarga' => $this->file_kk,
            'Akta Kelahiran' => $this->file_akta,
            'Halaman Depan Rapor' => $this->file_rapor_cover,
            'Nilai Rapor' => $this->file_rapor_nilai,
            'Pas Foto' => $this->file_pas_foto,
            'Ijazah / SKL' => $this->file_skl,
        ];

        foreach ($documents as $jenis => $file) {
            if (! $file) continue;
            PpdbDocument::create([
                'application_id' => $application->id,
                'jenis_dokumen' => $jenis,
                'file_path' => $file->store('ppdb/documents', 'public'),
                'status_verifikasi' => 'pending',
            ]);
        }

        foreach ($achievementRows as $index => $row) {
            PpdbAchievement::create([
                'application_id' => $application->id,
                'achievement_name' => $row['achievement_name'],
                'achievement_rank' => $row['achievement_rank'],
                'achievement_level' => $row['achievement_level'],
                'sort_order' => $index + 1,
            ]);
        }

        $downloadUrl = $this->buildDownloadUrl($application);
        $this->submittedNumber = $nomorPendaftaran;
        $this->submittedDownloadUrl = $downloadUrl;
        $this->submittedEmail = $application->email;
        $this->sendSubmissionEmail($application, $downloadUrl);
        $this->dispatch('ppdb-draft-clear');
    }

    // ── Computed ──

    #[Computed]
    public function stepLabels(): array { return self::STEP_LABELS; }

    #[Computed]
    public function progressPercent(): int { return (int) round(($this->currentStep / self::TOTAL_STEPS) * 100); }

    // ── Helpers ──

    protected function generateRegistrationNumber($period): string
    {
        $prefix = sprintf('PPDB-%s-G%s-', $period->tahun_mulai ?? now()->format('Y'), $period->gelombang_ke ?? 1);
        return $prefix . str_pad((string) (PpdbApplication::where('period_id', $period->id)->count() + 1), 4, '0', STR_PAD_LEFT);
        
    }

    protected function initializePrestasiRows(): void
    {
        if ($this->prestasi === []) $this->prestasi = [$this->emptyPrestasiRow()];
    }

    protected function emptyPrestasiRow(): array
    {
        return ['achievement_name' => '', 'achievement_rank' => '', 'achievement_level' => ''];
    }

    protected function normalizePhoneNumber(string $rawPhone): string
    {
        $digits = preg_replace('/\D+/', '', trim($rawPhone));
        if (! is_string($digits) || $digits === '') return '';
        if (str_starts_with($digits, '0')) return '62' . substr($digits, 1);
        if (str_starts_with($digits, '8')) return '62' . $digits;
        return $digits;
    }

    protected function normalizeOptionalPhone(?string $rawPhone): ?string
    {
        $n = $this->normalizePhoneNumber((string) ($rawPhone ?? ''));
        return $n !== '' ? $n : null;
    }

    protected function refreshNisnWarning(): void
    {
        $digits = preg_replace('/\D+/', '', $this->nisn);
        $this->nisn = is_string($digits) ? substr($digits, 0, 10) : '';

        $this->nisnRealtimeWarning = $this->nisn !== '' && strlen($this->nisn) < 10
            ? 'NISN minimal 10 digit. Pastikan lengkap sebelum lanjut ke langkah berikutnya.'
            : null;
    }

    protected function draftFieldWhitelist(): array
    {
        return [
            'currentStep',
            'selectedPeriod',
            'persetujuan_data',
            'nama_lengkap',
            'nisn',
            'nik',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'alamat_lengkap',
            'rt_rw',
            'kelurahan',
            'kecamatan',
            'anak_ke',
            'jumlah_saudara',
            'tinggi_badan',
            'berat_badan',
            'gol_darah',
            'ukuran_seragam',
            'nomor_hp',
            'email',
            'asal_sekolah',
            'alamat_sekolah',
            'nilai_rata_rata',
            'track_id',
            'pilihan_program_1_id',
            'pilihan_program_2_id',
            'pilihan_program_3_id',
            'nama_ayah',
            'tempat_lahir_ayah',
            'tanggal_lahir_ayah',
            'pendidikan_terakhir_ayah',
            'pekerjaan_ayah',
            'penghasilan_ayah',
            'alamat_ayah',
            'kelurahan_ayah',
            'kecamatan_ayah',
            'nomor_hp_ayah',
            'nama_ibu',
            'tempat_lahir_ibu',
            'tanggal_lahir_ibu',
            'pendidikan_terakhir_ibu',
            'pekerjaan_ibu',
            'penghasilan_ibu',
            'alamat_ibu',
            'kelurahan_ibu',
            'kecamatan_ibu',
            'nomor_hp_ibu',
            'alamat_ibu_sama',
            'catatan_pendaftar',
        ];
    }

    protected function sanitizeDraftValue(string $path, mixed $value): mixed
    {
        return match ($path) {
            'currentStep' => max(1, min(self::TOTAL_STEPS, (int) $value)),
            'persetujuan_data', 'alamat_ibu_sama' => filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
            default => is_scalar($value) ? (string) $value : '',
        };
    }

    protected function emptyToNull(?string $value): ?string
    {
        $t = trim((string) ($value ?? ''));
        return $t !== '' ? $t : null;
    }

    protected function toNullableInt(mixed $value): ?int
    {
        return ($value === null || $value === '') ? null : (int) $value;
    }

    protected function buildDownloadUrl(PpdbApplication $application): string
    {
        return SignedUrl::temporarySignedRoute('ppdb.form.download', now()->addDays(30), ['application' => $application->id]);
    }

    protected function sendSubmissionEmail(PpdbApplication $application, string $downloadUrl): void
    {
        $this->submissionEmailSent = false;
        try {
            Mail::to($application->email)->send(new PpdbRegistrationSubmittedMail($application, $downloadUrl));
            $this->submissionEmailSent = true;
        } catch (Throwable $e) {
            Log::warning('PPDB email failed.', ['id' => $application->id, 'msg' => $e->getMessage()]);
        }
    }

    protected function resolveSelectedPeriod()
    {
        $resolver = app(PpdbPeriodResolver::class);
        return $resolver->resolvePublic($resolver->resolveInput($this->selectedPeriod), [
            'tracks' => fn ($q) => $q->visible(), 'quotas.programKeahlian',
        ]);
    }

    public function render()
    {
        $period = $this->resolveSelectedPeriod();
        $availablePeriods = app(PpdbPeriodResolver::class)->publicOptions();
        $programs = ProgramKeahlian::tampil()->orderBy('nama_jurusan')->get();
        return view('livewire.frontend.ppdb-form-page', compact('period', 'programs', 'availablePeriods'));
    }
}